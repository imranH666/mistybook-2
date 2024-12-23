<?php

namespace App\Http\Controllers;

use App\Models\Logo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class LogoController extends Controller
{
    function upload_logo() {
        return view('Backend.logo.logo');
    }

    function add_logo(Request $request) {
        $request->validate([
            'logo' => ['required', 'mimes:png,jpg,jpeg', 'max:1024'],
        ], [
            'logo.required' => 'Please, select a Logo.',
            'logo.max' => 'Logo size should be less then 1 MB.',
            'logo.mimes' => 'Only jpg, jpeg, and png logo formats are allowed.'
        ]);

        $image = $request->logo;
        $extension = $image->extension();
        $file_name = 'Mistybook-Logo'.'-'.uniqid().'.'.$extension;

        $maneger = new ImageManager(new Driver);
        $maneger->read($image)->resize(400, 200)->save(public_path('upload/logo/'.$file_name));

        if (!Logo::exists()) {
            Logo::insert([
                'logo' => $file_name,
                'created_at' => Carbon::now(),
            ]);

            return back()->with('added', 'Logo added successfully');
        } else {
            $existingLogo = Logo::first();
            $existingLogoPath = public_path('upload/logo/' . $existingLogo->logo);

            if ($existingLogo && file_exists($existingLogoPath)) {
                unlink($existingLogoPath);
            }

            $existingLogo->update([
                'logo' => $file_name,
            ]);

            return back()->with('updated', 'Logo updated successfully');
        }
    }
}
