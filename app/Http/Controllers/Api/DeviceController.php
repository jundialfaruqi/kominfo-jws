<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DevicePairing;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function requestCode(Request $request)
    {
        $request->validate([
            'device_id'    => 'required|string',
            'device_brand' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:100',
            'os_version'   => 'nullable|string|max:100',
        ]);

        $deviceId = $request->device_id;

        // Cek apakah device ini sudah ada pairing yang masih pending atau linked
        $pairing = DevicePairing::where('device_id', $deviceId)->first();

        if ($pairing) {
            // Jika sudah di-link, kembalikan status linked
            if ($pairing->status === 'linked') {
                return response()->json([
                    'status' => 'linked',
                    'masjid_id' => $pairing->profil_id,
                ]);
            }
            // Jika masih pending, kita bisa menggunakan kode lama atau membuat baru
            // Untuk amannya, buat kode baru
        } else {
            $pairing = new DevicePairing();
            $pairing->device_id = $deviceId;
        }

        // Update device info setiap kali request code (bisa ganti TV)
        $pairing->device_brand = $request->device_brand;
        $pairing->device_model = $request->device_model;
        $pairing->os_version   = $request->os_version;

        // Generate unik 6 karakter alphanumeric kapital (hindari O dan 0 jika mau, tapi Str::upper(Str::random(6)) sudah cukup)
        $code = strtoupper(Str::random(6));
        
        // Pastikan unik
        while (DevicePairing::where('pairing_code', $code)->exists()) {
            $code = strtoupper(Str::random(6));
        }

        $pairing->pairing_code = $code;
        $pairing->status = 'pending';
        $pairing->save();

        return response()->json([
            'status' => 'pending',
            'pairing_code' => $code,
        ]);
    }
}
