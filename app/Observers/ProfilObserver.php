<?php

namespace App\Observers;

use App\Models\Profil;
use App\Models\User;
use App\Services\MasjidActivityLogger;

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

        if ($profil->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'profil',
                action: 'create',
                description: 'Membuat profil masjid "' . $profil->name . '"',
                profil: $profil,
                user: $profil->user_id
            );
            return;
        }

        $changes = [];

        if ($profil->wasChanged('name')) {
            $changes[] = 'mengubah nama menjadi "' . $profil->name . '"';
        }

        if ($profil->wasChanged('slug')) {
            $changes[] = 'mengubah slug (ID JWS) menjadi "' . $profil->slug . '"';
        }

        if ($profil->wasChanged('address')) {
            $changes[] = 'mengubah alamat';
        }

        if ($profil->wasChanged('phone')) {
            $changes[] = 'mengubah nomor telepon';
        }

        if ($profil->wasChanged('logo_masjid')) {
            $changes[] = empty($profil->logo_masjid)
                ? 'menghapus logo masjid'
                : 'mengubah logo masjid';
        }

        if ($profil->wasChanged('logo_pemerintah')) {
            $changes[] = empty($profil->logo_pemerintah)
                ? 'menghapus logo instansi'
                : 'mengubah logo instansi';
        }

        if (!empty($changes)) {
            $sentence = ucfirst(implode(', ', $changes));
            MasjidActivityLogger::log(
                module: 'profil',
                action: 'update',
                description: $sentence,
                profil: $profil,
                user: $profil->user_id
            );
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

        MasjidActivityLogger::log(
            module: 'profil',
            action: 'delete',
            description: 'Menghapus profil "' . $profil->name . '"',
            profil: null,
            user: $profil->user_id,
            fallbackMasjidName: $profil->name
        );
    }
}
