<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\AdzanAudio;
use App\Events\ContentUpdatedEvent;
use App\Models\Profil;

class MyAdzanAudioController extends Controller
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

            $adzan = AdzanAudio::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => true]
            );

            $items = [];
            // Slot 1: Audio Adzan (Reguler)
            // Slot 2: Adzan Shubuh
            $fields = [1 => 'audioadzan', 2 => 'adzanshubuh'];

            foreach ($fields as $slot => $field) {
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
                    'slot' => $slot,
                    'path' => $path,
                    'filename' => $filename,
                    'url' => $url,
                    'updated_at' => $updatedAt,
                    'label' => $slot == 1 ? 'Adzan Reguler' : 'Adzan Shubuh'
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data audio adzan',
                'data' => [
                    'items' => $items,
                    'status' => $adzan->status,
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

        if ($slot < 1 || $slot > 2) {
            return response()->json([
                'success' => false,
                'message' => 'Slot tidak valid. Gunakan 1 (Reguler) atau 2 (Shubuh).',
            ], 422);
        }

        $rules = [
            'audio' => 'required|file|mimes:mp3,wav|max:10240', // 10MB max
        ];
        $messages = [
            'audio.required' => 'File audio wajib diunggah',
            'audio.file' => 'File harus berupa audio',
            'audio.mimes' => 'Format audio harus mp3 atau wav',
            'audio.max' => 'Ukuran audio maksimal 10MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $adzan = AdzanAudio::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => true]
            );

            $file = $request->file('audio');
            $ext = $file->getClientOriginalExtension();
            $field = $slot == 1 ? 'audioadzan' : 'adzanshubuh';
            $prefix = $slot == 1 ? 'adzan_' : 'shubuh_';
            $fileName = $prefix . time() . '.' . $ext;
            
            // Ensure directory exists
            $path = public_path('sounds/adzan');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Delete old file if exists
            if ($adzan->$field && file_exists(public_path($adzan->$field))) {
                unlink(public_path($adzan->$field));
            }

            $file->move($path, $fileName);
            $relativePath = 'sounds/adzan/' . $fileName;

            $adzan->$field = $relativePath;
            $adzan->save();

            // Trigger event for websocket update
            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) {
                event(new ContentUpdatedEvent($profil->slug, 'adzan_audio'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengunggah audio',
                'data' => [
                    'url' => asset($relativePath),
                    'path' => $relativePath,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah: ' . $e->getMessage(),
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

        if ($slot < 1 || $slot > 2) {
            return response()->json([
                'success' => false,
                'message' => 'Slot tidak valid',
            ], 422);
        }

        try {
            $adzan = AdzanAudio::where('user_id', $user->id)->firstOrFail();
            $field = $slot == 1 ? 'audioadzan' : 'adzanshubuh';

            if ($adzan->$field) {
                $fullPath = public_path($adzan->$field);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                $adzan->$field = null;
                $adzan->save();

                $profil = Profil::where('user_id', $user->id)->first();
                if ($profil) {
                    event(new ContentUpdatedEvent($profil->slug, 'adzan_audio'));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus audio',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage(),
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
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $adzan = AdzanAudio::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => true]
            );
            
            $adzan->status = $request->status;
            $adzan->save();

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) {
                event(new ContentUpdatedEvent($profil->slug, 'adzan_audio'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'data' => ['status' => $adzan->status]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
