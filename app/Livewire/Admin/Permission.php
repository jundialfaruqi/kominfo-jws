<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Permission as PermissionModel;
use Illuminate\Support\Facades\Auth;

class Permission extends Component
{
    use WithPagination;

    #[Title('Manajemen Permission')]

    public $search = '';
    public $paginate = 10;
    protected $paginationTheme = 'bootstrap';

    // Form properties
    public $permissionId;
    public $name;
    public $guard_name = 'web';

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

        $permissions = PermissionModel::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate($this->paginate);

        return view('livewire.admin.permission', [
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

        $permission = PermissionModel::findOrFail($id);

        $this->permissionId = $permission->id;
        $this->name = $permission->name;
        $this->guard_name = $permission->guard_name;

        $this->showEditModal = true;
    }

    public function store()
    {
        $this->validate();

        try {
            PermissionModel::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
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
            'deletePermissionId',
            'deletePermissionName'
        ]);
        $this->guard_name = 'web';
        $this->resetValidation();
    }
}
