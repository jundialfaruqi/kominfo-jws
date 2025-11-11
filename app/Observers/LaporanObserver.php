<?php

namespace App\Observers;

use App\Models\Laporan;
use App\Models\Profil;
use App\Models\User;

class LaporanObserver
{
    public function saved(Laporan $laporan): void
    {
        // Map laporan -> profil -> user
        $profil = Profil::find($laporan->id_masjid);
        if ($profil && $profil->user_id) {
            $user = User::find($profil->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }

    public function deleted(Laporan $laporan): void
    {
        $profil = Profil::find($laporan->id_masjid);
        if ($profil && $profil->user_id) {
            $user = User::find($profil->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }
}