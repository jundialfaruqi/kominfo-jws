<?php

namespace App\Livewire\Inactive;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

class Inactive extends Component
{
    #[Title('Status Akun')]

    public function mount()
    {
        if (Auth::check() && Auth::user()->status === 'Active') {
            return redirect()->route('dashboard.index');
        }
    }

    public function render()
    {
        return view('livewire.inactive.inactive');
    }
}
