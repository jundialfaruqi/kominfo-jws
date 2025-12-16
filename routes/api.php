<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\controllers_api as API;

// [ROUTE PROFIL MASJID]
// API route untuk mendapatkan data profil masjid
Route::get('profil/{slug}', [API\ProfilController::class, 'get_profil'])->name('api.profil');

// API route untuk mendapatkan theme pilihan
Route::get('theme-check/{slug}', [API\ProfilController::class, 'get_theme'])->name('api.theme');
// API route untuk mendapatkan daftar tema (my-theme) dipindah ke auth:sanctum

// API route untuk mendapatkan data marquee
Route::get('marquee1/{slug}', [API\ProfilController::class, 'get_marquee1'])->name('api.marquee1'); // API LAMA
Route::get('marquee/{slug}', [API\ProfilController::class, 'get_marquee'])->name('api.marquee');
// API route untuk mendapatkan data durasi (raw)
Route::get('durasi/{slug}', [API\ProfilController::class, 'get_durasi'])->name('api.durasi');

// API route untuk mendapatkan data slide
Route::get('slides1/{slug}', [API\ProfilController::class, 'get_slides1'])->name('api.slides1'); // API LAMA
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
Route::get('jumbotron1', [API\MasterController::class, 'get_jumbotron1'])->name('api.jumbotron1'); // API LAMA
Route::get('jumbotron', [API\MasterController::class, 'get_jumbotron'])->name('api.jumbotron');
// API route untuk mendapatkan data jumbotron masjid berdasarkan slug
// Route::get('jumbotron-masjid/{slug}', [API\MasterController::class, 'get_jumbotron_masjid'])->name('api.jumbotron-masjid');
Route::get('jumbotron-all/{slug}', [API\MasterController::class, 'get_jumbotron_all'])->name('api.jumbotron-all');
// API route untuk mendapatkan data agenda masjid bulan ini berdasarkan slug
Route::get('agenda/{slug}', [API\MasterController::class, 'get_agenda'])->name('api.agenda');

// API route untuk mendapatkan server time
Route::get('server-time', [API\MasterController::class, 'get_server_time'])->name('api.server-time');

// API route untuk mendapatkan refresh prayer time
Route::get('refresh-prayer-times', [API\MasterController::class, 'get_refresh_prayer_times'])->name('api.refresh-prayer-times');

// API route untuk rekap total keseluruhan kategori per profil (ALL, tanpa filter tanggal)
Route::get('balance-summary/{slug}', [API\ProfilController::class, 'get_balance_summary'])->name('api.balance-summary');
// Endpoint yang sama mengembalikan rekap dan rincian items per kategori
Route::get('balance-details/{slug}', [API\ProfilController::class, 'get_balance_summary'])->name('api.balance-details');
// API route untuk rekap 7 hari terakhir (endpoint baru, bebas dari perubahan bulanan)
Route::get('balance-summary-7hari/{slug}', [API\ProfilController::class, 'get_balance_summary_7hari'])->name('api.balance-summary-7hari');

