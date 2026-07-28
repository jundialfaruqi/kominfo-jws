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

    private $slugChannel;
    private $idChannel;
    private $type;

    public function __construct($identifier, $type)
    {
        $this->slugChannel = $identifier; // Channel untuk Web (menggunakan slug asli)
        
        // Konversi identifier (slug) menjadi ID masjid agar WebSocket Flutter menggunakan ID
        $profil = \App\Models\Profil::where('slug', $identifier)->first();
        if ($profil) {
            $this->idChannel = $profil->id;
        } else {
            $this->idChannel = $identifier;
        }
        $this->type = $type;
    }

    // public function broadcastOn()
    public function broadcastOn(): array
    {
        // Dual-Broadcast: Kirim ke channel ID (untuk Flutter) dan channel slug (untuk Web)
        return [
            new Channel("masjid-{$this->idChannel}"),
            new Channel("masjid-{$this->slugChannel}"),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
