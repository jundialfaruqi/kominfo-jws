<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use App\Models\Adzan;
use App\Models\Profil;
use App\Events\ContentUpdatedEvent;
use Intervention\Image\Laravel\Facades\Image;

class MySlideIqomahJumatController extends Controller
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

            $adzan = Adzan::where('user_id', $user->id)->firstOrFail();
            $items = [];
            for ($i = 1; $i <= 15; $i++) {
                $field = 'adzan' . $i;
                $path = $adzan->$field ?: null;
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
                'message' => 'Berhasil mengambil data slide iqomah & jumat',
                'data' => [
                    'items' => $items,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki data slide iqomah & jumat',
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
        if ($slot < 1 || $slot > 15) {
            return response()->json([
                'success' => false,
                'message' => 'Slot tidak valid. Harus antara 1-15.',
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
        $adzan = Adzan::firstOrCreate(['user_id' => $user->id]);
        $field = 'adzan' . $slot;

        try {
            if ($adzan->$field) {
                $oldPath = public_path($adzan->$field);
                if (file_exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = strtolower($uploadedFile->getClientOriginalExtension());
            $fileName = time() . '_adzan' . $slot . '_' . $originalName . '.jpg';
            $relativePath = '/images/adzan/' . $fileName;
            $absolutePath = public_path('images/adzan/' . $fileName);

            $dir = dirname($absolutePath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $maxSizeBytes = 800 * 1024;

            // Process image - consistent with GambarAdzan.php logic (resize to JPEG)
            $image = Image::read($uploadedFile->getRealPath());
            $quality = 95;
            $minQuality = 60;

            // First try with quality reduction
            do {
                $encoded = $image->toJpeg($quality);
                if (strlen($encoded) <= $maxSizeBytes) {
                    break;
                }
                $quality -= 2;
            } while ($quality >= $minQuality);

            // If still too big, resize dimensions
            if (strlen($image->toJpeg($minQuality)) > $maxSizeBytes) {
                $scaleFactor = 0.9;
                while (strlen($image->toJpeg($minQuality)) > $maxSizeBytes && $scaleFactor > 0.5) {
                    $newWidth = (int)($image->width() * $scaleFactor);
                    $newHeight = (int)($image->height() * $scaleFactor);
                    $image->resize($newWidth, $newHeight);
                    $scaleFactor -= 0.05;
                }
                $image->toJpeg($minQuality)->save($absolutePath);
            } else {
                $image->toJpeg($quality)->save($absolutePath);
            }

            $finalSize = filesize($absolutePath);
            if ($finalSize > $maxSizeBytes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran file masih terlalu besar setelah diproses',
                ], 422);
            }

            $adzan->$field = $relativePath;
            $adzan->save();

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'adzan'));

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
            if ($slot < 1 || $slot > 15) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slot tidak valid. Harus antara 1-15.',
                ], 422);
            }
            $adzan = Adzan::where('user_id', $user->id)->firstOrFail();
            $field = 'adzan' . $slot;
            $path = $adzan->$field;
            if ($path) {
                $fullPath = public_path($path);
                if (file_exists($fullPath)) {
                    File::delete($fullPath);
                }
                $adzan->$field = null;
                $adzan->save();
            }

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'adzan'));

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
}
