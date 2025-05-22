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
    #[Title('Al-Firdaus')]
    public $serverTime; // UTC time from server
    public $serverTimestamp; // timestamp in milliseconds
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
            // Existing server time and prayer times logic remains the same
            // (Copying the previous mount method's time-related code)
            $response = Http::get('https://superapp.pekanbaru.go.id/api/server-time');

            if ($response->successful()) {


                /* Untuk testing hari jum'at*/
                // Set waktu ke Jumat pukul 12:00 siang
                // Jika hari ini belum Jumat, majukan ke Jumat minggu ini
                // Jika sudah lewat Jumat minggu ini, majukan ke Jumat minggu depan
                // $date = new \DateTime($response['serverTime']); // Ambil waktu asli
                // $dayOfWeek = (int) $date->format('w'); // 0 = Minggu, 5 = Jumat
                // $targetDay = 5; // Jumat

                // $daysToAdd = ($targetDay - $dayOfWeek + 7) % 7;
                // if ($daysToAdd === 0 && (int)$date->format('H') >= 12) {
                //     // Sudah hari Jumat lewat jam 12 siang, geser ke Jumat depan
                //     $daysToAdd = 7;
                // }

                // $date->modify("+$daysToAdd days");
                // $date->setTime(5, 12, 110); // Set jam ke 12:00 siang

                // $this->serverTime = $date->format('Y-m-d H:i:s');
                // $this->serverTimestamp = $date->getTimestamp() * 1000;


                $this->serverTime = $response['serverTime']; // mengambil waktu server
                $this->serverTimestamp = strtotime($this->serverTime) * 1000; // mengubah waktu server ke timestamp
                // $this->serverTimestamp = (strtotime($this->serverTime) + (1 * 3600) + (57 * 60)) * 1000; // Uji waktu server + 1 jam 41 menit
                // $this->serverTimestamp = (strtotime($this->serverTime) - (7 * 60 * 60 + 17 * 60)) * 1000; // Uji waktu server - 7 jam 17 menit

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
            } else {
                logger("Gagal mengambil waktu server dari API");
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

    /**
     * Calculate active prayer time status including timing information
     * @param string $currentTime Current time in HH:MM format
     * @return array|null Status information or null if no active prayer time
     */
    private function calculateActivePrayerTimeStatus($currentTime)
    {
        if (empty($this->prayerTimes) || $this->activeIndex < 0) {
            return null;
        }

        // Get active prayer time
        $activePrayer = $this->prayerTimes[$this->activeIndex];
        $prayerName = $activePrayer['name'];
        $prayerTime = $activePrayer['time'];

        // Skip if the active prayer is Shuruq
        if (strtolower($prayerName) === 'shuruq') {
            return null;
        }

        // Use DateTime objects for all date calculations for consistency and accuracy
        $serverDate = new \DateTime($this->serverTime);
        $today = $serverDate->format('Y-m-d');

        // Create DateTime objects for comparison rather than using string manipulation
        $currentDateTime = new \DateTime("{$today} {$currentTime}");
        $prayerDateTime = new \DateTime("{$today} {$prayerTime}");

        // Initialize prayer day with today by default
        $prayerDay = $today;

        // Extract hour for condition checks (using DateTime format is more reliable than substr)
        $currentHour = (int)$currentDateTime->format('H');
        // Define time periods for clearer logic
        $isEarlyMorning = $currentHour >= 0 && $currentHour < 6;  // Midnight to 6am
        $isMorning = $currentHour >= 6 && $currentHour < 12;      // 6am to noon
        $isAfternoon = $currentHour >= 12 && $currentHour < 18;   // Noon to 6pm
        $isEvening = $currentHour >= 18;                          // 6pm to midnight

        // Create day variation objects
        $tomorrowDate = (clone $serverDate)->modify('+1 day');
        $tomorrow = $tomorrowDate->format('Y-m-d');

        $yesterdayDate = (clone $serverDate)->modify('-1 day');
        $yesterday = $yesterdayDate->format('Y-m-d');

        // Determine correct prayer day based on prayer name and current time period
        if ($prayerName === 'Shubuh') {
            if ($isEarlyMorning) {
                // After midnight but before/at Shubuh time
                $prayerDateTime->setTime(
                    (int)substr($prayerTime, 0, 2),
                    (int)substr($prayerTime, 3, 2)
                );

                if ($currentDateTime < $prayerDateTime) {
                    // Current time is before Shubuh time - Shubuh is today
                    $prayerDay = $today;
                } else {
                    // Current time is after Shubuh time - next Shubuh is tomorrow
                    $prayerDay = $tomorrow;
                }
            } else {
                // Current time is after early morning, Shubuh is tomorrow
                $prayerDay = $tomorrow;
            }
        } else if ($prayerName === 'Isya') {
            if ($isEarlyMorning) {
                // After midnight, before dawn - Isya is from yesterday
                $prayerDay = $yesterday;
            } else if ($isEvening) {
                // Evening time - Isya is today
                $prayerDay = $today;
            } else {
                // Morning/Afternoon - Isya is today (next one)
                $prayerDay = $today;
            }
        } else {
            // Handle other prayer times based on prayer hour
            $prayerHour = (int)substr($prayerTime, 0, 2);

            if ($isEarlyMorning && $prayerHour >= 18) {
                // Current time is early morning but prayer is evening prayer from yesterday
                $prayerDay = $yesterday;
            } else if (($isEvening || $isAfternoon) && $prayerHour < 6) {
                // Current time is evening/afternoon but prayer is early morning prayer for tomorrow
                $prayerDay = $tomorrow;
            }
        }

        // Recalculate timestamps with correct day
        $prayerFullDateTime = new \DateTime("{$prayerDay} {$prayerTime}");
        $currentFullDateTime = new \DateTime("{$today} {$currentTime}");

        // Calculate elapsed time in seconds
        $elapsedSeconds = $currentFullDateTime->getTimestamp() - $prayerFullDateTime->getTimestamp();

        // Only process if we're within the relevant timeframes (0-10 minutes after prayer time)
        if ($elapsedSeconds < 0 || $elapsedSeconds > 600) { // 10 minutes max (for Iqomah)
            return null;
        }

        // Determine which phase we're in
        $status = [
            'prayerName' => $prayerName,
            'prayerTime' => $prayerTime,
            'elapsedSeconds' => $elapsedSeconds,
            'prayerDay' => $prayerDay // Add the day information for debugging if needed
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
            'adzanData' => $this->adzan ? [
                'adzan1' => $this->adzan->adzan1,
                'adzan2' => $this->adzan->adzan2,
                'adzan3' => $this->adzan->adzan3,
                'adzan4' => $this->adzan->adzan4,
                'adzan5' => $this->adzan->adzan5,
                'adzan6' => $this->adzan->adzan6,
                'adzan15' => $this->adzan->adzan15,
            ] : [],
            'petugasData' => $this->petugas ? [
                'khatib' => $this->petugas->khatib,
                'imam' => $this->petugas->imam,
                'muadzin' => $this->petugas->muadzin,
            ] : [],
        ]);
    }
}
