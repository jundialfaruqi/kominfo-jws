<?php

namespace App\Policies;

use App\Models\Laporan;
use App\Models\User;

class LaporanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin dan Super Admin selalu boleh
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }
        // User biasa boleh melihat jika punya permission atau memiliki profil masjid
        return $user->can('view-laporan-keuangan') || ($user->profil !== null);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Laporan $laporan): bool
    {
        // Boleh melihat jika punya permission atau memiliki laporan tersebut
        if ($user->can('view-laporan-keuangan')) {
            return true;
        }

        return $user->profil && $laporan->id_masjid === optional($user->profil)->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin dan Super Admin boleh membuat
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }
        // User biasa boleh membuat jika punya permission ATAU memiliki profil masjid
        return $user->can('create-laporan-keuangan') || ($user->profil !== null);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Laporan $laporan): bool
    {
        // Admin/Super Admin boleh update semua, atau user dengan permission yang memiliki laporan
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }

        return $user->can('edit-laporan-keuangan') && $user->profil && $laporan->id_masjid === optional($user->profil)->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Laporan $laporan): bool
    {
        // Admin/Super Admin boleh delete semua, atau user dengan permission yang memiliki laporan
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }

        return $user->can('delete-laporan-keuangan') && $user->profil && $laporan->id_masjid === optional($user->profil)->id;
    }
}