<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

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
        // Build password reset links pointing to the frontend app
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $base = rtrim(config('app.frontend_url') ?? config('app.url'), '/');
            $email = urlencode($notifiable->getEmailForPasswordReset());
            return $base.'/reset-password?token='.$token.'&email='.$email;
        });
    }
}
