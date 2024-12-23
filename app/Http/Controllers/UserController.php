<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Admin;
use App\Models\Answer;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Favourite;
use App\Models\Like;
use App\Models\PassReset;
use App\Models\Post;
use App\Models\Post_image;
use App\Models\Question;
use App\Models\Reply;
use App\Models\Reply_20;
use App\Models\Reply_30;
use App\Models\User;
use App\Models\Video;
use App\Notifications\PassResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class UserController extends Controller
{
    function user_profile_photo_edit(Request $request) {
        $request->validate([
            'user_profile_image' => ['required', 'mimes:png,jpg,jpeg', 'max:1024']
        ], [
            'user_profile_image.required' => "Please, select an image",
            'user_profile_image.mimes' => "Only jpg, jpeg, and png images are allowed",
            'user_profile_image.max' => "Image size should not exceed 1MB",
        ]);

        if(Auth::guard('user')->user()->photo != null) {
            $delete_from = public_path('upload/users/'.Auth::guard('user')->user()->photo);
            unlink($delete_from);
        }

        $photo = $request->user_profile_image;
        $extension = $photo->extension();
        $file_name = Auth::guard('user')->user()->id.'.'.$extension;

        $manager = new ImageManager(new Driver());
        $path = public_path('upload/users/'.$file_name);
        $image = $manager->read($photo);
        $resizeImage = $image->resize(300, 300);
        $resizeImage->toJpeg(80)->save($path);

        User::find(Auth::guard('user')->user()->id)->update([
            'photo' => $file_name,
        ]);

        return back()->with('profile_photo', 'Profile Photo Updated');
    }

    function user_profile_cover_photo_edit(Request $request) {
        $request->validate([
            'user_cover_image' => ['required', 'mimes:png,jpg,jpeg', 'max:1024']
        ], [
            'user_cover_image.required' => "Please, select an image",
            'user_cover_image.mimes' => "Only jpg, jpeg, and png images are allowed",
            'user_cover_image.max' => "Image size should not exceed 1MB",
        ]);

        if(Auth::guard('user')->user()->cover_photo != null) {
            $delete_from = public_path('upload/covers/'.Auth::guard('user')->user()->cover_photo);
            unlink($delete_from);
        }
        $cover_photo = $request->user_cover_image;
        $extension = $cover_photo->extension();
        $file_name = Auth::guard('user')->user()->id.'.'.$extension;

        $manager = new ImageManager(new Driver());
        $path = public_path('upload/covers/'.$file_name);
        $image = $manager->read($cover_photo);
        $resizeImage = $image->resize(500, 300);
        $resizeImage->toJpeg(80)->save($path);

        User::find(Auth::guard('user')->user()->id)->update([
            'cover_photo' => $file_name,
        ]);
        return back()->with('cover_photo', 'Cover Photo Updated');
    }

    function user_profile_description_edit(Request $request) {
        $request->validate([
            'profession' => 'required',
            'description' => 'required',
        ], [
            'profession.required' => 'Write something',
            'description.required' => 'Write something'
        ]);

        $user = Auth::guard('user')->user();
        User::find($user->id)->update([
            'profession' => $request->profession,
            'description' => $request->description,
        ]);
        return back()->with('updated', 'Profile updated');
    }

    function password_forgot() {
        $recent_blogs = Blog::inRandomOrder()->latest()->get();

        return view('Frontend.user.password_forgot', [
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function password_forgot_req_send(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Please, Enter your email address.',
            'email.email' => 'Invalid email format',
        ]);

        if(User::where('email', $request->email)->exists()) {
            $user = User::where('email', $request->email)->first();

            $info = PassReset::create([
                'token' => uniqid(),
                'user_id' => $user->id,
            ]);
            // sending mail
            Notification::send($user, new PassResetNotification($info));

            return back()->with('success', 'Password reset link has been sent to your email. Please check your inbox.');
        }else {
            return back()->with('invalid', 'Invalid email address');
        }
    }

    function password_reset_form($token) {
        if(PassReset::where('token', $token)->exists()) {
            $recent_blogs = Blog::inRandomOrder()->latest()->get();

            return view('Frontend.user.pass_reset_form', [
                'recent_blogs' => $recent_blogs,
                'token' => $token,
            ]);
        }else {
            abort('404');
        }
    }

    function password_reset_update(Request $request, $token) {
        $info = PassReset::where('token', $token)->first();

        if(PassReset::where('token', $token)->exists()) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::min(6)->letters()->mixedCase()->numbers()],
                'password_confirmation' => 'required',
            ], [
                'password.required' => 'Please, Enter the new password',
                'password.confirmed' => "Password does not match",
                'password.min' => "Password length must be minimum 6 character",
                'password_confirmation.required' => "Please, Enter the confirm password",
            ]);

            User::find($info->user_id)->update([
                'password' => bcrypt($request->password),
            ]);
            PassReset::where('token', $token)->delete();

            return redirect()->route('login')->with('updated', 'Password updated successfully');;
        }else {
            abort('404');
        }
    }

    function user_list() {
        $users = User::latest()->get();

        return view('Backend.user.user_list', [
            'users' => $users,
        ]);
    }

    function admin_profile_edit() {
        return view('Backend.user.edit_admin_profile');
    }

    function admin_profile_update(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(6)->letters()->mixedCase()->numbers()],
            'password_confirmation' => 'required',
        ], [
            'current_password.required' => 'Please, Enter the current password',
            'password.required' => 'Please, Enter the new password',
            'password.confirmed' => "Password does not match",
            'password.min' => "Password length must be minimum 6 character",
            'password_confirmation.required' => "Please, Enter the confirm password",
        ]);
        $admin = Auth::guard('admin')->user();

        if(password_verify($request->current_password, $admin->password)) {
            Admin::find($admin->id)->update([
                'password' => bcrypt($request->password),
            ]);
            return back()->with('password_updated', 'Password updated successfully');
        }else {
            return back()->with('current_pass_error', 'Current Password Wrong');
        }
    }

    function user_details($user_id) {
        $user = User::find($user_id);
        $posts = Post::where('user_id', $user->id)->latest()->get();
        $blogs = Blog::where('user_id', $user->id)->latest()->get();
        $questions = Question::where('user_id', $user->id)->latest()->get();
        $videos = Video::where('user_id', $user->id)->latest()->get();

        return view('Backend.user.user_details', [
            'user' => $user,
            'posts' => $posts,
            'blogs' => $blogs,
            'questions' => $questions,
            'videos' => $videos,
        ]);
    }

    function delete_user_post($id) {
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
        return back()->with('post_delete', 'Post deleted');
    }

    function delete_user_blog($id) {
        $blog = Blog::find($id);

        if($blog) {
            $delete_banner_image = public_path('upload/blogs/'.$blog->blog_banner);
            if(file_exists($delete_banner_image)) {
                unlink($delete_banner_image);
            }

            $content = $blog->blog_content;
            if (preg_match_all('/<img src="(.*?)"/', $content, $matches)) {
                $imageUrls = $matches[1];

                foreach ($imageUrls as $imageUrl) {
                    $filePath = str_replace(asset('upload/blogs/'), '', $imageUrl);
                    $fullPath = public_path('upload/blogs/' . $filePath);

                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            Like::where('blog_id', $id)->delete();
            Favourite::where('blog_id', $id)->delete();
            Comment::where('blog_id', $id)->delete();
            Reply::where('blog_id', $id)->delete();
            Reply_20::where('blog_id', $id)->delete();
            Reply_30::where('blog_id', $id)->delete();

            $blog->delete();

            return back()->with('blog_deleted', 'Blog deleted successfully');

        }else {
            return back()->with('blog_not_found', 'Blog not found');
        }
    }

    function delete_user_question($id) {
        $answers = Answer::where('question_id', $id)->get();

        $question = Question::find($id);
        if (!$question) {
            return back()->with('error', 'Question not found');
        }
        $question->delete();
        Favourite::where('question_id', $id)->delete();

        if($answers) {
            foreach ($answers as $answer) {
                $content = $answer->answer;
                if (preg_match_all('/<img src="(.*?)"/', $content, $matches)) {
                    $imageUrls = $matches[1];

                    foreach ($imageUrls as $imageUrl) {
                        $filePath = str_replace(asset('upload/answers/'), '', $imageUrl);
                        $fullPath = public_path('upload/answers/' . $filePath);

                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }
            }
        }else {
            return back()->with('question_not_found', 'Question not found');
        }
        Answer::where('question_id', $id)->delete();

        return back()->with('question_success', 'The Question was deleted successfully');
    }

    function delete_user_video($id) {
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

        $video->delete();
        return back()->with('video_deleted', 'The Video has deleted successfully');
    }
}

