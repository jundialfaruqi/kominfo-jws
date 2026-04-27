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
    public $isTimeSynced = false;

    public function mount()
    {
        try {
            // Priority 1: Local Server (Main Priority)
            $this->serverTime = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $this->serverTimestamp = Carbon::now('Asia/Jakarta')->timestamp * 1000; // in milliseconds
            $this->apiSource = 'server';
            $this->isTimeSynced = true;
            return;

            // Ambil waktu dari time.now API
            $timeResponse = Http::timeout(5)->get('https://time.now/developer/api/timezone/Asia/Jakarta');
            if ($timeResponse->successful()) {
                $timeData = $timeResponse->json();
                $serverNow = Carbon::createFromTimestamp($timeData['unixtime'], 'Asia/Jakarta');
                $this->serverTime = $serverNow->toDateTimeString();
                $this->serverTimestamp = $serverNow->timestamp * 1000;
                $this->apiSource = 'time.now';
            } else {
                throw new \Exception('API time.now gagal');
            }
        } catch (\Exception $e) {
            try {
                // Fallback 1: timeapi.io
                $fallbackResponse = Http::timeout(5)->get('https://timeapi.io/api/time/current/zone?timeZone=Asia%2FJakarta');
                if ($fallbackResponse->successful()) {
                    $serverDateTime = new \DateTime($fallbackResponse['dateTime'], new \DateTimeZone('Asia/Jakarta'));
                    $this->serverTime = $serverDateTime->format('Y-m-d H:i:s');
                    $this->serverTimestamp = $serverDateTime->getTimestamp() * 1000;
                    $this->apiSource = 'timeapi';
                } else {
                    throw new \Exception('API timeapi.io gagal');
                }
            } catch (\Exception $e) {
                // Fallback 2: waktu lokal server (Carbon)
                $serverNow = Carbon::now('Asia/Jakarta');
                $this->serverTime = $serverNow->toDateTimeString();
                $this->serverTimestamp = $serverNow->timestamp * 1000;
                $this->apiSource = 'local';
            }
        }
    }

    public function render()
    {
        return view('livewire.servertime.server-time');
    }
}
