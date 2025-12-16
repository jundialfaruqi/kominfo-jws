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

        $perPage = 10;
        $today = Carbon::today();
        $fromDate = $today->copy()->subDays(7);

        $paginator = Laporan::where('id_masjid', $profil->id)
            ->whereDate('tanggal', '>=', $fromDate->format('Y-m-d'))
            ->whereDate('tanggal', '<=', $today->format('Y-m-d'))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->select('id', 'id_group_category', 'tanggal', 'uraian', 'jenis', 'saldo', 'is_opening')
            ->paginate($perPage);

        $transformed = $paginator->getCollection()->map(function ($l) {
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
        });
        $paginator->setCollection($transformed);

        $allItems = Laporan::where('id_masjid', $profil->id)
            ->whereDate('tanggal', '>=', $fromDate->format('Y-m-d'))
            ->whereDate('tanggal', '<=', $today->format('Y-m-d'))
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->select('id', 'id_group_category', 'tanggal', 'uraian', 'jenis', 'saldo', 'is_opening')
            ->get();

        $grouped = $allItems->groupBy('id_group_category');
        $groups = $grouped->map(function ($items, $gcId) {
            $gc = $gcId ? GroupCategory::find($gcId) : null;
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
            })->values()->all();
            return [
                'group_category_id' => (int) ($gcId ?? 0),
                'group_category_name' => $gc?->name ?? '-',
                'items' => $mappedItems,
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar laporan keuangan 8 hari terakhir',
            'data' => [
                'items' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'groups' => $groups,
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
