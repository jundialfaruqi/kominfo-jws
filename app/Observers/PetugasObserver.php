<?php

namespace App\Observers;

use App\Models\Petugas;
use App\Models\User;
use App\Services\MasjidActivityLogger;

class PetugasObserver
{
    public function saved(Petugas $petugas): void
    {
        if ($petugas->user_id) {
            $user = User::find($petugas->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        $hari = trim((string) $petugas->hari) ?: '-';
        $khatib = trim((string) $petugas->khatib) ?: '-';

        if ($petugas->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'petugas_jumat',
                action: 'create',
                description: 'Menambahkan jadwal Petugas Jumat (' . $hari . ') - Khatib: ' . $khatib,
                user: $petugas->user_id
            );
            return;
        }

        if ($petugas->wasChanged(['hari', 'khatib', 'imam', 'muadzin'])) {
            MasjidActivityLogger::log(
                module: 'petugas_jumat',
                action: 'update',
                description: 'Mengubah jadwal Petugas Jumat (' . $hari . ') - Khatib: ' . $khatib,
                user: $petugas->user_id
            );
        }
    }

    public function deleted(Petugas $petugas): void
    {
        if ($petugas->user_id) {
            $user = User::find($petugas->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        $hari = trim((string) $petugas->hari) ?: '-';

        MasjidActivityLogger::log(
            module: 'petugas_jumat',
            action: 'delete',
            description: 'Menghapus jadwal Petugas Jumat (' . $hari . ')',
            user: $petugas->user_id
        );
    }
}
