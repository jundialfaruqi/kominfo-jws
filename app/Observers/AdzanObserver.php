<?php

namespace App\Observers;

use App\Models\Adzan;
use App\Services\MasjidActivityLogger;

class AdzanObserver
{
    private const SLOT_LABELS = [
        'adzan1'  => 'Slide Iqomah 1',
        'adzan2'  => 'Slide Iqomah 2',
        'adzan3'  => 'Slide Iqomah 3',
        'adzan4'  => 'Slide Iqomah 4',
        'adzan5'  => 'Slide Iqomah 5',
        'adzan6'  => 'Slide Iqomah 6',
        'adzan7'  => 'Slide Jumat 1',
        'adzan8'  => 'Slide Jumat 2',
        'adzan9'  => 'Slide Jumat 3',
        'adzan10' => 'Slide Jumat 4',
        'adzan11' => 'Slide Jumat 5',
        'adzan12' => 'Slide Jumat 6',
        'adzan13' => 'Slide Adzan 13',
        'adzan14' => 'Slide Adzan 14',
        'adzan15' => 'Gambar Setelah Iqomah (Final)',
    ];

    public function saved(Adzan $adzan): void
    {
        if ($adzan->wasRecentlyCreated) {
            $filled = [];
            foreach (self::SLOT_LABELS as $field => $label) {
                if (!empty($adzan->$field)) {
                    $filled[] = $label;
                }
            }
            $detail = !empty($filled) ? ' (' . implode(', ', $filled) . ')' : '';

            MasjidActivityLogger::log(
                module: 'slide_iqomah_jumat',
                action: 'create',
                description: 'Menambahkan gambar Slide Iqomah, Jumat & Final' . $detail,
                user: $adzan->user_id
            );
            return;
        }

        $updatedSlots = [];
        $deletedSlots = [];

        foreach (self::SLOT_LABELS as $field => $label) {
            if ($adzan->wasChanged($field)) {
                if (empty($adzan->$field)) {
                    $deletedSlots[] = $label;
                } else {
                    $updatedSlots[] = $label;
                }
            }
        }

        $parts = [];
        if (!empty($updatedSlots)) {
            $parts[] = 'mengubah gambar ' . implode(', ', $updatedSlots);
        }
        if (!empty($deletedSlots)) {
            $parts[] = 'menghapus gambar ' . implode(', ', $deletedSlots);
        }

        if (!empty($parts)) {
            MasjidActivityLogger::log(
                module: 'slide_iqomah_jumat',
                action: 'update',
                description: ucfirst(implode(' dan ', $parts)),
                user: $adzan->user_id
            );
        }
    }

    public function deleted(Adzan $adzan): void
    {
        MasjidActivityLogger::log(
            module: 'slide_iqomah_jumat',
            action: 'delete',
            description: 'Menghapus seluruh data Slide Iqomah, Jumat & Final',
            user: $adzan->user_id
        );
    }
}

