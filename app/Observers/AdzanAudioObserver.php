<?php

namespace App\Observers;

use App\Models\AdzanAudio;
use App\Services\MasjidActivityLogger;

class AdzanAudioObserver
{
    public function saved(AdzanAudio $adzanAudio): void
    {
        if ($adzanAudio->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'audio_adzan',
                action: 'create',
                description: 'Menambahkan pengaturan Audio Adzan',
                user: $adzanAudio->user_id
            );
            return;
        }

        $parts = [];

        if ($adzanAudio->wasChanged('audioadzan')) {
            $parts[] = empty($adzanAudio->audioadzan)
                ? 'menghapus file Audio Adzan Umum'
                : 'mengubah file Audio Adzan Umum';
        }

        if ($adzanAudio->wasChanged('adzanshubuh')) {
            $parts[] = empty($adzanAudio->adzanshubuh)
                ? 'menghapus file Audio Adzan Shubuh'
                : 'mengubah file Audio Adzan Shubuh';
        }

        if ($adzanAudio->wasChanged('status')) {
            $parts[] = $adzanAudio->status
                ? 'mengaktifkan pemutaran Audio Adzan'
                : 'menonaktifkan pemutaran Audio Adzan';
        }

        if (!empty($parts)) {
            MasjidActivityLogger::log(
                module: 'audio_adzan',
                action: 'update',
                description: ucfirst(implode(' dan ', $parts)),
                user: $adzanAudio->user_id
            );
        }
    }

    public function deleted(AdzanAudio $adzanAudio): void
    {
        MasjidActivityLogger::log(
            module: 'audio_adzan',
            action: 'delete',
            description: 'Menghapus seluruh data Audio Adzan',
            user: $adzanAudio->user_id
        );
    }
}

