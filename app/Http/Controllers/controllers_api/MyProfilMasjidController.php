<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Events\ContentUpdatedEvent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MyProfilMasjidController extends Controller
{
    public function show(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $profilMasjid = Profil::where('user_id', $user->id)->firstOrFail()->makeVisible([
                'logo_masjid_url',
                'logo_pemerintah_url',
            ]);

            $data = [
                'id' => $profilMasjid->id,
                'name' => $profilMasjid->name,
                'address' => $profilMasjid->address,
                'phone' => $profilMasjid->phone,
                'slug' => $profilMasjid->slug,
                'logo_masjid' => $profilMasjid->logo_masjid,
                'logo_pemerintah' => $profilMasjid->logo_pemerintah,
                'logo_masjid_url' => $profilMasjid->logo_masjid_url,
                'logo_pemerintah_url' => $profilMasjid->logo_pemerintah_url,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data profil masjid',
                'data' => $data,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki profil masjid',
                'error' => [
                    'code' => 'PROFIL_NOT_FOUND',
                    'message' => 'Data profil masjid tidak ditemukan.',
                ],
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data profil masjid',
                'error' => [
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $existing = Profil::where('user_id', $user->id)->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil masjid sudah ada untuk user ini',
                    'error' => [
                        'code' => 'PROFIL_ALREADY_EXISTS',
                        'message' => 'Gunakan endpoint update untuk mengubah profil yang sudah ada.',
                    ],
                ], 409);
            }

            $profilMasjid = new Profil();
            $profilMasjid->user_id = $user->id;

            $rules = [
                'name' => 'required|string|max:100',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'slug' => 'nullable|alpha_dash|unique:profils,slug',
                'logo_masjid' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif|max:5000',
                'logo_pemerintah' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif|max:5000',
            ];

            $messages = [
                'name.required' => 'Nama masjid wajib diisi.',
                'name.string' => 'Nama masjid harus berupa teks.',
                'name.max' => 'Nama masjid maksimal 100 karakter.',
                'address.required' => 'Alamat wajib diisi.',
                'address.string' => 'Alamat harus berupa teks.',
                'address.max' => 'Alamat maksimal 255 karakter.',
                'phone.string' => 'Nomor telepon harus berupa teks.',
                'phone.max' => 'Nomor telepon maksimal 20 karakter.',
                'slug.alpha_dash' => 'Slug hanya boleh huruf, angka, strip dan underscore.',
                'slug.unique' => 'Slug sudah digunakan oleh profil lain.',
                'logo_masjid.file' => 'File logo masjid tidak valid.',
                'logo_masjid.mimes' => 'Logo masjid harus berupa file jpeg, jpg, png, webp, atau gif.',
                'logo_masjid.max' => 'Ukuran logo masjid maksimal 5MB.',
                'logo_pemerintah.file' => 'File logo pemerintah tidak valid.',
                'logo_pemerintah.mimes' => 'Logo pemerintah harus berupa file jpeg, jpg, png, webp, atau gif.',
                'logo_pemerintah.max' => 'Ukuran logo pemerintah maksimal 5MB.',
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
            if (empty($validated['slug']) && !empty($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            if ($request->hasFile('logo_masjid')) {
                $file = $request->file('logo_masjid');
                $ext = strtolower($file->getClientOriginalExtension());
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = Str::slug($originalName);
                $fileName = time() . '_masjid_' . $safeName . '.' . $ext;
                $destDir = public_path('images/logo');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                $file->move($destDir, $fileName);
                $validated['logo_masjid'] = '/images/logo/' . $fileName;
            }

            if ($request->hasFile('logo_pemerintah')) {
                $file = $request->file('logo_pemerintah');
                $ext = strtolower($file->getClientOriginalExtension());
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = Str::slug($originalName);
                $fileName = time() . '_pemerintah_' . $safeName . '.' . $ext;
                $destDir = public_path('images/logo');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                $file->move($destDir, $fileName);
                $validated['logo_pemerintah'] = '/images/logo/' . $fileName;
            }

            $profilMasjid->fill($validated);
            $profilMasjid->save();

            $data = [
                'id' => $profilMasjid->id,
                'name' => $profilMasjid->name,
                'address' => $profilMasjid->address,
                'phone' => $profilMasjid->phone,
                'slug' => $profilMasjid->slug,
                'logo_masjid' => $profilMasjid->logo_masjid,
                'logo_pemerintah' => $profilMasjid->logo_pemerintah,
                'logo_masjid_url' => $profilMasjid->logo_masjid_url,
                'logo_pemerintah_url' => $profilMasjid->logo_pemerintah_url,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Berhasil membuat profil masjid',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat profil masjid',
                'error' => [
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $profilMasjid = Profil::where('user_id', $user->id)->firstOrFail();

            $rules = [
                'name' => 'required|string|max:100',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'slug' => 'nullable|alpha_dash|unique:profils,slug,' . $profilMasjid->id,
                'logo_masjid' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif|max:5000',
                'logo_pemerintah' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif|max:5000',
            ];

            $messages = [
                'name.required' => 'Nama masjid wajib diisi.',
                'name.string' => 'Nama masjid harus berupa teks.',
                'name.max' => 'Nama masjid maksimal 100 karakter.',
                'address.required' => 'Alamat wajib diisi.',
                'address.string' => 'Alamat harus berupa teks.',
                'address.max' => 'Alamat maksimal 255 karakter.',
                'phone.string' => 'Nomor telepon harus berupa teks.',
                'phone.max' => 'Nomor telepon maksimal 20 karakter.',
                'slug.alpha_dash' => 'Slug hanya boleh huruf, angka, strip dan underscore.',
                'slug.unique' => 'Slug sudah digunakan oleh profil lain.',
                'logo_masjid.file' => 'File logo masjid tidak valid.',
                'logo_masjid.mimes' => 'Logo masjid harus berupa file jpeg, jpg, png, webp, atau gif.',
                'logo_masjid.max' => 'Ukuran logo masjid maksimal 5MB.',
                'logo_pemerintah.file' => 'File logo pemerintah tidak valid.',
                'logo_pemerintah.mimes' => 'Logo pemerintah harus berupa file jpeg, jpg, png, webp, atau gif.',
                'logo_pemerintah.max' => 'Ukuran logo pemerintah maksimal 5MB.',
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

            if (empty($validated['slug']) && !empty($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            if ($request->hasFile('logo_masjid')) {
                if (!empty($profilMasjid->logo_masjid)) {
                    $oldPath = public_path($profilMasjid->logo_masjid);
                    if (file_exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
                $file = $request->file('logo_masjid');
                $ext = strtolower($file->getClientOriginalExtension());
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = Str::slug($originalName);
                $fileName = time() . '_masjid_' . $safeName . '.' . $ext;
                $destDir = public_path('images/logo');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                $file->move($destDir, $fileName);
                $validated['logo_masjid'] = '/images/logo/' . $fileName;
            }

            if ($request->hasFile('logo_pemerintah')) {
                if (!empty($profilMasjid->logo_pemerintah)) {
                    $oldPath = public_path($profilMasjid->logo_pemerintah);
                    if (file_exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
                $file = $request->file('logo_pemerintah');
                $ext = strtolower($file->getClientOriginalExtension());
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = Str::slug($originalName);
                $fileName = time() . '_pemerintah_' . $safeName . '.' . $ext;
                $destDir = public_path('images/logo');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                $file->move($destDir, $fileName);
                $validated['logo_pemerintah'] = '/images/logo/' . $fileName;
            }

            $profilMasjid->fill($validated);
            $profilMasjid->save();

            event(new ContentUpdatedEvent($profilMasjid->slug, 'profil'));

            $data = [
                'id' => $profilMasjid->id,
                'name' => $profilMasjid->name,
                'address' => $profilMasjid->address,
                'phone' => $profilMasjid->phone,
                'slug' => $profilMasjid->slug,
                'logo_masjid' => $profilMasjid->logo_masjid,
                'logo_pemerintah' => $profilMasjid->logo_pemerintah,
                'logo_masjid_url' => $profilMasjid->logo_masjid_url,
                'logo_pemerintah_url' => $profilMasjid->logo_pemerintah_url,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate data profil masjid',
                'data' => $data,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki profil masjid',
                'error' => [
                    'code' => 'PROFIL_NOT_FOUND',
                    'message' => 'Data profil masjid tidak ditemukan.',
                ],
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data profil masjid',
                'error' => [
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage(),
                ],
            ], 500);
        }
    }
}
