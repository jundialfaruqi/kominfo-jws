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

        session()->flash('success', 'TV berhasil diputus dari masjid.');
    }

    public function render()
    {
        $devices = DevicePairing::with('profil')
            ->where('status', 'linked')
            ->where(function ($query) {
                if (!empty($this->search)) {
                    $query->where('pairing_code', 'like', '%' . $this->search . '%')
                          ->orWhereHas('profil', function ($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          });
                }
            })
            ->latest()
            ->paginate(15);

        return view('livewire.device-pairing.monitor', [
            'devices' => $devices
        ]);
    }
}
