<?php

namespace App\Livewire\Statistik;

use App\Models\MasjidActivityLog;
use App\Models\Profil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Statistik & Aktivitas')]
class Index extends Component
{
    public string $initialDate = '';
    public string $initialMonth = '';

    public function mount(): void
    {
        if (!Auth::check() || !Auth::user()->can('statistik-masjid-lihat')) {
            abort(403, 'Anda tidak memiliki akses ke halaman Statistik & Aktivitas.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $this->initialDate = $now->format('Y-m-d');
        $this->initialMonth = $now->format('Y-m');
    }

    /**
     * Dipanggil oleh client browser (Alpine.js) hanya ketika user berpindah ke bulan/tahun yang berbeda
     * pada kalender filter tanggal.
     */
    public function fetchMonthData(string $yearMonth): array
    {
        if (!Auth::check() || !Auth::user()->can('statistik-masjid-lihat')) {
            abort(403);
        }

        return $this->buildMonthlyDataset($yearMonth);
    }

    /**
     * Membangun dataset grafik harian (1 bulan penuh) dan daftar aktivitas untuk bulan terkait.
     */
    private function buildMonthlyDataset(string $yearMonth): array
    {
        try {
            if (!preg_match('/^\d{4}-\d{2}$/', trim($yearMonth))) {
                $monthDate = Carbon::now('Asia/Jakarta')->startOfMonth();
            } else {
                $monthDate = Carbon::createFromFormat('Y-m-d', trim($yearMonth) . '-01', 'Asia/Jakarta')->startOfMonth();
            }
        } catch (\Throwable) {
            $monthDate = Carbon::now('Asia/Jakarta')->startOfMonth();
        }

        $startOfMonth = $monthDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $monthDate->copy()->endOfMonth()->endOfDay();
        $daysInMonth = $monthDate->daysInMonth;
        $monthKey = $monthDate->format('Y-m');
        $monthLabel = $monthDate->translatedFormat('F Y');

        $dailyCounts = [];
        $logs = [];

        if (Schema::hasTable('masjid_activity_logs')) {
            $rawRows = MasjidActivityLog::query()
                ->select(['id', 'masjid_name', 'description', 'created_at'])
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->limit(3000)
                ->get();

            foreach ($rawRows as $row) {
                $dt = $row->created_at
                    ? Carbon::parse($row->created_at)->timezone('Asia/Jakarta')
                    : null;

                if ($dt) {
                    $dayIndex = (int) $dt->format('j');
                    $dailyCounts[$dayIndex] = ($dailyCounts[$dayIndex] ?? 0) + 1;
                }

                $logs[] = [
                    'id'             => $row->id,
                    'masjid_name'    => $row->masjid_name,
                    'description'    => $row->description,
                    'date_key'       => $dt ? $dt->format('Y-m-d') : '',
                    'time_formatted' => $dt ? $dt->translatedFormat('d M Y, H:i') . ' WIB' : '-',
                    'time_human'     => $dt ? $dt->diffForHumans() : '',
                ];
            }
        }

        $categories = [];
        $fullDates = [];
        $dateLabels = [];
        $seriesData = [];
        $totalMonthlyActivities = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDay = $monthDate->copy()->day($day);
            $count = (int) ($dailyCounts[$day] ?? 0);

            $categories[] = (string) $day;
            $fullDates[] = $currentDay->format('Y-m-d');
            $dateLabels[] = $currentDay->translatedFormat('d F Y');
            $seriesData[] = $count;
            $totalMonthlyActivities += $count;
        }

        return [
            'monthKey'               => $monthKey,
            'monthLabel'             => $monthLabel,
            'categories'             => $categories,
            'fullDates'              => $fullDates,
            'dateLabels'             => $dateLabels,
            'seriesData'             => $seriesData,
            'totalMonthlyActivities' => $totalMonthlyActivities,
            'logs'                   => $logs,
        ];
    }

    public function render()
    {
        // Hitung Total Masjid & Total OPD berdasarkan Profil yang sudah dibuat dan dimiliki oleh user ber-role terkait
        $totalMasjid = Profil::whereHas('user.roles', function ($q) {
            $q->where('name', 'Admin Masjid');
        })->count();

        $totalOpd = Profil::whereHas('user.roles', function ($q) {
            $q->where('name', 'Admin OPD');
        })->count();

        $totalMasjidDanOpd = $totalMasjid + $totalOpd;

        $initialDataset = $this->buildMonthlyDataset($this->initialMonth);

        return view('livewire.statistik.index', [
            'totalMasjidDanOpd' => $totalMasjidDanOpd,
            'totalMasjid'       => $totalMasjid,
            'totalOpd'          => $totalOpd,
            'initialDataset'    => $initialDataset,
        ]);
    }
}

