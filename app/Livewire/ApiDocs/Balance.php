<?php

namespace App\Livewire\ApiDocs;

use Livewire\Attributes\Title;
use Livewire\Component;

class Balance extends Component
{
    #[Title('Balance API Documentation')]
    public function render()
    {
        return view('livewire.api-docs.balance');
    }
}