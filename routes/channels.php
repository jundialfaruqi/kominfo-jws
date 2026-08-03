<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Presence channel untuk TV (device).
// - Admin yang login: diizinkan subscribe untuk monitoring status online di /admin/tv-monitor
// - TV Flutter: diotorisasi via custom endpoint /api/v2/device/broadcasting/auth (bukan lewat sini)
Broadcast::channel('device.{deviceId}', function ($user, $deviceId) {
    \Illuminate\Support\Facades\Log::info("Admin broadcast auth for device $deviceId. User: " . ($user ? $user->id : 'null'));
    if ($user) {
        // Admin yang sudah login boleh masuk ke channel ini untuk monitoring
        return ['id' => 'admin-' . $user->id, 'name' => $user->name];
    }
    return false;
});
