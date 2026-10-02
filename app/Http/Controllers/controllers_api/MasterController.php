<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Jumbotron;
use App\Models\Profil;
use App\Models\JumbotronMasjid;
use App\Models\Agenda;

class MasterController extends Controller
{

    public function __construct() {}

    // GET JUMBOTRON (API LAMA)
    public function get_jumbotron1()
    {
        try {
            $jumbotron = Jumbotron::where('is_active', true)->firstOrFail();
            $data = [];
            if ($jumbotron->jumbo1) $data[] = asset($jumbotron->jumbo1);
            if ($jumbotron->jumbo2) $data[] = asset($jumbotron->jumbo2);
            if ($jumbotron->jumbo3) $data[] = asset($jumbotron->jumbo3);
            if ($jumbotron->jumbo4) $data[] = asset($jumbotron->jumbo4);
            if ($jumbotron->jumbo5) $data[] = asset($jumbotron->jumbo5);
            if ($jumbotron->jumbo6) $data[] = asset($jumbotron->jumbo6);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data jumbotron !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Jumbotron tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET JUMBOTRON
    public function get_jumbotron()
    {
        try {
            $data = [];
            // Video global aktif
            $videos = Jumbotron::where('is_active', true)
                ->where('media_type', 'video')
                ->whereNotNull('video_file')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($videos as $v) {
                $data[] = [
                    'type' => 'video',
                    'url' => asset($v->video_file),
                    'has_audio' => (bool) $v->has_audio,
                    'duration' => (int) $v->video_duration,
                ];
            }

            // Gambar global aktif
            $images = Jumbotron::where('is_active', true)
                ->where(function ($q) {
                    $q->where('media_type', 'image')->orWhereNull('media_type');
                })
                ->orderBy('id', 'asc')
                ->get();

            foreach ($images as $img) {
                foreach (['jumbo1', 'jumbo2', 'jumbo3', 'jumbo4', 'jumbo5', 'jumbo6'] as $field) {
                    if ($img->$field) {
                        $data[] = ['type' => 'image', 'url' => asset($img->$field)];
                    }
                }
            }

            if (empty($data)) {
                return response()->json(['success' => false, 'message' => 'Jumbotron tidak ditemukan !'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data jumbotron !',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET JUMBOTRON MASJID BY SLUG
    public function get_jumbotron_masjid($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $jms = JumbotronMasjid::where('masjid_id', $profil->id)->where('aktif', true)->orderBy('id', 'asc')->get();
            $slides = [];
            foreach ($jms as $jm) {
                foreach (['jumbotron_masjid_1', 'jumbotron_masjid_2', 'jumbotron_masjid_3', 'jumbotron_masjid_4', 'jumbotron_masjid_5', 'jumbotron_masjid_6'] as $field) {
                    if ($jm->$field) {
                        $slides[] = asset($jm->$field);
                    }
                }
            }

            if (empty($slides)) {
                return response()->json(['success' => false, 'message' => 'Jumbotron masjid tidak ditemukan!'], 404);
            }

            $data = [
                'jumbo1' => $slides[0] ?? null,
                'jumbo2' => $slides[1] ?? null,
                'jumbo3' => $slides[2] ?? null,
                'jumbo4' => $slides[3] ?? null,
                'jumbo5' => $slides[4] ?? null,
                'jumbo6' => $slides[5] ?? null,
                'all_slides' => $slides,
                'is_active' => true,
            ];
            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data jumbotron masjid!',
                'data' => $data,
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Jumbotron masjid tidak ditemukan!'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    public function get_jumbotron_all($slug)
    {
        try {
            // 1. Ambil semua Jumbotron Video Global yang aktif
            $activeVideos = Jumbotron::where('is_active', true)
                ->where('media_type', 'video')
                ->whereNotNull('video_file')
                ->orderBy('id', 'asc')
                ->get();

            $videoItems = [];
            foreach ($activeVideos as $v) {
                $videoItems[] = [
                    'type' => 'video',
                    'category' => 'pemko_video',
                    'url' => asset($v->video_file),
                    'has_audio' => (bool) $v->has_audio,
                    'duration' => (int) $v->video_duration,
                ];
            }

            // 2. Ambil semua Jumbotron Gambar Global yang aktif
            $activeGlobalImages = Jumbotron::where('is_active', true)
                ->where(function ($q) {
                    $q->where('media_type', 'image')->orWhereNull('media_type');
                })
                ->orderBy('id', 'asc')
                ->get();

            $globalImageItems = [];
            foreach ($activeGlobalImages as $img) {
                foreach (['jumbo1', 'jumbo2', 'jumbo3', 'jumbo4', 'jumbo5', 'jumbo6'] as $field) {
                    if ($img->$field) {
                        $globalImageItems[] = [
                            'type' => 'image',
                            'category' => 'pemko_image',
                            'url' => asset($img->$field),
                        ];
                    }
                }
            }

            // 3. Ambil semua Jumbotron Masjid yang aktif
            $masjidImageItems = [];
            try {
                $profil = Profil::where('slug', $slug)->firstOrFail();
                $activeMasjidJumbos = JumbotronMasjid::where('masjid_id', $profil->id)
                    ->where('aktif', true)
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($activeMasjidJumbos as $jm) {
                    foreach (['jumbotron_masjid_1', 'jumbotron_masjid_2', 'jumbotron_masjid_3', 'jumbotron_masjid_4', 'jumbotron_masjid_5', 'jumbotron_masjid_6'] as $field) {
                        if ($jm->$field) {
                            $masjidImageItems[] = [
                                'type' => 'image',
                                'category' => 'masjid_image',
                                'url' => asset($jm->$field),
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {}

            // Siklus penayangan:
            // slide -> jumbotron video -> jumbotron gambar global -> jumbotron masjid -> slide
            $mergedItems = array_merge($videoItems, $globalImageItems, $masjidImageItems);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data jumbotron masjid dan global !',
                'data' => [
                    'is_active' => count($mergedItems) > 0,
                    'items' => $mergedItems,
                    'videos' => $videoItems,
                    'global_images' => $globalImageItems,
                    'masjid_images' => $masjidImageItems,
                ],
            ]);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET AGENDA MASJID BY SLUG (CURRENT MONTH)
    public function get_agenda($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();

            $now = Carbon::now('Asia/Jakarta');
            $start = $now->copy()->startOfDay()->toDateString();
            $end = $now->copy()->addDays(30)->toDateString();

            $agendas = Agenda::where('id_masjid', $profil->id)
                ->where('aktif', true)
                ->whereBetween('date', [$start, $end])
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->select('id', 'date', 'name', 'aktif')
                ->get();

            if ($agendas->isEmpty()) {
                $items = collect([]);
            } else {
                $items = $agendas->map(function ($a) use ($now) {
                    $agendaDate = Carbon::parse($a->date, 'Asia/Jakarta')->startOfDay();
                    $today = $now->copy()->startOfDay();
                    $message = null;

                    if ($agendaDate->equalTo($today)) {
                        $message = 'Hari ini';
                    } elseif ($agendaDate->isTomorrow()) {
                        $message = 'Besok';
                    } elseif ($agendaDate->gt($today)) {
                        $days = $today->diffInDays($agendaDate);
                        $message = $days . ' Hari Lagi';
                    }

                    return [
                        'name' => $a->name,
                        'message' => $message,
                    ];
                });
            }

            $responseMessage = ($items->isEmpty()) ? 'Tidak ada agenda aktif' : 'Berhasil get agenda terdekat';

            return response()->json([
                'success' => true,
                'message' => $responseMessage,
                'data' => $items,
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil masjid tidak ditemukan!'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET SERVER TIME
    public function get_server_time()
    {
        try {
            // Ambil waktu dari time.now API
            $timeResponse = Http::timeout(5)->get('https://time.now/developer/api/timezone/Asia/Jakarta');
            if ($timeResponse->successful()) {
                $timeData = $timeResponse->json();
                $serverDateTime = Carbon::createFromTimestamp($timeData['unixtime'], 'Asia/Jakarta');

                return response()->json([
                    'success' => true,
                    'message' => 'Server time.now',
                    'data' => [
                        'timestamp'  => $serverDateTime->timestamp * 1000,
                        'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                        'source'     => 'time.now'
                    ]
                ]);
            } else {
                throw new \Exception('API time.now gagal');
            }
        } catch (\Exception $e) {
            try {
                // Fallback 1: timeapi.io
                $fallbackResponse = Http::timeout(5)->get('https://timeapi.io/api/time/current/zone?timeZone=Asia%2FJakarta');
                if ($fallbackResponse->successful()) {
                    $serverDateTime = new \DateTime($fallbackResponse['dateTime'], new \DateTimeZone('Asia/Jakarta'));

                    return response()->json([
                        'success' => true,
                        'message' => 'Server Timeapi',
                        'data' => [
                            'timestamp'  => $serverDateTime->getTimestamp() * 1000,
                            'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                            'source'     => 'timeapi'
                        ]
                    ]);
                } else {
                    throw new \Exception('API timeapi.io gagal');
                }
            } catch (\Exception $e) {
                // Fallback 2: waktu lokal server (Carbon)
                $serverDateTime = Carbon::now('Asia/Jakarta');

                return response()->json([
                    'success' => true,
                    'message' => 'Server Local',
                    'data' => [
                        'timestamp'  => $serverDateTime->timestamp * 1000,
                        'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                        'source'     => 'local'
                    ]
                ]);
            }
        }
    }



    // GET REFRESH PRAYER TIME
    public function get_refresh_prayer_times()
    {
        try {
            // Gunakan waktu server langsung dengan Carbon sebagai sumber utama
            $serverDateTime = Carbon::now('Asia/Jakarta');
            $serverTime = $serverDateTime->toDateTimeString();
            $currentMonth = (int) $serverDateTime->format('n');
            $currentYear = (int) $serverDateTime->format('Y');
        } catch (\Exception $e) {
            // Jika gagal menggunakan Carbon, coba API eksternal sebagai fallback
            try {
                $response = Http::timeout(5)->get('https://superapp.pekanbaru.go.id/api/server-time');
                if ($response->successful()) {
                    $serverTime = $response['serverTime'];
                    $serverDateTime = new \DateTime($serverTime, new \DateTimeZone('UTC'));
                    $serverDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
                    $serverTime = $serverDateTime->format('Y-m-d H:i:s');
                    $currentMonth = (int) $serverDateTime->format('n');
                    $currentYear = (int) $serverDateTime->format('Y');
                } else {
                    return response()->json(['success' => false, 'message' => 'API utama gagal']);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Failed to fetch server time: ' . $e->getMessage()]);
            }
        }

        // Fetch prayer times for current month
        try {
            $monthFormatted = str_pad($currentMonth, 2, '0', STR_PAD_LEFT);
            $baseUrl = 'https://api.myquran.com/v2/sholat/jadwal/0412';
            $url = $baseUrl . '/' . $currentYear . '/' . $monthFormatted;

            $jadwalResponse = Http::timeout(10)->get($url);
            if ($jadwalResponse->successful()) {
                $responseData = $jadwalResponse->json();
                $jadwalSholat = $responseData['data']['jadwal'] ?? [];

                return response()->json([
                    'success' => true,
                    'data' => [
                        'jadwal' => $jadwalSholat,
                        'server_time' => $serverTime,
                        'current_month' => $currentMonth,
                        'current_year' => $currentYear
                    ]
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to fetch prayer times data']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
