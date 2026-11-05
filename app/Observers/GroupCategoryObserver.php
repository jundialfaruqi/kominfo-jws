<?php

namespace App\Observers;

use App\Models\GroupCategory;
use App\Services\MasjidActivityLogger;

class GroupCategoryObserver
{
    public function saved(GroupCategory $groupCategory): void
    {
        if (empty($groupCategory->id_masjid)) {
            return;
        }

        $name = trim((string) $groupCategory->name) ?: '-';

        if ($groupCategory->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'group_category',
                action: 'create',
                description: 'Membuat kategori laporan keuangan "' . $name . '"',
                profil: (int) $groupCategory->id_masjid
            );
            return;
        }

        if ($groupCategory->wasChanged('name')) {
            MasjidActivityLogger::log(
                module: 'group_category',
                action: 'update',
                description: 'Mengubah kategori laporan keuangan menjadi "' . $name . '"',
                profil: (int) $groupCategory->id_masjid
            );
        }
    }

    public function deleted(GroupCategory $groupCategory): void
    {
        if (empty($groupCategory->id_masjid)) {
            return;
        }

        $name = trim((string) $groupCategory->name) ?: '-';

        MasjidActivityLogger::log(
            module: 'group_category',
            action: 'delete',
            description: 'Menghapus kategori laporan keuangan "' . $name . '"',
            profil: (int) $groupCategory->id_masjid
        );
    }
}

