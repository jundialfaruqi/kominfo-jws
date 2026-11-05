<?php

namespace App\Observers;

use App\Models\Marquee;
use App\Models\User;
use App\Services\MasjidActivityLogger;

class MarqueeObserver
{
    public function saved(Marquee $marquee): void
    {
        if ($marquee->user_id) {
            $user = User::find($marquee->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        if ($marquee->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'marquee',
                action: 'create',
                description: 'Menambahkan pengaturan Teks Berjalan (Marquee)',
                user: $marquee->user_id
            );
            return;
        }

        $changedTextSlots = [];
        for ($i = 1; $i <= 6; $i++) {
            $field = 'marquee' . $i;
            if ($marquee->wasChanged($field)) {
                $changedTextSlots[] = 'Teks ' . $i;
            }
        }

        $parts = [];
        if (!empty($changedTextSlots)) {
            $parts[] = 'mengubah isi Teks Berjalan (' . implode(', ', $changedTextSlots) . ')';
        }
        if ($marquee->wasChanged('marquee_speed')) {
            $parts[] = 'mengubah kecepatan Teks Berjalan menjadi ' . $marquee->marquee_speed;
        }

        if (!empty($parts)) {
            MasjidActivityLogger::log(
                module: 'marquee',
                action: 'update',
                description: ucfirst(implode(' dan ', $parts)),
                user: $marquee->user_id
            );
        }
    }

    public function deleted(Marquee $marquee): void
    {
        if ($marquee->user_id) {
            $user = User::find($marquee->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        MasjidActivityLogger::log(
            module: 'marquee',
            action: 'delete',
            description: 'Menghapus data Teks Berjalan (Marquee)',
            user: $marquee->user_id
        );
    }
}
