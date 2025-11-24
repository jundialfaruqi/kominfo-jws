<?php

namespace App\Livewire\Welcome;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Welcome extends Component
{
    public ?string $scheduleUrl = null;
    public bool $showScheduleBtn = false;
    public string $cityCode = '0412';
    public int $year;
    public int $month;
    public array $jadwal = [];
    public string $timezone = 'Asia/Jakarta';
    public array $todayTimes = [];
    public ?string $activePrayer = null;
    public ?string $nextPrayer = null;
    public ?string $nextPrayerAtIso = null;
    public ?string $todayIsoDate = null;

    #[Layout('components.layouts.welcome')]
    #[Title('Jadwal Waktu Sholat')]
    public function mount(): void
    {
        $now = now('Asia/Jakarta');
        $this->year = (int) $now->format('Y');
        $this->month = (int) $now->format('m');
        $this->fetchSchedule();
    }

    public function render()
    {
        $user = Auth::user();
        $slug = null;
        $this->computeTodayStatus();
        if ($user && $user->profil && $user->profil->slug) {
            $slug = $user->profil->slug;
            $this->scheduleUrl = '/' . ltrim($slug, '/');
            $this->showScheduleBtn = true;
        } else {
            $this->scheduleUrl = null;
            $this->showScheduleBtn = false;
        }

        view()->share('scheduleUrl', $this->scheduleUrl);
        view()->share('showScheduleBtn', $this->showScheduleBtn);
        view()->share('jadwalSholat', $this->jadwal);
        view()->share('todayTimes', $this->todayTimes);
        view()->share('activePrayer', $this->activePrayer);
        view()->share('nextPrayer', $this->nextPrayer);
        view()->share('nextPrayerAtIso', $this->nextPrayerAtIso);
        $monthName = \Carbon\Carbon::create($this->year, $this->month, 1, 0, 0, 0, $this->timezone)
            ->locale('id')
            ->translatedFormat('F');
        view()->share('monthName', $monthName);
        view()->share('yearNumber', $this->year);
        view()->share('todayIsoDate', $this->todayIsoDate);

        return view('livewire.welcome.welcome');
    }

    protected function fetchSchedule(): void
    {
        $url = sprintf('https://api.myquran.com/v2/sholat/jadwal/%s/%d/%02d', $this->cityCode, $this->year, $this->month);
        try {
            $resp = Http::timeout(10)->get($url);
            if ($resp->successful()) {
                $json = $resp->json();
                $this->jadwal = data_get($json, 'data.jadwal', []) ?: [];
            } else {
                $this->jadwal = [];
            }
        } catch (\Throwable $e) {
            $this->jadwal = [];
        }
    }

    protected function computeTodayStatus(): void
    {
        $now = \Carbon\Carbon::now($this->timezone);
        $todayDate = $now->toDateString();
        $this->todayIsoDate = $todayDate;
        $rows = collect($this->jadwal);
        $todayRow = $rows->firstWhere('date', $todayDate);
        $this->todayTimes = [];
        $this->activePrayer = null;
        $this->nextPrayer = null;
        $this->nextPrayerAtIso = null;

        if (!$todayRow) {
            return;
        }

        // Map waktu ke label
        $labels = [
            'imsak' => 'Imsak',
            'subuh' => 'Subuh',
            'terbit' => 'Terbit',
            'dhuha' => 'Dhuha',
            'dzuhur' => 'Dzuhur',
            'ashar' => 'Ashar',
            'maghrib' => 'Maghrib',
            'isya' => 'Isya',
        ];

        // Simpan jam string
        foreach ($labels as $key => $label) {
            $this->todayTimes[$key] = [
                'label' => $label,
                'time' => (string) ($todayRow[$key] ?? ''),
            ];
        }

        // Tentukan aktif (hanya fardhu) dan next
        $orderKeys = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
        $timeObjs = [];
        foreach ($orderKeys as $k) {
            $t = $todayRow[$k] ?? null;
            if ($t) {
                [$H, $M] = array_map('intval', explode(':', $t));
                $timeObjs[$k] = (clone $now)->setTime($H, $M, 0);
            }
        }

        // Active: waktu terakhir yang <= now
        $lastKey = null;
        foreach ($orderKeys as $k) {
            if (isset($timeObjs[$k]) && $timeObjs[$k]->lte($now)) {
                $lastKey = $k;
            }
        }
        // Next: waktu pertama yang > now
        $nextKey = null;
        foreach ($orderKeys as $k) {
            if (isset($timeObjs[$k]) && $timeObjs[$k]->gt($now)) {
                $nextKey = $k;
                break;
            }
        }

        // Jika sekarang sebelum Subuh, aktif null, next Subuh
        // Jika setelah Isya, next Subuh besok
        $this->activePrayer = $lastKey;
        if ($nextKey) {
            $this->nextPrayer = $nextKey;
            $this->nextPrayerAtIso = $timeObjs[$nextKey]->toIso8601String();
        } else {
            // Cari subuh besok
            $tomorrow = $now->copy()->addDay()->toDateString();
            $tomorrowRow = $rows->firstWhere('date', $tomorrow);
            if ($tomorrowRow && !empty($tomorrowRow['subuh'])) {
                [$h2, $m2] = array_map('intval', explode(':', $tomorrowRow['subuh']));
                $target = $now->copy()->addDay()->setTime($h2, $m2, 0);
                $this->nextPrayer = 'subuh';
                $this->nextPrayerAtIso = $target->toIso8601String();
            }
        }
    }
}
