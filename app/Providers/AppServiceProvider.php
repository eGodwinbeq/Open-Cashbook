<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // Share chapters variable with all views so layouts that reference $chapters don't break
        View::composer('*', function ($view) {
            $user = auth()->user();
            $chapters = $user ? $user->chapters : collect();
            $view->with('chapters', $chapters);
        });
    }
}
