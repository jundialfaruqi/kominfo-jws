<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\controllers_api as API;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// [ROUTE PROFIL MASJID]
// API route untuk mendapatkan data profil masjid
Route::get('profil/{slug}', [API\ProfilController::class, 'get_profil'])->name('api.profil');

// API route untuk mendapatkan theme pilihan
Route::get('theme-check/{slug}', [API\ProfilController::class, 'get_theme'])->name('api.theme');

// API route untuk mendapatkan data marquee
Route::get('marquee1/{slug}', [API\ProfilController::class, 'get_marquee1'])->name('api.marquee1'); // API LAMA
Route::get('marquee/{slug}', [API\ProfilController::class, 'get_marquee'])->name('api.marquee');

// API route untuk mendapatkan data slide
Route::get('slides1/{slug}', [API\ProfilController::class, 'get_slides1'])->name('api.slides1');
Route::get('slides/{slug}', [API\ProfilController::class, 'get_slides'])->name('api.slides');

// API route untuk mendapatkan data petugas jumat
Route::get('petugas/{slug}', [API\ProfilController::class, 'get_petugas'])->name('api.petugas');

// API route untuk mendapatkan data slider adzan / slider iqomah / durasi
Route::get('adzan1/{slug}', [API\ProfilController::class, 'get_adzan1'])->name('api.adzan1'); // API LAMA
Route::get('adzan/{slug}', [API\ProfilController::class, 'get_adzan'])->name('api.adzan');

// API route untuk mendapatkan data audio background masjid
Route::get('audio1/{slug}', [API\ProfilController::class, 'get_audio1'])->name('api.audio1'); // API LAMA
Route::get('audio/{slug}', [API\ProfilController::class, 'get_audio'])->name('api.audio');

// API route untuk mendapatkan data audio adzan masjid
Route::get('adzan-audio/{slug}', [API\ProfilController::class, 'get_adzan_audio'])->name('api.adzan-audio');

// API route untuk mendapatkan data prayer status
Route::get('prayer-status/{slug}', [API\ProfilController::class, 'get_prayer_status'])->name('api.prayer-status');

// [ROUTE MASTER]
// API route untuk mendapatkan data jumbotron
Route::get('jumbotron', [API\MasterController::class, 'get_jumbotron'])->name('api.jumbotron');

// API route untuk mendapatkan server time
Route::get('server-time', [API\MasterController::class, 'get_server_time'])->name('api.server-time');

// API route untuk mendapatkan refresh prayer time
Route::get('refresh-prayer-times', [API\MasterController::class, 'get_refresh_prayer_times'])->name('api.refresh-prayer-times');
