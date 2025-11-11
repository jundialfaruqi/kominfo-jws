<?php

namespace App\Observers;

use App\Models\Slides;
use App\Models\User;

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
    }

    public function deleted(Slides $slides): void
    {
        if ($slides->user_id) {
            $user = User::find($slides->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }
}