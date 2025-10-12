<?php

namespace App\Livewire\GroupCategory;

use App\Models\GroupCategory as ModelsGroupCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Group extends Component
{
    use WithPagination, AuthorizesRequests;

    #[Title('Group Category')]

    public $paginate = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public $showTable = true;
    public $deleteGroupId;
    public $deleteGroupName;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $group = ModelsGroupCategory::with('profil')->findOrFail($id);
        $this->deleteGroupId = $group->id;
        $this->deleteGroupName = optional($group->profil)->name;
    }

    public function destroyGroupCategory()
    {
        $group = ModelsGroupCategory::findOrFail($this->deleteGroupId);
        $this->authorize('delete', $group);
        $group->delete();
        $this->dispatch('closeDeleteModal');
        $this->dispatch('success', 'Group Category berhasil dihapus');
    }

    public function render()
    {
        // Authorization via Policy
        $this->authorize('viewAny', ModelsGroupCategory::class);

        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        $query = ModelsGroupCategory::with('profil')
            ->select('id', 'id_masjid', 'name');

        // If user is not admin, restrict to their own profil
        if (!$isAdmin) {
            $profilId = optional($currentUser->profil)->id;
            $query->where('id_masjid', $profilId);
        } else {
            // Admin can search across all
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('profil', function ($p) {
                      $p->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $groupList = $query->orderBy('id', 'asc')->paginate($this->paginate);

        return view('livewire.group-category.group', [
            'groupList' => $groupList,
            'isAdmin' => $isAdmin,
        ]);
    }
}
