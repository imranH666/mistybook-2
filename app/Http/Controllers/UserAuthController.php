<?php

namespace App\Http\Controllers;

use App\Models\LightDarkMode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    function user_signup(Request $request) {
        $request->validate([
            'fname'=>'required',
            'email'=> ['required', 'email'],
            'password'=> ['required', 'confirmed', 'min:4', 'regex:/[a-z]/', 'regex:/[A-Z]/','regex:/[0-9]/'],
            'password_confirmation'=>'required',
        ],[
            'fname.required'=>'Please, Enter The First Name',
            'email.required'=>'Please, Enter The Email',
            'password.required'=>'Please, Enter The Password',
            'password.min'=>'Password must be at least 4 characters long.',
            'password.regex' => 'Password must contain at least one lowercase letter, one uppercase letter, and one number.',
            'password.confirmed'=>"Password Doesn't match",
            'password_confirmation.required'=>'Please, Enter The Confirm Password',
        ]);

        if(User::where('email', $request->email)->exists()) {
            return back()->with('existEmail', 'This email is already exist');
        }else {
            $slug = Str::random(16).'_'.random_int(10000000, 999999999).Str::random(16).'_'.random_int(10000000, 999999999);

            $user_id = User::insertGetId([
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'profession' => 'Your Profession',
                'description' => 'This is description',
                'slug' => $slug,
                'created_at' => Carbon::now(),
            ]);

            LightDarkMode::insert([
                'user_id' => $user_id,
                'value' => 'dark',
            ]);

            if(Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])){
                return back()->with('success_signup', 'You signup successfully');
            }
        }
    }

    function user_login(Request $request) {
        $request->validate([
            'email'=> ['required'],
            'password'=> ['required'],
        ],[
            'email.required'=>'Please, Enter The Email',
            'password.required'=>'Please, Enter The Password',
        ]);

        if(User::where('email', $request->email)->exists()) {
            if(Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
                return back()->with('success_login', 'You loggedin successfully');
            }else {
                return back()->with('errorLogin', 'Wrong Password');
            }
        }else {
            return back()->with('errorLogin', 'Email does not exist');
        }
    }

    function user_logout() {
        Auth::guard('user')->logout();
        return redirect()->route('login');
    }
}
