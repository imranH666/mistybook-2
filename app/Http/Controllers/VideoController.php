<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Favourite;
use App\Models\Following_Follower;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Reply;
use App\Models\Reply_20;
use App\Models\Reply_30;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use FFMpeg;


ini_set('max_execution_time', 600); // 300 seconds (5 minutes)


class VideoController extends Controller
{
    function video() {
        $videos = Video::latest()->get();
        $recent_blogs = Blog::inRandomOrder()->latest()->limit(10)->get();

        return view('Frontend.video.video', [
            'videos' => $videos,
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function add_video() {
        $recent_blogs = Blog::inRandomOrder()->latest()->limit(10)->get();

        return view('Frontend.video.add_video', [
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function my_videos() {
        $videos = Video::where('user_id', Auth::guard('user')->user()->id)->latest()->get();

        return view('Frontend.video.my_videos', [
            'videos' => $videos,
        ]);
    }

    public function upload_video(Request $request) {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded()) {
            try {
                $fileReceived = $receiver->receive();

                if ($fileReceived->isFinished()) {
                    // Save the original file
                    $file = $fileReceived->getFile();
                    $fileName = 'original_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $compressedFileName = 'compressed_' . uniqid() . '.mp4';

                    $originalFilePath = public_path('upload/videos/') . $fileName;
                    $compressedFilePath = public_path('upload/videos/') . $compressedFileName;

                    $file->move(public_path('upload/videos/'), $fileName);

                    // Start compression and update status
                    $HandBrakeCLI = public_path('data/HandBrakeCLI.exe');
                    $handbrakeCommand = "\"$HandBrakeCLI\" -i \"{$originalFilePath}\" -o \"{$compressedFilePath}\" --preset=\"Very Fast 720p30\"";
                    exec($handbrakeCommand, $output, $returnCode);

                    // Check if the command was successful
                    if ($returnCode != 0) {
                        echo "Error occurred during compression.";
                    }

                    // Optionally delete the original file after compression
                    if (file_exists($originalFilePath)) {
                        unlink($originalFilePath);
                    }

                    return response()->json([
                        'success' => true,
                        'filename' => $compressedFileName,
                    ], 200);
                }

                // Chunk upload response
                return response()->json([
                    'done' => $fileReceived->handler()->getPercentageDone(),
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'File not uploaded!'], 400);
    }

    function store_video(Request $request) {
        if(empty($request->video_content)) {
            return 'error';
        }
        if(empty($request->video_name)) {
            return 'video_error';
        }

        $user_id = Auth::guard('user')->user();
        $slug = Str::random(10).'_'.random_int(10000000, 999999999).Str::random(10).'_'.random_int(10000000, 999999999);

        $video_id = Video::insertGetId([
            'user_id' => $user_id->id,
            'video_content' => $request->video_content,
            'video_name' => $request->video_name,
            'slug' => $slug,
            'created_at' => Carbon::now(),
        ]);

        $me = Auth::guard('user')->user();
        $video = Video::find($video_id);
        foreach (Following_Follower::where('follower', $me->id)->get() as $follower) {
            Notification::insert([
                'to_notification' => $follower->following,
                'from_notification' => $me->id,
                'message' => 'uploaded a new video'.' <span style="color: green">See</span>',
                'link' => $video->slug,
                'status' => 3,
                'created_at' => Carbon::now()
            ]);
        }
        return 'success';
    }

    function user_video_like(Request $request) {
        $user_id = Auth::guard('user')->user()->id;
        $slug = Video::find($request->video_id)->slug;

        $existingLike = Like::where('user_id', $user_id)->where('video_id', $request->video_id)->first();
        if($existingLike) {
            $existingLike->delete();
            return 'unlike';
        }else {
            Like::insert([
                'user_id' => $user_id,
                'video_id' => $request->video_id,
                'created_at' => Carbon::now(),
            ]);

            Notification::insert([
                'to_notification' => $request->video_user_id,
                'from_notification' => $user_id,
                'message' => '<span style="color: red">liked</span> on your video',
                'link' => $slug,
                'status' => 3,
                'created_at' => Carbon::now()
            ]);
            return 'like';
        }
    }

    function see_video($slug, $id = null) {
        $video = Video::where('slug', $slug)->first();

        if (Auth::guard('user')->check()) {
            if ($id != null) {
                $user_id = Auth::guard('user')->user()->id;
                Notification::where('id', $id)->where('to_notification', $user_id)->update([
                    'see' => 1,
                ]);
            }
        }

        if($video) {
            return view('Frontend.video.see_video', [
                'video' => $video,
            ]);
        }else {
            abort(404, 'not found');
        }
    }

    function video_comment_store(Request $request) {
        $request->validate([
            'comment_text' => 'required',
        ], [
            'comment_text' => 'Please, write something',
        ]);

        $user_id = Auth::guard('user')->user()->id;
        Comment::insert([
            'user_id' => $user_id,
            'video_id' => $request->video_id,
            'comment' => $request->comment_text,
            'created_at' => Carbon::now()
        ]);

        $slug = Video::find($request->video_id)->slug;

        Notification::insert([
            'to_notification' => $request->video_user_id,
            'from_notification' => $user_id,
            'message' => 'commented on your video'.' <span style="color: green">'.$request->comment_text.'</span>',
            'link' => $slug,
            'status' => 3,
            'created_at' => Carbon::now()
        ]);
        echo $request->comment_text;
    }

    function video_reply_store(Request $request) {
        $user_id = Auth::guard('user')->user()->id;
        $slug = Video::find($request->video_id)->slug;

        if($request->value == 10) {
            Reply_20::insert([
                'user_id' => $user_id,
                'video_id' => $request->video_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 20,
                'reply10' => $request->reply10_id,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->video_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'status' => 3,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;

        }else if($request->value == 20){
            Reply_30::insert([
                'user_id' => $user_id,
                'video_id' => $request->video_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 20,
                'reply10' => $request->reply10_id,
                'reply20' => $request->reply20_id,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->video_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'status' => 3,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;

        }else {
            Reply::insert([
                'user_id' => $user_id,
                'video_id' => $request->video_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 10,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->video_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'status' => 3,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;
        }
    }

    function delete_video($id) {
        $video = Video::find($id);

        $video_file = public_path('upload/videos/'.$video->video_name);
        if(file_exists($video_file)) {
            unlink($video_file);
        }
        Like::where('video_id', $id)->delete();
        Favourite::where('video_id', $id)->delete();
        Comment::where('video_id', $id)->delete();
        Reply::where('video_id', $id)->delete();
        Reply_20::where('video_id', $id)->delete();
        Reply_30::where('video_id', $id)->delete();
        Notification::where('link', $video->slug)->delete();

        $video->delete();
        return back();
    }
}
