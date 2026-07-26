<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MyMasjidController extends Controller
{
    /**
     * Mengembalikan waktu saat ini.
     */
    public function index(Request $request)
    {
        try {
            $slug = $request->query('slug');

            if (!$slug) {
                return response()->json([
                    'code' => 400,
                    'success' => false,
                    'message' => 'Parameter slug wajib diisi',
                ], 400);
            }

            // Ambil data Profil hanya dengan kolom yang diperlukan, plus user_id untuk keperluan relasi
            $profil = \App\Models\Profil::query()
                ->select(['id', 'user_id', 'name', 'slug', 'address', 'logo_masjid', 'logo_pemerintah'])
                ->where('slug', $slug)
                ->first();

            if (!$profil) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Profil masjid tidak ditemukan',
                ], 404);
            }

            $userId = $profil->user_id;
            
            // Ambil data Marquee hanya dengan kolom yang diperlukan
            $marquee = \App\Models\Marquee::query()
                ->select(['id', 'marquee1', 'marquee2', 'marquee3', 'marquee4', 'marquee5', 'marquee6', 'marquee_speed'])
                ->where('user_id', $userId)
                ->first();
            
            // Ambil data Slides
            $slides = \App\Models\Slides::query()
                ->select(['id', 'slide1', 'slide2', 'slide3', 'slide4', 'slide5', 'slide6'])
                ->where('user_id', $userId)
                ->first();
            
            // Ambil data Adzan (salah penamaan di backend, ini berisi gambar-gambar slide)
            $adzanData = \App\Models\Adzan::query()
                ->select(['id', 'adzan1', 'adzan2', 'adzan3', 'adzan4', 'adzan5', 'adzan6', 'adzan7', 'adzan8', 'adzan9', 'adzan10', 'adzan11', 'adzan12', 'adzan13', 'adzan14', 'adzan15'])
                ->where('user_id', $userId)
                ->first();

            $slideIqomah = null;
            $slideJumat = null;
            $slideFinal = null;

            if ($adzanData) {
                $slideIqomah = [
                    'id' => $adzanData->id,
                    'slide1' => $adzanData->adzan1,
                    'slide2' => $adzanData->adzan2,
                    'slide3' => $adzanData->adzan3,
                    'slide4' => $adzanData->adzan4,
                    'slide5' => $adzanData->adzan5,
                    'slide6' => $adzanData->adzan6,
                ];

                $slideJumat = [
                    'id' => $adzanData->id,
                    'slide1' => $adzanData->adzan7,
                    'slide2' => $adzanData->adzan8,
                    'slide3' => $adzanData->adzan9,
                    'slide4' => $adzanData->adzan10,
                    'slide5' => $adzanData->adzan11,
                    'slide6' => $adzanData->adzan12,
                ];

                $slideFinal = [
                    'id' => $adzanData->id,
                    'slide1' => $adzanData->adzan15,
                ];
            }
            
            // Ambil theme_id dari tabel users (select langsung)
            $user = \App\Models\User::query()->select('theme_id')->find($userId);
            $themeId = $user ? $user->theme_id : null;

            // Ambil data Durasi
            $durasiModel = \App\Models\Durasi::query()->where('user_id', $userId)->first();
            $durasi = null;
            if ($durasiModel) {
                $durasi = [
                    'adzan_imsak' => (int) $durasiModel->adzan_imsak,
                    'adzan_shuruq' => (int) $durasiModel->adzan_shuruq,
                    'adzan_dhuha' => (int) $durasiModel->adzan_dhuha,
                    'adzan_shubuh' => (int) $durasiModel->adzan_shubuh,
                    'iqomah_shubuh' => (int) $durasiModel->iqomah_shubuh,
                    'final_shubuh' => (int) $durasiModel->final_shubuh,
                    'adzan_dzuhur' => (int) $durasiModel->adzan_dzuhur,
                    'iqomah_dzuhur' => (int) $durasiModel->iqomah_dzuhur,
                    'final_dzuhur' => (int) $durasiModel->final_dzuhur,
                    'jumat_slide' => (int) $durasiModel->jumat_slide,
                    'adzan_ashar' => (int) $durasiModel->adzan_ashar,
                    'iqomah_ashar' => (int) $durasiModel->iqomah_ashar,
                    'final_ashar' => (int) $durasiModel->final_ashar,
                    'adzan_maghrib' => (int) $durasiModel->adzan_maghrib,
                    'iqomah_maghrib' => (int) $durasiModel->iqomah_maghrib,
                    'final_maghrib' => (int) $durasiModel->final_maghrib,
                    'adzan_isya' => (int) $durasiModel->adzan_isya,
                    'iqomah_isya' => (int) $durasiModel->iqomah_isya,
                    'final_isya' => (int) $durasiModel->final_isya,
                    'finance_scroll_speed' => (int) $durasiModel->finance_scroll_speed,
                ];
            }

            // Sembunyikan user_id karena tidak diperlukan oleh Flutter
            $profil->makeHidden(['user_id']);

            // Ambil jadwal sholat dari API eksternal (MyQuran) dengan caching (Bulan Ini & Bulan Depan)
            $now = Carbon::now('Asia/Jakarta');
            $cityId = '0412'; // Default Kota Pekanbaru

            // Bulan Ini
            $year = $now->format('Y');
            $month = $now->format('m');
            $cacheKey = "jadwal_sholat_{$cityId}_{$year}_{$month}";
            $jadwalBulanIni = Cache::remember($cacheKey, now()->addDays(7), function () use ($cityId, $year, $month) {
                try {
                    $response = Http::timeout(10)->get("https://api.myquran.com/v2/sholat/jadwal/{$cityId}/{$year}/{$month}");
                    if ($response->successful()) {
                        return $response->json('data.jadwal');
                    }
                } catch (\Exception $e) {}
                return [];
            });

            // Bulan Depan (Untuk jaga-jaga jika berganti bulan tanpa reload)
            $nextMonthDate = $now->copy()->addMonth();
            $nYear = $nextMonthDate->format('Y');
            $nMonth = $nextMonthDate->format('m');
            $nCacheKey = "jadwal_sholat_{$cityId}_{$nYear}_{$nMonth}";
            $jadwalBulanDepan = Cache::remember($nCacheKey, now()->addDays(7), function () use ($cityId, $nYear, $nMonth) {
                try {
                    $response = Http::timeout(10)->get("https://api.myquran.com/v2/sholat/jadwal/{$cityId}/{$nYear}/{$nMonth}");
                    if ($response->successful()) {
                        return $response->json('data.jadwal');
                    }
                } catch (\Exception $e) {}
                return [];
            });

            // Gabungkan menjadi satu array besar (sekitar 60 hari)
            $jadwalGabungan = array_merge(
                is_array($jadwalBulanIni) ? $jadwalBulanIni : [],
                is_array($jadwalBulanDepan) ? $jadwalBulanDepan : []
            );

            // Filter agar hanya mengirim dari hari ini ke depan (opsional untuk menghemat payload)
            $dateStr = $now->format('Y-m-d');
            $jadwalFinal = collect($jadwalGabungan)->filter(function ($item) use ($dateStr) {
                return isset($item['date']) && $item['date'] >= $dateStr;
            })->values()->toArray();

            // Ambil Agenda Masjid
            $todayStart = Carbon::now('Asia/Jakarta')->startOfDay();
            // Ambil hingga 30 hari ke depan
            $thirtyDaysLater = $todayStart->copy()->addDays(30);

            $agendaData = \App\Models\Agenda::query()->where('id_masjid', $profil->id)
                ->where('aktif', true)
                ->whereBetween('date', [$todayStart->toDateString(), $thirtyDaysLater->toDateString()])
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                ->map(function ($agenda) use ($todayStart) {
                    $agendaDate = Carbon::parse($agenda->date, 'Asia/Jakarta')->startOfDay();
                    
                    $hari = $agendaDate->locale('id')->translatedFormat('l');
                    if (strtolower($hari) === 'minggu') {
                        $hari = 'Ahad';
                    }
                    $formattedDate = $hari . ', ' . $agendaDate->format('d/m/Y');

                    if ($agendaDate->isSameDay($todayStart)) {
                        $days_label = 'Hari ini';
                    } else {
                        $days = $todayStart->diffInDays($agendaDate);
                        $days_label = $days . ' Hari Lagi';
                    }
                    return [
                        'name' => $agenda->name,
                        'date' => $formattedDate,
                        'date_raw' => $agendaDate->format('Y-m-d'),
                        'days_label' => $days_label,
                        'aktif' => $agenda->aktif,
                    ];
                });

            // Ambil Petugas Jumat (upcoming)
            $petugasData = \App\Models\Petugas::query()
                ->where('user_id', $userId)
                ->where('hari', '>=', $now->format('Y-m-d'))
                ->orderBy('hari', 'asc')
                ->get()
                ->map(function ($petugas) {
                    $date = Carbon::parse($petugas->hari, 'Asia/Jakarta');
                    $formattedDate = 'Jumat, ' . $date->format('d/m/Y');
                    return [
                        'id' => $petugas->id,
                        'hari' => $petugas->hari,
                        'tanggal_format' => $formattedDate,
                        'khatib' => $petugas->khatib,
                        'imam' => $petugas->imam,
                        'muadzin' => $petugas->muadzin,
                    ];
                });

            // Ambil Jumbotron Pemko (Global)
            $jumbotronPemko = \App\Models\Jumbotron::query()->first();
            $jumbotronPemkoData = null;
            if ($jumbotronPemko) {
                $jumbotronPemkoData = [
                    'id' => $jumbotronPemko->id,
                    'is_active' => (bool) $jumbotronPemko->is_active,
                    'slide1' => $jumbotronPemko->jumbo1,
                    'slide2' => $jumbotronPemko->jumbo2,
                    'slide3' => $jumbotronPemko->jumbo3,
                    'slide4' => $jumbotronPemko->jumbo4,
                    'slide5' => $jumbotronPemko->jumbo5,
                    'slide6' => $jumbotronPemko->jumbo6,
                ];
            }

            // Ambil Jumbotron Masjid
            $jumbotronMasjid = \App\Models\JumbotronMasjid::query()->where('masjid_id', $profil->id)->first();
            $jumbotronMasjidData = null;
            if ($jumbotronMasjid) {
                $jumbotronMasjidData = [
                    'id' => $jumbotronMasjid->id,
                    'is_active' => (bool) $jumbotronMasjid->aktif,
                    'slide1' => $jumbotronMasjid->jumbotron_masjid_1,
                    'slide2' => $jumbotronMasjid->jumbotron_masjid_2,
                    'slide3' => $jumbotronMasjid->jumbotron_masjid_3,
                    'slide4' => $jumbotronMasjid->jumbotron_masjid_4,
                    'slide5' => $jumbotronMasjid->jumbotron_masjid_5,
                    'slide6' => $jumbotronMasjid->jumbotron_masjid_6,
                ];
            }

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Data my-masjid berhasil diambil',
                'data' => [
                    'current_time' => [
                        'message' => 'Waktu server saat ini',
                        'data'    => $now->toDateTimeString(),
                    ],
                    'timestamp'    => [
                        'message' => 'Timestamp server saat ini (detik)',
                        'data'    => $now->timestamp,
                    ],
                    'theme_id'     => [
                        'message' => $themeId ? 'Tema pilihan pengguna' : 'Tema default belum diatur',
                        'data'    => $themeId,
                    ],
                    'profil'       => [
                        'message' => 'Profil masjid berhasil dimuat',
                        'data'    => $profil,
                    ],
                    'marquee'      => [
                        'message' => $marquee ? 'Data teks berjalan tersedia' : 'Tidak ada data teks berjalan',
                        'data'    => $marquee,
                    ],
                    'slides'       => [
                        'message' => $slides ? 'Data slide gambar tersedia' : 'Tidak ada data slide gambar',
                        'data'    => $slides,
                    ],
                    'slide_iqomah' => [
                        'message' => $slideIqomah ? 'Data gambar iqomah tersedia' : 'Tidak ada data gambar iqomah',
                        'data'    => $slideIqomah,
                    ],
                    'slide_jumat' => [
                        'message' => $slideJumat ? 'Data gambar jumat tersedia' : 'Tidak ada data gambar jumat',
                        'data'    => $slideJumat,
                    ],
                    'slide_final' => [
                        'message' => $slideFinal ? 'Data gambar final tersedia' : 'Tidak ada data gambar final',
                        'data'    => $slideFinal,
                    ],
                    'durasi'       => [
                        'message' => $durasi ? 'Pengaturan durasi khusus tersedia' : 'Tidak ada pengaturan durasi khusus (gunakan default)',
                        'data'    => $durasi,
                    ],
                    'jadwal_sholat'=> [
                        'message' => count($jadwalFinal) > 0 ? 'Jadwal sholat berhasil dimuat' : 'Jadwal sholat kosong atau gagal dimuat dari sumber',
                        'data'    => $jadwalFinal,
                    ],
                    'jumbotron_pemko'=> [
                        'message' => $jumbotronPemkoData ? 'Data jumbotron pemko tersedia' : 'Tidak ada data jumbotron pemko',
                        'data'    => $jumbotronPemkoData,
                    ],
                    'jumbotron_masjid'=> [
                        'message' => $jumbotronMasjidData ? 'Data jumbotron masjid tersedia' : 'Tidak ada data jumbotron masjid',
                        'data'    => $jumbotronMasjidData,
                    ],
                    'petugas_jumat'=> [
                        'message' => $petugasData->count() > 0 ? 'Data petugas jumat tersedia' : 'Tidak ada data petugas jumat',
                        'data'    => $petugasData,
                    ],
                    'agenda'       => [
                        'message' => $agendaData->count() > 0 ? 'Data agenda tersedia' : 'Tidak ada data agenda saat ini',
                        'data'    => $agendaData,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
