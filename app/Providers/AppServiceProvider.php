<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Laporan;
use App\Policies\LaporanPolicy;
use App\Models\GroupCategory;
use App\Policies\GroupCategoryPolicy;
use App\Models\Petugas;
use App\Models\Slides;
use App\Observers\PetugasObserver;
use App\Observers\SlidesObserver;
use App\Observers\LaporanObserver;
use App\Models\Profil;
use App\Observers\ProfilObserver;
use App\Models\Marquee;
use App\Observers\MarqueeObserver;

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

        // Register observers untuk aktivitas user
        Petugas::observe(PetugasObserver::class);
        Slides::observe(SlidesObserver::class);
        Laporan::observe(LaporanObserver::class);
        Profil::observe(ProfilObserver::class);
        Marquee::observe(MarqueeObserver::class);

        // Set locale Carbon ke bahasa Indonesia untuk diffForHumans()
        Carbon::setLocale('id');
    }
}
