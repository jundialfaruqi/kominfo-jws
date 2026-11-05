<?php

namespace App\Observers;

use App\Models\Agenda;
use App\Services\MasjidActivityLogger;

class AgendaObserver
{
    public function saved(Agenda $agenda): void
    {
        $profilId = $agenda->id_masjid ? (int) $agenda->id_masjid : null;
        $userId = $agenda->id_user ? (int) $agenda->id_user : null;
        $name = trim((string) $agenda->name) ?: '-';

        if ($agenda->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'agenda',
                action: 'create',
                description: 'Menambahkan agenda: "' . $name . '"',
                profil: $profilId,
                user: $userId
            );
            return;
        }

        $nameChanged = MasjidActivityLogger::hasFieldActuallyChanged($agenda, 'name', 'string');
        $dateChanged = MasjidActivityLogger::hasFieldActuallyChanged($agenda, 'date', 'string');
        $aktifChanged = MasjidActivityLogger::hasFieldActuallyChanged($agenda, 'aktif', 'bool');

        if ($nameChanged || $dateChanged || $aktifChanged) {
            if ($aktifChanged && !$nameChanged && !$dateChanged) {
                $statusText = $agenda->aktif ? 'Mengaktifkan' : 'Menonaktifkan';
                $description = $statusText . ' agenda: "' . $name . '"';
            } else {
                $description = 'Mengubah agenda: "' . $name . '"';
            }

            MasjidActivityLogger::log(
                module: 'agenda',
                action: 'update',
                description: $description,
                profil: $profilId,
                user: $userId
            );
        }
    }

    public function deleted(Agenda $agenda): void
    {
        $profilId = $agenda->id_masjid ? (int) $agenda->id_masjid : null;
        $userId = $agenda->id_user ? (int) $agenda->id_user : null;
        $name = trim((string) $agenda->name) ?: '-';

        MasjidActivityLogger::log(
            module: 'agenda',
            action: 'delete',
            description: 'Menghapus agenda: "' . $name . '"',
            profil: $profilId,
            user: $userId
        );
    }
}

