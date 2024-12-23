<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\UserCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CategoryController extends Controller
{

    function category() {
        $categories = Category::all();

        return view('Backend.category.category', [
            'categories' => $categories,
        ]);
    }

    function view_category() {
        return view('Frontend.category.view_category');
    }

    function see_category($category) {
        $category = Category::where('category_english_name', $category)->orWhere('category_bangla_name', $category)->first();
        $recent_blogs = Blog::latest()->limit(10)->get();

        if (!$category) {
            abort(404, 'Category not found');
        }
        $blogs = Blog::where('category_id', $category->id)->latest()->get();

        return view('Frontend.category.see_category', [
            'blogs' => $blogs,
            'recent_blogs' => $recent_blogs,
        ]);
    }

    function add_category(Request $request) {
        $request->validate([
            'category_bangla_name' => 'required',
            'category_english_name' => 'required',
            'category_bangla_disc' => 'required',
            'category_english_disc' => 'required',
            'category_image' => ['required', 'mimes:png,jpg,jpeg', 'max:1024'],
        ], [
            'category_bangla_name' => 'Write the bangla name',
            'category_english_name' => 'Write the english name',
            'category_bangla_disc' => 'Write the bangla description',
            'category_english_disc' => 'Write the english description',
            'category_image.required' => 'Please, select a category image.',
            'category_image.max' => 'Image size should be less then 1 MB.',
            'category_image.mimes' => 'Only jpg, jpeg, and png image formats are allowed.'
        ]);

        $image = $request->category_image;
        $extension = $image->extension();
        $file_name = $request->category_english_name.'.'.$extension;

        $maneger = new ImageManager(new Driver);
        $maneger->read($image)->resize(400, 300)->toJpeg(80)->save(public_path('upload/categories/'.$file_name));

        Category::insert([
            'category_bangla_name' => $request->category_bangla_name,
            'category_english_name' => $request->category_english_name,
            'category_bangla_description' => $request->category_bangla_disc,
            'category_english_description' => $request->category_english_disc,
            'category_image' => $file_name,
            'created_at' => Carbon::now(),
        ]);

        return back()->with('add_category', 'The Category added');
    }

    function delete_category($id) {
        $category = Category::find($id);

        if ($category) {
            $delete_from = public_path('upload/categories/'.$category->category_image);

            if (file_exists($delete_from)) {
                unlink($delete_from);
            }
            $category->delete();

            return back()->with('deleted', 'Category deleted successfully');
        } else {
            return back()->with('not_found', 'Category not found.');
        }
    }

    function edit_category($id) {
        $category = Category::find($id);

        return view('Backend.category.edit_category', [
            'category' => $category,
        ]);
    }

    function update_edit_category(Request $request, $id) {
        $request->validate([
            'category_bangla_name' => 'required',
            'category_english_name' => 'required',
            'category_bangla_disc' => 'required',
            'category_english_disc' => 'required',
            'category_image' => ['mimes:png,jpg,jpeg', 'max:1024'],
        ], [
            'category_bangla_name' => 'Write the bangla name',
            'category_english_name' => 'Write the english name',
            'category_bangla_disc' => 'Write the bangla description',
            'category_english_disc' => 'Write the english description',
            'category_image.max' => 'Image size should be less then 1 MB.',
            'category_image.mimes' => 'Only jpg, jpeg, and png image formats are allowed.'
        ]);

        $category = Category::find($id);

        if($category) {
            $category->update([
                'category_bangla_name' => $request->category_bangla_name,
                'category_english_name' => $request->category_english_name,
                'category_bangla_description' => $request->category_bangla_disc,
                'category_english_description' => $request->category_english_disc,
                'created_at' => Carbon::now(),
            ]);

            if($request->hasFile('category_image')) {
                if($category->category_image != null) {
                    $delete_from = public_path('upload/categories/'.$category->category_image);
                    if(file_exists($delete_from)) {
                        unlink($delete_from);
                    }

                    $image = $request->category_image;
                    $extension = $image->extension();
                    $file_name = $request->category_english_name.'.'.$extension;

                    $maneger = new ImageManager(new Driver);
                    $maneger->read($image)->resize(400, 300)->toJpeg(80)->save(public_path('upload/categories/'.$file_name));

                    $category->update([
                        'category_image' => $file_name,
                        'created_at' => Carbon::now(),
                    ]);
                }
            }
            return back()->with('update_category', 'The Category updated');
        }else {
            return back()->with('not_found', 'Category Not Found');
        }
    }

    function categories_store(Request $request) {
        $request->validate([
            'categories' => 'required',
        ], [
            'categories.required' => 'Plese, select category',
        ]);

        $user = Auth::guard('user')->user();

        if($user) {
            foreach($request->categories as $category_id) {
                UserCategory::insert([
                    'user_id' => $user->id,
                    'category_id' => $category_id,
                    'created_at' => Carbon::now(),
                ]);
            }
            return redirect()->route('index');
        }else {
            return back()->with('no_user', 'User not found');
        }
    }

}
