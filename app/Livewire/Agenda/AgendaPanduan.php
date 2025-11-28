<?php

namespace App\Livewire\Agenda;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Panduan Agenda')]
class AgendaPanduan extends Component
{
    public function render()
    {
        return view('livewire.agenda.agenda-panduan');
    }
}
