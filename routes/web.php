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

// Redirect the base URL to login page
use App\Livewire\Welcome\Welcome;
use App\Livewire\Register\Register;
use App\Livewire\UpdateProfile\Updateprofile;

Route::get('/', Welcome::class)->name('welcome.index');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Register Routes
Route::get('/register', Register::class)->name('register');

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

    // durasi routes
    Route::get('/durasi', \App\Livewire\Durasi\Durasi::class)->name('durasi.index');

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

// API route untuk mendapatkan data profil masjid
Route::get('/api/profil/{slug}', function ($slug) {
    $profil = \App\Models\Profil::where('slug', $slug)->first();
    if ($profil) {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $profil->name,
                'address' => $profil->address,
                'logo_masjid' => $profil->logo_masjid,
                'logo_pemerintah' => $profil->logo_pemerintah
            ]
        ]);
    }
    return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan'], 404);
})->name('api.profil');

// API route untuk mendapatkan data marquee
Route::get('/api/marquee/{slug}', function ($slug) {
    $profil = \App\Models\Profil::where('slug', $slug)->first();
    if ($profil) {
        $marquee = \App\Models\Marquee::where('user_id', $profil->user_id)->first();
        if ($marquee) {
            return response()->json([
                'success' => true,
                'data' => [
                    'marquee1' => $marquee->marquee1,
                    'marquee2' => $marquee->marquee2,
                    'marquee3' => $marquee->marquee3,
                    'marquee4' => $marquee->marquee4,
                    'marquee5' => $marquee->marquee5,
                    'marquee6' => $marquee->marquee6
                ]
            ]);
        }
    }
    return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
})->name('api.marquee');

// API route untuk mendapatkan data slide
Route::get('/api/slides/{slug}', function ($slug) {
    $profil = \App\Models\Profil::where('slug', $slug)->first();
    if ($profil) {
        $slides = \App\Models\Slides::where('user_id', $profil->user_id)->first();
        if ($slides) {
            return response()->json([
                'success' => true,
                'data' => [
                    'slide1' => $slides->slide1,
                    'slide2' => $slides->slide2,
                    'slide3' => $slides->slide3,
                    'slide4' => $slides->slide4,
                    'slide5' => $slides->slide5,
                    'slide6' => $slides->slide6
                ]
            ]);
        }
    }
    return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
})->name('api.slides');

// API route untuk mendapatkan data petugas jumat
Route::get('/api/petugas/{slug}', function ($slug) {
    $profil = \App\Models\Profil::where('slug', $slug)->first();
    if ($profil) {
        $petugas = \App\Models\Petugas::where('user_id', $profil->user_id)->first();
        if ($petugas) {
            return response()->json([
                'success' => true,
                'data' => [
                    'hari' => $petugas->hari,
                    'khatib' => $petugas->khatib,
                    'imam' => $petugas->imam,
                    'muadzin' => $petugas->muadzin
                ]
            ]);
        }
    }
    return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
})->name('api.petugas');

// API route untuk mendapatkan data adzan
Route::get('/api/adzan/{slug}', function ($slug) {
    $profil = \App\Models\Profil::where('slug', $slug)->first();
    if ($profil) {
        $adzan = \App\Models\Adzan::where('user_id', $profil->user_id)->first();
        if ($adzan) {
            return response()->json([
                'success' => true,
                'data' => [
                    'adzan1' => $adzan->adzan1,
                    'adzan2' => $adzan->adzan2,
                    'adzan3' => $adzan->adzan3,
                    'adzan4' => $adzan->adzan4,
                    'adzan5' => $adzan->adzan5,
                    'adzan6' => $adzan->adzan6,
                    'adzan15' => $adzan->adzan15,
                    'adzan7' => $adzan->adzan7,
                    'adzan8' => $adzan->adzan8,
                    'adzan9' => $adzan->adzan9,
                    'adzan10' => $adzan->adzan10,
                    'adzan11' => $adzan->adzan11,
                    'adzan12' => $adzan->adzan12,
                ]
            ]);
        }
    }
    return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
})->name('api.adzan');

