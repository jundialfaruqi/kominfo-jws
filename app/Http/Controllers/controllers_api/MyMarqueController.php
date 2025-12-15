<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Marquee;
use App\Models\Profil;
use App\Events\ContentUpdatedEvent;

class MyMarqueController extends Controller
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

            $marquee = Marquee::where('user_id', $user->id)->firstOrFail();
            $items = [];
            if ($marquee->marquee1) $items[] = $marquee->marquee1;
            if ($marquee->marquee2) $items[] = $marquee->marquee2;
            if ($marquee->marquee3) $items[] = $marquee->marquee3;
            if ($marquee->marquee4) $items[] = $marquee->marquee4;
            if ($marquee->marquee5) $items[] = $marquee->marquee5;
            if ($marquee->marquee6) $items[] = $marquee->marquee6;

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data teks berjalan',
                'data' => [
                    'items' => $items,
                    'speed' => (float) ($marquee->marquee_speed ?? 1.0),
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki data teks berjalan',
            ], 404);
        }
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

        $existing = Marquee::where('user_id', $user->id)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Data teks berjalan sudah ada untuk user ini',
                'error' => [
                    'code' => 'MARQUEE_ALREADY_EXISTS',
                    'message' => 'Gunakan endpoint update untuk mengubah data yang sudah ada.',
                ],
            ], 409);
        }

        $rules = [
            'items' => 'nullable|array|max:6',
            'items.*' => 'nullable|string|max:350',
            'speed' => 'nullable|numeric|min:0.1|max:10',
        ];
        $messages = [
            'items.array' => 'Format teks berjalan tidak valid',
            'items.max' => 'Maksimal 6 teks berjalan',
            'items.*.string' => 'Teks berjalan harus berupa string',
            'items.*.max' => 'Teks berjalan maksimal 350 karakter',
            'speed.numeric' => 'Kecepatan harus angka',
            'speed.min' => 'Kecepatan minimal 0.1',
            'speed.max' => 'Kecepatan maksimal 10',
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
        $items = $validated['items'] ?? [];
        $speed = $validated['speed'] ?? 1.0;

        $marquee = new Marquee();
        $marquee->user_id = $user->id;
        $marquee->marquee1 = $items[0] ?? null;
        $marquee->marquee2 = $items[1] ?? null;
        $marquee->marquee3 = $items[2] ?? null;
        $marquee->marquee4 = $items[3] ?? null;
        $marquee->marquee5 = $items[4] ?? null;
        $marquee->marquee6 = $items[5] ?? null;
        $marquee->marquee_speed = $speed;
        $marquee->save();

        $profil = Profil::where('user_id', $user->id)->first();
        if ($profil) event(new ContentUpdatedEvent($profil->slug, 'marquee'));

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membuat data teks berjalan',
            'data' => [
                'items' => $items,
                'speed' => (float) $speed,
            ],
        ], 201);
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

            $marquee = Marquee::where('user_id', $user->id)->firstOrFail();
            $rules = [
                'items' => 'nullable|array|max:6',
                'items.*' => 'nullable|string|max:350',
                'speed' => 'nullable|numeric|min:0.1|max:10',
            ];
            $messages = [
                'items.array' => 'Format teks berjalan tidak valid',
                'items.max' => 'Maksimal 6 teks berjalan',
                'items.*.string' => 'Teks berjalan harus berupa string',
                'items.*.max' => 'Teks berjalan maksimal 350 karakter',
                'speed.numeric' => 'Kecepatan harus angka',
                'speed.min' => 'Kecepatan minimal 0.1',
                'speed.max' => 'Kecepatan maksimal 10',
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
            $items = $validated['items'] ?? [];
            $speed = $validated['speed'] ?? $marquee->marquee_speed ?? 1.0;

            $marquee->marquee1 = $items[0] ?? null;
            $marquee->marquee2 = $items[1] ?? null;
            $marquee->marquee3 = $items[2] ?? null;
            $marquee->marquee4 = $items[3] ?? null;
            $marquee->marquee5 = $items[4] ?? null;
            $marquee->marquee6 = $items[5] ?? null;
            $marquee->marquee_speed = $speed;
            $marquee->save();

            $profil = Profil::where('user_id', $user->id)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'marquee'));

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate teks berjalan',
                'data' => [
                    'items' => array_values(array_filter([
                        $marquee->marquee1,
                        $marquee->marquee2,
                        $marquee->marquee3,
                        $marquee->marquee4,
                        $marquee->marquee5,
                        $marquee->marquee6,
                    ])),
                    'speed' => (float) ($marquee->marquee_speed ?? 1.0),
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data teks berjalan belum ada untuk user ini',
            ], 404);
        }
    }
}
