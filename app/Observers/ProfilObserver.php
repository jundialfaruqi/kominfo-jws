<?php

namespace App\Observers;

use App\Models\Profil;
use App\Models\User;

class ProfilObserver
{
    public function saved(Profil $profil): void
    {
        if ($profil->user_id) {
            $user = User::find($profil->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }

    public function deleted(Profil $profil): void
    {
        if ($profil->user_id) {
            $user = User::find($profil->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }
}