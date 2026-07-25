<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;

class ThemeController extends Controller
{
    /**
     * Mendapatkan data theme berdasarkan ID.
     */
    public function show(int $id)
    {
        try {
            $theme = Theme::query()->find($id);

            if (!$theme) {
                return response()->json([
                    'code' => 404,
                    'success' => false,
                    'message' => 'Theme tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Data theme berhasil diambil',
                'data' => $theme
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
