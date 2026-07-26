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
            
            // Ambil theme_id dari tabel users (select langsung)
            $user = \App\Models\User::query()->select('theme_id')->find($userId);
            $themeId = $user ? $user->theme_id : null;

            // Sembunyikan user_id karena tidak diperlukan oleh Flutter
            $profil->makeHidden(['user_id']);

            // Ambil jadwal sholat dari API eksternal (MyQuran) dengan caching
            $now = Carbon::now('Asia/Jakarta');
            $year = $now->format('Y');
            $month = $now->format('m');
            $dateStr = $now->format('Y-m-d');
            $cityId = '0412'; // Default Kota Pekanbaru
            $cacheKey = "jadwal_sholat_{$cityId}_{$year}_{$month}";

            $jadwalBulanIni = Cache::remember($cacheKey, now()->addDays(7), function () use ($cityId, $year, $month) {
                try {
                    $response = Http::timeout(10)->get("https://api.myquran.com/v2/sholat/jadwal/{$cityId}/{$year}/{$month}");
                    if ($response->successful()) {
                        return $response->json('data.jadwal');
                    }
                } catch (\Exception $e) {
                    // Jika API gagal, kembalikan null agar tidak disimpan dalam cache
                }
                return null;
            });

            // Filter jadwal spesifik untuk hari ini
            $jadwalHariIni = null;
            if (is_array($jadwalBulanIni)) {
                $jadwalHariIni = collect($jadwalBulanIni)->firstWhere('date', $dateStr);
            }

            // Ambil tanggal Hijriah dari API eksternal
            $hijriCacheKey = "hijri_{$dateStr}";
            $hijriData = Cache::remember($hijriCacheKey, now()->addDays(1), function () {
                try {
                    $response = Http::timeout(10)->get("https://api.myquran.com/v2/cal/hijr/?adj=-1");
                    if ($response->successful()) {
                        return $response->json('data.date.1');
                    }
                } catch (\Exception $e) {}
                return null;
            });

            if (is_array($jadwalHariIni) && $hijriData) {
                $jadwalHariIni['tanggal_hijriah'] = $hijriData;
            }

            // Ambil Agenda Masjid
            $todayStart = Carbon::now('Asia/Jakarta')->startOfDay();
            // Ambil hingga 30 hari ke depan
            $thirtyDaysLater = $todayStart->copy()->addDays(30);

            $agendaData = \App\Models\Agenda::where('id_masjid', $profil->id)
                ->where('aktif', true)
                ->whereBetween('date', [$todayStart->toDateString(), $thirtyDaysLater->toDateString()])
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                ->map(function ($agenda) use ($todayStart) {
                    $agendaDate = Carbon::parse($agenda->date, 'Asia/Jakarta')->startOfDay();
                    if ($agendaDate->isSameDay($todayStart)) {
                        $days_label = 'Hari ini';
                    } else {
                        $days = $todayStart->diffInDays($agendaDate);
                        $days_label = $days . ' Hari Lagi';
                    }
                    return [
                        'name' => $agenda->name,
                        'date' => $agenda->date,
                        'days_label' => $days_label,
                        'aktif' => $agenda->aktif,
                    ];
                });

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Data my-masjid berhasil diambil',
                'data' => [
                    'current_time' => $now->toDateTimeString(),
                    'timestamp'    => $now->timestamp,
                    'theme_id'     => $themeId,
                    'profil'       => $profil,
                    'marquee'      => $marquee,
                    'slides'       => $slides,
                    'jadwal_sholat'=> $jadwalHariIni,
                    'agenda'       => $agendaData,
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
