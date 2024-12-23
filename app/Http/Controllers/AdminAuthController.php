<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    function admin_register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:4',
            'password_confirmation' => 'required'
        ], [
            'name.required' => 'Please, Enter your name',
            'email.required' => 'Please, Enter your email',
            'password.required' => 'Please, Enter your password',
            'password.min' => 'Password should be minimum 4 character',
            'password.confirmed' => "Passwrod doesn't match, pleas try again",
            'password_confirmation.required' => 'Please, Enter the confirm password',
        ]);

        if(Admin::where('email', $request->email)->exists()) {
            return back()->with('existEmail', 'This email is already exist');
        }else {
            Admin::insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'created_at' => Carbon::now()
            ]);

            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
                return redirect()->route('dashboard');
            }
        }
    }

    function admin_login(Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Please, Enter your email',
            'password.required' => 'Please, Enter your password',
        ]);

        if(Admin::where('email', $request->email)->exists()) {
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
                return redirect()->route('dashboard');
            }else {
                return back()->with('errorLogin', 'Wrong password');
            }
        }else {
            return back()->with('errorLogin', 'Email does not exist');
        }

    }

    function admin_logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
