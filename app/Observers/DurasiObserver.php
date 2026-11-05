<?php

namespace App\Observers;

use App\Models\Durasi;
use App\Services\MasjidActivityLogger;

class DurasiObserver
{
    private const FIELD_LABELS = [
        'adzan_imsak'          => 'Durasi Adzan Imsak',
        'adzan_shuruq'         => 'Durasi Adzan Shuruq',
        'adzan_dhuha'          => 'Durasi Adzan Dhuha',
        'adzan_shubuh'         => 'Durasi Adzan Shubuh',
        'iqomah_shubuh'        => 'Durasi Iqomah Shubuh',
        'final_shubuh'         => 'Durasi Final Shubuh',
        'adzan_dzuhur'         => 'Durasi Adzan Dzuhur',
        'iqomah_dzuhur'        => 'Durasi Iqomah Dzuhur',
        'final_dzuhur'         => 'Durasi Final Dzuhur',
        'jumat_slide'          => 'Durasi Slide Jumat',
        'adzan_ashar'          => 'Durasi Adzan Ashar',
        'iqomah_ashar'         => 'Durasi Iqomah Ashar',
        'final_ashar'          => 'Durasi Final Ashar',
        'adzan_maghrib'        => 'Durasi Adzan Maghrib',
        'iqomah_maghrib'        => 'Durasi Iqomah Maghrib',
        'final_maghrib'        => 'Durasi Final Maghrib',
        'adzan_isya'           => 'Durasi Adzan Isya',
        'iqomah_isya'          => 'Durasi Iqomah Isya',
        'final_isya'           => 'Durasi Final Isya',
        'finance_scroll_speed' => 'Kecepatan Scroll Laporan Keuangan',
    ];

    public function saved(Durasi $durasi): void
    {
        if ($durasi->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'durasi',
                action: 'create',
                description: 'Menambahkan pengaturan Durasi Waktu Sholat & Keuangan',
                user: $durasi->user_id
            );
            return;
        }

        $changedLabels = [];
        foreach (self::FIELD_LABELS as $field => $label) {
            if ($durasi->wasChanged($field)) {
                $changedLabels[] = $label;
            }
        }

        if (!empty($changedLabels)) {
            if (count($changedLabels) <= 4) {
                $description = 'Mengubah pengaturan ' . implode(', ', $changedLabels);
            } else {
                $firstThree = array_slice($changedLabels, 0, 3);
                $remaining = count($changedLabels) - 3;
                $description = 'Mengubah pengaturan ' . implode(', ', $firstThree) . ' dan ' . $remaining . ' durasi lainnya';
            }

            MasjidActivityLogger::log(
                module: 'durasi',
                action: 'update',
                description: $description,
                user: $durasi->user_id
            );
        }
    }

    public function deleted(Durasi $durasi): void
    {
        MasjidActivityLogger::log(
            module: 'durasi',
            action: 'delete',
            description: 'Mereset / menghapus pengaturan Durasi',
            user: $durasi->user_id
        );
    }
}

