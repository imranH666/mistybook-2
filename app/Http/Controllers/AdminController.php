<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    function register() {
        return view('Backend.register.register');
    }

    function login() {
        return view('Backend.login.login');
    }

    function dashboard() {
        return view('Backend.dashboard.dashboard');
    }

    function language() {
        $languages = Language::latest()->get();

        return view('Backend.language.language', [
            'languages' => $languages,
        ]);
    }

    function add_language(Request $request) {
        $request->validate([
            'language_name' => 'required'
        ], [
            'language_name' => 'Please, Write a Language'
        ]);

        Language::insert([
            'language' => $request->language_name,
            'created_at' => Carbon::now(),
        ]);
        return back()->with('added', 'The Language added');
    }

    function delete_language($id) {
        Language::find($id)->delete();

        return back()->with('deleted', 'The Language deleted');
    }
}
