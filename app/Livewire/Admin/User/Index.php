<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    #[Title('Data User')]
    protected $paginationTheme = 'bootstrap';
    public $paginate = '5';
    public $search = '';
    public $userId;
    public $deleteUserId;
    public $deleteUserName;
    public $isLoading = false;

    public $name, $email, $phone, $address, $password, $password_confirmation, $role, $status;

    public function mount()
    {
        // Check if user has permission to access this page
        if (!$this->canAccessUserManagement()) {
            abort(403, 'Unauthorized access');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::select('id', 'name', 'phone', 'email', 'role', 'status')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('role', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%');
            });

        // Filter users based on current user's role
        if (!$this->isSuperAdmin()) {
            // Non-Super Admin users can only see User role
            $query->where('role', 'User');
        }

        $data = array(
            'user' => $query->orderBy('role', 'asc')
                ->orderBy('status', 'asc')
                ->paginate($this->paginate),
        );

        return view('livewire.admin.user.index', $data);
    }

    public function add()
    {
        if (!$this->canAccessUserManagement()) {
            abort(403, 'Unauthorized access');
        }
        $this->dispatch('openCreateModal');
    }

    public function cancel()
    {
        $this->resetValidation();
        $this->reset(
            [
                'name',
                'email',
                'phone',
                'address',
                'password',
                'password_confirmation',
                'role',
                'status',
            ]
        );
    }

    public function cancelDelete()
    {
        $this->reset('deleteUserId', 'deleteUserName');
        $this->dispatch('closeDeleteModal');
    }

    public function store()
    {
        if (!$this->canAccessUserManagement()) {
            abort(403, 'Unauthorized access');
        }

        // Validate role permission
        if (!$this->canCreateRole($this->role)) {
            $this->dispatch('error', 'Anda tidak memiliki izin untuk membuat user dengan role ini');
            return;
        }

        $this->validate(
            [
                'name'                  => 'required',
                'email'                 => 'required|email|unique:users',
                'password'              => 'required|min:6',
                'password_confirmation' => 'required|min:6|same:password',
                'phone'                 => 'required|numeric',
                'address'               => 'required',
                'role'                  => 'required|in:' . $this->getAllowedRoles(),
                'status'                => 'required|in:Active,Inactive',
            ],
            [
                'name.required'                  => 'Nama wajib diisi',
                'email.required'                 => 'Email wajib diisi',
                'email.email'                    => 'Email tidak valid',
                'email.unique'                   => 'Email sudah terdaftar',
                'password.required'              => 'Password wajib diisi',
                'password.min'                   => 'Password minimal 6 karakter',
                'password_confirmation.same'     => 'Konfirmasi password harus sama dengan password',
                'password_confirmation.required' => 'Konfirmasi password wajib diisi',
                'password_confirmation.min'      => 'Konfirmasi password minimal 6 karakter',
                'phone.required'                 => 'Nomor telepon wajib diisi',
                'phone.numeric'                  => 'Nomor telepon harus berupa angka',
                'address.required'               => 'Alamat wajib diisi',
                'role.required'                  => 'Role wajib diisi',
                'role.in'                        => 'Role tidak valid',
                'status.required'                => 'Status wajib diisi',
                'status.in'                      => 'Status tidak valid',
            ]
        );

        $user = new User();
        $user->name     = $this->name;
        $user->email    = $this->email;
        $user->phone    = $this->phone;
        $user->address  = $this->address;
        $user->password = bcrypt($this->password);
        $user->role     = $this->role;
        $user->status   = $this->status;
        $user->save();
        $this->dispatch('success', 'Data user berhasil ditambahkan');
        $this->dispatch('closeCreateModal');
        $this->cancel();
    }

    public function edit($id)
    {
        if (!$this->canAccessUserManagement()) {
            abort(403, 'Unauthorized access');
        }

        $user = User::find($id);

        if (!$user) {
            $this->dispatch('error', 'User tidak ditemukan');
            return;
        }

        // Check if current user can edit this user
        if (!$this->canEditUser($user)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit user ini');
        }

        $this->isLoading = true;

        try {
            $this->userId = $id;

            $this->name     = $user->name;
            $this->email    = $user->email;
            $this->phone    = $user->phone;
            $this->address  = $user->address;
            $this->role     = $user->role;
            $this->status   = $user->status;

            // Reset password fields
            $this->password = '';
            $this->password_confirmation = '';
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat memuat data');
        }

        $this->isLoading = false;
    }

    public function update()
    {
        if (!$this->canAccessUserManagement()) {
            abort(403, 'Unauthorized access');
        }

        $user = User::find($this->userId);

        if (!$user) {
            $this->dispatch('error', 'User tidak ditemukan');
            return;
        }

        // Check if current user can edit this user
        if (!$this->canEditUser($user)) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit user ini');
        }

        // Validate role permission
        if (!$this->canCreateRole($this->role)) {
            $this->dispatch('error', 'Anda tidak memiliki izin untuk mengubah role ini');
            return;
        }

        $rules = [
            'name'      => 'required',
            'phone'     => 'required|numeric',
            'address'   => 'required',
            'role'      => 'required|in:' . $this->getAllowedRoles(),
            'status'    => 'required|in:Active,Inactive',
        ];

        $rules['email'] = 'required|email|unique:users,email,' . $this->userId;

        if (!empty($this->password)) {
            $rules['password'] = 'required|min:6';
            $rules['password_confirmation'] = 'required|min:6|same:password';
        }

        $messages = [
            'name.required'                  => 'Nama wajib diisi',
            'email.required'                 => 'Email wajib diisi',
            'email.email'                    => 'Email tidak valid',
            'email.unique'                   => 'Email sudah terdaftar',
            'password.required'              => 'Password wajib diisi',
            'password.min'                   => 'Password minimal 6 karakter',
            'password_confirmation.same'     => 'Konfirmasi password harus sama dengan password',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi',
            'password_confirmation.min'      => 'Konfirmasi password minimal 6 karakter',
            'phone.required'                 => 'Nomor telepon wajib diisi',
            'phone.numeric'                  => 'Nomor telepon harus berupa angka',
            'address.required'               => 'Alamat wajib diisi',
            'role.required'                  => 'Role wajib diisi',
            'role.in'                        => 'Role tidak valid',
            'status.required'                => 'Status wajib diisi',
            'status.in'                      => 'Status tidak valid',
        ];

        $this->validate($rules, $messages);

        $user->name     = $this->name;
        $user->email    = $this->email;
        $user->phone    = $this->phone;
        $user->address  = $this->address;
        $user->role     = $this->role;
        $user->status   = $this->status;

        // Only update password if provided
        if (!empty($this->password)) {
            $user->password = bcrypt($this->password);
        }

        $user->save();

        $this->dispatch('success', 'Data user berhasil diperbarui');
        $this->dispatch('closeEditModal');
        $this->cancel();
    }

    public function delete($id)
    {
        if (!$this->canAccessUserManagement()) {
            abort(403, 'Unauthorized access');
        }

        $user = User::find($id);

        if (!$user) {
            $this->dispatch('error', 'User tidak ditemukan');
            return;
        }

        // Check if current user can delete this user
        if (!$this->canDeleteUser($user)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus user ini');
        }

        $this->deleteUserId = $id;
        $this->deleteUserName = $user->name;
    }

    public function destroyUser()
    {
        if (!$this->canAccessUserManagement()) {
            abort(403, 'Unauthorized access');
        }

        $user = User::find($this->deleteUserId);

        if (!$user) {
            $this->dispatch('error', 'User tidak ditemukan');
            $this->reset('deleteUserId');
            return;
        }

        // Check if current user can delete this user
        if (!$this->canDeleteUser($user)) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus user ini');
        }

        $user->delete();
        $this->dispatch('success', 'Data user berhasil dihapus');
        $this->dispatch('closeDeleteModal');
        $this->reset('deleteUserId');
    }

    // Helper methods for role-based access control
    private function isSuperAdmin()
    {
        return Auth::check() && Auth::user()->role === 'Super Admin';
    }

    private function isAdmin()
    {
        return Auth::check() && Auth::user()->role === 'Admin';
    }

    private function canAccessUserManagement()
    {
        return Auth::check() && (
            Auth::user()->role === 'Super Admin' ||
            Auth::user()->role === 'Admin'
        );
    }

    private function canEditUser($user)
    {
        $currentUserRole = Auth::user()->role;

        // Super Admin can edit anyone
        if ($currentUserRole === 'Super Admin') {
            return true;
        }

        // Admin can only edit Users (not Super Admin or other Admins)
        if ($currentUserRole === 'Admin') {
            return $user->role === 'User';
        }

        return false;
    }

    private function canDeleteUser($user)
    {
        $currentUserRole = Auth::user()->role;

        // Super Admin can delete anyone (except themselves)
        if ($currentUserRole === 'Super Admin') {
            return $user->id !== Auth::id();
        }

        // Admin can only delete Users (not Super Admin or other Admins)
        if ($currentUserRole === 'Admin') {
            return $user->role === 'User';
        }

        return false;
    }

    private function canCreateRole($role)
    {
        $currentUserRole = Auth::user()->role;

        // Super Admin can create any role
        if ($currentUserRole === 'Super Admin') {
            return true;
        }

        // Admin can only create Users
        if ($currentUserRole === 'Admin') {
            return $role === 'User';
        }

        return false;
    }

    private function getAllowedRoles()
    {
        $currentUserRole = Auth::user()->role;

        // Super Admin can create any role
        if ($currentUserRole === 'Super Admin') {
            return 'Super Admin,Admin,User';
        }

        // Admin can only create Users
        if ($currentUserRole === 'Admin') {
            return 'User';
        }

        return 'User';
    }
}