// [ROUTE AUTH]
Route::post('login', [API\AuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [API\AuthController::class, 'user'])->name('api.auth.user');
    Route::post('user/update', [API\AuthController::class, 'update'])->name('api.auth.update');
    Route::post('logout', [API\AuthController::class, 'logout'])->name('api.auth.logout');

    // [ROUTE PROFIL MASJID]
    // API route untuk mendapatkan data profil masjid
    Route::get('profil-masjid', [API\ProfilMasjidController::class, 'getAllProfilMasjid'])->name('api.profil-masjid');
    // API route untuk mendapatkan data profil masjid milik user itu sendiri (menggunakan path param id)
    Route::get('profil-masjid/{id}', [API\ProfilMasjidController::class, 'getProfilMasjid'])->name('api.profil-masjid');
    // API route untuk membuat profil masjid milik user itu sendiri
    Route::post('profil-masjid/{id}', [API\ProfilMasjidController::class, 'createProfilMasjid'])->name('api.profil-masjid.create');
    // API route untuk update data profil masjid milik user itu sendiri untuk non admin super admin user
    Route::post('profil-masjid/{id}', [API\ProfilMasjidController::class, 'updateProfilMasjid'])->name('api.profil-masjid.update');

    // [ROUTE MY PROFIL MASJID]
    Route::get('my-profil-masjid', [API\MyProfilMasjidController::class, 'show'])->name('api.my-profil-masjid.show');
    Route::post('my-profil-masjid', [API\MyProfilMasjidController::class, 'store'])->name('api.my-profil-masjid.store');
    Route::put('my-profil-masjid', [API\MyProfilMasjidController::class, 'update'])->name('api.my-profil-masjid.update');

    // [ROUTE MY THEME]
    Route::get('my-theme', [API\MyThemeController::class, 'list'])->name('api.my-theme.list');
    Route::post('my-theme', [API\MyThemeController::class, 'set'])->name('api.my-theme.set');

    // [ROUTE MY MARQUE]
    Route::get('my-marque', [API\MyMarqueController::class, 'show'])->name('api.my-marque.show');
    Route::post('my-marque', [API\MyMarqueController::class, 'store'])->name('api.my-marque.store');
    Route::put('my-marque', [API\MyMarqueController::class, 'update'])->name('api.my-marque.update');

    // [ROUTE MY PETUGAS]
    Route::get('my-petugas', [API\MyPetugasController::class, 'list'])->name('api.my-petugas.list');
    Route::post('my-petugas', [API\MyPetugasController::class, 'store'])->name('api.my-petugas.store');
    Route::put('my-petugas/{id}', [API\MyPetugasController::class, 'update'])->name('api.my-petugas.update');
    Route::delete('my-petugas/{id}', [API\MyPetugasController::class, 'destroy'])->name('api.my-petugas.destroy');

    // [ROUTE MY AGENDA]
    Route::get('my-agenda', [API\MyAgendaController::class, 'list'])->name('api.my-agenda.list');
    Route::post('my-agenda', [API\MyAgendaController::class, 'store'])->name('api.my-agenda.store');
    Route::put('my-agenda/{id}', [API\MyAgendaController::class, 'update'])->name('api.my-agenda.update');
    Route::delete('my-agenda/{id}', [API\MyAgendaController::class, 'destroy'])->name('api.my-agenda.destroy');

    // [ROUTE MY GROUP CATEGORY]
    Route::get('my-group-category', [API\MyGroupCategoryController::class, 'list'])->name('api.my-group-category.list');
    Route::post('my-group-category', [API\MyGroupCategoryController::class, 'store'])->name('api.my-group-category.store');
    Route::put('my-group-category/{id}', [API\MyGroupCategoryController::class, 'update'])->name('api.my-group-category.update');
    Route::delete('my-group-category/{id}', [API\MyGroupCategoryController::class, 'destroy'])->name('api.my-group-category.destroy');

    // [ROUTE MY LAPORAN KEUANGAN]
    Route::get('my-laporan-keuangan', [API\MyLaporanKeuanganController::class, 'list'])->name('api.my-laporan-keuangan.list');
    Route::get('my-laporan-keuangan/graph', [API\MyLaporanKeuanganController::class, 'graph'])->name('api.my-laporan-keuangan.graph');
    Route::post('my-laporan-keuangan', [API\MyLaporanKeuanganController::class, 'store'])->name('api.my-laporan-keuangan.store');
    Route::put('my-laporan-keuangan/{id}', [API\MyLaporanKeuanganController::class, 'update'])->name('api.my-laporan-keuangan.update');
    Route::delete('my-laporan-keuangan/{id}', [API\MyLaporanKeuanganController::class, 'destroy'])->name('api.my-laporan-keuangan.destroy');

    // [ROUTE MY SLIDE]
    Route::get('my-slide', [API\MySlideController::class, 'show'])->name('api.my-slide.show');
    Route::post('my-slide/{slot}', [API\MySlideController::class, 'upload'])->name('api.my-slide.upload');
    Route::put('my-slide/{slot}', [API\MySlideController::class, 'upload'])->name('api.my-slide.update');
    Route::delete('my-slide/{slot}', [API\MySlideController::class, 'destroy'])->name('api.my-slide.destroy');

    // [ROUTE MY JUMBOTRON MASJID]
    Route::get('my-jumbotron-masjid', [API\MyJumbotronMasjidController::class, 'show'])->name('api.my-jumbotron-masjid.show');
    Route::put('my-jumbotron-masjid', [API\MyJumbotronMasjidController::class, 'setAktif'])->name('api.my-jumbotron-masjid.set-aktif');
    Route::post('my-jumbotron-masjid/{slot}', [API\MyJumbotronMasjidController::class, 'upload'])->name('api.my-jumbotron-masjid.upload');
    Route::put('my-jumbotron-masjid/{slot}', [API\MyJumbotronMasjidController::class, 'upload'])->name('api.my-jumbotron-masjid.update');
    Route::delete('my-jumbotron-masjid/{slot}', [API\MyJumbotronMasjidController::class, 'destroy'])->name('api.my-jumbotron-masjid.destroy');
    Route::post('my-jumbotron-masjid/toggle', [API\MyJumbotronMasjidController::class, 'toggleAktif'])->name('api.my-jumbotron-masjid.toggle');

    // [ROUTE MY SLIDE IQOMAH JUMAT]
    Route::get('my-slide-iqomah-jumat', [API\MySlideIqomahJumatController::class, 'show'])->name('api.my-slide-iqomah-jumat.show');
    Route::post('my-slide-iqomah-jumat/{slot}', [API\MySlideIqomahJumatController::class, 'upload'])->name('api.my-slide-iqomah-jumat.upload');
    Route::put('my-slide-iqomah-jumat/{slot}', [API\MySlideIqomahJumatController::class, 'upload'])->name('api.my-slide-iqomah-jumat.update');
    Route::delete('my-slide-iqomah-jumat/{slot}', [API\MySlideIqomahJumatController::class, 'destroy'])->name('api.my-slide-iqomah-jumat.destroy');

    // [ROUTE MY DURASI]
    Route::get('my-durasi', [API\MyDurasiController::class, 'index'])->name('api.my-durasi.index');
    Route::post('my-durasi', [API\MyDurasiController::class, 'update'])->name('api.my-durasi.update');

    // [ROUTE MY MUROTTAL]
    Route::get('my-murottal', [API\MyMurottalController::class, 'show'])->name('api.my-murottal.show');
    Route::post('my-murottal/{slot}', [API\MyMurottalController::class, 'upload'])->name('api.my-murottal.upload');
    Route::delete('my-murottal/{slot}', [API\MyMurottalController::class, 'destroy'])->name('api.my-murottal.destroy');
    Route::put('my-murottal/status', [API\MyMurottalController::class, 'updateStatus'])->name('api.my-murottal.update-status');

    // [ROUTE MY ADZAN AUDIO]
    Route::get('my-audio-adzan', [API\MyAdzanAudioController::class, 'show'])->name('api.my-audio-adzan.show');
    Route::post('my-audio-adzan/{slot}', [API\MyAdzanAudioController::class, 'upload'])->name('api.my-audio-adzan.upload');
    Route::delete('my-audio-adzan/{slot}', [API\MyAdzanAudioController::class, 'destroy'])->name('api.my-audio-adzan.destroy');
    Route::put('my-audio-adzan/status', [API\MyAdzanAudioController::class, 'updateStatus'])->name('api.my-audio-adzan.update-status');
});
