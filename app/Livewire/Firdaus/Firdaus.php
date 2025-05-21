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
                $date = new \DateTime($response['serverTime']); // Ambil waktu asli

                // Set waktu ke Jumat pukul 12:00 siang
                // Jika hari ini belum Jumat, majukan ke Jumat minggu ini
                // Jika sudah lewat Jumat minggu ini, majukan ke Jumat minggu depan
                $dayOfWeek = (int) $date->format('w'); // 0 = Minggu, 5 = Jumat
                $targetDay = 5; // Jumat

                $daysToAdd = ($targetDay - $dayOfWeek + 7) % 7;
                if ($daysToAdd === 0 && (int)$date->format('H') >= 12) {
                    // Sudah hari Jumat lewat jam 12 siang, geser ke Jumat depan
                    $daysToAdd = 7;
                }

                $date->modify("+$daysToAdd days");
                $date->setTime(5, 12, 110); // Set jam ke 12:00 siang

                $this->serverTime = $date->format('Y-m-d H:i:s');
                $this->serverTimestamp = $date->getTimestamp() * 1000;


                $this->serverTime = $response['serverTime']; // mengambil waktu server
                // $this->serverTimestamp = strtotime($this->serverTime) * 1000; // mengubah waktu server ke timestamp
                // $this->serverTimestamp = (strtotime($this->serverTime) + (2 * 3600) + (39 * 60)) * 1000; // Uji waktu server + 1 jam 41 menit
                // $this->serverTimestamp = (strtotime($this->serverTime) - (7 * 60 * 60 + 17 * 60)) * 1000;

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

        // Use specific dates to handle day transitions correctly
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Current timestamp with today's date
        $currentTimeStamp = strtotime("{$today} {$currentTime}:00");

        // Initialize prayer timestamp with today's date by default
        $prayerDay = $today;

        // Special handling for Shubuh when current time is between midnight and Shubuh
        // This happens when activeIndex is 0 (Shubuh) and current time is after midnight
        if ($this->activeIndex === 0 && substr($currentTime, 0, 2) < 12 && substr($prayerTime, 0, 2) < 12) {
            // If current time is after midnight but before Shubuh, Shubuh is from today
            if (strtotime("{$today} {$currentTime}:00") < strtotime("{$today} {$prayerTime}:00")) {
                $prayerDay = $today;
            } else {
                // Shubuh has already passed, so the next one is tomorrow
                $prayerDay = $tomorrow;
            }
        }
        // Special handling for Isya when current time is after Isya
        else if ($prayerName === 'Isya' && substr($currentTime, 0, 2) >= 18) {
            // If current time is after 6 PM and prayer is Isya, Isya is from today
            $prayerDay = $today;
        }
        // Special handling when crossing midnight
        else if (substr($currentTime, 0, 2) < 12 && substr($prayerTime, 0, 2) > 12) {
            // Current time is AM but prayer time is PM, prayer is from yesterday
            $prayerDay = $yesterday;
        } else if (substr($currentTime, 0, 2) > 12 && substr($prayerTime, 0, 2) < 12) {
            // Current time is PM but prayer time is AM, prayer is from tomorrow
            $prayerDay = $tomorrow;
        }

        // Calculate prayer timestamp with the correct day
        $prayerTimeStamp = strtotime("{$prayerDay} {$prayerTime}:00");

        // Calculate elapsed time in seconds
        $elapsedSeconds = $currentTimeStamp - $prayerTimeStamp;

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
