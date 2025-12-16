<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\JumbotronMasjid;
use App\Models\Profil;
use App\Events\ContentUpdatedEvent;

class MyJumbotronMasjidController extends Controller
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
            $profil = Profil::where('user_id', $user->id)->firstOrFail();
            $jm = JumbotronMasjid::where('masjid_id', $profil->id)
                ->where('created_by', $user->id)
                ->first();
            $items = [];
            for ($i = 1; $i <= 6; $i++) {
                $field = 'jumbotron_masjid_' . $i;
                $path = $jm ? ($jm->$field ?: null) : null;
                $filename = null;
                $updatedAt = null;
                $url = null;
                if ($path) {
                    $filename = basename($path);
                    $fullPath = public_path($path);
                    if (file_exists($fullPath)) {
                        $updatedAt = date('c', filemtime($fullPath));
                    }
                    $url = asset($path);
                }
                $items[] = [
                    'slot' => $i,
                    'path' => $path,
                    'filename' => $filename,
                    'url' => $url,
                    'updated_at' => $updatedAt,
                ];
            }
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data jumbotron masjid',
                'data' => [
                    'items' => $items,
                    'aktif' => $jm ? (bool) $jm->aktif : false,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profil masjid belum ada untuk user ini',
            ], 404);
        }
    }

    public function upload(Request $request, int $slot)
    {
        $user = $request->user();
        if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }
        if ($slot < 1 || $slot > 6) {
            return response()->json([
                'success' => false,
                'message' => 'Slot tidak valid. Harus antara 1-6.',
            ], 422);
        }
        $rules = [
            'image' => 'required|image|mimes:jpg,png,jpeg,webp|max:1000',
        ];
        $messages = [
            'image.required' => 'File gambar wajib diunggah',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpg,png,jpeg,webp',
            'image.max' => 'Ukuran gambar maksimal 1000KB',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi input gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $uploadedFile = $request->file('image');
        try {
            $profil = Profil::where('user_id', $user->id)->firstOrFail();
            $jm = JumbotronMasjid::firstOrCreate([
                'masjid_id' => $profil->id,
                'created_by' => $user->id,
            ], [
                'aktif' => false,
            ]);
            $field = 'jumbotron_masjid_' . $slot;

            if ($jm->$field) {
                $oldPath = public_path($jm->$field);
                if (file_exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $processedImage = $this->resizeImageToLimit($uploadedFile, 1000);
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_jumbo' . $slot . '_' . $originalName . '.jpg';
            $relativePath = '/images/jumbotrons/' . $fileName;
            $absolutePath = public_path('images/jumbotrons/' . $fileName);

            $dir = dirname($absolutePath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $maxSizeBytes = 1000 * 1024;
            $quality = 95;
            do {
                $encoded = $processedImage->toJpeg($quality);
                if (strlen($encoded) <= $maxSizeBytes) {
                    break;
                }
                $quality -= 1;
            } while ($quality >= 60);
            $processedImage->toJpeg($quality)->save($absolutePath);
            $finalSize = filesize($absolutePath);
            if ($finalSize > $maxSizeBytes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran file masih terlalu besar setelah diproses',
                ], 422);
            }

            $jm->$field = $relativePath;
            $jm->save();

            event(new ContentUpdatedEvent($profil->slug, 'jumbotron_masjid'));

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengunggah gambar untuk slot ' . $slot,
                'data' => [
                    'slot' => $slot,
                    'path' => $relativePath,
                    'filename' => basename($relativePath),
                    'url' => asset($relativePath),
                    'updated_at' => date('c', filemtime($absolutePath)),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan gambar: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $slot)
    {
        try {
            $user = $request->user();
            if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            if ($slot < 1 || $slot > 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slot tidak valid. Harus antara 1-6.',
                ], 422);
            }
            $profil = Profil::where('user_id', $user->id)->firstOrFail();
            $jm = JumbotronMasjid::where('masjid_id', $profil->id)
                ->where('created_by', $user->id)
                ->firstOrFail();
            $field = 'jumbotron_masjid_' . $slot;
            $path = $jm->$field;
            if ($path) {
                $fullPath = public_path($path);
                if (file_exists($fullPath)) {
                    File::delete($fullPath);
                }
                $jm->$field = null;
                $jm->save();
            }

            event(new ContentUpdatedEvent($profil->slug, 'jumbotron_masjid'));

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus gambar pada slot ' . $slot,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data jumbotron belum ada untuk user ini',
            ], 404);
        }
    }

    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 1000)
    {
        try {
            $maxSizeBytes = $maxSizeKB * 1024;
            $image = Image::read($uploadedFile->getRealPath());
            $targetRatio = 16 / 9;
            $targetWidth = 1920;
            $targetHeight = 1080;
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $originalRatio = $originalWidth / $originalHeight;
            if (abs($originalRatio - $targetRatio) > 0.01) {
                if ($originalRatio > $targetRatio) {
                    $newWidth = (int)($originalHeight * $targetRatio);
                    $x = (int)(($originalWidth - $newWidth) / 2);
                    $image->crop($newWidth, $originalHeight, $x, 0);
                } else {
                    $newHeight = (int)($originalWidth / $targetRatio);
                    $y = (int)(($originalHeight - $newHeight) / 2);
                    $image->crop($originalWidth, $newHeight, 0, $y);
                }
            }
            $image->resize($targetWidth, $targetHeight);
            $quality = 95;
            $minQuality = 20;
            do {
                $encoded = $image->toJpeg($quality);
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
            } while ($quality >= $minQuality);
            if (strlen($image->toJpeg($minQuality)) > $maxSizeBytes) {
                $scaleFactor = 0.9;
                while (strlen($image->toJpeg($minQuality)) > $maxSizeBytes && $scaleFactor > 0.5) {
                    $newWidth = (int)($targetWidth * $scaleFactor);
                    $newHeight = (int)($targetHeight * $scaleFactor);
                    $image->resize($newWidth, $newHeight);
                    $scaleFactor -= 0.05;
                }
            }
            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }
}
