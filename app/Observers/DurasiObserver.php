<?php

namespace App\Observers;

use App\Models\Durasi;
use App\Services\MasjidActivityLogger;

class DurasiObserver
{
    private const FIELD_LABELS = [
        'adzan_imsak'          => ['label' => 'Durasi Adzan Imsak', 'type' => 'int'],
        'adzan_shuruq'         => ['label' => 'Durasi Adzan Shuruq', 'type' => 'int'],
        'adzan_dhuha'          => ['label' => 'Durasi Adzan Dhuha', 'type' => 'int'],
        'adzan_shubuh'         => ['label' => 'Durasi Adzan Shubuh', 'type' => 'int'],
        'iqomah_shubuh'        => ['label' => 'Durasi Iqomah Shubuh', 'type' => 'int'],
        'final_shubuh'         => ['label' => 'Durasi Final Shubuh', 'type' => 'int'],
        'adzan_dzuhur'         => ['label' => 'Durasi Adzan Dzuhur', 'type' => 'int'],
        'iqomah_dzuhur'        => ['label' => 'Durasi Iqomah Dzuhur', 'type' => 'int'],
        'final_dzuhur'         => ['label' => 'Durasi Final Dzuhur', 'type' => 'int'],
        'jumat_slide'          => ['label' => 'Durasi Slide Jumat', 'type' => 'int'],
        'adzan_ashar'          => ['label' => 'Durasi Adzan Ashar', 'type' => 'int'],
        'iqomah_ashar'         => ['label' => 'Durasi Iqomah Ashar', 'type' => 'int'],
        'final_ashar'          => ['label' => 'Durasi Final Ashar', 'type' => 'int'],
        'adzan_maghrib'        => ['label' => 'Durasi Adzan Maghrib', 'type' => 'int'],
        'iqomah_maghrib'        => ['label' => 'Durasi Iqomah Maghrib', 'type' => 'int'],
        'final_maghrib'        => ['label' => 'Durasi Final Maghrib', 'type' => 'int'],
        'adzan_isya'           => ['label' => 'Durasi Adzan Isya', 'type' => 'int'],
        'iqomah_isya'          => ['label' => 'Durasi Iqomah Isya', 'type' => 'int'],
        'final_isya'           => ['label' => 'Durasi Final Isya', 'type' => 'int'],
        'finance_scroll_speed' => ['label' => 'Kecepatan Scroll Laporan Keuangan', 'type' => 'float'],
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
        foreach (self::FIELD_LABELS as $field => $meta) {
            if (MasjidActivityLogger::hasFieldActuallyChanged($durasi, $field, $meta['type'])) {
                $changedLabels[] = $meta['label'];
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
