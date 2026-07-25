<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Profil;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Mendapatkan daftar nama dan slug profil masjid (Public API)
     * Pagination: 10 per halaman
     */
    public function getProfilMasjid(Request $request)
    {
        try {
            $query = Profil::select(['id', 'name', 'slug']);

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where('name', 'like', "%$search%");
            }

            $profils = $query->paginate(10);
            
            // Memastikan appends (logo_masjid_url dsb) dihapus agar response bersih dan tidak error 
            $profils->getCollection()->transform(function ($profil) {
                return $profil->setAppends([]);
            });

            // Cek jika data kosong
            if ($profils->isEmpty()) {
                return response()->json([
                    'success' => true, // tetap true (karena request berhasil) atau Anda bisa ganti false
                    'message' => 'Data profil masjid belum tersedia.',
                    'data'    => $profils
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar Profil Masjid',
                'data'    => $profils
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data profil masjid.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
