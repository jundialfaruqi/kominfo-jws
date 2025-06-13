<?php

namespace App\Livewire\Firdaus;

use App\Models\Profil;
use App\Models\Adzan;
use App\Models\Marquee;
use App\Models\Petugas;
use App\Models\Slides;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Firdaus extends Component
{
    #[Layout('components.layouts.firdaus')]
    #[Title('Jadwal Sholat Pekanbaru')]
    public $serverTime; // UTC time from server
    public $serverTimestamp; // timestamp in milliseconds
    public $apiSource; // Track API source
    public $jadwalSholat = []; // jadwal sholat hari ini
    public $prayerTimes = []; // waktu sholat
    public $activeIndex = 0; // index waktu sholat aktif
    public $nextPrayerIndex = 0; // index waktu sholat berikutnya
    public $currentMonth; // menyimpan bulan saat ini
    public $currentYear; // menyimpan tahun saat ini
    public $currentDayOfWeek; // menyimpan hari dalam seminggu (1-7, dengan 5 = Jumat)
    public $baseUrl = 'https://raw.githubusercontent.com/lakuapik/jadwalsholatorg/master/adzan/pekanbaru/'; // URL base untuk mengambil data jadwal sholat

    // Properties for active prayer time status
    public $activePrayerStatus = null; // Status waktu shalat aktif

    // New properties for related models
    public $profil;
    public $adzan;
    public $marquee;
    public $petugas;
    public $slides;

    public function mount($slug)
    {
        // Fetch Profil by slug instead of id
        $this->profil = Profil::where('slug', $slug)->firstOrFail();

        // Fetch related models using the user_id from Profil
        $user_id = $this->profil->user_id;

        // Fetch related models
        $this->adzan    = Adzan::where('user_id', $user_id)->first();
        $this->marquee  = Marquee::where('user_id', $user_id)->first();
        $this->petugas  = Petugas::where('user_id', $user_id)->first();
        $this->slides   = Slides::where('user_id', $user_id)->first();

        try {
            // Coba API utama
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
                // Fallback ke timeapi.io
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
                    // Fallback ke API Google Script
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
                    // Fallback ke waktu server lokal
                    $serverDateTime = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
                    $this->serverTime = $serverDateTime->format('Y-m-d H:i:s');
                    $this->serverTimestamp = $serverDateTime->getTimestamp() * 1000;
                    $this->apiSource = 'local';
                }
            }
        }

        // Lanjutkan dengan logika jadwal sholat
        try {
            // Ambil tanggal, bulan, dan tahun hari ini
            $tanggalHariIni         = date('Y-m-d', strtotime($this->serverTime));
            $this->currentMonth     = date('m', strtotime($this->serverTime));
            $this->currentYear      = date('Y', strtotime($this->serverTime));
            $this->currentDayOfWeek = date('N', strtotime($this->serverTime)); // 1 (Senin) hingga 7 (Minggu)

            // Buat URL dinamis berdasarkan tahun dan bulan saat ini
            $jadwalUrl = $this->baseUrl . $this->currentYear . '/' . $this->currentMonth . '.json';

            // Ambil data jadwal sholat berdasarkan URL dinamis
            $jadwalResponse = Http::get($jadwalUrl);

            if (!$jadwalResponse->successful()) {
                // Jika gagal, coba gunakan data bulan terakhir yang tersedia (fallback)
                $bulanSebelumnya = $this->getPreviousMonth($this->currentMonth, $this->currentYear);
                $tahunSebelumnya = $bulanSebelumnya['year'];
                $bulanSebelumnya = $bulanSebelumnya['month'];

                $fallbackUrl = $this->baseUrl . $tahunSebelumnya . '/' . $bulanSebelumnya . '.json';
                $jadwalResponse = Http::get($fallbackUrl);

                // Log untuk debugging
                logger("Menggunakan data jadwal fallback: " . $fallbackUrl);
            }

            if ($jadwalResponse->successful()) {
                $jadwalSholat = $jadwalResponse->json();
                $this->jadwalSholat = $jadwalSholat; // simpan seluruh jadwal bulan ini

                // Cari data jadwal sholat berdasarkan tanggal hari ini
                $jadwalHariIni = null;
                foreach ($jadwalSholat as $item) {
                    if ($item['tanggal'] === $tanggalHariIni) {
                        $jadwalHariIni = $item;
                        break;
                    }
                }

                // Pastikan data tersedia untuk hari ini
                if ($jadwalHariIni) {
                    // Tentukan nama untuk Dzuhur (Jum'at jika hari ini Jumat)
                    $dzuhurLabel = $this->currentDayOfWeek == 5 ? "Jum'at" : "Dzuhur";

                    // Menyusun data waktu sholat
                    $this->prayerTimes = [
                        ['name' => 'Shubuh', 'time' => $jadwalHariIni['shubuh'], 'icon' => 'sunset'],
                        ['name' => 'Shuruq', 'time' => $jadwalHariIni['terbit'], 'icon' => 'sunrise'],
                        ['name' => $dzuhurLabel, 'time' => $jadwalHariIni['dzuhur'], 'icon' => 'sun'],
                        ['name' => 'Ashar', 'time' => $jadwalHariIni['ashr'], 'icon' => 'sunwind'],
                        ['name' => 'Maghrib', 'time' => $jadwalHariIni['magrib'], 'icon' => 'hazemoon'],
                        ['name' => 'Isya', 'time' => $jadwalHariIni['isya'], 'icon' => 'moon'],
                    ];

                    // Tentukan waktu aktif berdasarkan waktu sekarang
                    $currentTime = date('H:i', strtotime($this->serverTime)); // waktu saat ini di server, format HH:MM

                    // PERUBAHAN: Logic untuk menentukan waktu sholat yang sedang aktif dan berikutnya
                    $prayerIndices = $this->determineActivePrayerTime($currentTime);
                    $this->activeIndex = $prayerIndices['active'];
                    $this->nextPrayerIndex = $prayerIndices['next'];

                    // Calculate active prayer time status
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

    /**
     * Menentukan waktu sholat yang sedang aktif dan berikutnya berdasarkan waktu saat ini
     * @param string $currentTime waktu saat ini dalam format HH:MM
     * @return array indeks waktu sholat yang aktif dan berikutnya
     */
    private function determineActivePrayerTime($currentTime)
    {
        // Konversi string waktu ke timestamp untuk perbandingan yang lebih mudah
        $currentTimeStamp = strtotime("1970-01-01 " . $currentTime . ":00");

        // Cari waktu sholat berikutnya (yang belum terjadi)
        $nextIndex = -1;
        $nextPrayerTimeStamp = PHP_INT_MAX;

        for ($i = 0; $i < count($this->prayerTimes); $i++) {
            $prayerTimeStamp = strtotime("1970-01-01 " . $this->prayerTimes[$i]['time'] . ":00");

            // Jika waktu sholat ini belum terjadi dan lebih dekat dari waktu sholat berikutnya yang sudah ditemukan
            if ($prayerTimeStamp > $currentTimeStamp && $prayerTimeStamp < $nextPrayerTimeStamp) {
                $nextIndex = $i;
                $nextPrayerTimeStamp = $prayerTimeStamp;
            }
        }

        // Jika tidak ada waktu sholat berikutnya hari ini, berarti waktu sholat berikutnya adalah Shubuh besok
        if ($nextIndex == -1) {
            $nextIndex = 0; // Shubuh
        }

        // Cari waktu sholat yang sedang aktif (waktu sholat terakhir yang sudah terjadi)
        $activeIndex = -1;
        $lastPrayerTimeStamp = 0;

        for ($i = 0; $i < count($this->prayerTimes); $i++) {
            $prayerTimeStamp = strtotime("1970-01-01 " . $this->prayerTimes[$i]['time'] . ":00");

            // Jika waktu sholat ini sudah terjadi dan lebih baru dari waktu sholat aktif yang sudah ditemukan
            if ($prayerTimeStamp <= $currentTimeStamp && $prayerTimeStamp > $lastPrayerTimeStamp) {
                $activeIndex = $i;
                $lastPrayerTimeStamp = $prayerTimeStamp;
            }
        }

        // Jika tidak ada waktu sholat yang sudah terjadi hari ini, berarti waktu sholat aktif adalah Isya kemarin
        if ($activeIndex == -1) {
            $activeIndex = 5; // Isya
        }

        return [
            'active' => $activeIndex,
            'next' => $nextIndex
        ];
    }

    /**
     * Mendapatkan bulan sebelumnya untuk fallback data
     * @param string $month bulan dalam format 2 digit (01-12)
     * @param string $year tahun dalam format 4 digit
     * @return array bulan sebelumnya dan tahunnya
     */
    private function getPreviousMonth($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;

        if ($month == 1) {
            return [
                'month' => '12',
                'year' => (string)($year - 1)
            ];
        } else {
            return [
                'month' => str_pad($month - 1, 2, '0', STR_PAD_LEFT),
                'year' => (string)$year
            ];
        }
    }

    /**
     * Mendapatkan bulan berikutnya untuk antisipasi pergantian bulan
     * @param string $month bulan dalam format 2 digit (01-12)
     * @param string $year tahun dalam format 4 digit
     * @return array bulan berikutnya dan tahunnya
     */
    private function getNextMonth($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;

        if ($month == 12) {
            return [
                'month' => '01',
                'year' => (string)($year + 1)
            ];
        } else {
            return [
                'month' => str_pad($month + 1, 2, '0', STR_PAD_LEFT),
                'year' => (string)$year
            ];
        }
    }

    private function calculateActivePrayerTimeStatus($currentTime)
    {
        if (empty($this->prayerTimes)) {
            return null;
        }

        // === STEP 1: Find Active Prayer Time Independently ===
        $activePrayerData = $this->findActivePrayerTime($currentTime);

        if (!$activePrayerData) {
            return null;
        }

        $activePrayer = $activePrayerData['prayer'];
        $activeIndex = $activePrayerData['index'];
        $prayerName = $activePrayer['name'];
        $prayerTime = $activePrayer['time'];

        // Skip if the active prayer is Shuruq
        if (strtolower($prayerName) === 'shuruq') {
            return null;
        }

        // === STEP 2: Calculate Prayer Day and Elapsed Time ===
        $serverDate = new \DateTime($this->serverTime);
        $today = $serverDate->format('Y-m-d');

        // Create DateTime objects for comparison
        $currentDateTime = new \DateTime("{$today} {$currentTime}");
        $prayerDateTime = new \DateTime("{$today} {$prayerTime}");

        // Determine correct prayer day
        $prayerDay = $this->determinePrayerDay($prayerName, $prayerTime, $currentTime, $today, $serverDate);

        // Recalculate timestamps with correct day
        $prayerFullDateTime = new \DateTime("{$prayerDay} {$prayerTime}");
        $currentFullDateTime = new \DateTime("{$today} {$currentTime}");

        // Calculate elapsed time in seconds
        $elapsedSeconds = $currentFullDateTime->getTimestamp() - $prayerFullDateTime->getTimestamp();

        // Only process if we're within the relevant timeframes (0-10 minutes after prayer time)
        if ($elapsedSeconds < 0 || $elapsedSeconds > 600) { // 10 minutes max (for Iqomah)
            return null;
        }

        // === STEP 3: Determine Phase and Status ===
        return $this->buildPrayerStatus($prayerName, $prayerTime, $elapsedSeconds, $prayerDay);
    }

    /**
     * Find the currently active prayer time independently
     * @param string $currentTime Current time in HH:MM format
     * @return array|null Array with 'prayer' and 'index' keys, or null if not found
     */
    private function findActivePrayerTime($currentTime)
    {
        $currentTimeStamp = strtotime("1970-01-01 " . $currentTime . ":00");

        $activeIndex = -1;
        $lastPrayerTimeStamp = 0;

        // Find the most recent prayer time that has already occurred
        for ($i = 0; $i < count($this->prayerTimes); $i++) {
            $prayerTimeStamp = strtotime("1970-01-01 " . $this->prayerTimes[$i]['time'] . ":00");

            // If this prayer time has occurred and is more recent than previously found
            if ($prayerTimeStamp <= $currentTimeStamp && $prayerTimeStamp > $lastPrayerTimeStamp) {
                $activeIndex = $i;
                $lastPrayerTimeStamp = $prayerTimeStamp;
            }
        }

        // Special handling for early morning (midnight to 6am)
        $currentHour = (int)substr($currentTime, 0, 2);
        $isEarlyMorning = $currentHour >= 0 && $currentHour < 6;

        if ($activeIndex == -1) {
            if ($isEarlyMorning) {
                // If it's early morning and no prayer has occurred today, 
                // the active prayer is Isya from yesterday
                $activeIndex = 5; // Isya
            } else {
                // No active prayer found
                return null;
            }
        }

        return [
            'prayer' => $this->prayerTimes[$activeIndex],
            'index' => $activeIndex
        ];
    }

    /**
     * Determine the correct day for the prayer time
     * @param string $prayerName Name of the prayer
     * @param string $prayerTime Time of the prayer (HH:MM)
     * @param string $currentTime Current time (HH:MM)
     * @param string $today Today's date (Y-m-d)
     * @param DateTime $serverDate Server date object
     * @return string Prayer day in Y-m-d format
     */
    private function determinePrayerDay($prayerName, $prayerTime, $currentTime, $today, $serverDate)
    {
        $currentHour = (int)substr($currentTime, 0, 2);
        $prayerHour = (int)substr($prayerTime, 0, 2);

        // Define time periods
        $isEarlyMorning = $currentHour >= 0 && $currentHour < 6;
        $isMorning = $currentHour >= 6 && $currentHour < 12;
        $isAfternoon = $currentHour >= 12 && $currentHour < 18;
        $isEvening = $currentHour >= 18;

        // Create day variation objects
        $tomorrowDate = (clone $serverDate)->modify('+1 day');
        $tomorrow = $tomorrowDate->format('Y-m-d');

        $yesterdayDate = (clone $serverDate)->modify('-1 day');
        $yesterday = $yesterdayDate->format('Y-m-d');

        // Determine correct prayer day based on prayer name and current time period
        if ($prayerName === 'Shubuh') {
            if ($isEarlyMorning) {
                // After midnight but before/at Shubuh time
                $currentDateTime = new \DateTime("{$today} {$currentTime}");
                $prayerDateTime = new \DateTime("{$today} {$prayerTime}");

                if ($currentDateTime < $prayerDateTime) {
                    // Current time is before Shubuh time - Shubuh is today
                    return $today;
                } else {
                    // Current time is after Shubuh time - next Shubuh is tomorrow
                    return $tomorrow;
                }
            } else {
                // Current time is after early morning, Shubuh is tomorrow
                return $tomorrow;
            }
        } else if ($prayerName === 'Isya') {
            if ($isEarlyMorning) {
                // After midnight, before dawn - Isya is from yesterday
                return $yesterday;
            } else if ($isEvening) {
                // Evening time - Isya is today
                return $today;
            } else {
                // Morning/Afternoon - Isya is today (next one)
                return $today;
            }
        } else {
            // Handle other prayer times
            if ($isEarlyMorning && $prayerHour >= 18) {
                // Current time is early morning but prayer is evening prayer from yesterday
                return $yesterday;
            } else if (($isEvening || $isAfternoon) && $prayerHour < 6) {
                // Current time is evening/afternoon but prayer is early morning prayer for tomorrow
                return $tomorrow;
            }
        }

        return $today;
    }

    /**
     * Build the prayer status array with phase information
     * @param string $prayerName Name of the prayer
     * @param string $prayerTime Time of the prayer
     * @param int $elapsedSeconds Elapsed seconds since prayer time
     * @param string $prayerDay Prayer day
     * @return array Prayer status information
     */
    private function buildPrayerStatus($prayerName, $prayerTime, $elapsedSeconds, $prayerDay)
    {
        $status = [
            'prayerName' => $prayerName,
            'prayerTime' => $prayerTime,
            'elapsedSeconds' => $elapsedSeconds,
            'prayerDay' => $prayerDay
        ];

        // Adzan phase (0-3 minutes)
        if ($elapsedSeconds <= 180) { // 3 minutes
            $status['phase'] = 'adzan';
            $status['remainingSeconds'] = 180 - $elapsedSeconds;
            $status['progress'] = ($elapsedSeconds / 180) * 100;
        }
        // Iqomah phase (3-10 minutes)
        else if ($elapsedSeconds <= 600) { // 10 minutes
            $status['phase'] = 'iqomah';
            // Iqomah starts at 3 minutes after prayer time
            $iqomahElapsedSeconds = $elapsedSeconds - 180;
            $status['remainingSeconds'] = 420 - $iqomahElapsedSeconds; // 7 minutes duration
            $status['progress'] = ($iqomahElapsedSeconds / 420) * 100;

            // Special case for final image
            if ($status['remainingSeconds'] <= 0) {
                $status['phase'] = 'final';
            }
        }

        // Special handling for Friday
        if ($this->currentDayOfWeek == 5 && $prayerName == "Jum'at" && $elapsedSeconds <= 600) {
            $status['isFriday'] = true;
        }

        return $status;
    }

    public function render()
    {
        return view('livewire.firdaus.firdaus', [
            'prayerTimes' => $this->prayerTimes,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
            'currentDayOfWeek' => $this->currentDayOfWeek,
            'profil' => $this->profil,
            'adzan' => $this->adzan,
            'marquee' => $this->marquee,
            'petugas' => $this->petugas,
            'slides' => $this->slides,
            'activePrayerStatus' => $this->activePrayerStatus,
            'apiSource' => $this->apiSource, // Tambahkan apiSource ke view
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
        ]);
    }
}
