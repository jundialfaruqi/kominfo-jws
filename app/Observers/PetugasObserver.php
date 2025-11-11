<?php

namespace App\Observers;

use App\Models\Petugas;
use App\Models\User;

class PetugasObserver
{
    public function saved(Petugas $petugas): void
    {
        if ($petugas->user_id) {
            $user = User::find($petugas->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }

    public function deleted(Petugas $petugas): void
    {
        if ($petugas->user_id) {
            $user = User::find($petugas->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }
}