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
        \Illuminate\Support\Facades\View::composer('admin.layouts.sidebar', function ($view) {
            $view->with('menus', \App\Models\Menu::whereNull('parent_id')->with('children')->orderBy('order')->get());
        });
        View::composer(
        [
            'admin.*',
            // 'admin.invoice_pdf',
        ],
            function ($view) {
                $view->with(
                    'center',
                    \App\Models\CenterDetails::first()
                );
            }
        );

        if (config('app.env') !== 'local') {
            \URL::forceScheme('https');
        }
    }
}
