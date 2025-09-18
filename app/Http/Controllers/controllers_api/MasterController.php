<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\User;
use App\Models\Theme;
use App\Models\Durasi;
use App\Models\Jumbotron;
use App\Models\Marquee;
use App\Models\Petugas;
use App\Models\Profil;
use App\Models\Slides;
use App\Models\Adzan;
use App\Models\AdzanAudio;
use App\Models\Audios;

class MasterController extends Controller {
    
    public function __construct() {
    }

    // GET JUMBOTRON
    public function get_jumbotron() {
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
        }
        catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Jumbotron tidak ditemukan !'], 404);
        } 
        catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET SERVER TIME
    public function get_server_time() {
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
        } 
        catch (\Exception $e) {
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
                } 
                else {
                    throw new \Exception('API utama gagal');
                }
            } 
            catch (\Exception $e) {
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
                    } 
                    else {
                        throw new \Exception('API timeapi.io gagal');
                    }
                } 
                catch (\Exception $e) {
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
                        } 
                        else {
                            throw new \Exception('API Google Script gagal');
                        }
                    } 
                    catch (\Exception $e) {
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
                        }
                        catch (\Exception $e) {
                            return response()->json(['success' => false, 'message' => 'Gagal mengambil semua waktu !'], 500);
                        }
                    }
                }
            }
        }
    }

    // GET REFRESH PRAYER TIME
    public function get_refresh_prayer_times() {
        try {
            // Gunakan waktu server langsung dengan Carbon sebagai sumber utama
            $serverDateTime = Carbon::now('Asia/Jakarta');
            $serverTime = $serverDateTime->toDateTimeString();
            $currentMonth = (int) $serverDateTime->format('n');
            $currentYear = (int) $serverDateTime->format('Y');
        } 
        catch (\Exception $e) {
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
