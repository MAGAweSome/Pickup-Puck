<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Games\Game;
use Illuminate\Support\Carbon;

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
