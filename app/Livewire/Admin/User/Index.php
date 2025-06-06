<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

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
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = array(
            'user' => User::select('id', 'name', 'phone', 'email', 'role', 'status')
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orderBy('role', 'asc')
                ->orderBy('status', 'asc')
                ->paginate($this->paginate),
        );
        return view('livewire.admin.user.index', $data);
    }

    public function add()
    {
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

    public function store()
    {
        $this->validate(
            [
                'name'                  => 'required',
                'email'                 => 'required|email|unique:users',
                'password'              => 'required|min:6',
                'password_confirmation' => 'required|min:6|same:password',
                'phone'                 => 'required|numeric',
                'address'               => 'required',
                'role'                  => 'required|in:Super Admin,Admin,User',
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
        $this->isLoading = true;

        try {
            $this->userId = $id;
            $user = User::find($id);

            if ($user) {
                $this->name     = $user->name;
                $this->email    = $user->email;
                $this->phone    = $user->phone;
                $this->address  = $user->address;
                $this->role     = $user->role;
                $this->status   = $user->status;

                // Reset password fields
                $this->password = '';
                $this->password_confirmation = '';
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat memuat data');
        }

        $this->isLoading = false;
    }

    public function update()
    {
        $rules = [
            'name'      => 'required',
            'phone'     => 'required|numeric',
            'address'   => 'required',
            'role'      => 'required|in:Super Admin,Admin,User',
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

        $user = User::find($this->userId);

        if ($user) {
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
        } else {
            $this->dispatch('error', 'User tidak ditemukan');
        }
    }

    public function delete($id)
    {
        $this->deleteUserId = $id;
        $user = User::find($id);
        if ($user) {
            $this->deleteUserName = $user->name;
        }
    }

    public function destroyUser()
    {
        $user = User::find($this->deleteUserId);
        if ($user) {
            $user->delete();
            $this->dispatch('success', 'Data user berhasil dihapus');
            $this->dispatch('closeDeleteModal');
        } else {
            $this->dispatch('error', 'User tidak ditemukan');
        }
        $this->reset('deleteUserId');
    }
}
