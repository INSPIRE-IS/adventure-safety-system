<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment(['local'])) {
            // Log::info('Mail is set to sandbox');
            Mail::alwaysTo('sandbox@inspireonline.co.za');
        }
        // if (app()->environment(['staging', 'development'])) {
        //     // Log::info('Mail is set to sandbox');
        //     Mail::alwaysTo([
        //         'sandbox@inspireonline.co.za'
        //     ]);
        // }
    }
}
