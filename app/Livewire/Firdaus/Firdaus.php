<?php

namespace App\Livewire\Firdaus;

use App\Models\Profil;
use App\Models\Adzan;
use App\Models\Marquee;
use App\Models\Petugas;
use App\Models\Slides;
use App\Models\Durasi;
use App\Models\Jumbotron; // Tambahkan model Jumbotron
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Theme;
use App\Models\User;

class Firdaus extends Component
{
    #[Layout('components.layouts.firdaus')]
    #[Title('Jadwal Sholat Pekanbaru')]
    public $serverTime;
    public $serverTimestamp;
    public $apiSource;
    public $jadwalSholat = [];
    public $prayerTimes = [];
    public $activeIndex = 0;
    public $nextPrayerIndex = 0;
    public $currentMonth;
    public $currentYear;
    public $currentDayOfWeek;
    public $baseUrl = 'https://raw.githubusercontent.com/lakuapik/jadwalsholatorg/master/adzan/pekanbaru/';
    public $activePrayerStatus = null;
    public $profil;
    public $adzan;
    public $marquee;
    public $petugas;
    public $slides;
    public $durasi;
    public $jumbotron; // Tambahkan properti untuk jumbotron
    public $slug;
    public $themeCss;
    public $theme;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->profil = Profil::where('slug', $slug)->firstOrFail();
        $user_id = $this->profil->user_id;

        // Ambil tema dan simpan ke properti $theme
        $user = User::find($user_id);
        $this->theme = $user && $user->theme_id ? Theme::find($user->theme_id) : null;
        $this->themeCss = $this->theme && $this->theme->css_file ? asset($this->theme->css_file) : asset('css/style.css');

        // Ambil data durasi
        $this->durasi = Durasi::where('user_id', $user_id)->first();

        // Ambil data jumbotron yang aktif
        $this->jumbotron = Jumbotron::where('is_active', true)->first();

        $this->adzan    = Adzan::where('user_id', $user_id)->first();
        $this->marquee  = Marquee::where('user_id', $user_id)->first();
        $this->petugas  = Petugas::where('user_id', $user_id)->first();
        $this->slides   = Slides::where('user_id', $user_id)->first();

        try {
            $response = Http::timeout(5)->get('https://superapp.pekanbaru.go.id/api/server-time');
            if ($response->successful()) {
                $this->serverTime = $response['serverTime'];
                $serverDateTime = new \DateTime($this->serverTime, new \DateTimeZone('UTC'));
                $serverDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
                $this->serverTime = $serverDateTime->format('Y-m-d H:i:s');
                $this->serverTimestamp = $serverDateTime->getTimestamp() * 1000;
                $this->apiSource = 'pekanbaru';
            } else {
                throw new \Exception('API utama gagal');
            }
        } catch (\Exception $e) {
            try {
                $fallbackResponse = Http::timeout(5)->get('https://timeapi.io/api/time/current/zone?timeZone=Asia%2FJakarta');
                if ($fallbackResponse->successful()) {
                    $this->serverTime = $fallbackResponse['dateTime'];
                    $serverDateTime = new \DateTime($this->serverTime, new \DateTimeZone('Asia/Jakarta'));
                    $this->serverTime = $serverDateTime->format('Y-m-d H:i:s');
                    $this->serverTimestamp = $serverDateTime->getTimestamp() * 1000;
                    $this->apiSource = 'timeapi';
                } else {
                    throw new \Exception('API timeapi.io gagal');
                }
            } catch (\Exception $e) {
                try {
                    $newApiResponse = Http::timeout(5)->get('https://script.google.com/macros/s/AKfycbyd5AcbAnWi2Yn0xhFRbyzS4qMq1VucMVgVvhul5XqS9HkAyJY/exec?tz=Asia/Jakarta');
                    if ($newApiResponse->successful() && $newApiResponse['status'] === 'ok') {
                        $this->serverTime = $newApiResponse['fulldate'];
                        $serverDateTime = new \DateTime($this->serverTime, new \DateTimeZone('Asia/Jakarta'));
                        $this->serverTime = $serverDateTime->format('Y-m-d H:i:s');
                        $this->serverTimestamp = $serverDateTime->getTimestamp() * 1000;
                        $this->apiSource = 'google-script';
                    } else {
                        throw new \Exception('API Google Script gagal');
                    }
                } catch (\Exception $e) {
                    $serverDateTime = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
                    $this->serverTime = $serverDateTime->format('Y-m-d H:i:s');
                    $this->serverTimestamp = $serverDateTime->getTimestamp() * 1000;
                    $this->apiSource = 'local';
                }
            }
        }

