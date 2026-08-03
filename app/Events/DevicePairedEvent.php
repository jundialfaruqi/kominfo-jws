<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\DevicePairing;

class DevicePairedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DevicePairing $devicePairing;

    /**
     * Create a new event instance.
     */
    public function __construct(DevicePairing $devicePairing)
    {
        $this->devicePairing = $devicePairing;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('device-' . $this->devicePairing->device_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'device.paired';
    }

    public function broadcastWith(): array
    {
        // Menyertakan slug masjid yang tertaut agar TV tahu profil mana yang di-load
        $profil = $this->devicePairing->profil;
        return [
            'status' => 'linked',
            'masjid_id' => $this->devicePairing->profil_id,
            'slug' => $profil ? $profil->slug : null,
        ];
    }
}
