<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Title;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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

        // Mengambil data jadwal sholat dari Aladhan API untuk Pekanbaru
        $response = Http::get('http://api.aladhan.com/v1/timingsByCity', [
            'city' => 'Pekanbaru',
            'country' => 'Indonesia',
            'method' => 8, // Sihat/Kemenag (Kementerian Agama RI)
            'date' => now()->format('d-m-Y'), // Tanggal hari ini: 7 Juli 2025
        ]);

        if ($response->successful()) {
            $data = $response->json()['data']['timings'];
            $currentTime = Carbon::now('Asia/Jakarta')->format('H:i');
            $this->prayerTimes = [
                [
                    'name' => 'Subuh',
                    'time' => $this->formatTime($data['Fajr']),
                    'icon' => 'bi bi-sunrise',
                    'is_active' => $this->isCurrentPrayer($currentTime, $data['Fajr'], $data['Sunrise']),
                ],
                [
                    'name' => 'Shuruq',
                    'time' => $this->formatTime($data['Sunrise']),
                    'icon' => 'bi bi-sun',
                    'is_active' => $this->isCurrentPrayer($currentTime, $data['Sunrise'], $data['Dhuhr']),
                ],
                [
                    'name' => 'Dzuhur',
                    'time' => $this->formatTime($data['Dhuhr']),
                    'icon' => 'bi bi-sun-fill',
                    'is_active' => $this->isCurrentPrayer($currentTime, $data['Dhuhr'], $data['Asr']),
                ],
                [
                    'name' => 'Ashar',
                    'time' => $this->formatTime($data['Asr']),
                    'icon' => 'bi bi-sunset',
                    'is_active' => $this->isCurrentPrayer($currentTime, $data['Asr'], $data['Maghrib']),
                ],
                [
                    'name' => 'Maghrib',
                    'time' => $this->formatTime($data['Maghrib']),
                    'icon' => 'bi bi-sunset-fill',
                    'is_active' => $this->isCurrentPrayer($currentTime, $data['Maghrib'], $data['Isha']),
                ],
                [
                    'name' => 'Isya',
                    'time' => $this->formatTime($data['Isha']),
                    'icon' => 'bi bi-moon-stars',
                    'is_active' => $this->isCurrentPrayer($currentTime, $data['Isha'], '23:59'),
                ],
            ];

            // Menentukan sholat aktif
            foreach ($this->prayerTimes as $prayer) {
                if ($prayer['is_active']) {
                    $this->currentPrayer = $prayer['name'];
                    break;
                }
            }
        } else {
            // Fallback jika API gagal
            $this->prayerTimes = [
                ['name' => 'Subuh', 'time' => 'N/A', 'icon' => 'bi bi-sunrise', 'is_active' => false],
                ['name' => 'Shuruq', 'time' => 'N/A', 'icon' => 'bi bi-sun', 'is_active' => false],
                ['name' => 'Dzuhur', 'time' => 'N/A', 'icon' => 'bi bi-sun-fill', 'is_active' => false],
                ['name' => 'Ashar', 'time' => 'N/A', 'icon' => 'bi bi-sunset', 'is_active' => false],
                ['name' => 'Maghrib', 'time' => 'N/A', 'icon' => 'bi bi-sunset-fill', 'is_active' => false],
                ['name' => 'Isya', 'time' => 'N/A', 'icon' => 'bi bi-moon-stars', 'is_active' => false],
            ];
        }
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
