<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Support;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    function support() {
        $recent_blogs = Blog::latest()->limit(10)->get();

        return view('Frontend.support.support', [
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function support_store(Request $request) {
        $request->validate([
            'support_content' => 'required',
        ], [
            'support_content.required' => "Please, write something",
        ]);
        $user = Auth::guard('user')->user();

        Support::insert([
            'user_id' => $user->id,
            'support_content' => $request->support_content,
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "Submission successful! Our Support Team will contact you shortly.");
    }

    function see_user_support() {
        $supports = Support::latest()->get();

        return view('Backend.support.user_support', [
            'supports' => $supports,
        ]);
    }

    function user_support_delete($id) {
        Support::find($id)->delete();
        return back()->with('deleted', "The Support message deleted successfully!");
    }
}
