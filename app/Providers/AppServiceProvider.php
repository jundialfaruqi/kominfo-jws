<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

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
        if (request()->getHost() !== 'localhost' && request()->getHost() !== '127.0.0.1' && request()->httpHost() !== 'localhost:8000') {
            URL::forceScheme('https');
        }

        Livewire::addPersistentMiddleware([
            \App\Http\Middleware\AdminMiddleware::class,
        ]);
    }
}
