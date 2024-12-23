<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Following_Follower;
use App\Models\LightDarkMode;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class FrontendController extends Controller
{
    function setLocale ($lang) {
        if (in_array($lang, ['en', 'bn'])) {
            App::setLocale($lang);
            Session::put('locale', $lang);
        }
        return back();
    }

    function light_dark_mode(Request $request) {
        $user = Auth::guard('user')->user();

        $isDarkMode = LightDarkMode::firstOrCreate(
            ['user_id' => $user->id],
            ['value' => 'light']
        );
        $newValue = ($request->value === 'dark') ? 'dark' : 'light';
        $isDarkMode->update(['value' => $newValue]);
        return $newValue;
    }

    function search(Request $request) {
        $data = $request->all();

        $blogs = Blog::join('categories', 'blogs.category_id', '=', 'categories.id')
        ->where(function($q) use($data) {
            if (!empty($data['keyword']) && $data['keyword'] != '' && $data['keyword'] != 'undefined') {
                $q->where('blog_title', 'like', '%' . $data['keyword'] . '%');
                $q->orWhere('blog_content', 'like', '%' . $data['keyword'] . '%');
            }
            $q->orWhere('categories.category_english_name', 'like', '%' . $data['keyword'] . '%');
            $q->orWhere('categories.category_bangla_name', 'like', '%' . $data['keyword'] . '%');
        })
        ->orderBy('blogs.created_at', 'DESC')
        ->select('blogs.*')
        ->get();


        return view('Frontend.search.search', [
            'blogs' => $blogs,
        ]);
    }

    function index() {
        $posts = Post::latest()->get();
        $categories = Category::inRandomOrder()->get();
        $recent_blogs = Blog::latest()->limit(10)->get();
        $blogs = Blog::inRandomOrder()->limit(10)->get();

        if(Auth::guard('user')->user()) {
            $loggedInUserId = Auth::guard('user')->user()->id;
            $friends = User::where('id', '!=', $loggedInUserId)
            ->where(function ($query) use ($loggedInUserId) {
                $query->whereNotIn('id', function ($subQuery) use ($loggedInUserId) {
                    $subQuery->select('follower') // আপনি যাদের ফলো করেন
                        ->from('following__followers')
                        ->where('following', $loggedInUserId);
                })
                ->orWhereNotIn('id', function ($subQuery) use ($loggedInUserId) {
                    $subQuery->select('follower') // যারা আপনাকে ফলো করে
                        ->from('following__followers')
                        ->where('following', $loggedInUserId);
                });
            })
            ->inRandomOrder()
            ->limit(10)
            ->get();
        }else {
            $friends = User::all();
        }


        return view('Frontend.home.home', [
            'posts' => $posts,
            'recent_blogs' => $recent_blogs,
            'blogs' => $blogs,
            'friends' => $friends,
            'categories' => $categories,
        ]);
    }

    function category() {
        $categories = Category::all();
        $recent_blogs = Blog::latest()->limit(10)->get();

        return view('Frontend.category.category', [
            'categories' => $categories,
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function profile() {
        $user_id = Auth::guard('user')->user()->id;
        $followingCount = Following_Follower::where('following', $user_id)->count();
        $followerCount = Following_Follower::where('follower', $user_id)->count();
        $postCount = Post::where('user_id', $user_id)->count();
        $recent_blogs = Blog::inRandomOrder()->latest()->limit(10)->get();

        return view('Frontend.profile.profile', [
            'followingCount' => $followingCount,
            'followerCount' => $followerCount,
            'postCount' => $postCount,
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function user_profile($slug, $id = null) {
        $user = User::where('slug', $slug)->first();
        $recent_blogs = Blog::latest()->limit(10)->get();

        if (!$user) {
            abort(404);
        }
        $authUser = Auth::guard('user')->user();

        if ($authUser && $authUser->id == $user->id) {
            $user_id = $authUser->id;
            $followingCount = Following_Follower::where('following', $user_id)->count();
            $followerCount = Following_Follower::where('follower', $user_id)->count();
            $postCount = Post::where('user_id', $user_id)->count();

            if (Auth::guard('user')->check()) {
                if ($id != null) {
                    $user_id = Auth::guard('user')->user()->id;
                    Notification::where('id', $id)->where('to_notification', $user_id)->update([
                        'see' => 1,
                    ]);
                }
            }

            return view('Frontend.profile.profile', [
                'followingCount' => $followingCount,
                'followerCount' => $followerCount,
                'postCount' => $postCount,
                'recent_blogs' => $recent_blogs,
            ]);
        } else {
            $followerCount2 = Following_Follower::where('follower', $user->id)->count();
            $followingCount2 = Following_Follower::where('following', $user->id)->count();
            $postCount2 = Post::where('user_id', $user->id)->count();

            if (Auth::guard('user')->check()) {
                $userId = Auth::guard('user')->user()->id;
                $isSee = Notification::where('link', $slug)->where('to_notification', $userId);
                if ($isSee) {
                    $isSee->update([
                        'see' => 1,
                    ]);
                }
            }

            return view('Frontend.profile.user_profile', [
                'user' => $user,
                'followerCount2' => $followerCount2,
                'followingCount2' => $followingCount2,
                'postCount2' => $postCount2,
                'recent_blogs' => $recent_blogs,
            ]);
        }
    }

    function notification() {
        $loggedInUserId = Auth::guard('user')->user()->id;

        $users = User::where('id', '!=', $loggedInUserId)
        ->where(function ($query) use ($loggedInUserId) {
            $query->whereNotIn('id', function ($subQuery) use ($loggedInUserId) {
                $subQuery->select('follower') // আপনি যাদের ফলো করেন
                    ->from('following__followers')
                    ->where('following', $loggedInUserId);
            })
            ->orWhereNotIn('id', function ($subQuery) use ($loggedInUserId) {
                $subQuery->select('follower') // যারা আপনাকে ফলো করে
                    ->from('following__followers')
                    ->where('following', $loggedInUserId);
            });
        })
        ->inRandomOrder()
        ->limit(6)
        ->get();

        return view('Frontend.notification.notification', [
            'users' => $users,
        ]);
    }

    function login() {
        $recent_blogs = Blog::latest()->limit(10)->get();

        return view('Frontend.login.login', [
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function signup() {
        $recent_blogs = Blog::latest()->limit(10)->get();

        return view('Frontend.signup.signup', [
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function friends() {
        $recent_blogs = Blog::latest()->limit(10)->get();

        if(Auth::guard('user')->user()) {
            $loggedInUserId = Auth::guard('user')->user()->id;

            $followers = User::whereIn('id', function ($query) use ($loggedInUserId) {
                $query->select('following')
                    ->from('following__followers')
                    ->where('follower', $loggedInUserId);
            })->get();

            $following = User::whereIn('id', function ($query) use ($loggedInUserId) {
                $query->select('follower')
                    ->from('following__followers')
                    ->where('following', $loggedInUserId);
            })->get();

            return view('Frontend.friends.friends', [
                'followers' => $followers,
                'followings' => $following,
                'recent_blogs' => $recent_blogs,
            ]);
        }else {
            return view('Frontend.friends.friends', [
                'followers' => [],
                'followings' => [],
                'recent_blogs' => $recent_blogs,
            ]);
        }
    }
}
