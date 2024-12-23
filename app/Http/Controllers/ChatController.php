<?php

namespace App\Http\Controllers;

use Ably\AblyRest;
use App\Models\Blog;
use App\Models\Chat;
use App\Models\Chat_Bg;
use App\Models\Set_Chat_Bg;
use App\Models\User;
use Illuminate\Http\Request;
use Avatar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ChatController extends Controller
{
    function message() {
        $recent_blogs = Blog::latest()->limit(10)->get();
        $chat_bgs = Chat_Bg::latest()->get();
        // $users = User::where('id', '!=', Auth::guard('user')->user()->id)->latest()->get();
        $ablyApiKey = env('ABLY_API_KEY');

        $authUserId = Auth::guard('user')->user()->id;

        $users = User::select(
                'users.id',
                'users.fname',
                'users.lname',
                'users.slug',
                'users.photo',
                DB::raw('MAX(chats.created_at) as last_message_time'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(chats.message ORDER BY chats.created_at DESC), ",", 1) as last_message')
            )
            ->leftJoin('chats', function ($join) use ($authUserId) {
                $join->on('users.id', '=', 'chats.sender_id')
                    ->where('chats.recipient_id', '=', $authUserId)
                    ->orWhere(function ($query) use ($authUserId) {
                        $query->on('users.id', '=', 'chats.recipient_id')
                              ->where('chats.sender_id', '=', $authUserId);
                    });
            })
            ->where('users.id', '!=', $authUserId)
            ->groupBy('users.id', 'users.fname', 'users.lname', 'users.slug', 'users.photo')
            ->orderBy('last_message_time', 'desc')
            ->get();

        foreach ($users as $user) {
            $lastMessage = Chat::where(function ($query) use ($authUserId, $user) {
                    $query->where('sender_id', $authUserId)->where('recipient_id', $user->id);
                })
                ->orWhere(function ($query) use ($authUserId, $user) {
                    $query->where('sender_id', $user->id)->where('recipient_id', $authUserId);
                })
                ->latest()
                ->first();

            $user->last_message = $lastMessage;
        }

        return view('Frontend.chat.chat', [
            'recent_blogs' => $recent_blogs,
            'users' => $users,
            'chat_bgs' => $chat_bgs,
            'ablyApiKey' => $ablyApiKey,
        ]);
    }

    function show_chat($recipientId) {
        $authUserId = Auth::guard('user')->user()->id;
        $recipient = User::findOrFail($recipientId);

        Chat::where('recipient_id', $authUserId)->where('sender_id', $recipientId)->update([
            'see' => true,
        ]);

        $messages = Chat::where(function ($query) use ($authUserId, $recipientId) {
            $query->where('sender_id', $authUserId)->where('recipient_id', $recipientId);
        })
        ->orWhere(function ($query) use ($authUserId, $recipientId) {
            $query->where('sender_id', $recipientId)->where('recipient_id', $authUserId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        if (!$recipient) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $avatar = Avatar::create($recipient->fname . ' ' . ($recipient->lname ?? ''))->toBase64();

        return response()->json([
            'recipient' => $recipient,
            'messages' => $messages,
            'avatar' => $avatar,
        ], 200);
    }

    public function send_message(Request $request, $recipientId) {
        if (!$request->has('message') && !$request->hasFile('image_name')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message or image is required.',
            ], 422);
        }

        $sender = Auth::guard('user')->user();
        $recipientId_image = User::find($sender->id)->photo;
        $file_name = null;
        $chat_text = '';

        if ($request->has('message')) {
            $chat_text = $request->message;
        }

        if ($request->hasFile('image_name')) {
            $image = $request->file('image_name');
            $extension = $image->extension();
            $file_name = uniqid() . '.' . $extension;

            $maneger = new ImageManager(new Driver);
            $maneger->read($image)->toJpeg(80)->save(public_path('upload/chats/'.$file_name));
        }

        $chat_id = Chat::insertGetId([
            'sender_id' => $sender->id,
            'recipient_id' => $recipientId,
            'recipient_image' => $recipientId_image,
            'sender_name' => $sender->fname,
            'message' => $chat_text,
            'image' => $file_name,
            'created_at' => Carbon::now()
        ]);

        // Ably মেসেজ পাঠানো
        try {
            $ably = new AblyRest(env('ABLY_API_KEY'));

            $channelName = $this->getPrivateChannelName($sender->id, $recipientId);
            $channel = $ably->channels->get($channelName);

            $channel->publish('message', [
                'sender_id' => $sender->id,
                'sender_name' => $sender->fname,
                'sender_image' => $sender->photo === null
                    ? Avatar::create($sender->fname . ' ' . ($sender->lname ?? ''))->toBase64()
                    : $sender->photo,
                'message' => $chat_text,
                'chat_image' => $file_name == null ? null : asset('upload/chats/' . $file_name),
                'chat_id' => $chat_id,
                'seen' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message could not be sent via Ably.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully',
        ]);
    }


    function mark_as_seen($chatId, $recipientId) {
        $chat = Chat::findOrFail($chatId);
        $ably = new AblyRest(env('ABLY_API_KEY'));
        $sender = Auth::guard('user')->user();

        $channelName = $this->getPrivateChannelName($sender->id, $recipientId);
        $channel = $ably->channels->get($channelName);
        $channel->publish('message_update', [
            'chat_id' => $chat->id,
            'seen' => true,
        ]);

        if($chat) {
            $chat->update([
                'see' => true,
            ]);
            return response()->json([
                'status' => 'success',
                'chatId' => $chatId,
                'recipientId' => $recipientId,
                'message' => 'Message marked as seen'
            ]);
        }else {
            return response()->json(['status' => 'error', 'message' => 'Chat not found'], 403);
        }
    }

    /**
     * Generate a unique private channel name for the sender and recipient.
     */
    private function getPrivateChannelName($senderId, $recipientId) {
        $sortedIds = [min($senderId, $recipientId), max($senderId, $recipientId)];
        return 'private-' . implode('-', $sortedIds);
    }

    function delete_chat($msg_id) {
        $chat = Chat::find($msg_id);

        if (!$chat) {
            return response()->json(['status' => 'error', 'message' => 'Chat message not found'], 404);
        }

        if ($chat->image != null) {
            $chat_img_file = public_path('upload/chats/' . $chat->image);
            if (file_exists($chat_img_file)) {
                unlink($chat_img_file);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Image file not found'], 404);
            }
        }
        $chat->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Chat deleted successfully'
        ]);
    }


    function add_chat_background() {
        $chat_bgs = Chat_Bg::latest()->get();

        return view('Backend.chat.chat_bg', [
            'chat_bgs' => $chat_bgs,
        ]);
    }

    function add_chat_background_store(Request $request) {
        $request->validate([
            'chat_bg' => 'required|mimes:jpeg,jpg|max:2048',
        ], [
            'chat_bg.required' => 'Please, select an image',
            'chat_bg.mimes' => 'Image type must be jpeg, jpg',
            'chat_bg.max' => 'Image size is less than 2 MB',
        ]);

        $image = $request->chat_bg;
        $extension = $image->extension();
        $file_name = uniqid().'.'.$extension;

        $maneger = new ImageManager(new Driver);
        $maneger->read($image)->resize(400, 600)->toJpeg(80)->save(public_path('upload/chat-backgrounds/'.$file_name));

        Chat_Bg::insert([
            'chat_bg' => $file_name,
            'created_at' => Carbon::now(),
        ]);
        return back()->with('add_bg', 'Chat Background added.');
    }

    function delete_chat_background($id) {
        $chat_bg = Chat_Bg::find($id);

        if($chat_bg) {
            $image_file = public_path('upload/chat-backgrounds/'.$chat_bg->chat_bg);
            unlink($image_file);
        }
        $chat_bg->delete();
        return back()->with('delete_bg', 'The Chat Background deleted successfully!');
    }

    function update_status_chat_background($id) {
        $chat_bg = Chat_Bg::find($id);

        Chat_Bg::find($id)->update([
            'status' => $chat_bg->status == 0? 1 : 0,
        ]);
        return back()->with('change_status', 'The Chat Background status chenged successfully!');
    }

    function set_chat_bg(Request $request) {
        $user = Auth::guard('user')->user();
        $chat_bg = Set_Chat_Bg::where('user_id', $user->id)->exists();

        if($chat_bg) {
            Set_Chat_Bg::where('user_id', $user->id)->update([
                'chat_bg_id' => $request->chat_bg_id,
            ]);
        }else {
            Set_Chat_Bg::insert([
                'chat_bg_id' => $request->chat_bg_id,
                'user_id' => $user->id,
                'created_at' => Carbon::now()
            ]);
        }
        $bg = Chat_Bg::find($request->chat_bg_id)->chat_bg;

        return asset('upload/chat-backgrounds/'.$bg);
    }
}
