<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Favourite;
use App\Models\Following_Follower;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Post_image;
use App\Models\Reply;
use App\Models\Reply_20;
use App\Models\Reply_30;
use App\Models\Report;
use App\Models\Share;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;


class PostController extends Controller
{
    function create_post() {
        if(Auth::guard('user')->user()) {
            $posts = Post::where('user_id', Auth::guard('user')->user()->id)->latest()->get();

            return view('Frontend.post.create_post', [
                'posts' => $posts,
            ]);
        }else {
            return view('Frontend.post.create_post');
        }
    }

    function create_post_store(Request $request) {
        $request->validate([
            'post_content' => 'required',
            'post_image' => 'array',
            'post_image.*' => 'file|mimes:png,jpg,jpeg|max:2048',
        ], [
            'post_content.required' => 'Please, Write a post.',
            'post_image.*.max' => 'Each image must be less than 2 MB.',
            'post_image.*.mimes' => 'Only jpg, jpeg, and png image formats are allowed.',
        ]);

        $user_id = Auth::guard('user')->user()->id;
        $slug = Str::random(16).'_'.random_int(10000000, 999999999).Str::random(16).'_'.random_int(10000000, 999999999);

        $post_id = Post::insertGetId([
            'user_id' => $user_id,
            'content' => $request->post_content,
            'slug' => $slug,
            'created_at' => Carbon::now(),
        ]);

        if($request->post_image) {
            foreach($request->post_image as $image) {
                $extension = $image->extension();
                $file_name = 'post_image'.'_'.random_int(10000000, 999999999).'.'.$extension;

                $maneger = new ImageManager(new Driver);
                $maneger->read($image)->toJpeg(80)->save(public_path('upload/posts/'.$file_name));

                Post_image::insert([
                    'post_id' => $post_id,
                    'image_path' => $file_name,
                    'created_at' => Carbon::now(),
                ]);
            }
        }

        $me = Auth::guard('user')->user();
        $post = Post::find($post_id);
        foreach (Following_Follower::where('follower', $me->id)->get() as $follower) {
            Notification::insert([
                'to_notification' => $follower->following,
                'from_notification' => $me->id,
                'message' => 'published a new post'.' <span style="color: green">See</span>',
                'link' => $post->slug,
                'created_at' => Carbon::now()
            ]);
        }
        return back()->with('post_uploaded', 'The post uploaded');
    }

    function update_comment_status(Request $request) {
        echo $request->post_id;
    }

    function user_comment_store(Request $request) {
        $request->validate([
            'comment_text' => 'required',
        ], [
            'comment_text' => 'Please, write something',
        ]);

        $user_id = Auth::guard('user')->user()->id;
        Comment::insert([
            'user_id' => $user_id,
            'post_id' => $request->post_id,
            'comment' => $request->comment_text,
            'created_at' => Carbon::now()
        ]);

        $slug = Post::find($request->post_id)->slug;

        Notification::insert([
            'to_notification' => $request->post_user_id,
            'from_notification' => $user_id,
            'message' => 'commented on your post'.' <span style="color: green">'.$request->comment_text.'</span>',
            'link' => $slug,
            'created_at' => Carbon::now()
        ]);
        echo $request->comment_text;
    }

