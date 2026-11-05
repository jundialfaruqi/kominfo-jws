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
use App\Models\Profil;
use App\Models\Marquee;
use App\Models\DevicePairing;
use App\Models\User;
use App\Models\Audios;
use App\Models\AdzanAudio;
use App\Models\JumbotronMasjid;
use App\Models\Adzan;
use App\Models\Agenda;
use App\Models\Durasi;
use App\Observers\PetugasObserver;
use App\Observers\SlidesObserver;
use App\Observers\LaporanObserver;
use App\Observers\ProfilObserver;
use App\Observers\MarqueeObserver;
use App\Observers\DevicePairingObserver;
use App\Observers\UserObserver;
use App\Observers\GroupCategoryObserver;
use App\Observers\AudiosObserver;
use App\Observers\AdzanAudioObserver;
use App\Observers\JumbotronMasjidObserver;
use App\Observers\AdzanObserver;
use App\Observers\AgendaObserver;
use App\Observers\DurasiObserver;

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

        // Register observers untuk aktivitas masjid & user
        Petugas::observe(PetugasObserver::class);
        Slides::observe(SlidesObserver::class);
        Laporan::observe(LaporanObserver::class);
        Profil::observe(ProfilObserver::class);
        Marquee::observe(MarqueeObserver::class);
        DevicePairing::observe(DevicePairingObserver::class);
        User::observe(UserObserver::class);
        GroupCategory::observe(GroupCategoryObserver::class);
        Audios::observe(AudiosObserver::class);
        AdzanAudio::observe(AdzanAudioObserver::class);
        JumbotronMasjid::observe(JumbotronMasjidObserver::class);
        Adzan::observe(AdzanObserver::class);
        Agenda::observe(AgendaObserver::class);
        Durasi::observe(DurasiObserver::class);

        // Set locale Carbon ke bahasa Indonesia untuk diffForHumans()
        Carbon::setLocale('id');
    }
}
