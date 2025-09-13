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
            // Gunakan waktu server langsung dengan Carbon
            $this->serverTime = Carbon::now('Asia/Jakarta')->toDateTimeString();
            $this->serverTimestamp = Carbon::now('Asia/Jakarta')->timestamp * 1000; // in milliseconds
            $this->apiSource = 'server';
            return;
        } catch (\Exception $e) {
            // Jika gagal menggunakan Carbon langsung, coba API eksternal
        }
        
        try {
            // Coba API utama
            $response = Http::timeout(5)->get('https://superapp.pekanbaru.go.id/api/server-time');

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
            try {
                // Fallback ke timeapi.io
                $fallbackResponse = Http::timeout(5)->get('https://timeapi.io/api/time/current/zone?timeZone=Asia%2FJakarta');

                if ($fallbackResponse->successful()) {
                    $this->serverTime = $fallbackResponse['dateTime'];
                    $this->serverTimestamp = Carbon::parse($this->serverTime, 'Asia/Jakarta')
                        ->timestamp * 1000; // in milliseconds
                    $this->apiSource = 'timeapi';
                } else {
                    throw new \Exception('API timeapi.io gagal');
                }
            } catch (\Exception $e) {
                try {
                    // Fallback ke API Google Script
                    $newApiResponse = Http::timeout(5)->get('https://script.google.com/macros/s/AKfycbyd5AcbAnWi2Yn0xhFRbyzS4qMq1VucMVgVvhul5XqS9HkAyJY/exec?tz=Asia/Jakarta');

                    if ($newApiResponse->successful() && $newApiResponse['status'] === 'ok') {
                        $this->serverTime = $newApiResponse['fulldate'];
                        $this->serverTimestamp = Carbon::parse($this->serverTime, 'Asia/Jakarta')
                            ->timestamp * 1000; // in milliseconds
                        $this->apiSource = 'google-script';
                    } else {
                        throw new \Exception('API Google Script gagal');
                    }
                } catch (\Exception $e) {
                    // Fallback ke waktu server lokal
                    $this->serverTime = Carbon::now('Asia/Jakarta')->toDateTimeString();
                    $this->serverTimestamp = Carbon::now('Asia/Jakarta')->timestamp * 1000; // in milliseconds
                    $this->apiSource = 'local';
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.servertime.server-time');
    }
}
