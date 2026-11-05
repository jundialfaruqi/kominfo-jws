<?php

namespace App\Observers;

use App\Models\JumbotronMasjid;
use App\Services\MasjidActivityLogger;

class JumbotronMasjidObserver
{
    public function saved(JumbotronMasjid $jumbotronMasjid): void
    {
        $profilId = $jumbotronMasjid->masjid_id ? (int) $jumbotronMasjid->masjid_id : null;
        $userId = $jumbotronMasjid->created_by ? (int) $jumbotronMasjid->created_by : null;

        if ($jumbotronMasjid->wasRecentlyCreated) {
            $filled = [];
            for ($i = 1; $i <= 6; $i++) {
                $field = 'jumbotron_masjid_' . $i;
                if (!empty($jumbotronMasjid->$field)) {
                    $filled[] = 'Jumbotron ' . $i;
                }
            }
            $detail = !empty($filled) ? ' (' . implode(', ', $filled) . ')' : '';

            MasjidActivityLogger::log(
                module: 'jumbotron_masjid',
                action: 'create',
                description: 'Menambahkan gambar Jumbotron Masjid' . $detail,
                profil: $profilId,
                user: $userId
            );
            return;
        }

        $updatedSlots = [];
        $deletedSlots = [];

        for ($i = 1; $i <= 6; $i++) {
            $field = 'jumbotron_masjid_' . $i;
            if ($jumbotronMasjid->wasChanged($field)) {
                if (empty($jumbotronMasjid->$field)) {
                    $deletedSlots[] = 'Jumbotron ' . $i;
                } else {
                    $updatedSlots[] = 'Jumbotron ' . $i;
                }
            }
        }

        $parts = [];
        if (!empty($updatedSlots)) {
            $parts[] = 'mengubah gambar Jumbotron Masjid (' . implode(', ', $updatedSlots) . ')';
        }
        if (!empty($deletedSlots)) {
            $parts[] = 'menghapus gambar Jumbotron Masjid (' . implode(', ', $deletedSlots) . ')';
        }
        if ($jumbotronMasjid->wasChanged('aktif')) {
            $parts[] = $jumbotronMasjid->aktif
                ? 'mengaktifkan tampilan Jumbotron Masjid'
                : 'menonaktifkan tampilan Jumbotron Masjid';
        }

        if (!empty($parts)) {
            MasjidActivityLogger::log(
                module: 'jumbotron_masjid',
                action: 'update',
                description: ucfirst(implode(' dan ', $parts)),
                profil: $profilId,
                user: $userId
            );
        }
    }

    public function deleted(JumbotronMasjid $jumbotronMasjid): void
    {
        $profilId = $jumbotronMasjid->masjid_id ? (int) $jumbotronMasjid->masjid_id : null;
        $userId = $jumbotronMasjid->created_by ? (int) $jumbotronMasjid->created_by : null;

        MasjidActivityLogger::log(
            module: 'jumbotron_masjid',
            action: 'delete',
            description: 'Menghapus seluruh data Jumbotron Masjid',
            profil: $profilId,
            user: $userId
        );
    }
}

