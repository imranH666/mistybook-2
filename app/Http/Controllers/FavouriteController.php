<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Favourite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    function favourite() {
        $user = Auth::guard('user')->user();
        $recent_blogs = Blog::latest()->get();

        if($user) {
            $questions = Favourite::where('user_id', $user->id)->whereNotNull('question_id')->latest()->get();
            $posts = Favourite::where('user_id', $user->id)->whereNotNull('post_id')->latest()->get();
            $blogs = Favourite::where('user_id', $user->id)->whereNotNull('blog_id')->latest()->get();
            $videos = Favourite::where('user_id', $user->id)->whereNotNull('video_id')->latest()->get();

            return view('Frontend.favourite.favourite', [
                'questions' => $questions,
                'posts' => $posts,
                'blogs' => $blogs,
                'videos' => $videos,
                'recent_blogs' => $recent_blogs,
            ]);
        }else {
            return view('Frontend.favourite.favourite', [
                'questions' => [],
                'posts' => [],
                'blogs' => [],
                'videos' => [],
                'recent_blogs' => $recent_blogs,
            ]);
        }
    }

    function add_favourite(Request $request) {
        $user = Auth::guard('user')->user();

        if($request->type == 'post') {
            if(Favourite::where('user_id', $user->id)->where('post_id', $request->post_id)->exists()) {
                Favourite::where('user_id', $user->id)->where('post_id', $request->post_id)->delete();
                return 'not-favourite';
            }else {
                Favourite::insert([
                    'user_id' => $user->id,
                    'post_id' => $request->post_id,
                    'created_at' => Carbon::now()
                ]);
                return 'favourite';
            }
        }else if($request->type == 'question') {
            if(Favourite::where('user_id', $user->id)->where('question_id', $request->question_id)->exists()) {
                Favourite::where('user_id', $user->id)->where('question_id', $request->question_id)->delete();
                return 'not-favourite';
            }else {
                Favourite::insert([
                    'user_id' => $user->id,
                    'question_id' => $request->question_id,
                    'created_at' => Carbon::now()
                ]);
                return 'favourite';
            }
        }else if($request->type == 'blog') {
            if(Favourite::where('user_id', $user->id)->where('blog_id', $request->blog_id)->exists()) {
                Favourite::where('user_id', $user->id)->where('blog_id', $request->blog_id)->delete();
                return 'not-favourite';
            }else {
                Favourite::insert([
                    'user_id' => $user->id,
                    'blog_id' => $request->blog_id,
                    'created_at' => Carbon::now()
                ]);
                return 'favourite';
            }
        }else if($request->type == 'video') {
            if(Favourite::where('user_id', $user->id)->where('video_id', $request->video_id)->exists()) {
                Favourite::where('user_id', $user->id)->where('video_id', $request->video_id)->delete();
                return 'not-favourite';
            }else {
                Favourite::insert([
                    'user_id' => $user->id,
                    'video_id' => $request->video_id,
                    'created_at' => Carbon::now()
                ]);
                return 'favourite';
            }
        }
    }

    function delete_favourite_question($id) {
        $user = Auth::guard('user')->user();
        Favourite::where('question_id', $id)->where('user_id', $user->id)->delete();
        return back();
    }

    function delete_favourite_post($id) {
        $user = Auth::guard('user')->user();
        Favourite::where('post_id', $id)->where('user_id', $user->id)->delete();
        return back();
    }

    function delete_favourite_blog($id) {
        $user = Auth::guard('user')->user();
        Favourite::where('blog_id', $id)->where('user_id', $user->id)->delete();
        return back();
    }

    function delete_favourite_video($id) {
        $user = Auth::guard('user')->user();
        Favourite::where('video_id', $id)->where('user_id', $user->id)->delete();
        return back();
    }
}
