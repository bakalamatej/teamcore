<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

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
        Blade::component('layouts.panel-layout', 'panel-layout');

        View::composer('panel.sidebar', function ($view) {
            $role = Auth::user()?->getRole() ?? 'player';
            $panelLabel = match ($role) {
                'admin' => 'Admin panel',
                'coach' => 'Coach panel',
                default => 'Profile',
            };

            $view->with('role', $role)
                ->with('panelLabel', $panelLabel);
        });
    }
}
