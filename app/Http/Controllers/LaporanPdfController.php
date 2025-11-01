<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use App\Models\Profil;
use App\Models\GroupCategory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPdfController extends Controller
{
    public function downloadPdf7Hari(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Tentukan masjid/profil
        $idMasjid = null;
        $isAdmin = in_array($user->role, ['Super Admin', 'Admin']);
        if ($isAdmin) {
            $idMasjid = $request->input('idMasjid');
            if (empty($idMasjid)) {
                return back()->with('error', 'Profil Masjid harus dipilih untuk export PDF 7 hari.');
            }
        } else {
            $profil = $user->profil;
            if (!$profil) {
                return back()->with('error', 'Profil Masjid tidak ditemukan untuk pengguna ini.');
            }
            $idMasjid = $profil->id;
        }

        $masjid = Profil::find($idMasjid);
        if (!$masjid) {
            return back()->with('error', 'Profil Masjid tidak ditemukan.');
        }

        // Range 7 hari terakhir (hari ini sampai 6 hari ke belakang)
        $endDate = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $periodStartDate = Carbon::today('Asia/Jakarta')->subDays(6)->format('Y-m-d');

        $categories = GroupCategory::where('id_masjid', $idMasjid)->orderBy('name')->get();

        $summaryCategoriesAdmin = [];
        $grandTotalsAdmin = ['sumMasuk' => 0, 'sumKeluar' => 0, 'ending' => 0];

        $group = [
            'masjidId' => $masjid->id,
            'masjidName' => $masjid->name,
            'categories' => [],
        ];

        foreach ($categories as $cat) {
            // Ambil transaksi dalam 7 hari ini
            $rowsRaw = DB::table('laporans')
                ->where('id_masjid', $idMasjid)
                ->where('id_group_category', $cat->id)
                ->whereBetween('tanggal', [$periodStartDate, $endDate])
                ->orderByRaw("CASE WHEN is_opening = 1 OR jenis = 'masuk' THEN 0 ELSE 1 END")
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get(['id', 'tanggal', 'uraian', 'jenis', 'saldo', 'is_opening']);

            // Numbering per jenis (masuk/keluar) sepanjang periode 7 hari
            $masukNo = 0;
            $keluarNo = 0;
            $rows = [];
            foreach ($rowsRaw as $r) {
                $jenisNormalized = ($r->is_opening == 1 || $r->jenis === 'masuk') ? 'masuk' : 'keluar';
                if ($jenisNormalized === 'masuk') {
                    $masukNo++;
                    $no = $masukNo;
                } else {
                    $keluarNo++;
                    $no = $keluarNo;
                }
                $rows[] = [
                    'id' => $r->id,
                    'tanggal' => $r->tanggal,
                    'uraian' => $r->uraian,
                    'jenis' => $jenisNormalized,
                    'saldo' => (int) $r->saldo,
                    'isOpening' => (int) $r->is_opening,
                    'no' => $no,
                ];
            }

            // Aggregasi periode 7 hari
            $agg = DB::table('laporans')
                ->where('id_masjid', $idMasjid)
                ->where('id_group_category', $cat->id)
                ->whereBetween('tanggal', [$periodStartDate, $endDate])
                ->selectRaw(
                    'COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE 0 END), 0) AS sum_masuk, ' .
                        'COALESCE(SUM(CASE WHEN jenis = "keluar" THEN saldo ELSE 0 END), 0) AS sum_keluar'
                )
                ->first();
            $sumMasuk = (int) ($agg->sum_masuk ?? 0);
            $sumKeluar = (int) ($agg->sum_keluar ?? 0);

            // Opening sebelum periode
            $openingAgg = DB::table('laporans')
                ->where('id_masjid', $idMasjid)
                ->where('id_group_category', $cat->id)
                ->where('tanggal', '<', $periodStartDate)
                ->selectRaw('COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE -saldo END), 0) AS opening')
                ->first();
            $opening = (int) ($openingAgg->opening ?? 0);

            $ending = $opening + $sumMasuk - $sumKeluar;

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

            $group['categories'][] = [
                'categoryId' => $cat->id,
                'categoryName' => $cat->name,
                'rows' => $rows,
                'sumMasuk' => $sumMasuk,
                'sumKeluar' => $sumKeluar,
                'ending' => $ending,
            ];
        }

        // Hitung total sebelumnya (saldo sebelum periode, agregat semua kategori)
        $prevAgg = DB::table('laporans')
            ->where('id_masjid', $idMasjid)
            ->where('tanggal', '<', $periodStartDate)
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE 0 END), 0) AS sum_masuk, ' .
                    'COALESCE(SUM(CASE WHEN jenis = "keluar" THEN saldo ELSE 0 END), 0) AS sum_keluar'
            )
            ->first();
        $prevMasuk = (int) ($prevAgg->sum_masuk ?? 0);
        $prevKeluar = (int) ($prevAgg->sum_keluar ?? 0);
        $previousTotalsAdmin = [
            'sumMasuk' => $prevMasuk,
            'sumKeluar' => $prevKeluar,
            'ending' => $prevMasuk - $prevKeluar,
        ];

        // Label periode 7 hari untuk header PDF
        $filterLabel = Carbon::parse($periodStartDate)->translatedFormat('d M Y') . ' – ' . Carbon::parse($endDate)->translatedFormat('d M Y');

        $viewData = [
            'group' => $group,
            'summaryCategoriesAdmin' => $summaryCategoriesAdmin,
            'grandTotalsAdmin' => $grandTotalsAdmin,
            'previousTotalsAdmin' => $previousTotalsAdmin,
            'filterLabel' => $filterLabel,
            'masjid' => $masjid,
        ];

        $filename = 'laporan-keuangan-' . $masjid->name . '-7hari-' . Carbon::parse($endDate)->format('Y-m-d') . '.pdf';

        $dom = DomPDF::loadView('livewire.laporan.keuangan_pdf_7hari', $viewData)
            ->setPaper('a4', 'portrait');
        return $dom->download($filename);
    }
    public function downloadPdfBulan(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $filterMonth = $request->input('filterMonth'); // format Y-m
        if (empty($filterMonth)) {
            $filterMonth = Carbon::now()->format('Y-m');
        }

        // Validate month format
        try {
            $year = (int) substr($filterMonth, 0, 4);
            $month = (int) substr($filterMonth, 5, 2);
            $periodStartDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
        } catch (\Exception $e) {
            return back()->with('error', 'Format filter bulan tidak valid.');
        }

        // Determine masjid/profile
        $idMasjid = null;
        $isAdmin = in_array($user->role, ['Super Admin', 'Admin']);
        if ($isAdmin) {
            $idMasjid = $request->input('idMasjid');
            if (empty($idMasjid)) {
                return back()->with('error', 'Profil Masjid harus dipilih untuk export PDF bulanan.');
            }
        } else {
            $profil = $user->profil;
            if (!$profil) {
                return back()->with('error', 'Profil Masjid tidak ditemukan untuk pengguna ini.');
            }
            $idMasjid = $profil->id;
        }

        $masjid = Profil::find($idMasjid);
        if (!$masjid) {
            return back()->with('error', 'Profil Masjid tidak ditemukan.');
        }

        // Kategori untuk masjid ini
        $categories = GroupCategory::where('id_masjid', $idMasjid)->orderBy('name')->get();

        $summaryCategoriesAdmin = [];
        $grandTotalsAdmin = ['sumMasuk' => 0, 'sumKeluar' => 0, 'ending' => 0];

        $group = [
            'masjidId' => $masjid->id,
            'masjidName' => $masjid->name,
            'categories' => [],
        ];

        foreach ($categories as $cat) {
            // Ambil transaksi bulan ini untuk kategori
            $rowsRaw = DB::table('laporans')
                ->where('id_masjid', $idMasjid)
                ->where('id_group_category', $cat->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->orderByRaw("CASE WHEN is_opening = 1 OR jenis = 'masuk' THEN 0 ELSE 1 END")
                ->orderBy('tanggal', 'asc')
                ->orderBy('id', 'asc')
                ->get(['id', 'tanggal', 'uraian', 'jenis', 'saldo', 'is_opening']);

            // Numbering per jenis (masuk/keluar) secara kontinyu sepanjang bulan
            $masukNo = 0;
            $keluarNo = 0;
            $rows = [];
            foreach ($rowsRaw as $r) {
                $jenisNormalized = ($r->is_opening == 1 || $r->jenis === 'masuk') ? 'masuk' : 'keluar';
                if ($jenisNormalized === 'masuk') {
                    $masukNo++;
                    $no = $masukNo;
                } else {
                    $keluarNo++;
                    $no = $keluarNo;
                }
                $rows[] = [
                    'id' => $r->id,
                    'tanggal' => $r->tanggal,
                    'uraian' => $r->uraian,
                    'jenis' => $jenisNormalized,
                    'saldo' => (int) $r->saldo,
                    'isOpening' => (int) $r->is_opening,
                    'no' => $no,
                ];
            }

            // Aggregasi bulan ini
            $agg = DB::table('laporans')
                ->where('id_masjid', $idMasjid)
                ->where('id_group_category', $cat->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->selectRaw(
                    'COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE 0 END), 0) AS sum_masuk, ' .
                        'COALESCE(SUM(CASE WHEN jenis = "keluar" THEN saldo ELSE 0 END), 0) AS sum_keluar'
                )
                ->first();
            $sumMasuk = (int) ($agg->sum_masuk ?? 0);
            $sumKeluar = (int) ($agg->sum_keluar ?? 0);

            // Opening sebelum periode
            $openingAgg = DB::table('laporans')
                ->where('id_masjid', $idMasjid)
                ->where('id_group_category', $cat->id)
                ->where('tanggal', '<', $periodStartDate)
                ->selectRaw('COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE -saldo END), 0) AS opening')
                ->first();
            $opening = (int) ($openingAgg->opening ?? 0);

            $ending = $opening + $sumMasuk - $sumKeluar;

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

            $group['categories'][] = [
                'categoryId' => $cat->id,
                'categoryName' => $cat->name,
                'rows' => $rows,
                'sumMasuk' => $sumMasuk,
                'sumKeluar' => $sumKeluar,
                'ending' => $ending,
            ];
        }

        // Hitung total sebelumnya (saldo sebelum periode, agregat semua kategori)
        $prevAgg = DB::table('laporans')
            ->where('id_masjid', $idMasjid)
            ->where('tanggal', '<', $periodStartDate)
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = "masuk" THEN saldo ELSE 0 END), 0) AS sum_masuk, ' .
                    'COALESCE(SUM(CASE WHEN jenis = "keluar" THEN saldo ELSE 0 END), 0) AS sum_keluar'
            )
            ->first();
        $prevMasuk = (int) ($prevAgg->sum_masuk ?? 0);
        $prevKeluar = (int) ($prevAgg->sum_keluar ?? 0);
        $previousTotalsAdmin = [
            'sumMasuk' => $prevMasuk,
            'sumKeluar' => $prevKeluar,
            'ending' => $prevMasuk - $prevKeluar,
        ];

        // Label bulan untuk header PDF
        $filterLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        $viewData = [
            'group' => $group,
            'summaryCategoriesAdmin' => $summaryCategoriesAdmin,
            'grandTotalsAdmin' => $grandTotalsAdmin,
            'previousTotalsAdmin' => $previousTotalsAdmin,
            'filterMonth' => $filterMonth,
            'filterLabel' => $filterLabel,
            'masjid' => $masjid,
        ];

        $filename = 'laporan-keuangan-' . $masjid->name . '-' . $filterMonth . '.pdf';

        // Gunakan DomPDF sepenuhnya
        $dom = DomPDF::loadView('livewire.laporan.keuangan_pdf_bulan', $viewData)
            ->setPaper('a4', 'portrait');
        return $dom->download($filename);
    }
}
