<?php

namespace App\Livewire\Laporan;

use App\Models\Laporan as ModelsLaporan;
use App\Models\Profil;
use App\Models\GroupCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Keuangan extends Component
{
    use WithPagination, AuthorizesRequests;

    #[Title('Laporan Keuangan')]

    public $paginate;
    public $search;
    protected $paginationTheme = 'bootstrap';
    public $paginatePerCategory = [];
    public $paginatePerCategoryNonAdmin = [];
    public $searchPerCategory = [];
    public $searchPerCategoryNonAdmin = [];

    public $laporanId;
    public $idMasjid;
    public $idGroupCategory; // baru: id group category terpilih
    public $tanggal;
    public $uraian;
    public $jenis;
    public $saldo;
    // flag saldo awal
    public $isOpening = false;

    public $showForm = false;
    public $isEdit = false;
    public $showTable = true;
    public $deleteLaporanId;
    public $deleteLaporanName;

    // Filter tanggal global: hari/bulan/tahun
    public $filterDateMode = 'bulan'; // opsi: 'hari', 'bulan', 'tahun'
    public $filterDay;   // format: Y-m-d
    public $filterMonth; // format: Y-m
    public $filterYear;  // integer (YYYY)
    // Tambahan untuk pilihan bulan agar konsisten (dropdown):
    public $filterMonthSelect; // 1-12
    public $filterMonthYearSelect; // YYYY

    protected $rules = [
        'idMasjid' => 'required|exists:profils,id',
        'idGroupCategory' => 'required|exists:group_categories,id',
        'tanggal'  => 'required|date',
        'uraian'   => 'required|string',
        'jenis'    => 'required|in:masuk,keluar',
        'saldo'    => 'required|integer|min:0',
    ];

    protected $messages = [
        'idMasjid.required' => 'Pilih Profil Masjid terlebih dahulu',
        'idMasjid.exists'   => 'Profil Masjid tidak ditemukan',
        'idGroupCategory.required' => 'Pilih Group Category terlebih dahulu',
        'idGroupCategory.exists'   => 'Group Category tidak ditemukan',
        'tanggal.required'  => 'Tanggal tidak boleh kosong',
        'tanggal.date'      => 'Format tanggal tidak valid',
        'uraian.required'   => 'Uraian tidak boleh kosong',
        'jenis.required'    => 'Jenis transaksi harus dipilih',
        'jenis.in'          => 'Jenis transaksi harus salah satu dari: masuk atau keluar',
        'saldo.required'    => 'Saldo tidak boleh kosong',
        'saldo.integer'     => 'Saldo harus berupa bilangan bulat',
        'saldo.min'         => 'Saldo tidak boleh kurang dari 0',
    ];

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // Default filter tanggal
        $this->filterDateMode = 'bulan';
        $this->filterDay = Carbon::now()->format('Y-m-d');
        $this->filterMonth = Carbon::now()->format('Y-m');
        $this->filterYear = Carbon::now()->year;
        // Default pilihan bulan (dropdown)
        $this->filterMonthSelect = (int) Carbon::now()->format('m');
        $this->filterMonthYearSelect = Carbon::now()->year;

        // Tampilkan tabel untuk non-admin, dan set idMasjid ke profil user
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->showForm = false;
            $this->showTable = true;
            $profil = Auth::user()->profil;
            if ($profil) {
                $this->idMasjid = $profil->id;
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->showForm = false;
        $this->showTable = true;
        $this->resetValidation();
        $this->reset([
            'laporanId',
            'idMasjid',
            'idGroupCategory',
            'tanggal',
            'uraian',
            'jenis',
            'saldo',
            'isOpening',
        ]);
    }

    public function showAddForm()
    {
        $this->resetValidation();
        $this->reset([
            'laporanId',
            'idGroupCategory',
            'tanggal',
            'uraian',
            'jenis',
            'saldo',
            'isOpening',
        ]);

        $this->isEdit = false;
        $this->showForm = true;
        $this->showTable = false;

        // Set default tanggal ke hari ini
        $this->tanggal = Carbon::now()->format('Y-m-d');

        // Untuk non-admin, pastikan idMasjid sesuai profil user
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $profil = Auth::user()->profil;
            if ($profil) {
                $this->idMasjid = $profil->id;
            }
        }
    }

    public function updated($name, $value)
    {
        if ($name === 'paginate') {
            $this->resetPage();
            return;
        }
        // Reset halaman saat admin mengganti profil masjid
        if ($name === 'idMasjid') {
            $this->resetPage();
            $this->resetAllCategoryPages();
            return;
        }
        if (Str::startsWith($name, 'paginatePerCategory.')) {
            $parts = explode('.', $name);
            if (count($parts) === 3) {
                $masjidId = $parts[1];
                $catId = $parts[2];
                $this->resetPage('page_' . $masjidId . '_' . $catId);
            }
            return;
        }
        if (Str::startsWith($name, 'paginatePerCategoryNonAdmin.')) {
            $parts = explode('.', $name);
            if (count($parts) === 2) {
                $catId = $parts[1];
                $this->resetPage('page_cat_' . $catId);
            }
            return;
        }
        // Tambahkan reset halaman saat pencarian per kategori berubah (admin)
        if (Str::startsWith($name, 'searchPerCategory.')) {
            $parts = explode('.', $name);
            if (count($parts) === 3) {
                $masjidId = $parts[1];
                $catId = $parts[2];
                $this->resetPage('page_' . $masjidId . '_' . $catId);
            }
            return;
        }
        // Tambahkan reset halaman saat pencarian per kategori berubah (non-admin)
        if (Str::startsWith($name, 'searchPerCategoryNonAdmin.')) {
            $parts = explode('.', $name);
            if (count($parts) === 2) {
                $catId = $parts[1];
                $this->resetPage('page_cat_' . $catId);
            }
            return;
        }
        // Reset halaman saat filter tanggal global berubah
        if (in_array($name, ['filterDateMode', 'filterDay', 'filterMonth', 'filterYear'])) {
            $this->resetPage();
            $this->resetAllCategoryPages();
            return;
        }
        // Sinkronisasi nilai filterMonth ketika dropdown bulan/tahun berubah
        if (in_array($name, ['filterMonthSelect', 'filterMonthYearSelect'])) {
            if ($this->filterDateMode === 'bulan') {
                $month = str_pad((string) ($this->filterMonthSelect ?? 1), 2, '0', STR_PAD_LEFT);
                $year = (int) ($this->filterMonthYearSelect ?? Carbon::now()->year);
                $this->filterMonth = sprintf('%04d-%s', $year, $month);
            }
            $this->resetPage();
            $this->resetAllCategoryPages();
            return;
        }
    }

    private function resetAllCategoryPages()
    {
        // Reset semua pagination per kategori untuk admin
        foreach ((array) $this->paginatePerCategory as $masjidId => $cats) {
            if (is_array($cats)) {
                foreach ($cats as $catId => $val) {
                    $this->resetPage('page_' . $masjidId . '_' . $catId);
                }
            }
        }
        // Reset semua pagination per kategori untuk non-admin
        foreach ((array) $this->paginatePerCategoryNonAdmin as $catId => $val) {
            $this->resetPage('page_cat_' . $catId);
        }
    }

    public function render()
    {
        $this->authorize('viewAny', ModelsLaporan::class);

        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);

        // Query builder untuk laporan (tanpa paginasi global)
        $baseQuery = ModelsLaporan::with(['profil', 'groupCategory'])
            ->selectRaw("laporans.id, laporans.id_masjid, laporans.id_group_category, laporans.tanggal, laporans.uraian, laporans.jenis, laporans.saldo, laporans.is_opening,
                (SELECT COALESCE(SUM(CASE WHEN l2.is_opening = 1 OR l2.jenis = 'masuk' THEN l2.saldo ELSE -l2.saldo END), 0)
                 FROM laporans AS l2
                 WHERE l2.id_masjid = laporans.id_masjid
                   AND l2.id_group_category = laporans.id_group_category
                   AND (l2.tanggal < laporans.tanggal OR (l2.tanggal = laporans.tanggal AND l2.id <= laporans.id))
                ) AS running_balance_sql");

        // Filter berdasarkan role dan pencarian
        if (!$isAdmin) {
            $profil = $currentUser->profil;
            if ($profil) {
                $baseQuery->where('id_masjid', $profil->id);
                if (!empty($this->search)) {
                    $baseQuery->where('uraian', 'like', '%' . $this->search . '%');
                }
            } else {
                // Jika tidak ada profil, kosongkan hasil
                $baseQuery->whereRaw('1 = 0');
            }
        } else {
            // Admin: pencarian global (uraian atau nama masjid)
            $baseQuery->where(function ($q) {
                $q->where('uraian', 'like', '%' . $this->search . '%')
                    ->orWhereHas('profil', function ($qp) {
                        $qp->where('name', 'like', '%' . $this->search . '%');
                    });
            });
            // Admin: jika belum memilih profil masjid, kosongkan hasil; jika sudah, batasi pada profil terpilih
            if ($this->idMasjid) {
                $baseQuery->where('id_masjid', $this->idMasjid);
            } else {
                $baseQuery->whereRaw('1 = 0');
            }
        }

        // Terapkan filter tanggal global (hari/bulan/tahun)
        if ($this->filterDateMode === 'hari' && !empty($this->filterDay)) {
            $baseQuery->whereDate('tanggal', $this->filterDay);
        } elseif ($this->filterDateMode === 'bulan' && !empty($this->filterMonth)) {
            $year = substr($this->filterMonth, 0, 4);
            $month = substr($this->filterMonth, 5, 2);
            if (is_numeric($year) && is_numeric($month)) {
                $baseQuery->whereYear('tanggal', (int) $year)
                          ->whereMonth('tanggal', (int) $month);
            }
        } elseif ($this->filterDateMode === 'tahun' && !empty($this->filterYear) && is_numeric($this->filterYear)) {
            $baseQuery->whereYear('tanggal', (int) $this->filterYear);
        }

        // Urutkan kronologis
        $baseQuery->orderBy('tanggal', 'asc');

        // Daftar profil untuk admin memilih masjid
        $profils = collect([]);
        if ($isAdmin) {
            $profils = Profil::orderBy('name')->get();
        }

        // Daftar Group Category berdasarkan profil/masjid terpilih
        $groupCategories = collect([]);
        if ($isAdmin) {
            if ($this->idMasjid) {
                $groupCategories = GroupCategory::where('id_masjid', $this->idMasjid)->orderBy('name')->get();
            }
        } else {
            $profil = $currentUser->profil;
            if ($profil) {
                $groupCategories = GroupCategory::where('id_masjid', $profil->id)->orderBy('name')->get();
            }
        }

        // Ringkasan per kategori dan total keseluruhan (mengikuti filter tanggal)
        $summaryCategoriesAdmin = [];
        $grandTotalsAdmin = ['sumMasuk' => 0, 'sumKeluar' => 0, 'ending' => 0];
        if ($isAdmin && $this->idMasjid) {
            $categoriesForSummary = GroupCategory::where('id_masjid', $this->idMasjid)->orderBy('name')->get();
            foreach ($categoriesForSummary as $cat) {
                $aggQuery = DB::table('laporans')
                    ->where('id_masjid', $this->idMasjid)
                    ->where('id_group_category', $cat->id);
                if ($this->filterDateMode === 'hari' && !empty($this->filterDay)) {
                    $aggQuery->whereDate('tanggal', $this->filterDay);
                } elseif ($this->filterDateMode === 'bulan' && !empty($this->filterMonth)) {
                    $year = substr($this->filterMonth, 0, 4);
                    $month = substr($this->filterMonth, 5, 2);
                    if (is_numeric($year) && is_numeric($month)) {
                        $aggQuery->whereYear('tanggal', (int) $year)->whereMonth('tanggal', (int) $month);
                    }
                } elseif ($this->filterDateMode === 'tahun' && !empty($this->filterYear) && is_numeric($this->filterYear)) {
                    $aggQuery->whereYear('tanggal', (int) $this->filterYear);
                }
                $agg = $aggQuery->selectRaw(
                    'COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE 0 END), 0) AS sum_masuk, ' .
                    'COALESCE(SUM(CASE WHEN jenis = "keluar" THEN saldo ELSE 0 END), 0) AS sum_keluar'
                )->first();
                $sumMasuk = (int) ($agg->sum_masuk ?? 0);
                $sumKeluar = (int) ($agg->sum_keluar ?? 0);
                $ending = $sumMasuk - $sumKeluar;
                $summaryCategoriesAdmin[] = [
                    'categoryId' => $cat->id,
                    'categoryName' => $cat->name,
                    'sumMasuk' => $sumMasuk,
                    'sumKeluar' => $sumKeluar,
                    'ending' => $ending,
                ];
                $grandTotalsAdmin['sumMasuk'] += $sumMasuk;
                $grandTotalsAdmin['sumKeluar'] += $sumKeluar;
                $grandTotalsAdmin['ending'] += $ending;
            }
        }

        $summaryCategoriesNonAdmin = [];
        $grandTotalsNonAdmin = ['sumMasuk' => 0, 'sumKeluar' => 0, 'ending' => 0];
        if (!$isAdmin) {
            $profil = $currentUser->profil;
            if ($profil) {
                $categoriesForSummary = GroupCategory::where('id_masjid', $profil->id)->orderBy('name')->get();
                foreach ($categoriesForSummary as $cat) {
                    $aggQuery = DB::table('laporans')
                        ->where('id_masjid', $profil->id)
                        ->where('id_group_category', $cat->id);
                    if ($this->filterDateMode === 'hari' && !empty($this->filterDay)) {
                        $aggQuery->whereDate('tanggal', $this->filterDay);
                    } elseif ($this->filterDateMode === 'bulan' && !empty($this->filterMonth)) {
                        $year = substr($this->filterMonth, 0, 4);
                        $month = substr($this->filterMonth, 5, 2);
                        if (is_numeric($year) && is_numeric($month)) {
                            $aggQuery->whereYear('tanggal', (int) $year)->whereMonth('tanggal', (int) $month);
                        }
                    } elseif ($this->filterDateMode === 'tahun' && !empty($this->filterYear) && is_numeric($this->filterYear)) {
                        $aggQuery->whereYear('tanggal', (int) $this->filterYear);
                    }
                    $agg = $aggQuery->selectRaw(
                        'COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE 0 END), 0) AS sum_masuk, ' .
                        'COALESCE(SUM(CASE WHEN jenis = "keluar" THEN saldo ELSE 0 END), 0) AS sum_keluar'
                    )->first();
                    $sumMasuk = (int) ($agg->sum_masuk ?? 0);
                    $sumKeluar = (int) ($agg->sum_keluar ?? 0);
                    $ending = $sumMasuk - $sumKeluar;
                    $summaryCategoriesNonAdmin[] = [
                        'categoryId' => $cat->id,
                        'categoryName' => $cat->name,
                        'sumMasuk' => $sumMasuk,
                        'sumKeluar' => $sumKeluar,
                        'ending' => $ending,
                    ];
                    $grandTotalsNonAdmin['sumMasuk'] += $sumMasuk;
                    $grandTotalsNonAdmin['sumKeluar'] += $sumKeluar;
                    $grandTotalsNonAdmin['ending'] += $ending;
                }
            }
        }

        // Persiapan data terhitung dengan paginasi per kategori
        $computedGroups = [];
        $computedCategoryGroupsNonAdmin = [];

        if ($isAdmin) {
            // Ambil daftar masjid yang memiliki data sesuai filter
            $masjidIds = (clone $baseQuery)
                // hilangkan orderBy('tanggal') agar kompatibel dengan DISTINCT
                ->reorder()
                ->select('id_masjid')
                ->distinct()
                ->orderBy('id_masjid')
                ->pluck('id_masjid');
            foreach ($masjidIds as $masjidId) {
                $masjidName = optional(Profil::find($masjidId))->name;

                // Ambil kategori yang memiliki data pada masjid ini sesuai filter
                $categoryIds = (clone $baseQuery)
                    ->where('id_masjid', $masjidId)
                    // hilangkan orderBy('tanggal') agar kompatibel dengan DISTINCT
                    ->reorder()
                    ->select('id_group_category')
                    ->distinct()
                    ->orderBy('id_group_category')
                    ->pluck('id_group_category');

                $categoryBlocks = [];
                foreach ($categoryIds as $catId) {
                    $catQuery = (clone $baseQuery)
                        ->where('id_masjid', $masjidId)
                        ->where('id_group_category', $catId);
                    // Terapkan pencarian lokal per kategori (admin)
                    $localSearchAdmin = $this->searchPerCategory[$masjidId][$catId] ?? null;
                    if (!empty($localSearchAdmin)) {
                        $catQuery->where('uraian', 'like', '%' . $localSearchAdmin . '%');
                    }
                    // Paginasi per kategori dengan pageName unik per masjid+kategori
                    $pageName = 'page_' . $masjidId . '_' . $catId;
                    $perPage = $this->paginatePerCategory[$masjidId][$catId] ?? $this->paginate;
                    $paginator = $catQuery->paginate($perPage, ['*'], $pageName);
                    $startNo = ((int) $paginator->currentPage() - 1) * (int) $paginator->perPage();

                    $runningBalance = 0;
                    $totalMasuk = 0;
                    $totalKeluar = 0;
                    $rows = [];

                    foreach ($paginator->items() as $i => $laporan) {
                        $masuk = 0;
                        $keluar = 0;
                        if ($laporan->is_opening) {
                            $masuk = $laporan->saldo;
                        } else {
                            if ($laporan->jenis === 'masuk') {
                                $masuk = $laporan->saldo;
                            } elseif ($laporan->jenis === 'keluar') {
                                $keluar = $laporan->saldo;
                            }
                        }
                        $totalMasuk += $masuk;
                        $totalKeluar += $keluar;
                        $runningBalance += $masuk - $keluar;

                        $rows[] = [
                            'id' => $laporan->id,
                            'no' => (int) $startNo + (int) $i + 1,
                            'tanggal' => Carbon::parse($laporan->tanggal)->format('d/m/Y'),
                            'uraian' => $laporan->is_opening ? ($laporan->uraian ?: 'Sisa bulan yang lalu') : $laporan->uraian,
                            'is_opening' => (bool) $laporan->is_opening,
                            'groupCategoryName' => optional($laporan->groupCategory)->name ?? '-',
                            'masukDisplay' => $masuk > 0 ? number_format($masuk, 0, ',', '.') : '-',
                            'keluarDisplay' => $keluar > 0 ? number_format($keluar, 0, ',', '.') : '-',
                            'runningBalanceDisplay' => number_format((int) $laporan->running_balance_sql, 0, ',', '.'),
                        ];
                    }

                    // Ambil total dari tb_balance (persisted in DB)
                    $agg = DB::table('tb_balance')
                        ->where('id_masjid', $masjidId)
                        ->where('id_group_category', $catId)
                        ->first();
                    $sumMasukAll = $agg?->total_masuk ?? 0;
                    $sumKeluarAll = $agg?->total_keluar ?? 0;
                    $endingBalanceAll = $agg?->ending_balance ?? 0;

                    $categoryBlocks[] = [
                        'categoryId' => $catId,
                        'categoryName' => optional(GroupCategory::find($catId))->name ?? '-',
                        'items' => $rows,
                        'totals' => [
                            'totalMasukDisplay' => number_format($sumMasukAll, 0, ',', '.'),
                            'totalKeluarDisplay' => number_format($sumKeluarAll, 0, ',', '.'),
                            'endingBalanceDisplay' => number_format($endingBalanceAll, 0, ',', '.'),
                        ],
                        'paginator' => $paginator,
                    ];
                }

                $computedGroups[] = [
                    'masjidId' => $masjidId,
                    'masjidName' => $masjidName,
                    'categories' => $categoryBlocks,
                ];
            }
        } else {
            // Non-admin: paginasi per kategori pada masjid milik user
            $profil = $currentUser->profil;
            if ($profil) {
                $categoryIds = (clone $baseQuery)
                    ->where('id_masjid', $profil->id)
                    // hilangkan orderBy('tanggal') agar kompatibel dengan DISTINCT
                    ->reorder()
                    ->select('id_group_category')
                    ->distinct()
                    ->orderBy('id_group_category')
                    ->pluck('id_group_category');

                foreach ($categoryIds as $catId) {
                    $catQuery = (clone $baseQuery)
                        ->where('id_masjid', $profil->id)
                        ->where('id_group_category', $catId);
                    // Terapkan pencarian lokal per kategori (non-admin)
                    $localSearchNonAdmin = $this->searchPerCategoryNonAdmin[$catId] ?? null;
                    if (!empty($localSearchNonAdmin)) {
                        $catQuery->where('uraian', 'like', '%' . $localSearchNonAdmin . '%');
                    }

                    $pageName = 'page_cat_' . $catId;
                    $perPageNonAdmin = $this->paginatePerCategoryNonAdmin[$catId] ?? $this->paginate;
                    $paginator = $catQuery->paginate($perPageNonAdmin, ['*'], $pageName);
                    $startNo = ((int) $paginator->currentPage() - 1) * (int) $paginator->perPage();

                    $runningBalance = 0;
                    $totalMasuk = 0;
                    $totalKeluar = 0;
                    $rows = [];

                    foreach ($paginator->items() as $i => $laporan) {
                        $masuk = 0;
                        $keluar = 0;
                        if ($laporan->is_opening) {
                            $masuk = $laporan->saldo;
                        } else {
                            if ($laporan->jenis === 'masuk') {
                                $masuk = $laporan->saldo;
                            } elseif ($laporan->jenis === 'keluar') {
                                $keluar = $laporan->saldo;
                            }
                        }
                        $totalMasuk += $masuk;
                        $totalKeluar += $keluar;
                        $runningBalance += $masuk - $keluar;

                        $rows[] = [
                            'id' => $laporan->id,
                            'no' => (int) $startNo + (int) $i + 1,
                            'tanggal' => Carbon::parse($laporan->tanggal)->format('d/m/Y'),
                            'uraian' => $laporan->is_opening ? ($laporan->uraian ?: 'Sisa bulan yang lalu') : $laporan->uraian,
                            'is_opening' => (bool) $laporan->is_opening,
                            'groupCategoryName' => optional($laporan->groupCategory)->name ?? '-',
                            'masukDisplay' => $masuk > 0 ? number_format($masuk, 0, ',', '.') : '-',
                            'keluarDisplay' => $keluar > 0 ? number_format($keluar, 0, ',', '.') : '-',
                            'runningBalanceDisplay' => number_format((int) $laporan->running_balance_sql, 0, ',', '.'),
                        ];
                    }

                    // Ambil total dari tb_balance (persisted in DB) untuk non-admin
                    $agg = DB::table('tb_balance')
                        ->where('id_masjid', $profil->id)
                        ->where('id_group_category', $catId)
                        ->first();
                    $sumMasukAll = $agg?->total_masuk ?? 0;
                    $sumKeluarAll = $agg?->total_keluar ?? 0;
                    $endingBalanceAll = $agg?->ending_balance ?? 0;

                    $computedCategoryGroupsNonAdmin[] = [
                        'categoryId' => $catId,
                        'categoryName' => optional(GroupCategory::find($catId))->name ?? '-',
                        'items' => $rows,
                        'totals' => [
                            'totalMasukDisplay' => number_format($sumMasukAll, 0, ',', '.'),
                            'totalKeluarDisplay' => number_format($sumKeluarAll, 0, ',', '.'),
                            'endingBalanceDisplay' => number_format($endingBalanceAll, 0, ',', '.'),
                        ],
                        'paginator' => $paginator,
                    ];
                }
            }
        }

        return view('livewire.laporan.keuangan', [
            'isAdmin' => $isAdmin,
            'profils' => $profils,
            'groupCategories' => $groupCategories,
            'computedGroups' => $computedGroups,
            'computedCategoryGroupsNonAdmin' => $computedCategoryGroupsNonAdmin,
            'summaryCategoriesAdmin' => $summaryCategoriesAdmin,
            'grandTotalsAdmin' => $grandTotalsAdmin,
            'summaryCategoriesNonAdmin' => $summaryCategoriesNonAdmin,
            'grandTotalsNonAdmin' => $grandTotalsNonAdmin,
        ]);
    }

    public function edit($id)
    {
        $laporan = ModelsLaporan::findOrFail($id);
        $this->authorize('update', $laporan);

        $this->laporanId = $laporan->id;
        $this->idMasjid = $laporan->id_masjid;
        $this->idGroupCategory = $laporan->id_group_category;
        $this->tanggal = Carbon::parse($laporan->tanggal)->format('Y-m-d');
        $this->uraian = $laporan->uraian;
        $this->jenis = $laporan->jenis;
        $this->saldo = $laporan->saldo;
        $this->isOpening = (bool) $laporan->is_opening;

        $this->isEdit = true;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function save()
    {
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);

        // Jika saldo awal, set jenis menjadi 'masuk' dan uraian default jika kosong
        if ($this->isOpening) {
            $this->jenis = 'masuk';
            if (!$this->uraian) {
                $this->uraian = 'Sisa bulan yang lalu';
            }
        }

        // Untuk non-admin, pastikan idMasjid sesuai profil user
        if (!$isAdmin) {
            $profil = $currentUser->profil;
            if ($profil) {
                $this->idMasjid = $profil->id;
            }
        }

        $validated = $this->validate();

        if ($this->isEdit && $this->laporanId) {
            $laporan = ModelsLaporan::findOrFail($this->laporanId);
            $this->authorize('update', $laporan);
        } else {
            $this->authorize('create', ModelsLaporan::class);
            $laporan = new ModelsLaporan();
        }

        $laporan->id_masjid = $validated['idMasjid'];
        $laporan->id_group_category = $validated['idGroupCategory'];
        $laporan->tanggal = $validated['tanggal'];
        $laporan->uraian = $validated['uraian'];
        $laporan->jenis = $validated['jenis'];
        $laporan->saldo = $validated['saldo'];
        $laporan->is_opening = $this->isOpening ? 1 : 0;
        $laporan->save();

        $this->resetFormFields();
        $this->isEdit = false;
        $this->showForm = false;
        $this->showTable = true;
    }

    public function cancelForm()
    {
        $this->resetFormFields();
        $this->isEdit = false;
        $this->showForm = false;
        $this->showTable = true;
    }

    public function delete($id)
    {
        $laporan = ModelsLaporan::findOrFail($id);
        $this->authorize('delete', $laporan);

        // Set data untuk modal konfirmasi hapus, jangan hapus langsung
        $this->deleteLaporanId = $laporan->id;
        $this->deleteLaporanName = optional($laporan->profil)->name ?? 'Tidak diketahui';
    }

    public function destroyLaporan()
    {
        if (!$this->deleteLaporanId) {
            return;
        }

        $laporan = ModelsLaporan::findOrFail($this->deleteLaporanId);
        $this->authorize('delete', $laporan);
        $laporan->delete();

        // Reset state dan tutup modal
        $this->deleteLaporanId = null;
        $this->deleteLaporanName = null;
        $this->dispatch('closeDeleteModal');
    }

    private function resetFormFields()
    {
        $this->resetValidation();
        $this->reset([
            'laporanId',
            'idMasjid',
            'idGroupCategory',
            'tanggal',
            'uraian',
            'jenis',
            'saldo',
            'isOpening',
        ]);
    }
}
