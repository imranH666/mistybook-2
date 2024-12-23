<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class ExplorController extends Controller
{
    function explor() {
        $recent_blogs = Blog::latest()->limit(10)->get();
        $random_blogs = Blog::inRandomOrder()->get();
        $random_nested_blogs = Blog::inRandomOrder()->get();

        return view('Frontend.explor.explor', [
            'recent_blogs' => $recent_blogs,
            'random_blogs' => $random_blogs,
            'random_nested_blogs' => $random_nested_blogs,
        ]);
    }
}
