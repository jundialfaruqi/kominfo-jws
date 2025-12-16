<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use App\Models\Audios;
use App\Events\ContentUpdatedEvent;
use App\Models\Profil;

class MyMurottalController extends Controller
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

            $audios = Audios::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => true]
            );

            $items = [];
            for ($i = 1; $i <= 3; $i++) {
                $field = 'audio' . $i;
                $path = $audios->$field ?: null;
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
                'message' => 'Berhasil mengambil data audio murottal',
                'data' => [
                    'items' => $items,
                    'status' => $audios->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
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

        if ($slot < 1 || $slot > 3) {
            return response()->json([
                'success' => false,
                'message' => 'Slot tidak valid. Harus antara 1-3.',
            ], 422);
        }

        $rules = [
            'audio' => 'required|file|mimes:mp3,wav|max:51200', // 50MB max
        ];
        $messages = [
            'audio.required' => 'File audio wajib diunggah',
            'audio.file' => 'File harus berupa audio',
            'audio.mimes' => 'Format audio harus mp3 atau wav',
            'audio.max' => 'Ukuran audio maksimal 50MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi input gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audios = Audios::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => true]
            );

            $field = 'audio' . $slot;
            $oldPath = $audios->$field;

            // Delete old file if exists
            if ($oldPath && File::exists(public_path($oldPath))) {
                File::delete(public_path($oldPath));
            }

            // Upload new file
            if ($request->hasFile('audio')) {
                $file = $request->file('audio');
                $filename = time() . '_' . $slot . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $file->getClientOriginalName());
                $path = 'sounds/musik';

                // Ensure directory exists
                if (!File::exists(public_path($path))) {
                    File::makeDirectory(public_path($path), 0755, true);
                }

                $file->move(public_path($path), $filename);
                $fullPath = $path . '/' . $filename;

                $audios->$field = $fullPath;
                $audios->save();

                // Trigger content update event
                $profil = Profil::where('user_id', $user->id)->first();
                if ($profil) {
                    event(new ContentUpdatedEvent($profil->slug, 'audio-murottal'));
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Audio berhasil diunggah ke slot ' . $slot,
                    'data' => [
                        'slot' => $slot,
                        'url' => asset($fullPath),
                        'filename' => $filename,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah file',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $slot)
    {
        $user = $request->user();
        if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        if ($slot < 1 || $slot > 3) {
            return response()->json([
                'success' => false,
                'message' => 'Slot tidak valid. Harus antara 1-3.',
            ], 422);
        }

        try {
            $audios = Audios::where('user_id', $user->id)->firstOrFail();
            $field = 'audio' . $slot;
            $path = $audios->$field;

            if ($path) {
                if (File::exists(public_path($path))) {
                    File::delete(public_path($path));
                }
                $audios->$field = null;
                $audios->save();

                // Trigger content update event
                $profil = Profil::where('user_id', $user->id)->first();
                if ($profil) {
                    event(new ContentUpdatedEvent($profil->slug, 'audio-murottal'));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Audio slot ' . $slot . ' berhasil dihapus',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data audio tidak ditemukan',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus audio: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $user = $request->user();
        if (!$user || in_array($user->role, ['Admin', 'Super Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $audios = Audios::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => true]
            );

            $audios->status = $request->status;
            $audios->save();

            // Trigger content update event
            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) {
                event(new ContentUpdatedEvent($profil->slug, 'audio-murottal'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Status audio berhasil diperbarui',
                'data' => [
                    'status' => $audios->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
