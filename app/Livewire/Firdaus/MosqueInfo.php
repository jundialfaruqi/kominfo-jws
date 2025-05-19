<?php

namespace App\Livewire\Firdaus;

use App\Models\Profil;
use Livewire\Attributes\Title;
use Livewire\Component;

class MosqueInfo extends Component
{
    #[Title('Mosque Info')]

    // New properties for related models
    public $profil;

    public function mount($slug)
    {
        // Fetch Profil by slug instead of id
        $this->profil = Profil::where('slug', $slug)->firstOrFail();
    }
    public function render()
    {
        return view('livewire.firdaus.mosque-info', [
            'profil' => $this->profil,
        ]);
    }
}
