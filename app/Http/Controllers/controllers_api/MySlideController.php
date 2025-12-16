<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use App\Models\Slides;
use App\Models\Profil;
use App\Events\ContentUpdatedEvent;
use Intervention\Image\Laravel\Facades\Image;

class MySlideController extends Controller
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

            $slides = Slides::where('user_id', $user->id)->firstOrFail();
            $items = [];
            for ($i = 1; $i <= 6; $i++) {
                $field = 'slide' . $i;
                $path = $slides->$field ?: null;
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
                'message' => 'Berhasil mengambil data slide utama',
                'data' => [
                    'items' => $items,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki data slide utama',
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
            'image' => 'required|image|mimes:jpg,png,jpeg,webp|max:800',
        ];
        $messages = [
            'image.required' => 'File gambar wajib diunggah',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpg,png,jpeg,webp',
            'image.max' => 'Ukuran gambar maksimal 800KB',
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
        $slides = Slides::firstOrCreate(['user_id' => $user->id]);
        $field = 'slide' . $slot;

        try {
            if ($slides->$field) {
                $oldPath = public_path($slides->$field);
                if (file_exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $processedImage = $this->resizeImageToLimit($uploadedFile, 800);
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_slide' . $slot . '_' . $originalName . '.jpg';
            $relativePath = '/images/slides/' . $fileName;
            $absolutePath = public_path('images/slides/' . $fileName);

            $dir = dirname($absolutePath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $maxSizeBytes = 800 * 1024;
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

            $slides->$field = $relativePath;
            $slides->save();

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'slide'));

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
            $slides = Slides::where('user_id', $user->id)->firstOrFail();
            $field = 'slide' . $slot;
            $path = $slides->$field;
            if ($path) {
                $fullPath = public_path($path);
                if (file_exists($fullPath)) {
                    File::delete($fullPath);
                }
                $slides->$field = null;
                $slides->save();
            }

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'slide'));

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus gambar pada slot ' . $slot,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data slide belum ada untuk user ini',
            ], 404);
        }
    }

    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 800)
    {
        try {
            $image = Image::read($uploadedFile->getRealPath());
            $maxSizeBytes = $maxSizeKB * 1024;
            $quality = 95;
            $minQuality = 60;
            do {
                $encoded = $image->toJpeg($quality);
                if (strlen($encoded) <= $maxSizeBytes) {
                    break;
                }
                $quality -= 2;
            } while ($quality >= $minQuality);
            if (strlen($image->toJpeg($minQuality)) > $maxSizeBytes) {
                $scaleFactor = 0.9;
                while (strlen($image->toJpeg($minQuality)) > $maxSizeBytes && $scaleFactor > 0.5) {
                    $newWidth = (int)($image->width() * $scaleFactor);
                    $newHeight = (int)($image->height() * $scaleFactor);
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
