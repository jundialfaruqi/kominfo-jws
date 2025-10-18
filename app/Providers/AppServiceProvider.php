<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Laporan;
use App\Policies\LaporanPolicy;
use App\Models\GroupCategory;
use App\Policies\GroupCategoryPolicy;

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
        /**
         * Mendefinisikan 'Super Admin'
         * selalu diizinkan melakukan semua aksi (permissions)
         */
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        if (request()->getHttpHost() == 'jadwalsholat.pekanbaru.go.id' or App::environment('production'))
            URL::forceScheme('https');

        // if (request()->getHost() !== 'localhost' && request()->getHost() !== '127.0.0.1' && request()->httpHost() !== 'localhost:8000') {
        //     URL::forceScheme('https');
        // }

        Livewire::addPersistentMiddleware([
            \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Register policy untuk Laporan
        Gate::policy(Laporan::class, LaporanPolicy::class);
        // Register policy untuk GroupCategory
        Gate::policy(GroupCategory::class, GroupCategoryPolicy::class);
    }
}
