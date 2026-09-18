<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Games\Game;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
        // Custom branded dark HTML email for Password Reset
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Your Password - Pickup Puck')
                ->view('emails.auth.reset-password', [
                    'user' => $notifiable,
                    'url' => $resetUrl,
                    'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60),
                ]);
        });

        // Custom branded dark HTML email for Email Verification
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Your Email Address - Pickup Puck')
                ->view('emails.auth.verify-email', [
                    'user' => $notifiable,
                    'url' => $url,
                ]);
        });

        // Share sidebar next-game data with all views (fallback computation)
        View::composer('*', function ($view) {
            try {
                $now = Carbon::now()->setTimezone('America/Toronto');
                $upcomingCount = Game::where('time', '>', $now)->count();
                $nextGame = Game::where('time', '>', $now)->orderBy('time', 'asc')->first();
            } catch (\Exception $e) {
                $upcomingCount = 0;
                $nextGame = null;
            }

            $view->with('sidebarNextGame', $nextGame);
            $view->with('sidebarUpcomingCount', $upcomingCount);
        });
    }
}
