<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Admin\User\Index as UserIndex;
use App\Livewire\Inactive\Inactive;
use App\Livewire\Petugas\Petugas;
use App\Livewire\Profil\ProfilMasjid;
use App\Livewire\Slides\Slide;
use Illuminate\Support\Facades\Auth;
use App\Models\Profil;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

// Redirect the base URL to login page
use App\Livewire\Welcome\Welcome;
use App\Livewire\Register\Register;
use App\Livewire\UpdateProfile\Updateprofile;
use App\Models\User;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Livewire\GroupCategory\Group as GroupCategoryGroup;

Route::get('/', Welcome::class)->name('welcome.index');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Register Routes
// Route::get('/register', Register::class)->name('register');

Route::middleware('auth')->group(function () {
    Route::get('/inactive', Inactive::class)->name('inactive.index');
});

// Protected Routes (require authentication)
Route::middleware('auth', 'ensure-user-is-active')->group(function () {
    // Dashboard Route
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard.index');

    // Update Profile Route
    Route::get('/pengaturan', Updateprofile::class)->name('updateprofile.index');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/user', UserIndex::class)->name('user.index')->middleware('can:view-users');
        Route::get('/role', \App\Livewire\Admin\Role::class)->name('role.index')->middleware('can:view-roles');
        Route::get('/permission', \App\Livewire\Admin\Permission::class)->name('permission.index')->middleware('can:view-permissions');
        Route::get('/user-role-assignment', \App\Livewire\Admin\UserRoleAssignment::class)->name('user-role-assignment.index')->middleware('can:view-user-role-assignment');
        // Add other admin routes here
    });

    // Jumbotron Route
    Route::get('/jumbotron', \App\Livewire\Jumbotron\Jumbotron::class)
        ->name('jumbotron.index')
        ->middleware('jumbotron.permission');

    Route::get('/audios', \App\Livewire\Audios\Audio::class)->name('audios');
    // Tema Routes
    Route::get('/tema', \App\Livewire\Tema\Tema::class)->name('tema.index');

    // Set Tema Routes
    Route::get('/tema/set-tema', \App\Livewire\Tema\SetTema::class)->name('tema.set-tema');

    // Profile Routes
    Route::get('/profil-masjid', ProfilMasjid::class)->name('profilmasjid.index')->middleware('can:view-profil-masjid');

    // Slider Routes
    Route::get('/slider-utama', Slide::class)->name('slide.index');

    // Petugas Routes
    Route::get('/petugas-jumat', Petugas::class)->name('petugas.index');

    // Marquee Routes
    Route::get('/marquee', \App\Livewire\Marquee\Marquee::class)->name('marquee.index');

    // Laporan Keuangan Routes
    Route::get('/laporan-keuangan', \App\Livewire\Laporan\Keuangan::class)
        ->name('laporan-keuangan.index')
        ->middleware('can:view-laporan-keuangan');

    // Group Category Routes
    Route::get('/group-category', GroupCategoryGroup::class)
        ->name('group-category.index')
        ->middleware('can:view-group-category');

    Route::get('/group-category/create', \App\Livewire\GroupCategory\Create::class)
        ->name('group-category.create')
        ->middleware('can:create-group-category');

    Route::get('/group-category/{id}/edit', \App\Livewire\GroupCategory\Edit::class)
        ->name('group-category.edit')
        ->middleware('can:edit-group-category');
    // adzan routes
    Route::get('/adzan', \App\Livewire\Adzan\GambarAdzan::class)->name('adzan.index');

    // adzan audio routes
    Route::get('/adzan-audio', \App\Livewire\AdzanAudio\AdzanAudio::class)->name('adzan-audio.index');

    // durasi routes
    Route::get('/durasi', \App\Livewire\Durasi\Durasi::class)->name('durasi.index');

    // faq routes
    Route::get('/faq', \App\Livewire\Faq\Index::class)->name('faq.index');

    // Balance API Docs
    Route::get('/api-docs/balance', \App\Livewire\ApiDocs\Balance::class)
        ->name('api-docs.balance')
        ->middleware('can:view-api-docs');

    // about routes
    Route::get('/about', \App\Livewire\About\Index::class)->name('about.index');

    // contact routes
    Route::get('/contact', \App\Livewire\Contact\Index::class)->name('contact.index');

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

Route::get('{slug}', \App\Livewire\Firdaus\Firdaus::class)->name('firdaus');



// TESTING WEB SOCKER
Route::get('test/testing-channel/view/{slug}', function ($slug) {
    $profil = Profil::where('slug', $slug)->first();
    if (!$profil) {
        return redirect()->route('dashboard.index')->with('error', 'Profil masjid tidak ditemukan !');
    }
    return view('testingWebSocket', ['slug' => $slug]);
});
Route::get('test/testing-channel/event/{slug}', function ($slug) {
    $profil = Profil::where('slug', $slug)->first();
    if (!$profil) {
        return redirect()->route('dashboard.index')->with('error', 'Profil masjid tidak ditemukan !');
    }
    event(new \App\Events\ContentUpdatedEvent($slug, 'hello ' . $profil->name));
});
