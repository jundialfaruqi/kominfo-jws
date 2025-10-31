<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use App\Events\ContentUpdatedEvent;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfilMasjidController extends Controller
{
    public function __construct() {}

    // Helper: resize, crop 1:1, dan simpan sebagai WebP dengan batas ukuran
    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 800)
    {
        try {
            $maxSizeBytes = $maxSizeKB * 1024;

            $image = Image::read($uploadedFile->getRealPath());

            // Crop ke rasio 1:1 (square)
            $width = $image->width();
            $height = $image->height();
            $size = min($width, $height);
            $image->crop($size, $size, ($width - $size) / 2, ($height - $size) / 2);

            // Turunkan kualitas hingga memenuhi batas ukuran
            $quality = 95;
            $minQuality = 20;

            do {
                $encoded = $image->toWebp($quality);
                $currentSize = strlen($encoded);

                if ($currentSize <= $maxSizeBytes) {
                    break;
                }

                if ($currentSize > $maxSizeBytes * 1.5) {
                    $quality -= 10;
                } elseif ($currentSize > $maxSizeBytes * 1.2) {
                    $quality -= 5;
                } else {
                    $quality -= 2;
                }

                // Jika masih terlalu besar pada kualitas minimum, lakukan resize dimensi
                if ($quality < $minQuality && strlen($image->toWebp($minQuality)) > $maxSizeBytes) {
                    $scaleFactor = 0.9;
                    while (strlen($image->toWebp($minQuality)) > $maxSizeBytes && $scaleFactor > 0.5) {
                        $newSize = (int)($size * $scaleFactor);
                        $image->resize($newSize, $newSize);
                        $scaleFactor -= 0.05;
                    }
                }
            } while ($quality >= $minQuality);

            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }

    private function saveProcessedImage($uploadedFile, $type, $maxSizeKB = 800)
    {
        try {
            $processedImage = $this->resizeImageToLimit($uploadedFile, $maxSizeKB);

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_' . $type . '_' . $originalName . '.webp';
            $filePath = public_path('images/logo/' . $fileName);

            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Fine-tune kualitas untuk mendekati batas ukuran maksimum
            $maxSizeBytes = $maxSizeKB * 1024;
            $quality = 95;
            do {
                $encoded = $processedImage->toWebp($quality);
                $currentSize = strlen($encoded);
                if ($currentSize <= $maxSizeBytes) {
                    break;
                }
                $quality -= 1;
            } while ($quality >= 60);

            $processedImage->toWebp($quality)->save($filePath);

            $finalSize = filesize($filePath);
            if ($finalSize > $maxSizeBytes) {
                throw new \Exception('Ukuran file masih terlalu besar: ' . round($finalSize / 1024, 2) . 'KB');
            }

            return '/images/logo/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    // Get All Profil Masjid For Admin and Super Admin Role
    public function getAllProfilMasjid(Request $request)
    {
        try {
            // Cek Auth
            $user = $request->user();
            if (!$user || !in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get All Profil Masjid
            $profilMasjid = Profil::paginate(10)->makeVisible([
                'logo_masjid_url',
                'logo_pemerintah_url'
            ]);

            // Mapping data
            $data = $profilMasjid->map(function ($item) {
                return [
                    'name' => $item->name,
                    'address' => $item->address,
                    'phone' => $item->phone,
                    'slug' => $item->slug,
                    'logo_masjid' => $item->logo_masjid,
                    'logo_pemerintah' => $item->logo_pemerintah,
                    'logo_masjid_url' => $item->logo_masjid_url,
                    'logo_pemerintah_url' => $item->logo_pemerintah_url,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data profil masjid',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data profil masjid',
                'error' => [
                    'code' => 'PROFIL_NOT_FOUND',
                    'message' => 'Data profil masjid tidak ditemukan.',
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data profil masjid',
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => $e->getMessage(),
                ]
            ], 500);
        }
    }

    // Get data profil masjid milik user itu sendiri untuk non admin super admin user
    public function getProfilMasjid(Request $request, $id)
    {
        try {
            // Cek Auth
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Validasi akses: user hanya boleh mengakses profil miliknya sendiri
            if ((string) $user->id !== (string) $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access ke profil user lain'
                ], 403);
            }

            // Get Profil Masjid
            $profilMasjid = Profil::where('user_id', $id)->firstOrFail()->makeVisible([
                'logo_masjid_url',
                'logo_pemerintah_url'
            ]);

            // Mapping data
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
                'data' => $data
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki profil masjid',
                'error' => [
                    'code' => 'PROFIL_NOT_FOUND',
                    'message' => 'Data profil masjid tidak ditemukan.',
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data profil masjid',
                'error' => [
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage(),
                ]
            ], 500);
        }
    }

    // Update data profil masjid milik user itu sendiri untuk non admin super admin user
    public function updateProfilMasjid(Request $request, $id)
    {
        try {
            // Cek Auth
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Validasi akses: user hanya boleh mengupdate profil miliknya sendiri
            if ((string) $user->id !== (string) $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access ke profil user lain'
                ], 403);
            }

            // Get Profil Masjid (update-only) – jika tidak ada, 404
            $profilMasjid = Profil::where('user_id', $id)->firstOrFail();

            // Validasi input
            $rules = [
                'name' => 'required|string|max:100',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'slug' => 'nullable|alpha_dash|unique:profils,slug,' . $profilMasjid->id,
                'logo_masjid' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif|max:5000',
                'logo_pemerintah' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif|max:5000',
            ];

            // Pesan validasi input
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

            // Auto-generate slug dari name jika slug tidak diisi
            if (empty($validated['slug']) && !empty($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Handle upload file logo_masjid (konsisten dengan Livewire: crop + WebP, simpan ke /images/logo)
            if ($request->hasFile('logo_masjid')) {
                // Hapus file lama jika ada
                if (!empty($profilMasjid->logo_masjid)) {
                    $oldPath = public_path($profilMasjid->logo_masjid);
                    if (file_exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $file = $request->file('logo_masjid');
                $validated['logo_masjid'] = $this->saveProcessedImage($file, 'masjid', 800);
            }

            // Handle upload file logo_pemerintah (konsisten dengan Livewire: crop + WebP, simpan ke /images/logo)
            if ($request->hasFile('logo_pemerintah')) {
                // Hapus file lama jika ada
                if (!empty($profilMasjid->logo_pemerintah)) {
                    $oldPath = public_path($profilMasjid->logo_pemerintah);
                    if (file_exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $file = $request->file('logo_pemerintah');
                $validated['logo_pemerintah'] = $this->saveProcessedImage($file, 'pemerintah', 800);
            }

            // Simpan data profil masjid (upsert)
            $profilMasjid->fill($validated);
            $profilMasjid->save();

            // Trigger websocket event seperti di Livewire (hanya saat update)
            event(new ContentUpdatedEvent($profilMasjid->slug, 'profil'));

            // Mapping data
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

            $message = 'Berhasil mengupdate data profil masjid';
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki profil masjid',
                'error' => [
                    'code' => 'PROFIL_NOT_FOUND',
                    'message' => 'Data profil masjid tidak ditemukan.',
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data profil masjid',
                'error' => [
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage(),
                ]
            ], 500);
        }
    }

    // Create data profil masjid milik user itu sendiri (dipisah dari update)
    public function createProfilMasjid(Request $request, $id)
    {
        try {
            // Cek Auth
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Validasi akses: user hanya boleh membuat profil miliknya sendiri
            if ((string) $user->id !== (string) $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access ke profil user lain'
                ], 403);
            }

            // Cek apakah sudah ada profil
            $existing = Profil::where('user_id', $id)->first();
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil masjid sudah ada untuk user ini',
                    'error' => [
                        'code' => 'PROFIL_ALREADY_EXISTS',
                        'message' => 'Gunakan endpoint update untuk mengubah profil yang sudah ada.',
                    ]
                ], 409);
            }

            // Siapkan instance baru
            $profilMasjid = new Profil();
            $profilMasjid->user_id = $id;

            // Validasi input (slug nullable + unique, mimes dan max sesuai update)
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

            // Handle upload file logo_masjid
            if ($request->hasFile('logo_masjid')) {
                $file = $request->file('logo_masjid');
                $validated['logo_masjid'] = $this->saveProcessedImage($file, 'masjid', 800);
            }

            // Handle upload file logo_pemerintah
            if ($request->hasFile('logo_pemerintah')) {
                $file = $request->file('logo_pemerintah');
                $validated['logo_pemerintah'] = $this->saveProcessedImage($file, 'pemerintah', 800);
            }

            // Simpan data profil masjid
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
                'data' => $data
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki profil masjid',
                'error' => [
                    'code' => 'PROFIL_NOT_FOUND',
                    'message' => 'Data profil masjid tidak ditemukan.',
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat profil masjid',
                'error' => [
                    'code' => 'INTERNAL_SERVER_ERROR',
                    'message' => $e->getMessage(),
                ]
            ], 500);
        }
    }
}
