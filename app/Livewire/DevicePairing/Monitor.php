<?php

namespace App\Livewire\DevicePairing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DevicePairing;
use App\Events\DeviceUnpairedEvent;
use Livewire\Attributes\Title;

class Monitor extends Component
{
    use WithPagination;

    #[Title('Monitor TV Masjid')]
    protected $paginationTheme = 'bootstrap';
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function unlinkDevice(int $id)
    {
        $pairing = DevicePairing::findOrFail($id);
        
        // Broadcast event ke TV yang mungkin sedang menyala agar ia memutuskan koneksinya secara real-time
        broadcast(new DeviceUnpairedEvent($pairing->device_id));

        // Hapus data pairing
        $pairing->delete();

        $this->dispatch('success', message: 'TV berhasil diputus dari masjid.');
    }

    public function render()
    {
        $devices = DevicePairing::select('device_pairings.*')
            ->leftJoin('profils', 'device_pairings.profil_id', '=', 'profils.id')
            ->with('profil')
            ->where('device_pairings.status', 'linked')
            ->where(function ($query) {
                if (!empty($this->search)) {
                    $query->where('device_pairings.pairing_code', 'like', '%' . $this->search . '%')
                          ->orWhere('profils.name', 'like', '%' . $this->search . '%');
                }
            })
            ->orderBy('profils.name', 'asc')
            ->orderBy('device_pairings.created_at', 'desc')
            ->paginate(10);

        return view('livewire.device-pairing.monitor', [
            'devices' => $devices
        ]);
    }
}
