<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Title;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class Index extends Component
{
    #[Title('Dashboard')]

    public $currentMonth;
    public $currentYear;
    public $daysInMonth;
    public $firstDayOfMonth;
    public $weeks;
    public $today;
    public $prayerTimes = [];
    public $currentPrayer = null;
    public $serverTime;
    public $apiSource;

    public function mount()
    {
        // Ambil waktu dari API server
        $this->getServerTime();
        
        // Inisialisasi kalender menggunakan waktu server
        $serverDate = Carbon::parse($this->serverTime, 'Asia/Jakarta');
        $this->currentMonth = $serverDate->month;
        $this->currentYear = $serverDate->year;
        $this->today = $serverDate->day;
        $this->updateCalendar();

        // Kunci cache berdasarkan tanggal dan kota
        $cacheKey = 'prayer_times_pekanbaru_' . Carbon::parse($this->serverTime, 'Asia/Jakarta')->format('d-m-Y');

        // Coba ambil dari cache terlebih dahulu
        $cachedPrayerTimes = Cache::get($cacheKey);

        if ($cachedPrayerTimes) {
            $this->prayerTimes = $cachedPrayerTimes;
            $this->setCurrentPrayer();
            return;
        }

        // Mengambil data jadwal sholat dari MyQuran API untuk Pekanbaru
        try {
            $currentDate = Carbon::parse($this->serverTime, 'Asia/Jakarta')->format('Y-m-d');
            $response = Http::get("https://api.myquran.com/v2/sholat/jadwal/0412/{$currentDate}");

            if ($response->successful()) {
                $responseData = $response->json();
                $todaySchedule = $responseData['data']['jadwal'];
                
                // Pastikan data jadwal tersedia
                if (!$todaySchedule || !isset($todaySchedule['date'])) {
                    $todaySchedule = null;
                }
                
                if ($todaySchedule) {
                    $currentTime = Carbon::parse($this->serverTime, 'Asia/Jakarta')->format('H:i');
                    $this->prayerTimes = [
                        [
                            'name' => 'Subuh',
                            'time' => $todaySchedule['subuh'],
                            'icon' => 'sunrise',
                            'is_active' => $this->isCurrentPrayer($currentTime, $todaySchedule['subuh'], $todaySchedule['terbit']),
                        ],
                        [
                            'name' => 'Dzuhur',
                            'time' => $todaySchedule['dzuhur'],
                            'icon' => 'sun',
                            'is_active' => $this->isCurrentPrayer($currentTime, $todaySchedule['dzuhur'], $todaySchedule['ashar']),
                        ],
                        [
                            'name' => 'Maghrib',
                            'time' => $todaySchedule['maghrib'],
                            'icon' => 'hazemoon',
                            'is_active' => $this->isCurrentPrayer($currentTime, $todaySchedule['maghrib'], $todaySchedule['isya']),
                        ],
                        [
                            'name' => 'Shuruq',
                            'time' => $todaySchedule['terbit'],
                            'icon' => 'sunset',
                            'is_active' => $this->isCurrentPrayer($currentTime, $todaySchedule['terbit'], $todaySchedule['dzuhur']),
                        ],
                        [
                            'name' => 'Ashar',
                            'time' => $todaySchedule['ashar'],
                            'icon' => 'sunwind',
                            'is_active' => $this->isCurrentPrayer($currentTime, $todaySchedule['ashar'], $todaySchedule['maghrib']),
                        ],
                        [
                            'name' => 'Isya',
                            'time' => $todaySchedule['isya'],
                            'icon' => 'moon',
                            'is_active' => $this->isCurrentPrayer($currentTime, $todaySchedule['isya'], '23:59'),
                        ],
                    ];
                } else {
                    $this->setFallbackPrayerTimes();
                }

                // Simpan ke cache selama 24 jam
                Cache::put($cacheKey, $this->prayerTimes, now()->addDay());

                // Menentukan sholat aktif
                $this->setCurrentPrayer();
            } else {
                $this->setFallbackPrayerTimes();
            }
        } catch (\Exception $e) {
            $this->setFallbackPrayerTimes();
        }
    }

    private function getServerTime()
    {
        try {
            // Gunakan waktu server langsung dengan Carbon
            $this->serverTime = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $this->apiSource = 'server';
            return;
        } catch (\Exception $e) {
            // Jika gagal menggunakan Carbon langsung, coba API eksternal
        }
        
        try {
            // Coba API utama (Pekanbaru)
            $response = Http::timeout(5)->get('https://superapp.pekanbaru.go.id/api/server-time');

            if ($response->successful()) {
                $this->serverTime = $response['serverTime'];
                $this->serverTime = Carbon::parse($this->serverTime, 'UTC')
                    ->setTimezone('Asia/Jakarta')
                    ->toDateTimeString();
                $this->apiSource = 'pekanbaru';
                return;
            } else {
                throw new \Exception('API utama gagal');
            }
        } catch (\Exception $e) {
            // Jika API utama gagal, coba API fallback
        }
        
        try {
            // Fallback ke timeapi.io
            $fallbackResponse = Http::timeout(5)->get('https://timeapi.io/api/time/current/zone?timeZone=Asia%2FJakarta');

            if ($fallbackResponse->successful()) {
                $this->serverTime = $fallbackResponse['dateTime'];
                $this->apiSource = 'timeapi';
                return;
            } else {
                throw new \Exception('API timeapi.io gagal');
            }
        } catch (\Exception $e) {
            // Jika timeapi.io gagal, coba Google Script
        }
        
        try {
            // Fallback ke API Google Script
            $newApiResponse = Http::timeout(5)->get('https://script.google.com/macros/s/AKfycbyd5AcbAnWi2Yn0xhFRbyzS4qMq1VucMVgVvhul5XqS9HkAyJY/exec?tz=Asia/Jakarta');

            if ($newApiResponse->successful() && $newApiResponse['status'] === 'ok') {
                $this->serverTime = $newApiResponse['fulldate'];
                $this->apiSource = 'google-script';
                return;
            } else {
                throw new \Exception('API Google Script gagal');
            }
        } catch (\Exception $e) {
            // Jika semua API gagal, gunakan waktu server lokal sebagai fallback terakhir
        }
        
        // Fallback terakhir ke waktu server lokal
        $this->serverTime = Carbon::now('Asia/Jakarta')->toDateTimeString();
        $this->apiSource = 'local';
    }

    private function setCurrentPrayer()
    {
        foreach ($this->prayerTimes as $prayer) {
            if ($prayer['is_active']) {
                $this->currentPrayer = $prayer['name'];
                break;
            }
        }
    }

    private function setFallbackPrayerTimes()
    {
        $this->prayerTimes = [
            ['name' => 'Subuh', 'time' => 'N/A', 'icon' => 'sunrise', 'is_active' => false],
            ['name' => 'Dzuhur', 'time' => 'N/A', 'icon' => 'sun', 'is_active' => false],
            ['name' => 'Maghrib', 'time' => 'N/A', 'icon' => 'hazemoon', 'is_active' => false],
            ['name' => 'Shuruq', 'time' => 'N/A', 'icon' => 'sunset', 'is_active' => false],
            ['name' => 'Ashar', 'time' => 'N/A', 'icon' => 'sunwind', 'is_active' => false],
            ['name' => 'Isya', 'time' => 'N/A', 'icon' => 'moon', 'is_active' => false],
        ];
    }

    public function updateCalendar()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $this->daysInMonth = $date->daysInMonth;
        $this->firstDayOfMonth = $date->dayOfWeek;
        $this->weeks = [];

        $days = [];
        for ($i = 0; $i < $this->firstDayOfMonth; $i++) {
            $days[] = null;
        }
        for ($day = 1; $day <= $this->daysInMonth; $day++) {
            $days[] = $day;
        }
        $this->weeks = array_chunk($days, 7);
    }

    private function formatTime($time)
    {
        return date('H:i', strtotime($time));
    }

    private function isCurrentPrayer($currentTime, $startTime, $endTime)
    {
        $current = Carbon::createFromFormat('H:i', $currentTime, 'Asia/Jakarta');
        $start = Carbon::createFromFormat('H:i', $startTime, 'Asia/Jakarta');
        $end = Carbon::createFromFormat('H:i', $endTime, 'Asia/Jakarta');
        return $current->between($start, $end);
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
