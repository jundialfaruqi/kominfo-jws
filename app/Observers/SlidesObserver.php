<?php

namespace App\Observers;

use App\Models\Slides;
use App\Models\User;
use App\Services\MasjidActivityLogger;

class SlidesObserver
{
    public function saved(Slides $slides): void
    {
        if ($slides->user_id) {
            $user = User::find($slides->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        if ($slides->wasRecentlyCreated) {
            $filled = [];
            for ($i = 1; $i <= 6; $i++) {
                $field = 'slide' . $i;
                if (!empty($slides->$field)) {
                    $filled[] = 'Slide ' . $i;
                }
            }
            $detail = !empty($filled) ? ' (' . implode(', ', $filled) . ')' : '';

            MasjidActivityLogger::log(
                module: 'slide_utama',
                action: 'create',
                description: 'Menambahkan gambar Slide Utama' . $detail,
                user: $slides->user_id
            );
            return;
        }

        $updatedSlots = [];
        $deletedSlots = [];

        for ($i = 1; $i <= 6; $i++) {
            $field = 'slide' . $i;
            if ($slides->wasChanged($field)) {
                if (empty($slides->$field)) {
                    $deletedSlots[] = 'Slide ' . $i;
                } else {
                    $updatedSlots[] = 'Slide ' . $i;
                }
            }
        }

        $parts = [];
        if (!empty($updatedSlots)) {
            $parts[] = 'mengubah gambar Slide Utama (' . implode(', ', $updatedSlots) . ')';
        }
        if (!empty($deletedSlots)) {
            $parts[] = 'menghapus gambar Slide Utama (' . implode(', ', $deletedSlots) . ')';
        }

        if (!empty($parts)) {
            MasjidActivityLogger::log(
                module: 'slide_utama',
                action: 'update',
                description: ucfirst(implode(' dan ', $parts)),
                user: $slides->user_id
            );
        }
    }

    public function deleted(Slides $slides): void
    {
        if ($slides->user_id) {
            $user = User::find($slides->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        MasjidActivityLogger::log(
            module: 'slide_utama',
            action: 'delete',
            description: 'Menghapus seluruh data Slide Utama',
            user: $slides->user_id
        );
    }
}
