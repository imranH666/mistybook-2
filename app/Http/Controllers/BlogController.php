<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favourite;
use App\Models\Following_Follower;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Reply;
use App\Models\Reply_20;
use App\Models\Reply_30;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Mail\BlogNewsletterMail;
use App\Models\User;
use App\Models\UserCategory;
use Illuminate\Support\Facades\Mail;

class BlogController extends Controller
{
    function blog() {
        $categories = Category::all();

        return view('Frontend.blog.blog', [
            'categories' => $categories,
        ]);
    }

    function see_blog() {
        $user = Auth::guard('user')->user();

        if($user) {
            $blogs = Blog::where('user_id', $user->id)->latest()->get();
            return view('Frontend.blog.see_blog', [
                'blogs' => $blogs,
            ]);
        }else {
            return view('Frontend.blog.see_blog', [
                'blogs' => [],
            ]);
        }
    }

    function create_blog(Request $request) {
        $request->validate([
            'blog_heading' => 'required',
            'blog_category' => 'required',
            'blog_banner' => 'required|mimes:png,jpg,jpeg|max:2048',
            'blog_content' => 'required',
        ], [
            'blog_heading.required' => 'Please, write something for heading',
            'blog_category.required' => 'Please, select a category',
            'blog_banner.required' => 'Please, select a image',
            'blog_banner.max' => 'Each image must be less than 2 MB.',
            'blog_banner.mimes' => 'Only jpg, jpeg, and png image formats are allowed.',
            'blog_content.required' => 'Please, write something',
        ]);

        $user = Auth::guard('user')->user();
        $slug = preg_replace('/[^\p{L}\p{M}\p{N}\s-]+/u', '', $request->blog_heading);
        $slug = preg_replace('/[\s-]+/u', '-', trim($slug));
        $slug = uniqid().'-'.Str::lower($slug);

        $lang = app()->getLocale() == 'en' ? 'en' : 'bn';
        $blog_banner = $request->blog_banner;
        $banner_extension = $blog_banner->extension();
        $banner_file_name = 'blog-banner'.'-'.uniqid().'.'.$banner_extension;

        $manegerBanner = new ImageManager(new Driver);
        $manegerBanner->read($blog_banner)->resize(600, 500)->toJpeg(80)->save(public_path('upload/blogs/'.$banner_file_name));

        $content = $request->blog_content;
        if (preg_match_all('/<img src="data:image\/(.*?);base64,(.*?)"/', $content, $matches)) {
            $images = [];

            foreach ($matches[2] as $index => $data) {
                $imageData = base64_decode($data);
                $extension = $matches[1][$index];
                $filename = 'blog'.'-'.uniqid().'.'.$extension;

                $maneger = new ImageManager(new Driver);
                $maneger->read($imageData)->toJpeg(80)->save(public_path('upload/blogs/'.$filename));

                $imageUrl = asset('upload/blogs/'.$filename);
                $images[] = $imageUrl;

                $content = str_replace($matches[0][$index], "<img src=\"$imageUrl\"", $content);
            }

            $blog_id = Blog::insertGetId([
                'user_id' => $user->id,
                'category_id' => $request->blog_category,
                'blog_title' => $request->blog_heading,
                'blog_content' => $content,
                'blog_banner' => $banner_file_name,
                'slug' => $slug,
                'lang' => $lang,
                'created_at' => Carbon::now()
            ]);
        }else {
            $blog_id = Blog::insertGetId([
                'user_id' => $user->id,
                'category_id' => $request->blog_category,
                'blog_title' => $request->blog_heading,
                'blog_content' => $content,
                'blog_banner' => $banner_file_name,
                'slug' => $slug,
                'lang' => $lang,
                'created_at' => Carbon::now()
            ]);
        }

        $blog = Blog::find($blog_id);
        foreach (Following_Follower::where('follower', $user->id)->get() as $follower) {
            Notification::insert([
                'to_notification' => $follower->following,
                'from_notification' => $user->id,
                'message' => 'wrote a new blog'.' <span style="color: green">See</span>',
                'link' => $blog->slug,
                'status' => 2,
                'created_at' => Carbon::now()
            ]);
        }

        foreach (UserCategory::with('user')->where('category_id', $request->blog_category)->get() as $user) {
            $toUser = $user->user;
            $subject = "You'll Love This: " . $request->blog_heading;
            Mail::to($toUser->email)->send(new BlogNewsletterMail($blog, $subject));
        }

        return back()->with('upload_blog', 'Uploaded your blog');
    }

