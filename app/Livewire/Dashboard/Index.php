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

    public function mount()
    {
        // Inisialisasi kalender
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
        $this->today = Carbon::now()->day;
        $this->updateCalendar();

        // Kunci cache berdasarkan tanggal dan kota
        $cacheKey = 'prayer_times_pekanbaru_' . now()->format('d-m-Y');

        // Coba ambil dari cache terlebih dahulu
        $cachedPrayerTimes = Cache::get($cacheKey);

        if ($cachedPrayerTimes) {
            $this->prayerTimes = $cachedPrayerTimes;
            $this->setCurrentPrayer();
            return;
        }

        // Mengambil data jadwal sholat dari Aladhan API untuk Pekanbaru
        try {
            $response = Http::get('http://api.aladhan.com/v1/timingsByCity', [
                'city' => 'Pekanbaru',
                'country' => 'Indonesia',
                'method' => 8,
                'date' => now()->format('d-m-Y'),
            ]);

            if ($response->successful()) {
                $data = $response->json()['data']['timings'];
                $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');
                $this->prayerTimes = [
                    [
                        'name' => 'Subuh',
                        'time' => $this->formatTime($data['Fajr']),
                        'icon' => 'sunrise',
                        'is_active' => $this->isCurrentPrayer($currentTime, $data['Fajr'], $data['Sunrise']),
                    ],
                    [
                        'name' => 'Dzuhur',
                        'time' => $this->formatTime($data['Dhuhr']),
                        'icon' => 'sun',
                        'is_active' => $this->isCurrentPrayer($currentTime, $data['Dhuhr'], $data['Asr']),
                    ],
                    [
                        'name' => 'Maghrib',
                        'time' => $this->formatTime($data['Maghrib']),
                        'icon' => 'hazemoon',
                        'is_active' => $this->isCurrentPrayer($currentTime, $data['Maghrib'], $data['Isha']),
                    ],
                    [
                        'name' => 'Shuruq',
                        'time' => $this->formatTime($data['Sunrise']),
                        'icon' => 'sunset',
                        'is_active' => $this->isCurrentPrayer($currentTime, $data['Sunrise'], $data['Dhuhr']),
                    ],
                    [
                        'name' => 'Ashar',
                        'time' => $this->formatTime($data['Asr']),
                        'icon' => 'sunwind',
                        'is_active' => $this->isCurrentPrayer($currentTime, $data['Asr'], $data['Maghrib']),
                    ],
                    [
                        'name' => 'Isya',
                        'time' => $this->formatTime($data['Isha']),
                        'icon' => 'moon',
                        'is_active' => $this->isCurrentPrayer($currentTime, $data['Isha'], '23:59'),
                    ],
                ];

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
