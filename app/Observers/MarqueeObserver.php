<?php

namespace App\Observers;

use App\Models\Marquee;
use App\Models\User;

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
    }

    public function deleted(Marquee $marquee): void
    {
        if ($marquee->user_id) {
            $user = User::find($marquee->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }
    }
}