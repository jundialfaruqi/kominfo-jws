<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DevicePairing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class DeviceBroadcastAuthController extends Controller
{
    /**
     * Autentikasi TV ke Presence Channel tanpa memerlukan login email/password.
     * TV cukup mengirimkan device_id yang sudah tersimpan di perangkat.
     */
    public function auth(Request $request)
    {
        $request->validate([
            'socket_id'    => 'required|string',
            'channel_name' => 'required|string',
        ]);

        // device_id dikirim sebagai header dari Flutter TV
        $deviceId    = $request->header('X-Device-ID');
        if (!$deviceId) {
            return response()->json(['error' => 'Device ID missing'], 403);
        }

        $socketId    = $request->socket_id;
        $channelName = $request->channel_name;

        // Hanya izinkan presence channel milik device ini
        $expectedChannel = 'presence-device-' . $deviceId;
        if ($channelName !== $expectedChannel) {
            return response()->json(['error' => 'Unauthorized channel'], 403);
        }

        // Cek apakah device terdaftar (boleh pending atau linked)
        $pairing = DevicePairing::where('device_id', $deviceId)->first();

        if (!$pairing) {
            return response()->json(['error' => 'Device not registered'], 403);
        }

        // Buat tanda tangan Pusher secara manual (tanpa Auth::user())
        $appKey    = config('broadcasting.connections.reverb.key');
        $appSecret = config('broadcasting.connections.reverb.secret');

        $stringToSign = $socketId . ':' . $channelName;
        $signature    = hash_hmac('sha256', $stringToSign, $appSecret);
        $auth         = $appKey . ':' . $signature;

        // Data identitas TV yang akan tampil di presence channel
        $channelData = [
            'user_id'   => $deviceId,
            'user_info' => [
                'brand' => $pairing->device_brand,
                'model' => $pairing->device_model,
                'os'    => $pairing->os_version,
            ],
        ];

        $channelDataJson      = json_encode($channelData);
        $channelDataSignature = hash_hmac('sha256', $socketId . ':' . $channelName . ':' . $channelDataJson, $appSecret);
        $authWithData         = $appKey . ':' . $channelDataSignature;

        return response()->json([
            'auth'         => $authWithData,
            'channel_data' => $channelDataJson,
        ]);
    }
}