Route::get('/api/server-time', function () {
    try {
        // Coba API utama (Pekanbaru)
        $response = Http::timeout(5)->get('https://superapp.pekanbaru.go.id/api/server-time');

        if ($response->successful()) {
            $serverTime = $response['serverTime'];
            $serverDateTime = new \DateTime($serverTime, new \DateTimeZone('UTC'));
            $serverDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
            // $serverDateTime->modify('+0 hour 8 minutes'); // Tambah 1 jam 20 menit

            // untuk testing hari jumat
            // $currentDay = (int)$serverDateTime->format('w');
            // $daysToFriday = 5 - $currentDay;
            // if ($daysToFriday < 0) {
            //     $daysToFriday += 7;
            // }
            // $serverDateTime->modify("+{$daysToFriday} days");
            // $serverDateTime->setTime(12, 17, 55);

            return response()->json([
                'success' => true,
                'data' => [
                    'timestamp' => $serverDateTime->getTimestamp() * 1000, // dalam milidetik
                    'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                    'source' => 'pekanbaru'
                ]
            ]);
        } else {
            throw new \Exception('API utama gagal');
        }
    } catch (\Exception $e) {
        try {
            // Fallback ke timeapi.io
            $fallbackResponse = Http::timeout(5)->get('https://timeapi.io/api/time/current/zone?timeZone=Asia%2FJakarta');

            if ($fallbackResponse->successful()) {
                $serverTime = $fallbackResponse['dateTime'];
                $serverDateTime = new \DateTime($serverTime, new \DateTimeZone('Asia/Jakarta'));
                // $serverDateTime->modify('+2 hour 33 minutes'); // Tambah 1 jam 20 menit
                return response()->json([
                    'success' => true,
                    'data' => [
                        'timestamp' => $serverDateTime->getTimestamp() * 1000, // dalam milidetik
                        'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                        'source' => 'timeapi'
                    ]
                ]);
            } else {
                throw new \Exception('API timeapi.io gagal');
            }
        } catch (\Exception $e) {
            try {
                // Fallback ke API Google Script
                $newApiResponse = Http::timeout(5)->get('https://script.google.com/macros/s/AKfycbyd5AcbAnWi2Yn0xhFRbyzS4qMq1VucMVgVvhul5XqS9HkAyJY/exec?tz=Asia/Jakarta');

                if ($newApiResponse->successful() && $newApiResponse['status'] === 'ok') {
                    $serverTime = $newApiResponse['fulldate'];
                    $serverDateTime = new \DateTime($serverTime, new \DateTimeZone('Asia/Jakarta'));

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'timestamp' => $serverDateTime->getTimestamp() * 1000, // dalam milidetik
                            'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                            'source' => 'google-script'
                        ]
                    ]);
                } else {
                    throw new \Exception('API Google Script gagal');
                }
            } catch (\Exception $e) {
                // Fallback ke waktu server lokal
                $serverDateTime = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
                return response()->json([
                    'success' => true,
                    'data' => [
                        'timestamp' => $serverDateTime->getTimestamp() * 1000, // dalam milidetik
                        'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                        'source' => 'local'
                    ]
                ]);
            }
        }
    }
});

// Public route for accessing specific mosque page by slug
// This must be the last route to avoid conflicts with named routes
Route::get('/api/prayer-status/{slug}', function ($slug) {
    try {
        // Get the Firdaus component instance
        $firdaus = new \App\Livewire\Firdaus\Firdaus();
        $firdaus->mount($slug);

        // Get current server time
        $response = Http::get('https://superapp.pekanbaru.go.id/api/server-time');
        if (!$response->successful()) {
            return response()->json(['success' => false, 'message' => 'Server time unavailable']);
        }

        $serverTime = $response['serverTime'];

        // Convert UTC time to Asia/Jakarta timezone
        $utcDateTime = new DateTime($serverTime, new DateTimeZone('UTC'));
        $jakartaDateTime = $utcDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        $currentTime = $jakartaDateTime->format('H:i');

        // Get prayer status using reflection to call private method
        $reflection = new ReflectionClass($firdaus);
        $method = $reflection->getMethod('calculateActivePrayerTimeStatus');
        $method->setAccessible(true);
        $status = $method->invoke($firdaus, $currentTime);

        return response()->json([
            'success' => true,
            'data' => $status,
            'current_time_jakarta' => $jakartaDateTime->format('Y-m-d H:i:s'), // Optional: untuk debugging
            'timezone' => 'Asia/Jakarta'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error calculating prayer status: ' . $e->getMessage()
        ]);
    }
});

Route::get('{slug}', \App\Livewire\Firdaus\Firdaus::class)->name('firdaus');
