<?php

namespace App\Livewire\Agenda;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Agenda')]
class AgendaAll extends Component
{
    public function render()
    {
        return view('livewire.agenda.agenda-all');
    }
}