    function user_reply_store(Request $request) {
        $user_id = Auth::guard('user')->user()->id;
        $slug = Post::find($request->post_id)->slug;

        if($request->value == 10) {
            Reply_20::insert([
                'user_id' => $user_id,
                'post_id' => $request->post_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 20,
                'reply10' => $request->reply10_id,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->post_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;

        }else if($request->value == 20){
            Reply_30::insert([
                'user_id' => $user_id,
                'post_id' => $request->post_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 20,
                'reply10' => $request->reply10_id,
                'reply20' => $request->reply20_id,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->post_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;

        }else {
            Reply::insert([
                'user_id' => $user_id,
                'post_id' => $request->post_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 10,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->post_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;
        }
    }

    function user_post_like(Request $request) {
        $user_id = Auth::guard('user')->user()->id;
        $slug = Post::find($request->post_id)->slug;

        $existingLike = Like::where('user_id', $user_id)->where('post_id', $request->post_id)->first();
        if($existingLike) {
            $existingLike->delete();
            return 'unlike';
        }else {
            Like::insert([
                'user_id' => $user_id,
                'post_id' => $request->post_id,
                'created_at' => Carbon::now(),
            ]);

            Notification::insert([
                'to_notification' => $request->post_user_id,
                'from_notification' => $user_id,
                'message' => '<span style="color: red">liked</span> on your post',
                'link' => $slug,
                'created_at' => Carbon::now()
            ]);
            return 'like';
        }
    }

    function user_following(Request $request) {
        $my_id = Auth::guard('user')->user();

        $isFollowing = Following_Follower::where('following', $my_id->id)->where('follower', $request->user_id)->first();
        if($isFollowing) {
            $isFollowing->delete();
            return 'unfollow';
        }else {
            Following_Follower::insert([
                'following' => $my_id->id,
                'follower' => $request->user_id,
                'created_at' => Carbon::now(),
            ]);

            Notification::insert([
                'to_notification' => $request->user_id,
                'from_notification' => $my_id->id,
                'message' => 'is following you',
                'link' => $my_id->slug,
                'status' => 1,
                'created_at' => Carbon::now()
            ]);
            return 'following';
        }
    }

    function show_post($slug, $id = null) {
        $post = Post::where('slug', $slug)->first();
        $recent_blogs = Blog::latest()->get();

        if(!$post) {
            abort(404, 'not found');
        }

        if (Auth::guard('user')->check()) {
            if ($id != null) {
                $user_id = Auth::guard('user')->user()->id;
                Notification::where('id', $id)->where('to_notification', $user_id)->update([
                    'see' => 1,
                ]);
            }
        }

        return view('Frontend.show_post.show_post', [
            'post' => $post,
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function post_share(Request $request) {
        $user_id = Auth::guard('user')->user()->id;
        $post = Post::find($request->post_id);
        $share_images = $post->rel_to_post_images;
        $slug = Str::random(16).'_'.random_int(10000000, 999999999).Str::random(16).'_'.random_int(10000000, 999999999);

        Share::insert([
            'user_id' => $user_id,
            'post_id' => $request->post_id,
            'created_at' => Carbon::now()
        ]);

        $post_id = Post::insertGetId([
            'user_id' => $user_id,
            'content' => $post->content,
            'slug' => $slug,
            'share_id' => $post->user_id,
            'share' => 1,
            'created_at' => Carbon::now(),
        ]);

        foreach ($share_images as $image_path) {
            Post_image::insert([
                'post_id' => $post_id,
                'image_path' => $image_path->image_path,
                'created_at' => Carbon::now(),
            ]);
        }

        $post = Post::find($post_id);
        $me = Auth::guard('user')->user();
        foreach (Following_Follower::where('follower', $me->id)->get() as $follower) {
            Notification::insert([
                'to_notification' => $follower->following,
                'from_notification' => $me->id,
                'message' => 'shared a post'.' <span style="color: green">See</span>',
                'link' => $post->slug,
                'created_at' => Carbon::now()
            ]);
        }
        return 'success';
    }

    function post_delete($id) {
        $post = Post::find($id);
        if($post->share == 0) {
            $images = Post_image::where('post_id', $id)->get();
            foreach ($images as $image) {
                $delete_from = public_path('upload/posts/'.$image->image_path);
                unlink($delete_from);
                Post_image::find($image->id)->delete();
            }
            Post::find($id)->delete();
        }else {
            Post::find($id)->delete();
        }
        Like::where('post_id', $id)->delete();
        Favourite::where('post_id', $id)->delete();
        Comment::where('post_id', $id)->delete();
        Reply::where('post_id', $id)->delete();
        Reply_20::where('post_id', $id)->delete();
        Reply_30::where('post_id', $id)->delete();
        Notification::where('link', $post->slug)->delete();
        return back()->with('post_delete', 'Post deleted');
    }

    function notification_delete($id) {
        Notification::find($id)->delete();

        return back();
    }

    function post_report(Request $request) {
        if (empty($request->report_text)) {
            return 'error';
        }
        $user = Auth::guard('user')->user();

        Report::insert([
            'user_id' => $user->id,
            'post_id' => $request->post_id,
            'report_text' => $request->report_text,
            'created_at' => Carbon::now()
        ]);
        return 'success';
    }

    function see_user_reports() {
        $reports = Report::latest()->get();

        return view('Backend.post.post_reports', [
            'reports' => $reports,
        ]);
    }

    function user_reports_message($user_id) {
        $user = User::find($user_id);

        return view('Backend.post.user_reports_message');
    }

    function user_reports_message_store(Request $request) {
        $request->validate([
            'report_text' => 'required',
        ]);
        echo $request->report_text;
    }

    function user_reports_delete($id) {
        Report::find($id)->delete();
        return back()->with('deleted', 'Report deleted successfully');
    }
}
