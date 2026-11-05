<?php

namespace App\Observers;

use App\Models\Audios;
use App\Services\MasjidActivityLogger;

class AudiosObserver
{
    public function saved(Audios $audios): void
    {
        if ($audios->wasRecentlyCreated) {
            $slots = [];
            for ($i = 1; $i <= 3; $i++) {
                $field = 'audio' . $i;
                if (!empty($audios->$field)) {
                    $slots[] = 'Audio ' . $i;
                }
            }
            $detail = !empty($slots) ? ' (' . implode(', ', $slots) . ')' : '';

            MasjidActivityLogger::log(
                module: 'audio_murottal',
                action: 'create',
                description: 'Menambahkan pengaturan Audio Murottal' . $detail,
                user: $audios->user_id
            );
            return;
        }

        $updatedSlots = [];
        $deletedSlots = [];

        for ($i = 1; $i <= 3; $i++) {
            $field = 'audio' . $i;
            if ($audios->wasChanged($field)) {
                if (empty($audios->$field)) {
                    $deletedSlots[] = 'Audio Murottal ' . $i;
                } else {
                    $updatedSlots[] = 'Audio Murottal ' . $i;
                }
            }
        }

        $parts = [];
        if (!empty($updatedSlots)) {
            $parts[] = 'mengubah file ' . implode(', ', $updatedSlots);
        }
        if (!empty($deletedSlots)) {
            $parts[] = 'menghapus file ' . implode(', ', $deletedSlots);
        }
        if ($audios->wasChanged('status')) {
            $parts[] = $audios->status
                ? 'mengaktifkan pemutaran Audio Murottal'
                : 'menonaktifkan pemutaran Audio Murottal';
        }

        if (!empty($parts)) {
            MasjidActivityLogger::log(
                module: 'audio_murottal',
                action: 'update',
                description: ucfirst(implode(' dan ', $parts)),
                user: $audios->user_id
            );
        }
    }

    public function deleted(Audios $audios): void
    {
        MasjidActivityLogger::log(
            module: 'audio_murottal',
            action: 'delete',
            description: 'Menghapus seluruh data Audio Murottal',
            user: $audios->user_id
        );
    }
}

