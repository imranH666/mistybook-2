<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use App\Models\UserCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    function setting() {
        $recent_blogs = Blog::latest()->limit(10)->get();
        $categories = Category::all();
        $userCategories = UserCategory::where('user_id', Auth::guard('user')->id())
            ->pluck('category_id')
            ->toArray();

        return view('Frontend.setting.setting', [
            'recent_blogs' => $recent_blogs,
            'categories' => $categories,
            'userCategories' => $userCategories,
        ]);
    }

    function update_name(Request $request) {
        $user = Auth::guard('user')->user();

        $request->validate([
            'fname' => 'required',
        ], [
            'fname.required' => 'Pleas, Enter your first name',
        ]);

        if($user) {
            User::find($user->id)->update([
                'fname' => $request->fname,
                'lname' => $request->lname,
            ]);
            return back()->with('name_updated', 'Name updated successfully');
        }else {
            return back()->with('not_log_in', 'Please, Login');
        }
    }

    function change_password(Request $request) {
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
        $user = Auth::guard('user')->user();

        if(password_verify($request->current_password, $user->password)) {
            User::find($user->id)->update([
                'password' => bcrypt($request->password),
            ]);
            return back()->with('password_updated', 'Password updated successfully');
        }else {
            return back()->with('current_pass_error', 'Current Password Wrong');
        }
    }

    function update_user_category(Request $request) {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return back()->with('no_user', 'User not found. Please log in.');
        }

        UserCategory::where('user_id', $user->id)->delete();

        foreach ($request->categories as $category_id) {
            UserCategory::insert([
                'user_id' => $user->id,
                'category_id' => $category_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return back()->with('updated_category', 'Categories updated successfully.');
    }
}
