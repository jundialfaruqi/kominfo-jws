<?php

namespace App\Observers;

use App\Models\Theme;
use App\Models\User;
use App\Services\MasjidActivityLogger;

class UserObserver
{
    /**
     * Hanya merekam ketika user/masjid mengganti atau mereset tema JWS (kolom theme_id).
     * Mengabaikan perubahan last_activity_at, remember_token, password, dll.
     */
    public function updated(User $user): void
    {
        if (!$user->wasChanged('theme_id')) {
            return;
        }

        if (!empty($user->theme_id)) {
            $themeName = Theme::where('id', $user->theme_id)->value('name') ?: ('Tema #' . $user->theme_id);
            $description = 'Mengganti tema JWS ke "' . $themeName . '"';
        } else {
            $description = 'Mereset tema JWS ke tema default';
        }

        MasjidActivityLogger::log(
            module: 'tema',
            action: 'update',
            description: $description,
            user: $user
        );
    }
}

