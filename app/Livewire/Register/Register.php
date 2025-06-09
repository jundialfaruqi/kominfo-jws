<?php

namespace App\Livewire\Register;

use App\Models\User;
use App\Models\Profil;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Register extends Component
{
    #[Layout('components.layouts.register')]
    #[Title('Jadwal Sholat - Register')]

    public $name;
    public $email;
    public $address;
    public $password;
    public $password_confirmation;
    public $phone;
    public $masjid;
    public $terms_agreed = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'address' => 'required|string|max:255',
        'phone' => 'required|string|max:15',
        'password' => 'required|min:6',
        'password_confirmation' => 'required|min:6|same:password',
        'masjid' => 'required|string|max:255',
        'terms_agreed' => 'accepted',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi',
        'name.max' => 'Nama maksimal 255 karakter',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.max' => 'Email maksimal 255 karakter',
        'email.unique' => 'Email sudah terdaftar',
        'address.required' => 'Alamat wajib diisi',
        'address.max' => 'Alamat maksimal 255 karakter',
        'phone.required' => 'Nomor telepon wajib diisi',
        'phone.max' => 'Nomor telepon maksimal 15 karakter',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'password_confirmation.same' => 'Password tidak sama',
        'password_confirmation.required' => 'Konfirmasi password wajib diisi',
        'password_confirmation.min' => 'Konfirmasi password minimal 6 karakter',
        'masjid.required' => 'Nama masjid wajib diisi',
        'masjid.max' => 'Nama masjid maksimal 255 karakter',
        'terms_agreed.accepted' => 'Anda harus menyetujui syarat dan ketentuan',
    ];

    /**
     * Generate unique slug from masjid name
     */
    private function generateUniqueSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug already exists, if yes add counter
        while (Profil::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function register()
    {
        // Debug: Tampilkan data yang diterima
        Log::info('Register attempt:', [
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
            'masjid' => $this->masjid,
            'terms_agreed' => $this->terms_agreed,
            'password_length' => strlen($this->password ?? ''),
            'password_confirmation_length' => strlen($this->password_confirmation ?? ''),
        ]);

        $this->validate();

        try {
            DB::transaction(function () {
                // Simpan data user
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'address' => $this->address,
                    'phone' => $this->phone,
                    'password' => Hash::make($this->password), // Explicit hashing
                    'role' => 'User',
                    'status' => 'Inactive',
                ]);

                Log::info('User created:', ['user_id' => $user->id]);

                // Generate unique slug dari nama masjid
                $slug = $this->generateUniqueSlug($this->masjid);

                // Simpan data profil masjid dengan slug
                $profil = Profil::create([
                    'user_id' => $user->id,
                    'name' => $this->masjid,
                    'slug' => $slug,
                    'address' => $this->address,
                    'phone' => $this->phone,
                ]);

                Log::info('Profil created:', [
                    'profil_id' => $profil->id,
                    'name' => $profil->name,
                    'slug' => $profil->slug,
                    'address' => $profil->address,
                    'phone' => $profil->phone,
                ]);

                // Reset form
                $this->reset(['name', 'email', 'address', 'password', 'password_confirmation', 'masjid', 'terms_agreed']);

                // Flash message dan redirect
                session()->flash('success', 'Akun berhasil dibuat! Silakan login.');

                // Redirect ke halaman login
                return redirect()->route('login');
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            throw $e; // Re-throw validation errors
        } catch (\Exception $e) {
            Log::error('Registration error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Terjadi kesalahan saat membuat akun: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.register.register');
    }
}
