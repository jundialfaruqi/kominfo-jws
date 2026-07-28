<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $slug;
    private $type;

    public function __construct($identifier, $type)
    {
        // Konversi identifier (slug) menjadi ID masjid agar WebSocket menggunakan ID
        $profil = \App\Models\Profil::where('slug', $identifier)->first();
        if ($profil) {
            $this->slug = $profil->id;
        } else {
            $this->slug = $identifier;
        }
        $this->type = $type;
    }

    // public function broadcastOn()
    public function broadcastOn(): array
    {
        // return new Channel("masjid-{$this->slug}");
        return [
            new Channel("masjid-{$this->slug}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
