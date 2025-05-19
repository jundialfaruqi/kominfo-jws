<?php

namespace App\Livewire\Servertime;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ServerTime extends Component
{
    public $serverTime; // UTC time from server, raw
    public $serverTimestamp; // timestamp in milliseconds

    public function mount()
    {
        $response = Http::get('https://superapp.pekanbaru.go.id/api/server-time');

        if ($response->successful()) {
            $this->serverTime = $response['serverTime'];
            $this->serverTimestamp = strtotime($this->serverTime) * 1000; // in milliseconds
        } else {
            $this->serverTimestamp = null;
        }
    }

    public function render()
    {
        return view('livewire.servertime.server-time');
    }
}
