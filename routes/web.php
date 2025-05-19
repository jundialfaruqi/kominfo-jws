<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Admin\User\Index as UserIndex;
use App\Livewire\Petugas\Petugas;
use App\Livewire\Profil\ProfilMasjid;
use App\Livewire\Slides\Slide;
use Illuminate\Support\Facades\Auth;
use App\Models\Profil;

// Redirect the base URL to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Dashboard Route
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard.index');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/user', UserIndex::class)->name('user.index');
        // Add other admin routes here
    });

    // Profile Routes
    Route::get('/profil-masjid', ProfilMasjid::class)->name('profilmasjid.index');

    // Slider Routes
    Route::get('/slider-utama', Slide::class)->name('slide.index');

    // Petugas Routes
    Route::get('/petugas-jumat', Petugas::class)->name('petugas.index');

    // Marquee Routes
    Route::get('/marquee', \App\Livewire\Marquee\Marquee::class)->name('marquee.index');

    // adzan routes
    Route::get('/adzan', \App\Livewire\Adzan\GambarAdzan::class)->name('adzan.index');

    // User-specific route that redirects to their own mosque page
    Route::get('/my/mosque', function () {
        // Get the authenticated user
        $user = Auth::user();

        // Find the profile associated with this user
        $profile = Profil::where('user_id', $user->id)->first();

        // If profile exists, redirect to their firdaus page
        if ($profile && $profile->slug) {
            return redirect()->route('firdaus', ['slug' => $profile->slug]);
        }

        // If no profile found, redirect to dashboard with error
        return redirect()->route('dashboard.index')->with('error', 'Profil masjid tidak ditemukan. Silahkan buat profil terlebih dahulu.');
    })->name('my.mosque');

    // Logout Route
    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});

// Public route for accessing specific mosque page by slug
// This must be the last route to avoid conflicts with named routes
Route::get('{slug}', \App\Livewire\Firdaus\Firdaus::class)->name('firdaus');
