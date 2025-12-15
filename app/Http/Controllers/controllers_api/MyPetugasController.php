<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Petugas;
use App\Models\Profil;
use App\Events\ContentUpdatedEvent;

class MyPetugasController extends Controller
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
        $perPage = 10;
        $paginator = Petugas::where('user_id', $user->id)
            ->select('id', 'hari', 'khatib', 'imam', 'muadzin')
            ->orderBy('hari', 'asc')
            ->paginate($perPage);

        $transformed = $paginator->getCollection()->map(function ($p) {
            return [
                'id' => $p->id,
                'hari' => $p->hari,
                'khatib' => $p->khatib,
                'imam' => $p->imam,
                'muadzin' => $p->muadzin,
            ];
        });
        $paginator->setCollection($transformed);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar petugas',
            'data' => [
                'items' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
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

        $rules = [
            'hari' => 'required|date',
            'khatib' => 'required|string|max:255',
            'imam' => 'required|string|max:255',
            'muadzin' => 'required|string|max:255',
        ];
        $messages = [
            'hari.required' => 'Hari wajib diisi',
            'hari.date' => 'Format tanggal tidak valid',
            'khatib.required' => 'Khatib wajib diisi',
            'imam.required' => 'Imam wajib diisi',
            'muadzin.required' => 'Muadzin wajib diisi',
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

        $dayOfWeek = date('N', strtotime($validated['hari']));
        if ($dayOfWeek != 5) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal yang dipilih bukan Hari Jum\'at',
                'errors' => ['hari' => ['Tanggal harus di Hari Jum\'at']],
            ], 422);
        }

        $existsSameDay = Petugas::where('user_id', $user->id)
            ->where('hari', $validated['hari'])
            ->exists();
        if ($existsSameDay) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal ini sudah digunakan untuk petugas lain',
                'errors' => ['hari' => ['Tanggal sudah terpakai, pilih Jumat di tanggal lain']],
            ], 422);
        }

        $petugas = new Petugas();
        $petugas->user_id = $user->id;
        $petugas->hari = $validated['hari'];
        $petugas->khatib = $validated['khatib'];
        $petugas->imam = $validated['imam'];
        $petugas->muadzin = $validated['muadzin'];
        $petugas->save();

        $profil = Profil::where('user_id', $user->id)->first();
        if ($profil) event(new ContentUpdatedEvent($profil->slug, 'petugas'));

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membuat petugas',
            'data' => [
                'id' => $petugas->id,
                'hari' => $petugas->hari,
                'khatib' => $petugas->khatib,
                'imam' => $petugas->imam,
                'muadzin' => $petugas->muadzin,
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

            $petugas = Petugas::where('user_id', $user->id)->findOrFail($id);

            $rules = [
                'hari' => 'required|date',
                'khatib' => 'required|string|max:255',
                'imam' => 'required|string|max:255',
                'muadzin' => 'required|string|max:255',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi input gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $validated = $validator->validated();

            $dayOfWeek = date('N', strtotime($validated['hari']));
            if ($dayOfWeek != 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal yang dipilih bukan Hari Jum\'at',
                    'errors' => ['hari' => ['Tanggal harus di Hari Jum\'at']],
                ], 422);
            }

            $existsSameDay = Petugas::where('user_id', $user->id)
                ->where('hari', $validated['hari'])
                ->where('id', '!=', $petugas->id)
                ->exists();
            if ($existsSameDay) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal ini sudah digunakan untuk petugas lain',
                    'errors' => ['hari' => ['Tanggal sudah terpakai, pilih Jumat di tanggal lain']],
                ], 422);
            }

            $petugas->hari = $validated['hari'];
            $petugas->khatib = $validated['khatib'];
            $petugas->imam = $validated['imam'];
            $petugas->muadzin = $validated['muadzin'];
            $petugas->save();

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'petugas'));

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate petugas',
                'data' => [
                    'id' => $petugas->id,
                    'hari' => $petugas->hari,
                    'khatib' => $petugas->khatib,
                    'imam' => $petugas->imam,
                    'muadzin' => $petugas->muadzin,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Petugas tidak ditemukan',
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

            $petugas = Petugas::where('user_id', $user->id)->findOrFail($id);
            $petugas->delete();

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'petugas'));

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus petugas',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Petugas tidak ditemukan',
            ], 404);
        }
    }
}
