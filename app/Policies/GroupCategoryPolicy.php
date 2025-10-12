<?php

namespace App\Policies;

use App\Models\GroupCategory;
use App\Models\User;

class GroupCategoryPolicy
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
        return $user->can('view-group-category') || ($user->profil !== null);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GroupCategory $groupCategory): bool
    {
        // Boleh melihat jika punya permission
        if ($user->can('view-group-category')) {
            return true;
        }

        return $user->profil && $groupCategory->id_masjid === optional($user->profil)->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GroupCategory $groupCategory): bool
    {
        // Admin dan Super Admin boleh menghapus apapun
        if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }

        // User biasa harus punya permission dan hanya bisa hapus milik profilnya
        return $user->can('delete-group-category')
            && $user->profil
            && $groupCategory->id_masjid === optional($user->profil)->id;
    }
}