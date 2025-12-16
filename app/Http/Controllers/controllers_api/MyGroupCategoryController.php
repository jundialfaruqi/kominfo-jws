<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\GroupCategory;
use App\Models\Profil;

class MyGroupCategoryController extends Controller
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
        $paginator = GroupCategory::where('id_masjid', $profil->id)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        $transformed = $paginator->getCollection()->map(function ($c) {
            return [
                'id' => (int) $c->id,
                'name' => $c->name,
            ];
        });
        $paginator->setCollection($transformed);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar group category',
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

        $profil = Profil::where('user_id', $user->id)->first();
        if (!$profil) {
            return response()->json([
                'success' => false,
                'message' => 'Profil masjid tidak ditemukan',
            ], 404);
        }

        $rules = [
            'name' => 'required|string|max:255',
        ];
        $messages = [
            'name.required' => 'Nama wajib diisi',
            'name.string' => 'Nama harus berupa teks',
            'name.max' => 'Nama maksimal 255 karakter',
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

        $existsSameName = GroupCategory::where('id_masjid', $profil->id)
            ->where('name', $validated['name'])
            ->exists();
        if ($existsSameName) {
            return response()->json([
                'success' => false,
                'message' => 'Nama kategori sudah digunakan',
                'errors' => ['name' => ['Nama kategori harus unik dalam profil ini']],
            ], 422);
        }

        $cat = new GroupCategory();
        $cat->id_masjid = $profil->id;
        $cat->name = $validated['name'];
        $cat->save();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membuat group category',
            'data' => [
                'id' => (int) $cat->id,
                'name' => $cat->name,
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
            $cat = GroupCategory::where('id_masjid', $profil->id)->findOrFail($id);

            $rules = [
                'name' => 'required|string|max:255',
            ];
            $messages = [
                'name.required' => 'Nama wajib diisi',
                'name.string' => 'Nama harus berupa teks',
                'name.max' => 'Nama maksimal 255 karakter',
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

            $existsSameName = GroupCategory::where('id_masjid', $profil->id)
                ->where('name', $validated['name'])
                ->where('id', '!=', $cat->id)
                ->exists();
            if ($existsSameName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama kategori sudah digunakan',
                    'errors' => ['name' => ['Nama kategori harus unik dalam profil ini']],
                ], 422);
            }

            $cat->name = $validated['name'];
            $cat->save();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate group category',
                'data' => [
                    'id' => (int) $cat->id,
                    'name' => $cat->name,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Group category tidak ditemukan',
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
            $cat = GroupCategory::where('id_masjid', $profil->id)->findOrFail($id);
            $cat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus group category',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Group category tidak ditemukan',
            ], 404);
        }
    }
}
