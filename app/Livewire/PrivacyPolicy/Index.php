<?php

namespace App\Livewire\PrivacyPolicy;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Layout('components.layouts.article')]
    #[Title('Kebijakan Privasi & Formulir Kepatuhan - JWS Kominfo Kota Pekanbaru')]
    public function render()
    {
        return view('livewire.privacy-policy.index');
    }
}
