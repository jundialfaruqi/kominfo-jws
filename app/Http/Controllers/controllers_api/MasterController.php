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

    // GET JUMBOTRON MASJID BY SLUG
    public function get_jumbotron_masjid($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $jm = JumbotronMasjid::where('masjid_id', $profil->id)->where('aktif', true)->firstOrFail();
            $data = [
                'jumbo1' => $jm->jumbotron_masjid_1 ? asset($jm->jumbotron_masjid_1) : null,
                'jumbo2' => $jm->jumbotron_masjid_2 ? asset($jm->jumbotron_masjid_2) : null,
                'jumbo3' => $jm->jumbotron_masjid_3 ? asset($jm->jumbotron_masjid_3) : null,
                'jumbo4' => $jm->jumbotron_masjid_4 ? asset($jm->jumbotron_masjid_4) : null,
                'jumbo5' => $jm->jumbotron_masjid_5 ? asset($jm->jumbotron_masjid_5) : null,
                'jumbo6' => $jm->jumbotron_masjid_6 ? asset($jm->jumbotron_masjid_6) : null,
                'is_active' => (bool) $jm->aktif,
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
            $globalItems = [];
            $globalActive = false;
            try {
                $jumbotron = Jumbotron::where('is_active', true)->firstOrFail();
                if ($jumbotron->jumbo1) $globalItems[] = asset($jumbotron->jumbo1);
                if ($jumbotron->jumbo2) $globalItems[] = asset($jumbotron->jumbo2);
                if ($jumbotron->jumbo3) $globalItems[] = asset($jumbotron->jumbo3);
                if ($jumbotron->jumbo4) $globalItems[] = asset($jumbotron->jumbo4);
                if ($jumbotron->jumbo5) $globalItems[] = asset($jumbotron->jumbo5);
                if ($jumbotron->jumbo6) $globalItems[] = asset($jumbotron->jumbo6);
                $globalActive = true;
            } catch (ModelNotFoundException $e) {
            }

            $masjidItems = [];
            $masjidActive = false;
            try {
                $profil = Profil::where('slug', $slug)->firstOrFail();
                $jm = JumbotronMasjid::where('masjid_id', $profil->id)->where('aktif', true)->firstOrFail();
                if ($jm->jumbotron_masjid_1) $masjidItems[] = asset($jm->jumbotron_masjid_1);
                if ($jm->jumbotron_masjid_2) $masjidItems[] = asset($jm->jumbotron_masjid_2);
                if ($jm->jumbotron_masjid_3) $masjidItems[] = asset($jm->jumbotron_masjid_3);
                if ($jm->jumbotron_masjid_4) $masjidItems[] = asset($jm->jumbotron_masjid_4);
                if ($jm->jumbotron_masjid_5) $masjidItems[] = asset($jm->jumbotron_masjid_5);
                if ($jm->jumbotron_masjid_6) $masjidItems[] = asset($jm->jumbotron_masjid_6);
                $masjidActive = (bool) $jm->aktif;
            } catch (ModelNotFoundException $e) {
            }

            $mergedItems = [];
            $maxLen = max(count($masjidItems), count($globalItems));
            for ($i = 0; $i < $maxLen; $i++) {
                if (isset($masjidItems[$i])) $mergedItems[] = $masjidItems[$i];
                if (isset($globalItems[$i])) $mergedItems[] = $globalItems[$i];
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data jumbotron masjid dan global !',
                'data' => [
                    'is_active' => ($globalActive || $masjidActive),
                    'items' => $mergedItems,
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
            // Gunakan waktu server langsung dengan Carbon
            $serverDateTime = Carbon::now('Asia/Jakarta');

            return response()->json([
                'success' => true,
                'message' => 'Server Asia/Jakarta',
                'data' => [
                    'timestamp' => $serverDateTime->timestamp * 1000, // dalam milidetik
                    'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                    'source' => 'server'
                ]
            ]);
        } catch (\Exception $e) {
            try {
                // Coba API utama (Pekanbaru)
                $response = Http::timeout(5)->get('https://superapp.pekanbaru.go.id/api/server-time');

                if ($response->successful()) {
                    $serverTime = $response['serverTime'];
                    $serverDateTime = new \DateTime($serverTime, new \DateTimeZone('UTC'));
                    $serverDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
                    // $serverDateTime->modify('+23 hour 28 minutes'); // Tambah 1 jam 20 menit
                    // $serverDateTime->modify('+2 hour 6 minutes'); // Tambah 1 jam 20 menit
                    // $serverDateTime->modify('-8 hour 43 minutes'); // Tambah 1 jam 20 menit

                    // untuk testing hari jumat
                    // $currentDay = (int)$serverDateTime->format('w');
                    // $daysToFriday = 5 - $currentDay;
                    // if ($daysToFriday < 0) {
                    //     $daysToFriday += 7;
                    // }
                    // $serverDateTime->modify("+{$daysToFriday} days");
                    // $serverDateTime->setTime(12, 17, 40);

                    return response()->json([
                        'success' => true,
                        'message' => 'Server Super App',
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
                            'message' => 'Server Timeapi',
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
                                'message' => 'Server Google',
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
                        try {
                            // Fallback ke waktu server lokal
                            $serverDateTime = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
                            return response()->json([
                                'success' => true,
                                'message' => 'Server Local',
                                'data' => [
                                    'timestamp' => $serverDateTime->getTimestamp() * 1000, // dalam milidetik
                                    'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                                    'source' => 'local'
                                ]
                            ]);
                        } catch (\Exception $e) {
                            return response()->json(['success' => false, 'message' => 'Gagal mengambil semua waktu !'], 500);
                        }
                    }
                }
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
