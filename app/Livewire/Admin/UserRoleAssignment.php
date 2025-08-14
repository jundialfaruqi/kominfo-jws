<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserRoleAssignment extends Component
{
    use WithPagination;

    #[Title('Assign Roles to Users')]

    public $search = '';
    public $paginate = 10;
    protected $paginationTheme = 'bootstrap';

    // Form properties
    public $selectedUserId;
    public $selectedUserName;
    public $selectedRoles = [];
    public $currentUserRoles = [];

    // Modal states
    public $showAssignModal = false;

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

        $query = User::with('roles')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%');

        // Filter users based on current user's role
        if (Auth::user()->role !== 'Super Admin') {
            // Non-Super Admin users can only see User role
            $query->where('role', 'User');
        }

        $users = $query->orderBy('name', 'asc')->paginate($this->paginate);
        $availableRoles = $this->getAvailableRoles();

        return view('livewire.admin.user-role-assignment', [
            'users' => $users,
            'availableRoles' => $availableRoles
        ]);
    }

    public function assignRole($userId)
    {
        $user = User::with('roles')->findOrFail($userId);

        $this->selectedUserId = $user->id;
        $this->selectedUserName = $user->name;
        $this->currentUserRoles = $user->roles->pluck('name')->toArray();
        $this->selectedRoles = $this->currentUserRoles;

        $this->showAssignModal = true;
    }

    public function updateRoles()
    {
        try {
            $user = User::findOrFail($this->selectedUserId);

            // Sync roles
            $user->syncRoles($this->selectedRoles);

            $this->dispatch('success', 'Roles berhasil diperbarui untuk user: ' . $this->selectedUserName);
            $this->dispatch('closeAssignModal');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showAssignModal = false;
    }

    private function resetForm()
    {
        $this->reset([
            'selectedUserId',
            'selectedUserName',
            'selectedRoles',
            'currentUserRoles'
        ]);
    }

    private function getAvailableRoles()
    {
        $currentUserRole = Auth::user()->role;

        if ($currentUserRole === 'Super Admin') {
            // Super Admin can assign any role
            return Role::all();
        } elseif ($currentUserRole === 'Admin') {
            // Admin can only assign User and Admin Masjid roles
            return Role::whereIn('name', ['User', 'Admin Masjid'])->get();
        }

        return collect([]);
    }
}
