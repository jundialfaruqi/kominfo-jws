<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Theme;

class ThemeController extends Controller
{
    /**
     * Mendapatkan daftar seluruh theme.
     */
    public function index()
    {
        try {
            $themes = Theme::query()->select(['id', 'name'])->get();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Daftar theme berhasil diambil',
                'data' => $themes
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
