<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channels for real-time updates
Broadcast::channel('server-updates', function () {
    return true; // Public channel
});

Broadcast::channel('audio-updates', function () {
    return true; // Public channel
});

Broadcast::channel('content-updates', function () {
    return true; // Public channel
});

Broadcast::channel('adzan-updates', function () {
    return true; // Public channel
});

Broadcast::channel('profile-updates', function () {
    return true; // Public channel
});