    function delete_blog($id) {
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
            Notification::where('link', $blog->slug)->delete();

            $blog->delete();

            return back()->with('blog_deleted', 'Blog deleted successfully');

        }else {
            return back()->with('blog_not_found', 'Blog not found');
        }
    }

    function read_blog($slug, $id = null) {
        $blog = Blog::where('slug', $slug)->first();

        if($blog) {
            $related_blogs = Blog::where('category_id', $blog->category_id)->where('slug', '!=', $slug)->limit(10)->get();
            $show_related_blogs = Blog::where('category_id', $blog->category_id)->where('slug', '!=', $slug)->inRandomOrder()->limit(6)->get();

            if (Auth::guard('user')->check()) {
                if ($id != null) {
                    $user_id = Auth::guard('user')->user()->id;
                    Notification::where('id', $id)->where('to_notification', $user_id)->update([
                        'see' => 1,
                    ]);
                }
            }

            return view('Frontend.blog.read_blog', [
                'blog' => $blog,
                'related_blogs' => $related_blogs,
                'show_related_blogs' => $show_related_blogs,
            ]);
        }else {
            abort(404, 'not found');
        }
    }

    function user_blog_like(Request $request) {
        $user_id = Auth::guard('user')->user()->id;
        $slug = Blog::find($request->blog_id);

        $existingLike = Like::where('user_id', $user_id)->where('blog_id', $request->blog_id)->first();
        if($existingLike) {
            $existingLike->delete();
            return 'unlike';
        }else {
            Like::insert([
                'user_id' => $user_id,
                'blog_id' => $request->blog_id,
                'created_at' => Carbon::now(),
            ]);

            Notification::insert([
                'to_notification' => $request->blog_user_id,
                'from_notification' => $user_id,
                'message' => '<span style="color: red">liked</span> on your blog',
                'link' => $slug->slug,
                'status' => 2,
                'created_at' => Carbon::now()
            ]);
            return 'like';
        }
    }

    function blog_comment_store(Request $request) {
        $request->validate([
            'comment_text' => 'required',
        ], [
            'comment_text' => 'Please, write something',
        ]);

        $user_id = Auth::guard('user')->user()->id;
        Comment::insert([
            'user_id' => $user_id,
            'blog_id' => $request->blog_id,
            'comment' => $request->comment_text,
            'created_at' => Carbon::now()
        ]);

        $slug = Blog::find($request->blog_id)->slug;

        Notification::insert([
            'to_notification' => $request->blog_user_id,
            'from_notification' => $user_id,
            'message' => 'commented on your blog'.' <span style="color: green">'.$request->comment_text.'</span>',
            'link' => $slug,
            'status' => 2,
            'created_at' => Carbon::now()
        ]);
        echo $request->comment_text;
    }

    function blog_reply_store(Request $request) {
        $user_id = Auth::guard('user')->user()->id;
        $slug = Blog::find($request->blog_id)->slug;

        if($request->value == 10) {
            Reply_20::insert([
                'user_id' => $user_id,
                'blog_id' => $request->blog_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 20,
                'reply10' => $request->reply10_id,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->blog_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'status' => 2,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;

        }else if($request->value == 20){
            Reply_30::insert([
                'user_id' => $user_id,
                'blog_id' => $request->blog_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 20,
                'reply10' => $request->reply10_id,
                'reply20' => $request->reply20_id,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->blog_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'status' => 2,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;

        }else {
            Reply::insert([
                'user_id' => $user_id,
                'blog_id' => $request->blog_id,
                'comment_id' => $request->comment_id,
                'reply' => $request->reply_text,
                'status' => 10,
                'created_at' => Carbon::now()
            ]);

            Notification::insert([
                'to_notification' => $request->blog_user_id,
                'from_notification' => $user_id,
                'message' => 'replied on your comment'.' <span style="color: green">'.$request->reply_text.'</span>',
                'link' => $slug,
                'status' => 2,
                'created_at' => Carbon::now()
            ]);
            return $request->reply_text;
        }
    }
}
