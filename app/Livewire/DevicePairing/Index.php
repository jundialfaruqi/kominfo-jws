<?php

namespace App\Livewire\DevicePairing;

use Livewire\Component;
use App\Models\DevicePairing;
use App\Events\DevicePairedEvent;
use App\Events\DeviceUnpairedEvent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

class Index extends Component
{
    #[Title('Tautkan TV')]
    public $pairingCode = '';
    public $message = '';
    public $messageType = ''; // 'success' or 'error'

    public function linkDevice()
    {
        $this->validate([
            'pairingCode' => 'required|string|size:6',
        ]);

        $code = strtoupper($this->pairingCode);
        $pairing = DevicePairing::where('pairing_code', $code)->where('status', 'pending')->first();

        if (!$pairing) {
            $this->message = 'Kode tidak valid atau sudah kadaluarsa.';
            $this->messageType = 'error';
            return;
        }

        // Ambil masjid_id dari user yang login
        $user = Auth::user();
        if (!$user || !$user->profil) {
            $this->message = 'Akun Anda belum memiliki profil masjid.';
            $this->messageType = 'error';
            return;
        }

        $pairing->profil_id = $user->profil->id;
        $pairing->status = 'linked';
        $pairing->save();

        // Broadcast event ke TV
        broadcast(new DevicePairedEvent($pairing));

        $this->message = 'TV berhasil ditautkan ke Masjid Anda!';
        $this->messageType = 'success';
        $this->pairingCode = '';
    }

    public function unlinkDevice(int $id)
    {
        $user = Auth::user();
        $pairing = DevicePairing::where('id', $id)
            ->whereHas('profil', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        broadcast(new DeviceUnpairedEvent($pairing->device_id));
        $pairing->delete();

        $this->message = 'TV berhasil diputus dari masjid.';
        $this->messageType = 'success';
    }

    public function render()
    {
        $user = Auth::user();
        $devices = DevicePairing::with('profil')
            ->where('status', 'linked')
            ->whereHas('profil', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();

        return view('livewire.device-pairing.index', compact('devices'));
    }
}
