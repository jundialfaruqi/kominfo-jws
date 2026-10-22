<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class ForgotPassword extends Component
{
    #[Layout('components.layouts.auth')]
    #[Title('JWS - Lupa Password')]

    public $email;

    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
    ];

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }

    public function sendResetLink()
    {
        $this->validate();

        // Selalu kirim request ke Password::sendResetLink (jika email tidak ditemukan,
        // Laravel secara internal tidak mengirim email, tapi kita tetap tampilkan
        // pesan yang sama untuk mencegah user enumeration / account disclosure).
        Password::sendResetLink(['email' => $this->email]);

        // Pesan generik — tidak membedakan email terdaftar atau tidak (OWASP: CWE-204)
        session()->flash('status', 'Jika email Anda terdaftar di sistem, link reset password akan segera dikirim.');
        $this->reset('email');
    }
}
