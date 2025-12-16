<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use App\Models\Laporan;
use App\Models\Profil;
use App\Models\GroupCategory;
use App\Events\ContentUpdatedEvent;

class MyLaporanKeuanganController extends Controller
{
    public function list(Request $request)
    {
        $user = $request->user();
        if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $profil = Profil::where('user_id', $user->id)->first();
        if (!$profil) {
            return response()->json([
                'success' => true,
                'message' => 'User belum memiliki profil masjid',
                'data' => [
                    'items' => [],
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'last_page' => 1,
                ],
            ]);
        }

        $now = Carbon::now();
        $recentDays = (int) ($request->query('recent_days', 0));
        $year = (int) ($request->query('year', $now->year));
        $month = (int) ($request->query('month', $now->month));
        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }
        // Ambil semua item berdasarkan mode: recent atau bulan berjalan
        $queryBase = Laporan::where('id_masjid', $profil->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->select('id', 'id_group_category', 'tanggal', 'uraian', 'jenis', 'saldo', 'is_opening');
        if ($recentDays > 0) {
            $start = $now->copy()->subDays($recentDays)->toDateString();
            $end = $now->toDateString();
            $queryBase->whereDate('tanggal', '>=', $start)->whereDate('tanggal', '<=', $end);
        } else {
            $queryBase->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
        }
        $itemsRaw = $queryBase->get();
        $items = $itemsRaw->map(function ($l) {
            $gc = $l->id_group_category ? GroupCategory::find($l->id_group_category) : null;
            return [
                'id' => (int) $l->id,
                'tanggal' => $l->tanggal,
                'uraian' => $l->uraian,
                'jenis' => $l->is_opening ? 'masuk' : ($l->jenis ?? 'masuk'),
                'is_opening' => (bool) $l->is_opening,
                'saldo' => (int) $l->saldo,
                'group_category_id' => (int) ($l->id_group_category ?? 0),
                'group_category_name' => $gc?->name ?? '-',
            ];
        })->values()->all();

        $allItems = clone $queryBase;
        $allItems = $allItems->get();

        $grouped = $allItems->groupBy('id_group_category');
        $groupsArray = [];
        foreach ($grouped as $gcId => $items) {
            $gc = $gcId ? GroupCategory::find($gcId) : null;
            // Sort items by tanggal desc, id desc already applied at query level
            $mappedItems = collect($items)->map(function ($l) use ($gc, $gcId) {
                return [
                    'id' => (int) $l->id,
                    'tanggal' => $l->tanggal,
                    'uraian' => $l->uraian,
                    'jenis' => $l->is_opening ? 'masuk' : ($l->jenis ?? 'masuk'),
                    'is_opening' => (bool) $l->is_opening,
                    'saldo' => (int) $l->saldo,
                    'group_category_id' => (int) ($gcId ?? 0),
                    'group_category_name' => $gc?->name ?? '-',
                ];
            })->values();
            // Monthly sums (opening treated as 'masuk')
            $sumMasukMonth = $mappedItems->reduce(function ($carry, $row) {
                $jenisNormalized = ($row['is_opening'] === true || $row['jenis'] === 'masuk') ? 'masuk' : 'keluar';
                return $carry + ($jenisNormalized === 'masuk' ? (int) $row['saldo'] : 0);
            }, 0);
            $sumKeluarMonth = $mappedItems->reduce(function ($carry, $row) {
                $jenisNormalized = ($row['is_opening'] === true || $row['jenis'] === 'masuk') ? 'masuk' : 'keluar';
                return $carry + ($jenisNormalized === 'keluar' ? (int) $row['saldo'] : 0);
            }, 0);
            // Total saldo all-time for this group category (masuk - keluar)
            $sumAllMasuk = Laporan::where('id_masjid', $profil->id)
                ->where('id_group_category', $gcId)
                ->where(function ($q) {
                    $q->where('is_opening', 1)->orWhere('jenis', 'masuk');
                })
                ->sum('saldo');
            $sumAllKeluar = Laporan::where('id_masjid', $profil->id)
                ->where('id_group_category', $gcId)
                ->where('is_opening', '!=', 1)
                ->where('jenis', 'keluar')
                ->sum('saldo');
            $totalSaldoAll = (int) $sumAllMasuk - (int) $sumAllKeluar;
            $groupsArray[] = [
                'group_category_id' => (int) ($gcId ?? 0),
                'group_category_name' => $gc?->name ?? '-',
                'items' => $mappedItems->values()->all(),
                'items_total' => $mappedItems->count(),
                'sum_masuk_month' => (int) $sumMasukMonth,
                'sum_keluar_month' => (int) $sumKeluarMonth,
                'total_saldo_all' => (int) $totalSaldoAll,
            ];
        }
        // Sort groups by name asc
        usort($groupsArray, function ($a, $b) {
            return strcmp($a['group_category_name'], $b['group_category_name']);
        });
        $groups = $groupsArray;

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar laporan keuangan',
            'data' => [
                'items' => $items,
                'groups' => $groups,
                'mode' => $recentDays > 0 ? 'recent' : 'month',
                'recent_days' => $recentDays > 0 ? $recentDays : null,
                'year' => $recentDays > 0 ? null : $year,
                'month' => $recentDays > 0 ? null : $month,
            ],
        ]);
    }

    public function graph(Request $request)
    {
        $user = $request->user();
        if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $profil = Profil::where('user_id', $user->id)->first();
        if (!$profil) {
            return response()->json([
                'success' => true,
                'message' => 'User belum memiliki profil masjid',
                'data' => [
                    'labels' => [],
                    'masuk' => [],
                    'keluar' => [],
                    'total_masuk' => 0,
                    'total_keluar' => 0,
                    'net_total' => 0,
                ],
            ]);
        }

        $now = Carbon::now();
        $recentDays = (int) ($request->query('recent_days', 0));
        $year = (int) ($request->query('year', $now->year));
        $month = (int) ($request->query('month', $now->month));
        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        $startDate = null;
        $endDate = null;
        $mode = 'month';
        if ($recentDays > 0) {
            $mode = 'recent';
            $startDate = $now->copy()->subDays($recentDays - 1)->startOfDay();
            $endDate = $now->copy()->endOfDay();
        } else {
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        }

        $rows = Laporan::where('id_masjid', $profil->id)
            ->whereDate('tanggal', '>=', $startDate->toDateString())
            ->whereDate('tanggal', '<=', $endDate->toDateString())
            ->orderBy('tanggal', 'asc')
            ->get(['tanggal', 'jenis', 'saldo', 'is_opening']);

        $labels = [];
        $sumMasuk = [];
        $sumKeluar = [];

        $cursor = $startDate->copy();
        while ($cursor <= $endDate) {
            $key = $cursor->toDateString();
            $labels[] = $key;
            $sumMasuk[$key] = 0;
            $sumKeluar[$key] = 0;
            $cursor->addDay();
        }

        foreach ($rows as $r) {
            $key = Carbon::parse($r->tanggal)->toDateString();
            $jenisNormalized = ($r->is_opening == 1 || $r->jenis === 'masuk') ? 'masuk' : 'keluar';
            if (!isset($sumMasuk[$key])) {
                $sumMasuk[$key] = 0;
            }
            if (!isset($sumKeluar[$key])) {
                $sumKeluar[$key] = 0;
            }
            if ($jenisNormalized === 'masuk') {
                $sumMasuk[$key] += (int) $r->saldo;
            } else {
                $sumKeluar[$key] += (int) $r->saldo;
            }
        }

        $masukSeries = [];
        $keluarSeries = [];
        $totalMasuk = 0;
        $totalKeluar = 0;
        foreach ($labels as $d) {
            $m = (int) ($sumMasuk[$d] ?? 0);
            $k = (int) ($sumKeluar[$d] ?? 0);
            $masukSeries[] = $m;
            $keluarSeries[] = $k;
            $totalMasuk += $m;
            $totalKeluar += $k;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data grafik laporan keuangan',
            'data' => [
                'labels' => $labels,
                'masuk' => $masukSeries,
                'keluar' => $keluarSeries,
                'total_masuk' => (int) $totalMasuk,
                'total_keluar' => (int) $totalKeluar,
                'net_total' => (int) ($totalMasuk - $totalKeluar),
                'mode' => $mode,
                'recent_days' => $recentDays > 0 ? $recentDays : null,
                'year' => $recentDays > 0 ? null : $year,
                'month' => $recentDays > 0 ? null : $month,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $profil = Profil::where('user_id', $user->id)->first();
        if (!$profil) {
            return response()->json([
                'success' => false,
                'message' => 'Profil masjid tidak ditemukan',
            ], 404);
        }

        $rules = [
            'id_group_category' => 'required|exists:group_categories,id',
            'tanggal' => 'required|date',
            'uraian' => 'required|string|max:255',
            'jenis' => 'nullable|in:masuk,keluar',
            'saldo' => 'required|integer|min:1',
            'is_opening' => 'sometimes|boolean',
        ];
        $messages = [
            'id_group_category.required' => 'Group Category wajib dipilih',
            'id_group_category.exists' => 'Group Category tidak ditemukan',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'uraian.required' => 'Uraian wajib diisi',
            'uraian.max' => 'Uraian terlalu panjang',
            'jenis.in' => 'Jenis transaksi harus masuk atau keluar',
            'saldo.required' => 'Nominal wajib diisi',
            'saldo.integer' => 'Nominal harus berupa angka bulat',
            'saldo.min' => 'Nominal minimal 1',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi input gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }
        $validated = $validator->validated();

        $isOpening = (bool) ($validated['is_opening'] ?? false);
        $jenis = $validated['jenis'] ?? 'masuk';
        if ($isOpening) {
            $jenis = 'masuk';
        }

        $lap = new Laporan();
        $lap->id_masjid = $profil->id;
        $lap->id_group_category = (int) $validated['id_group_category'];
        $lap->tanggal = $validated['tanggal'];
        $lap->uraian = $validated['uraian'];
        $lap->jenis = $jenis;
        $lap->saldo = (int) $validated['saldo'];
        $lap->is_opening = $isOpening ? 1 : 0;
        $lap->save();

        $gc = GroupCategory::find($lap->id_group_category);

        if (!empty($profil->slug)) {
            event(new ContentUpdatedEvent($profil->slug, 'laporan'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membuat laporan keuangan',
            'data' => [
                'id' => (int) $lap->id,
                'tanggal' => $lap->tanggal,
                'uraian' => $lap->uraian,
                'jenis' => $lap->is_opening ? 'masuk' : ($lap->jenis ?? 'masuk'),
                'is_opening' => (bool) $lap->is_opening,
                'saldo' => (int) $lap->saldo,
                'group_category_id' => (int) ($lap->id_group_category ?? 0),
                'group_category_name' => $gc?->name ?? '-',
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $profil = Profil::where('user_id', $user->id)->firstOrFail();
            $lap = Laporan::where('id_masjid', $profil->id)->findOrFail($id);

            $rules = [
                'id_group_category' => 'required|exists:group_categories,id',
                'tanggal' => 'required|date',
                'uraian' => 'required|string|max:255',
                'jenis' => 'nullable|in:masuk,keluar',
                'saldo' => 'required|integer|min:1',
                'is_opening' => 'sometimes|boolean',
            ];
            $messages = [
                'id_group_category.required' => 'Group Category wajib dipilih',
                'id_group_category.exists' => 'Group Category tidak ditemukan',
                'tanggal.required' => 'Tanggal wajib diisi',
                'tanggal.date' => 'Format tanggal tidak valid',
                'uraian.required' => 'Uraian wajib diisi',
                'uraian.max' => 'Uraian terlalu panjang',
                'jenis.in' => 'Jenis transaksi harus masuk atau keluar',
                'saldo.required' => 'Nominal wajib diisi',
                'saldo.integer' => 'Nominal harus berupa angka bulat',
                'saldo.min' => 'Nominal minimal 1',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi input gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $validated = $validator->validated();

            $isOpening = (bool) ($validated['is_opening'] ?? false);
            $jenis = $validated['jenis'] ?? 'masuk';
            if ($isOpening) {
                $jenis = 'masuk';
            }

            $lap->id_group_category = (int) $validated['id_group_category'];
            $lap->tanggal = $validated['tanggal'];
            $lap->uraian = $validated['uraian'];
            $lap->jenis = $jenis;
            $lap->saldo = (int) $validated['saldo'];
            $lap->is_opening = $isOpening ? 1 : 0;
            $lap->save();

            $gc = GroupCategory::find($lap->id_group_category);

            if (!empty($profil->slug)) {
                event(new ContentUpdatedEvent($profil->slug, 'laporan'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate laporan keuangan',
                'data' => [
                    'id' => (int) $lap->id,
                    'tanggal' => $lap->tanggal,
                    'uraian' => $lap->uraian,
                    'jenis' => $lap->is_opening ? 'masuk' : ($lap->jenis ?? 'masuk'),
                    'is_opening' => (bool) $lap->is_opening,
                    'saldo' => (int) $lap->saldo,
                    'group_category_id' => (int) ($lap->id_group_category ?? 0),
                    'group_category_name' => $gc?->name ?? '-',
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $profil = Profil::where('user_id', $user->id)->firstOrFail();
            $lap = Laporan::where('id_masjid', $profil->id)->findOrFail($id);
            $lap->delete();

            if (!empty($profil->slug)) {
                event(new ContentUpdatedEvent($profil->slug, 'laporan'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus laporan keuangan',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }
    }
}
