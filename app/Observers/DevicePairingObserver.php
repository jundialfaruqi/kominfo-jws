<?php

namespace App\Observers;

use App\Models\DevicePairing;
use App\Services\MasjidActivityLogger;

class DevicePairingObserver
{
    public function saved(DevicePairing $pairing): void
    {
        // Abaikan proses request kode pairing saat status masih 'pending' atau belum memiliki profil_id
        if ($pairing->status !== 'linked' || empty($pairing->profil_id)) {
            return;
        }

        // Hanya catat ketika perangkat baru ditautkan atau status/profil_id berubah menjadi linked
        if ($pairing->wasRecentlyCreated || $pairing->wasChanged('status') || $pairing->wasChanged('profil_id')) {
            $deviceLabel = $this->formatDeviceLabel($pairing);

            MasjidActivityLogger::log(
                module: 'tv_pairing',
                action: 'pair',
                description: 'Menautkan perangkat TV (' . $deviceLabel . ')',
                profil: (int) $pairing->profil_id
            );
        }
    }

    public function deleted(DevicePairing $pairing): void
    {
        // Hanya catat jika TV yang dihapus sebelumnya sudah tertaut ke profil masjid
        if (empty($pairing->profil_id)) {
            return;
        }

        $deviceLabel = $this->formatDeviceLabel($pairing);

        MasjidActivityLogger::log(
            module: 'tv_pairing',
            action: 'unpair',
            description: 'Memutuskan tautan perangkat TV (' . $deviceLabel . ')',
            profil: (int) $pairing->profil_id
        );
    }

    private function formatDeviceLabel(DevicePairing $pairing): string
    {
        $brandModel = trim(($pairing->device_brand ?? '') . ' ' . ($pairing->device_model ?? ''));
        if ($brandModel !== '') {
            return $brandModel;
        }

        return trim((string) $pairing->device_id) ?: 'Smart TV';
    }
}

