<?php

namespace App\Http\Controllers;

use App\Models\LightDarkMode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    function google_redirect() {
        return Socialite::driver('google')->redirect();
    }

    function google_callback() {
        $user = Socialite::driver('google')->user();
        $existingCustomer = User::where('email', $user->getEmail())->first();

        if ($existingCustomer) {
            Auth::guard('user')->login($existingCustomer);
        } else {
            $slug = Str::random(16).'_'.random_int(10000000, 999999999).Str::random(16).'_'.random_int(10000000, 999999999);

            $newCustomer = User::create([
                'fname' => $user->getName(),
                'email' => $user->getEmail(),
                'photo' => $user->getAvatar(),
                'password' => bcrypt('pass@123'),
                'profession' => 'Your Profession',
                'description' => 'This is description',
                'slug' => $slug,
                'created_at' => Carbon::now(),
            ]);

            LightDarkMode::insert([
                'user_id' => $newCustomer->id,
                'value' => 'dark',
            ]);

            Auth::guard('user')->login($newCustomer);
        }
        return redirect('/');
    }
}
