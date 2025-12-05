<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Permission as PermissionModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Permission extends Component
{
    use WithPagination;

    #[Title('Manajemen Permission')]

    public $search = '';
    public $paginate = 4;
    protected $paginationTheme = 'bootstrap';

    // Form properties
    public $permissionId;
    public $name;
    public $guard_name = 'web';
    public $group;

    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    // Delete confirmation
    public $deletePermissionId;
    public $deletePermissionName;

    protected $rules = [
        'name' => 'required|string|max:255|unique:permissions,name',
        'guard_name' => 'required|string',
        'group' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'name.required' => 'Nama permission wajib diisi!',
        'name.unique' => 'Nama permission sudah digunakan!',
        'name.max' => 'Nama permission maksimal 255 karakter!',
        'guard_name.required' => 'Guard name wajib diisi!',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Check permission
        if (!Auth::check() || !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        $groupsRaw = PermissionModel::select('group')->distinct()->orderBy('group', 'asc')->get();
        $groupNames = [];
        foreach ($groupsRaw as $gr) {
            if (!empty($gr->group)) {
                $groupNames[] = $gr->group;
            }
        }
        $groupNames[] = 'Other Permission';

        $groupedPermissions = [];
        $groupPaginators = [];
        $groupMeta = [];

        foreach ($groupNames as $groupName) {
            $key = Str::slug($groupName);
            $perPage = $this->paginate;

            $query = PermissionModel::where('name', 'like', '%' . $this->search . '%')
                ->orderBy('name', 'asc');

            if ($groupName === 'Other Permission') {
                $query = $query->where(function ($q) {
                    $q->whereNull('group')->orWhere('group', '');
                });
            } else {
                $query = $query->where('group', $groupName);
            }

            $paginator = $query->paginate($perPage, ['*'], 'page_' . $key);

            if ($paginator->total() === 0) {
                continue;
            }

            $startIndex = ($paginator->currentPage() - 1) * $paginator->perPage();
            $seq = 0;
            $items = [];
            foreach ($paginator->items() as $perm) {
                $index = $startIndex + (++$seq);
                $items[] = [
                    'model' => $perm,
                    'index' => $index,
                ];
            }

            $groupedPermissions[$key] = $items;
            $groupPaginators[$key] = $paginator;
            $groupMeta[$key] = ['name' => $groupName];
        }

        $groups = PermissionModel::select('group')->distinct()->pluck('group')->filter()->values();

        return view('livewire.admin.permission', [
            'groups' => $groups,
            'groupedPermissions' => $groupedPermissions,
            'groupPaginators' => $groupPaginators,
            'groupMeta' => $groupMeta,
        ]);
    }

    public function add()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();

        $permission = PermissionModel::findOrFail($id);

        $this->permissionId = $permission->id;
        $this->name = $permission->name;
        $this->guard_name = $permission->guard_name;
        $this->group = $permission->group;

        $this->showEditModal = true;
    }

    public function store()
    {
        $this->validate();

        try {
            PermissionModel::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
                'group' => $this->group,
            ]);

            $this->dispatch('success', 'Permission berhasil ditambahkan!');
            $this->dispatch('closeCreateModal');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $rules = $this->rules;
        $rules['name'] = 'required|string|max:255|unique:permissions,name,' . $this->permissionId;

        $this->validate($rules);

        try {
            $permission = PermissionModel::findOrFail($this->permissionId);

            $permission->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
                'group' => $this->group,
            ]);

            $this->dispatch('success', 'Permission berhasil diperbarui!');
            $this->dispatch('closeEditModal');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $permission = PermissionModel::findOrFail($id);
        $this->deletePermissionId = $permission->id;
        $this->deletePermissionName = $permission->name;
        $this->showDeleteModal = true;
    }

    public function destroy()
    {
        try {
            $permission = PermissionModel::findOrFail($this->deletePermissionId);

            // Check if permission is being used by roles
            if ($permission->roles()->count() > 0) {
                $this->dispatch('error', 'Permission tidak dapat dihapus karena masih digunakan oleh role!');
                return;
            }

            $permission->delete();

            $this->dispatch('success', 'Permission berhasil dihapus!');
            $this->dispatch('closeDeleteModal');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
    }

    private function resetForm()
    {
        $this->reset([
            'permissionId',
            'name',
            'guard_name',
            'group',
            'deletePermissionId',
            'deletePermissionName'
        ]);
        $this->guard_name = 'web';
        $this->resetValidation();
    }
}
