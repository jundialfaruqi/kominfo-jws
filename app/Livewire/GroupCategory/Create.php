<?php

namespace App\Livewire\GroupCategory;

use App\Models\GroupCategory as ModelsGroupCategory;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    #[Title('Tambah Group Category')]

    public $name = '';
    public $profilId = null; // digunakan oleh admin untuk memilih profil

    public $isAdmin = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'profilId' => 'nullable|exists:profils,id',
    ];

    protected $messages = [
        'name.required' => 'Nama group wajib diisi',
        'name.max' => 'Nama group terlalu panjang',
        'profilId.exists' => 'Profil masjid tidak ditemukan',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->isAdmin = in_array($user->role, ['Super Admin', 'Admin']);

        // Untuk non-admin, set profilId ke profil miliknya
        if (!$this->isAdmin && $user && $user->profil) {
            $this->profilId = $user->profil->id;
        }
    }

    public function save()
    {
        $user = Auth::user();

        // Validasi dasar
        $this->validate();

        // Tentukan profil id
        $profilId = $this->profilId;
        if (!$this->isAdmin) {
            // Non-admin hanya boleh membuat untuk profilnya sendiri
            $profilId = optional($user->profil)->id;
            if (!$profilId) {
                $this->dispatch('error', 'Profil masjid Anda tidak ditemukan, silakan lengkapi profil terlebih dahulu.');
                return;
            }
        } else {
            // Admin harus memilih profil
            if (!$profilId) {
                $this->dispatch('error', 'Silakan pilih Profil Masjid terlebih dahulu.');
                return;
            }
        }

        try {
            $group = new ModelsGroupCategory();
            $group->name = $this->name;
            $group->id_masjid = $profilId;
            $group->save();

            $this->dispatch('success', 'Group Category berhasil ditambahkan!');
            // Redirect ke halaman list
            return $this->redirectRoute('group-category.index');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $profiles = collect([]);
        if ($this->isAdmin) {
            $profiles = Profil::select('id', 'name')->orderBy('name')->get();
        }

        return view('livewire.group-category.create', [
            'profiles' => $profiles,
        ]);
    }
}