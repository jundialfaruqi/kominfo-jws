<?php

namespace App\Livewire\DevicePairing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DevicePairing;
use App\Events\DeviceUnpairedEvent;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

class Monitor extends Component
{
    use WithPagination;

    #[Title('Monitor TV Masjid')]
    protected $paginationTheme = 'bootstrap';

    #[Url(except: '')]
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
        $keyword = trim((string) $this->search);

        $devices = DevicePairing::select('device_pairings.*')
            ->leftJoin('profils', 'device_pairings.profil_id', '=', 'profils.id')
            ->with('profil')
            ->where('device_pairings.status', 'linked')
            ->where(function ($query) use ($keyword) {
                if (strlen($keyword) >= 3) {
                    $query->where('device_pairings.pairing_code', 'like', '%' . $keyword . '%')
                          ->orWhere('profils.name', 'like', '%' . $keyword . '%');
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
