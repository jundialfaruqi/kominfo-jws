<?php

namespace App\Livewire\GroupCategory;

use App\Models\GroupCategory as ModelsGroupCategory;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;

    #[Title('Edit Group Category')]

    public $groupId;
    public $name = '';
    public $profilId = null; // digunakan oleh admin untuk memilih profil

    public $isAdmin = false;
    public $deleteGroupName;

    protected $rules = [
        'name' => 'required|string|max:255',
        'profilId' => 'nullable|exists:profils,id',
    ];

    protected $messages = [
        'name.required' => 'Nama group wajib diisi',
        'name.max' => 'Nama group terlalu panjang',
        'profilId.exists' => 'Profil masjid tidak ditemukan',
    ];

    public function mount($id)
    {
        $this->groupId = $id;

        $user = Auth::user();
        $this->isAdmin = in_array($user->role, ['Super Admin', 'Admin']);

        $group = ModelsGroupCategory::findOrFail($id);
        $userProfilId = optional($user->profil)->id;
        $this->deleteGroupName = optional($group->profil)->name;

        // Jika bukan admin, hanya boleh mengedit data milik profilnya sendiri
        if (!$this->isAdmin && $group->id_masjid !== $userProfilId) {
            session()->flash('error', 'Anda tidak diizinkan mengedit data Group Category ini.');
            return redirect()->route('group-category.index');
        }

        $this->name = $group->name;
        $this->profilId = $group->id_masjid;
    }

    public function update()
    {
        $this->validate();

        $user = Auth::user();
        $isAdmin = in_array($user->role, ['Super Admin', 'Admin']);
        $userProfilId = optional($user->profil)->id;

        $group = ModelsGroupCategory::findOrFail($this->groupId);

        // Validasi kepemilikan untuk non-admin
        if (!$isAdmin && $group->id_masjid !== $userProfilId) {
            session()->flash('error', 'Anda tidak diizinkan mengedit data Group Category ini.');
            return redirect()->route('group-category.index');
        }

        $targetProfilId = $isAdmin ? ($this->profilId ?: $group->id_masjid) : $userProfilId;

        $group->update([
            'name' => $this->name,
            'id_masjid' => $targetProfilId,
        ]);

        session()->flash('success', 'Group Category berhasil diperbarui');
        return redirect()->route('group-category.index');
    }

    public function destroyGroup()
    {
        $group = ModelsGroupCategory::findOrFail($this->groupId);
        // Authorisasi menggunakan policy
        $this->authorize('delete', $group);

        // Hapus data
        $group->delete();

        // Tutup modal jika terbuka
        $this->dispatch('closeDeleteModal');

        session()->flash('success', 'Group Category berhasil dihapus');
        return redirect()->route('group-category.index');
    }

    public function render()
    {
        $profilList = $this->isAdmin ? Profil::select('id', 'name')->orderBy('name')->get() : collect();

        return view('livewire.group-category.edit', [
            'profilList' => $profilList,
            'isAdmin' => $this->isAdmin,
        ]);
    }
}