<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MyMasjidController extends Controller
{
    /**
     * Mengembalikan waktu saat ini.
     */
    public function index(Request $request)
    {
        try {
            $slug = $request->query('slug');

            if (!$slug) {
                return response()->json([
                    'code' => 400,
                    'success' => false,
                    'message' => 'Parameter slug wajib diisi',
                ], 400);
            }

            $profil = \App\Models\Profil::query()->where('slug', $slug)->first();

            if (!$profil) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Profil masjid tidak ditemukan',
                ], 404);
            }

            $userId = $profil->user_id;
            $marquee = \App\Models\Marquee::query()->where('user_id', $userId)->first();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Data my-masjid berhasil diambil',
                'data' => [
                    'current_time' => Carbon::now('Asia/Jakarta')->toDateTimeString(),
                    'timestamp' => Carbon::now('Asia/Jakarta')->timestamp,
                    'profil' => $profil,
                    'marquee' => $marquee,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
