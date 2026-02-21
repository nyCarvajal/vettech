<?php

namespace App\Providers;

use App\Support\ClinicaActual;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        View::composer('*', function ($view) {
            if (! array_key_exists('clinica', $view->getData())) {
                $view->with('clinica', ClinicaActual::get());
            }
        });
    }
}
