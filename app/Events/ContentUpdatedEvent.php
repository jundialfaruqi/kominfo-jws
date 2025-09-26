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

    public function __construct($slug, $type)
    {
        $this->slug = $slug;
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
