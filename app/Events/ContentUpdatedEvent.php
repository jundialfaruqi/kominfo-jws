<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ContentUpdatedEvent implements ShouldBroadcast
{
    // use SerializesModels;

    public $masjidId;
    public $type;

    public function __construct($masjidId, $type)
    {
        $this->masjidId = $masjidId;
        $this->type = $type;
    }

    public function broadcastOn()
    {
        return new Channel("masjid-{$this->masjidId}");
    }
    
}