        try {
            $tanggalHariIni         = date('Y-m-d', strtotime($this->serverTime));
            $this->currentMonth     = date('m', strtotime($this->serverTime));
            $this->currentYear      = date('Y', strtotime($this->serverTime));
            $this->currentDayOfWeek = date('N', strtotime($this->serverTime));

            $jadwalUrl = $this->baseUrl . $this->currentYear . '/' . $this->currentMonth . '.json';
            $jadwalResponse = Http::get($jadwalUrl);

            if (!$jadwalResponse->successful()) {
                $bulanSebelumnya = $this->getPreviousMonth($this->currentMonth, $this->currentYear);
                $tahunSebelumnya = $bulanSebelumnya['year'];
                $bulanSebelumnya = $bulanSebelumnya['month'];
                $fallbackUrl = $this->baseUrl . $tahunSebelumnya . '/' . $bulanSebelumnya . '.json';
                $jadwalResponse = Http::get($fallbackUrl);
                logger("Menggunakan data jadwal fallback: " . $fallbackUrl);
            }

            if ($jadwalResponse->successful()) {
                $jadwalSholat = $jadwalResponse->json();
                $this->jadwalSholat = $jadwalSholat;

                $jadwalHariIni = null;
                foreach ($jadwalSholat as $item) {
                    if ($item['tanggal'] === $tanggalHariIni) {
                        $jadwalHariIni = $item;
                        break;
                    }
                }

                if ($jadwalHariIni) {
                    $dzuhurLabel = $this->currentDayOfWeek == 5 ? "Jum'at" : "Dzuhur";
                    $this->prayerTimes = [
                        ['name' => 'Shubuh', 'time' => $jadwalHariIni['shubuh'], 'icon' => 'sunset'],
                        ['name' => 'Shuruq', 'time' => $jadwalHariIni['terbit'], 'icon' => 'sunrise'],
                        ['name' => $dzuhurLabel, 'time' => $jadwalHariIni['dzuhur'], 'icon' => 'sun'],
                        ['name' => 'Ashar', 'time' => $jadwalHariIni['ashr'], 'icon' => 'sunwind'],
                        ['name' => 'Maghrib', 'time' => $jadwalHariIni['magrib'], 'icon' => 'hazemoon'],
                        ['name' => 'Isya', 'time' => $jadwalHariIni['isya'], 'icon' => 'moon'],
                    ];

                    $currentTime = date('H:i', strtotime($this->serverTime));
                    $prayerIndices = $this->determineActivePrayerTime($currentTime);
                    $this->activeIndex = $prayerIndices['active'];
                    $this->nextPrayerIndex = $prayerIndices['next'];
                    $this->activePrayerStatus = $this->calculateActivePrayerTimeStatus($currentTime);
                } else {
                    logger("Data jadwal sholat tidak ditemukan untuk tanggal: " . $tanggalHariIni);
                }
            } else {
                logger("Gagal mengambil data jadwal sholat dari API");
            }
        } catch (\Exception $e) {
            logger("Error saat memproses jadwal sholat: " . $e->getMessage());
        }
    }

    private function determineActivePrayerTime($currentTime)
    {
        $currentTimeStamp = strtotime("1970-01-01 " . $currentTime . ":00");
        $nextIndex = -1;
        $nextPrayerTimeStamp = PHP_INT_MAX;

        for ($i = 0; $i < count($this->prayerTimes); $i++) {
            $prayerTimeStamp = strtotime("1970-01-01 " . $this->prayerTimes[$i]['time'] . ":00");
            if ($prayerTimeStamp > $currentTimeStamp && $prayerTimeStamp < $nextPrayerTimeStamp) {
                $nextIndex = $i;
                $nextPrayerTimeStamp = $prayerTimeStamp;
            }
        }

        if ($nextIndex == -1) {
            $nextIndex = 0;
        }

        $activeIndex = -1;
        $lastPrayerTimeStamp = 0;

        for ($i = 0; $i < count($this->prayerTimes); $i++) {
            $prayerTimeStamp = strtotime("1970-01-01 " . $this->prayerTimes[$i]['time'] . ":00");
            if ($prayerTimeStamp <= $currentTimeStamp && $prayerTimeStamp > $lastPrayerTimeStamp) {
                $activeIndex = $i;
                $lastPrayerTimeStamp = $prayerTimeStamp;
            }
        }

        if ($activeIndex == -1) {
            $activeIndex = 5;
        }

        return [
            'active' => $activeIndex,
            'next' => $nextIndex
        ];
    }

    private function getPreviousMonth($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;
        if ($month == 1) {
            return ['month' => '12', 'year' => (string)($year - 1)];
        } else {
            return ['month' => str_pad($month - 1, 2, '0', STR_PAD_LEFT), 'year' => (string)$year];
        }
    }

    private function getNextMonth($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;
        if ($month == 12) {
            return ['month' => '01', 'year' => (string)($year + 1)];
        } else {
            return ['month' => str_pad($month + 1, 2, '0', STR_PAD_LEFT), 'year' => (string)$year];
        }
    }

    private function calculateActivePrayerTimeStatus($currentTime)
    {
        if (empty($this->prayerTimes)) {
            return null;
        }

        $activePrayerData = $this->findActivePrayerTime($currentTime);
        if (!$activePrayerData) {
            return null;
        }

        $activePrayer = $activePrayerData['prayer'];
        $activeIndex = $activePrayerData['index'];
        $prayerName = $activePrayer['name'];
        $prayerTime = $activePrayer['time'];

        if (strtolower($prayerName) === 'shuruq') {
            return null;
        }

        $serverDate = new \DateTime($this->serverTime);
        $today = $serverDate->format('Y-m-d');
        $currentDateTime = new \DateTime("{$today} {$currentTime}");
        $prayerDateTime = new \DateTime("{$today} {$prayerTime}");
        $prayerDay = $this->determinePrayerDay($prayerName, $prayerTime, $currentTime, $today, $serverDate);

        $prayerFullDateTime = new \DateTime("{$prayerDay} {$prayerTime}");
        $currentFullDateTime = new \DateTime("{$today} {$currentTime}");
        $elapsedSeconds = $currentFullDateTime->getTimestamp() - $prayerFullDateTime->getTimestamp();

        // Tentukan durasi maksimum berdasarkan waktu sholat
        $maxDuration = $this->getMaxDuration($prayerName);

        if ($elapsedSeconds < 0 || $elapsedSeconds > $maxDuration) {
            return null;
        }

        return $this->buildPrayerStatus($prayerName, $prayerTime, $elapsedSeconds, $prayerDay);
    }

    private function findActivePrayerTime($currentTime)
    {
        $currentTimeStamp = strtotime("1970-01-01 " . $currentTime . ":00");
        $activeIndex = -1;
        $lastPrayerTimeStamp = 0;

        for ($i = 0; $i < count($this->prayerTimes); $i++) {
            $prayerTimeStamp = strtotime("1970-01-01 " . $this->prayerTimes[$i]['time'] . ":00");
            if ($prayerTimeStamp <= $currentTimeStamp && $prayerTimeStamp > $lastPrayerTimeStamp) {
                $activeIndex = $i;
                $lastPrayerTimeStamp = $prayerTimeStamp;
            }
        }

        $currentHour = (int)substr($currentTime, 0, 2);
        $isEarlyMorning = $currentHour >= 0 && $currentHour < 6;

        if ($activeIndex == -1) {
            if ($isEarlyMorning) {
                $activeIndex = 5;
            } else {
                return null;
            }
        }

        return [
            'prayer' => $this->prayerTimes[$activeIndex],
            'index' => $activeIndex
        ];
    }

    private function determinePrayerDay($prayerName, $prayerTime, $currentTime, $today, $serverDate)
    {
        $currentHour = (int)substr($currentTime, 0, 2);
        $prayerHour = (int)substr($prayerTime, 0, 2);
        $isEarlyMorning = $currentHour >= 0 && $currentHour < 6;
        $isMorning = $currentHour >= 6 && $currentHour < 12;
        $isAfternoon = $currentHour >= 12 && $currentHour < 18;
        $isEvening = $currentHour >= 18;

        $tomorrowDate = (clone $serverDate)->modify('+1 day');
        $tomorrow = $tomorrowDate->format('Y-m-d');
        $yesterdayDate = (clone $serverDate)->modify('-1 day');
        $yesterday = $yesterdayDate->format('Y-m-d');

        if ($prayerName === 'Shubuh') {
            if ($isEarlyMorning) {
                $currentDateTime = new \DateTime("{$today} {$currentTime}");
                $prayerDateTime = new \DateTime("{$today} {$prayerTime}");
                if ($currentDateTime < $prayerDateTime) {
                    return $today;
                } else {
                    return $tomorrow;
                }
            } else {
                return $tomorrow;
            }
        } elseif ($prayerName === 'Isya') {
            if ($isEarlyMorning) {
                return $yesterday;
            } else {
                return $today;
            }
        } else {
            if ($isEarlyMorning && $prayerHour >= 18) {
                return $yesterday;
            } elseif (($isEvening || $isAfternoon) && $prayerHour < 6) {
                return $tomorrow;
            }
        }

        return $today;
    }

    private function getMaxDuration($prayerName)
    {
        if (!$this->durasi) {
            // Default jika durasi belum ada
            return ($prayerName === "Jum'at" && $this->currentDayOfWeek == 5) ? (20 * 60) : (4 * 60 + 10 * 60 + 30);
        }

        $prayerLower = strtolower($prayerName);
        if ($prayerLower === "juma'at" && $this->currentDayOfWeek == 5) {
            return $this->durasi->adzan_dzuhur * 60 + $this->durasi->jumat_slide * 60;
        } elseif ($prayerLower === "shubuh") {
            return ($this->durasi->adzan_shubuh * 60) + ($this->durasi->iqomah_shubuh * 60) + $this->durasi->final_shubuh;
        } elseif ($prayerLower === "dzuhur") {
            return ($this->durasi->adzan_dzuhur * 60) + ($this->durasi->iqomah_dzuhur * 60) + $this->durasi->final_dzuhur;
        } elseif ($prayerLower === "ashar") {
            return ($this->durasi->adzan_ashar * 60) + ($this->durasi->iqomah_ashar * 60) + $this->durasi->final_ashar;
        } elseif ($prayerLower === "maghrib") {
            return ($this->durasi->adzan_maghrib * 60) + ($this->durasi->iqomah_maghrib * 60) + $this->durasi->final_maghrib;
        } elseif ($prayerLower === "isya") {
            return ($this->durasi->adzan_isya * 60) + ($this->durasi->iqomah_isya * 60) + $this->durasi->final_isya;
        }

        return 0;
    }

    private function buildPrayerStatus($prayerName, $prayerTime, $elapsedSeconds, $prayerDay)
    {
        $status = [
            'prayerName' => $prayerName,
            'prayerTime' => $prayerTime,
            'elapsedSeconds' => $elapsedSeconds,
            'prayerDay' => $prayerDay
        ];

        $prayerLower = strtolower($prayerName);
        $isFriday = $this->currentDayOfWeek == 5 && $prayerLower === 'juma\'at';

        if (!$this->durasi) {
            // Fallback ke durasi statis (dalam detik)
            $durasi = [
                'adzan' => 4 * 60,      // 4 menit
                'iqomah' => 10 * 60,    // 10 menit
                'final' => 30 * 60,     // 30 menit
                'jumat_slide' => 10 * 60 // 20 menit
            ];
        } else {
            if ($prayerLower === 'shubuh') {
                $durasi = [
                    'adzan' => $this->durasi->adzan_shubuh * 60,
                    'iqomah' => $this->durasi->iqomah_shubuh * 60,
                    'final' => $this->durasi->final_shubuh * 60,
                ];
            } elseif ($prayerLower === 'dzuhur' || $prayerLower === 'juma\'at') {
                $durasi = [
                    'adzan' => $this->durasi->adzan_dzuhur * 60,
                    'iqomah' => $this->durasi->iqomah_dzuhur * 60,
                    'final' => $this->durasi->final_dzuhur * 60,
                    'jumat_slide' => $this->durasi->jumat_slide * 60
                ];
            } elseif ($prayerLower === 'ashar') {
                $durasi = [
                    'adzan' => $this->durasi->adzan_ashar * 60,
                    'iqomah' => $this->durasi->iqomah_ashar * 60,
                    'final' => $this->durasi->final_ashar * 60,
                ];
            } elseif ($prayerLower === 'maghrib') {
                $durasi = [
                    'adzan' => $this->durasi->adzan_maghrib * 60,
                    'iqomah' => $this->durasi->iqomah_maghrib * 60,
                    'final' => $this->durasi->final_maghrib * 60,
                ];
            } elseif ($prayerLower === 'isya') {
                $durasi = [
                    'adzan' => $this->durasi->adzan_isya * 60,
                    'iqomah' => $this->durasi->iqomah_isya * 60,
                    'final' => $this->durasi->final_isya * 60,
                ];
            }
        }

        if ($isFriday) {
            // Adzan phase
            if ($elapsedSeconds <= $durasi['adzan']) {
                $status['phase'] = 'adzan';
                $status['remainingSeconds'] = $durasi['adzan'] - $elapsedSeconds;
                $status['progressPercentage'] = ($elapsedSeconds / $durasi['adzan']) * 100;
            }
            // Jum'at slide phase
            elseif ($elapsedSeconds <= $durasi['adzan'] + $durasi['jumat_slide']) {
                $status['phase'] = 'friday';
                $jumatElapsed = $elapsedSeconds - $durasi['adzan'];
                $status['remainingSeconds'] = $durasi['jumat_slide'] - $jumatElapsed;
                $status['progressPercentage'] = ($jumatElapsed / $durasi['jumat_slide']) * 100;
            }
            $status['isFriday'] = true;
        } else {
            // Adzan phase
            if ($elapsedSeconds <= $durasi['adzan']) {
                $status['phase'] = 'adzan';
                $status['remainingSeconds'] = $durasi['adzan'] - $elapsedSeconds;
                $status['progressPercentage'] = ($elapsedSeconds / $durasi['adzan']) * 100;
            }
            // Iqomah phase
            elseif ($elapsedSeconds <= $durasi['adzan'] + $durasi['iqomah']) {
                $status['phase'] = 'iqomah';
                $iqomahElapsed = $elapsedSeconds - $durasi['adzan'];
                $status['remainingSeconds'] = $durasi['iqomah'] - $iqomahElapsed;
                $status['progressPercentage'] = ($iqomahElapsed / $durasi['iqomah']) * 100;
            }
            // Final phase
            elseif ($elapsedSeconds <= $durasi['adzan'] + $durasi['iqomah'] + $durasi['final']) {
                $status['phase'] = 'final';
                $finalElapsed = $elapsedSeconds - ($durasi['adzan'] + $durasi['iqomah']);
                $status['remainingSeconds'] = $durasi['final'] - $finalElapsed;
                $status['progressPercentage'] = ($finalElapsed / $durasi['final']) * 100;
            }
        }

        return $status;
    }

    public function render()
    {
        return view('livewire.firdaus.firdaus', [
            'themeCss' => $this->themeCss,
            'prayerTimes' => $this->prayerTimes,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
            'currentDayOfWeek' => $this->currentDayOfWeek,
            'profil' => $this->profil,
            'adzan' => $this->adzan,
            'marquee' => $this->marquee,
            'petugas' => $this->petugas,
            'slides' => $this->slides,
            'durasi' => $this->durasi,
            'jumbotron' => $this->jumbotron,
            'activePrayerStatus' => $this->activePrayerStatus,
            'apiSource' => $this->apiSource,
            'adzanData' => $this->adzan ? [
                'adzan1' => $this->adzan->adzan1,
                'adzan2' => $this->adzan->adzan2,
                'adzan3' => $this->adzan->adzan3,
                'adzan4' => $this->adzan->adzan4,
                'adzan5' => $this->adzan->adzan5,
                'adzan6' => $this->adzan->adzan6,
                'adzan15' => $this->adzan->adzan15,
                'adzan7' => $this->adzan->adzan7,
                'adzan8' => $this->adzan->adzan8,
                'adzan9' => $this->adzan->adzan9,
                'adzan10' => $this->adzan->adzan10,
                'adzan11' => $this->adzan->adzan11,
                'adzan12' => $this->adzan->adzan12,
            ] : [],
            'petugasData' => $this->petugas ? [
                'khatib' => $this->petugas->khatib,
                'imam' => $this->petugas->imam,
                'muadzin' => $this->petugas->muadzin,
            ] : [],
            'theme' => $this->theme,
        ]);
    }
}
