<?php

namespace App\Livewire\Inactive;

use Livewire\Component;
use Livewire\Attributes\Title;

class Inactive extends Component
{
    #[Title('Status Akun')]
    public function render()
    {
        return view('livewire.inactive.inactive');
    }
}
