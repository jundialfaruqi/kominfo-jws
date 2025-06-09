<?php

namespace App\Livewire\Servertime;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Carbon\Carbon;

class ServerTime extends Component
{
    public $serverTime; // Raw time from server
    public $serverTimestamp; // Timestamp in milliseconds
    public $apiSource; // Track API source

    public function mount()
    {
        try {
            // Coba API utama
            $response = Http::get('https://superapp.pekanbaru.go.id/api/server-time');

            if ($response->successful()) {
                $this->serverTime = $response['serverTime'];
                $this->serverTimestamp = Carbon::parse($this->serverTime, 'UTC')
                    ->setTimezone('Asia/Jakarta')
                    ->timestamp * 1000; // in milliseconds
                $this->apiSource = 'pekanbaru';
            } else {
                throw new \Exception('API utama gagal');
            }
        } catch (\Exception $e) {
            // Fallback ke timeapi.io
            $fallbackResponse = Http::get('https://timeapi.io/api/time/current/zone?timeZone=Asia%2FJakarta');

            if ($fallbackResponse->successful()) {
                $this->serverTime = $fallbackResponse['dateTime'];
                $this->serverTimestamp = Carbon::parse($this->serverTime, 'Asia/Jakarta')
                    ->timestamp * 1000; // in milliseconds
                $this->apiSource = 'timeapi';
            } else {
                $this->serverTimestamp = null;
                $this->apiSource = null;
            }
        }
    }

    public function render()
    {
        return view('livewire.servertime.server-time');
    }
}
