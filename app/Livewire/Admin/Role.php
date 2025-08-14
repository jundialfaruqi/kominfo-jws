<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class Role extends Component
{
    use WithPagination;

    #[Title('Manajemen Role')]

    public $search = '';
    public $paginate = 10;
    protected $paginationTheme = 'bootstrap';

    // Form properties
    public $roleId;
    public $name;
    public $guard_name = 'web';
    public $selectedPermissions = [];
    public $permissionSearch = '';

    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    // Delete confirmation
    public $deleteRoleId;
    public $deleteRoleName;

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'guard_name' => 'required|string',
        'selectedPermissions' => 'array',
    ];

    protected $messages = [
        'name.required' => 'Nama role wajib diisi!',
        'name.unique' => 'Nama role sudah digunakan!',
        'name.max' => 'Nama role maksimal 255 karakter!',
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

        $roles = RoleModel::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate($this->paginate);

        // Filter permissions based on search
        $permissions = Permission::when($this->permissionSearch, function ($query) {
            return $query->where('name', 'like', '%' . $this->permissionSearch . '%');
        })
            ->orderBy('name', 'asc')
            ->get();

        return view('livewire.admin.role', [
            'roles' => $roles,
            'permissions' => $permissions
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

        $role = RoleModel::with('permissions')->findOrFail($id);

        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();

        $this->showEditModal = true;
    }

    public function store()
    {
        $this->validate();

        try {
            $role = RoleModel::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            // Sync permissions
            if (!empty($this->selectedPermissions)) {
                $role->permissions()->sync($this->selectedPermissions);
            }

            $this->dispatch('success', 'Role berhasil ditambahkan!');
            $this->dispatch('closeCreateModal');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $rules = $this->rules;
        $rules['name'] = 'required|string|max:255|unique:roles,name,' . $this->roleId;

        $this->validate($rules);

        try {
            $role = RoleModel::findOrFail($this->roleId);

            $role->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);

            // Sync permissions
            $role->permissions()->sync($this->selectedPermissions);

            $this->dispatch('success', 'Role berhasil diperbarui!');
            $this->dispatch('closeEditModal');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $role = RoleModel::findOrFail($id);
        $this->deleteRoleId = $role->id;
        $this->deleteRoleName = $role->name;
        $this->showDeleteModal = true;
    }

    public function destroy()
    {
        try {
            $role = RoleModel::findOrFail($this->deleteRoleId);

            // Check if role is being used by users
            if ($role->users()->count() > 0) {
                $this->dispatch('error', 'Role tidak dapat dihapus karena masih digunakan oleh user!');
                return;
            }

            $role->delete();

            $this->dispatch('success', 'Role berhasil dihapus!');
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
            'roleId',
            'name',
            'guard_name',
            'selectedPermissions',
            'deleteRoleId',
            'deleteRoleName',
            'permissionSearch'
        ]);
        $this->guard_name = 'web';
        $this->resetValidation();
    }
}
