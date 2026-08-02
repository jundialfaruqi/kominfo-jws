<?php

namespace App\Livewire\DevicePairing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DevicePairing;
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

    public function unlinkDevice($id)
    {
        $pairing = DevicePairing::findOrFail($id);
        
        // Return status to pending or delete it? We'll just delete the pairing record
        // so the TV is forced to request a new code if it reconnects.
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
                              $q->where('nama_masjid', 'like', '%' . $this->search . '%');
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
