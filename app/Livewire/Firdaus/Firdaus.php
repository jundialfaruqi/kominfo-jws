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
                $serverDateTime = new \DateTime($this->serverTime);
                $serverDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
                $this->serverTime = $serverDateTime->format('Y-m-d H:i:s');
                $this->serverTimestamp = strtotime($this->serverTime) * 1000; // mengubah waktu server ke timestamp
                // $this->serverTimestamp = (strtotime($this->serverTime) + (1 * 3600) + (10 * 60)) * 1000; // Uji waktu server + 1 jam 41 menit
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
    private function calculateActivePrayerTimeStatus()
    {
        if (empty($this->prayerTimes)) {
            return null;
        }

        // Get fresh server time from API
        $response = Http::get('https://superapp.pekanbaru.go.id/api/server-time');
        if (!$response->successful()) {
            return null;
        }

        $serverTime = $response['serverTime'];
        $serverDateTime = new \DateTime($serverTime);
        $serverDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
        $currentTimeFormatted = $serverDateTime->format('H:i');

        // Get today's prayer schedule from API
        $today = $serverDateTime->format('Y-m-d');
        $month = $serverDateTime->format('m');
        $year = $serverDateTime->format('Y');

        try {
            $prayerResponse = Http::get("https://raw.githubusercontent.com/lakuapik/jadwalsholatorg/master/adzan/pekanbaru/{$year}/{$month}.json");
            if (!$prayerResponse->successful()) {
                return null;
            }

            $prayerData = $prayerResponse->json();
            $dayOfMonth = (int)$serverDateTime->format('d');
            $todayPrayers = $prayerData[$dayOfMonth - 1] ?? null;

            if (!$todayPrayers) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        // Check each prayer time
        $prayerNames = ['Shubuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

        // Add Friday prayer if today is Friday
        if ($serverDateTime->format('N') == 5) {
            $prayerNames = ['Shubuh', 'Dzuhur', "Jum'at", 'Ashar', 'Maghrib', 'Isya'];
        }

        foreach ($prayerNames as $prayerName) {
            $prayerTimeKey = strtolower($prayerName);
            if ($prayerName === "Jum'at") {
                $prayerTimeKey = 'dzuhur'; // Use Dzuhur time for Friday prayer
            }

            $prayerTime = $todayPrayers[$prayerTimeKey] ?? null;
            if (!$prayerTime) {
                continue;
            }

            // Create DateTime objects for comparison
            $prayerDateTime = new \DateTime("{$today} {$prayerTime}");
            $prayerDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
            $currentDateTime = new \DateTime("{$today} {$currentTimeFormatted}");
            $currentDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));

            // Calculate elapsed seconds since prayer time
            $elapsedSeconds = $currentDateTime->getTimestamp() - $prayerDateTime->getTimestamp();

            // Check if we're in the active prayer window
            if ($elapsedSeconds >= 0 && $elapsedSeconds <= 660) { // 11 minutes total (3 adzan + 7 iqomah + 1 final)
                $status = [
                    'prayerName' => $prayerName,
                    'prayerTime' => $prayerTime,
                    'elapsedSeconds' => $elapsedSeconds,
                    'serverTime' => $serverTime
                ];

                // Determine phase based on elapsed time
                if ($elapsedSeconds <= 180) { // 0-3 minutes: Adzan phase
                    $status['phase'] = 'adzan';
                    $status['remainingSeconds'] = 180 - $elapsedSeconds;
                    $status['progress'] = ($elapsedSeconds / 180) * 100;
                } elseif ($elapsedSeconds <= 600) { // 3-10 minutes: Iqomah phase
                    $status['phase'] = 'iqomah';
                    $iqomahElapsed = $elapsedSeconds - 180;
                    $status['remainingSeconds'] = 420 - $iqomahElapsed; // 7 minutes iqomah
                    $status['progress'] = ($iqomahElapsed / 420) * 100;
                } elseif ($elapsedSeconds <= 660) { // 10-11 minutes: Final phase
                    $status['phase'] = 'final';
                    $finalElapsed = $elapsedSeconds - 600;
                    $status['remainingSeconds'] = 60 - $finalElapsed; // 1 minute final
                    $status['progress'] = ($finalElapsed / 60) * 100;
                }

                // Special flag for Friday prayer
                if ($prayerName === "Jum'at") {
                    $status['isFriday'] = true;
                }

                return $status;
            }
        }

        return null;
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
