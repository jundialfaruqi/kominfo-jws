<?php

namespace App\Livewire\Faq;

use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Title('Frequently Asked Questions')]
    public function render()
    {
        return view('livewire.faq.index');
    }
}
