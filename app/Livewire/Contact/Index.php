<?php

namespace App\Livewire\Contact;

use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Title('Contact')]
    public function render()
    {
        return view('livewire.contact.index');
    }
}
