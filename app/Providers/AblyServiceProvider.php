<?php

namespace App\Providers;

use Ably\AblyRest;
use Illuminate\Support\ServiceProvider;

class AblyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Singleton ব্যবহার করুন যাতে একই AblyRest ইন্সট্যান্স পাওয়া যায়
        $this->app->singleton(AblyRest::class, function ($app) {
            return new AblyRest(['key' => env('ABLY_API_KEY')]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
