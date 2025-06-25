<?php

namespace App\Livewire\Tema;

use App\Models\Profil;
use App\Models\Theme;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class SetTema extends Component
{
    use WithPagination;

    #[Title('Set Tema Pengguna')]

    public $search;
    public $paginate = 5;
    protected $paginationTheme = 'bootstrap';

    public $userId;
    public $selectedThemeId;
    public $profilName;

    protected $rules = [
        'selectedThemeId' => 'nullable|exists:themes,id',
    ];

    protected $messages = [
        'selectedThemeId.exists' => 'Tema yang dipilih tidak valid.',
    ];

    public function mount()
    {
        $this->search = '';
        if (!in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengatur tema pengguna!');
            return redirect()->route('dashboard');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Profil::select('profils.id', 'profils.name as masjid_name', 'users.id as user_id', 'users.theme_id')
            ->join('users', 'profils.user_id', '=', 'users.id')
            ->where('profils.name', 'like', '%' . $this->search . '%');

        $profilList = $query->orderBy('profils.name', 'asc')->paginate($this->paginate);

        // Mengambil nama tema untuk setiap profil
        $profilList->getCollection()->transform(function ($profil) {
            $profil->theme_name = $profil->theme_id ? Theme::find($profil->theme_id)->name ?? '-' : 'Default';
            return $profil;
        });

        $availableThemes = Theme::select('id', 'name', 'preview_image')->orderBy('name', 'asc')->get();

        return view('livewire.tema.set-tema', [
            'profilList' => $profilList,
            'availableThemes' => $availableThemes,
        ]);
    }

    public function selectTempTheme($themeId)
    {
        $this->selectedThemeId = $themeId;
    }

    public function edit($userId)
    {
        if (!in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit tema pengguna!');
            return;
        }

        $this->resetValidation();

        $user = User::findOrFail($userId);
        $profil = Profil::where('user_id', $userId)->firstOrFail();

        $this->userId = $user->id;
        $this->selectedThemeId = $user->theme_id;
        $this->profilName = $profil->name;
    }

    public function save()
    {
        if (!in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menyimpan tema pengguna!');
            return;
        }

        $this->validate();

        try {
            $user = User::findOrFail($this->userId);
            $user->theme_id = $this->selectedThemeId ?: null;
            $user->save();

            $this->dispatch('success', 'Tema pengguna berhasil diperbarui!');
            $this->reset(['userId', 'selectedThemeId', 'profilName']);
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset(['userId', 'selectedThemeId', 'profilName']);
        $this->resetValidation();
    }
}
