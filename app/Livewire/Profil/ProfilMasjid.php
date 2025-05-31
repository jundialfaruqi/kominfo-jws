<?php

namespace App\Livewire\Profil;

use App\Models\Profil;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfilMasjid extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Profil Masjid')]

    public $search;
    public $paginate;
    protected $paginationTheme = 'bootstrap';

    public $profileId;
    public $userId;
    public $name;
    public $address;
    public $phone;
    public $logo_masjid;
    public $temp_logo;
    public $logo_pemerintah;
    public $temp_logo_pemerintah;

    public $isEdit = false;
    public $showForm = false;
    public $deleteProfileId;
    public $deleteProfileName;

    protected $rules = [
        'userId'            => 'required|exists:users,id',
        'name'              => 'required|string|max:255',
        'address'           => 'required|string',
        'phone'             => 'required|string|max:15',
        'logo_masjid'       => 'nullable|image|max:1024|mimes:jpeg,jpg,png,gif',
        'logo_pemerintah'   => 'nullable|image|max:1024|mimes:jpeg,jpg,png,gif',
    ];

    protected $messages = [
        'userId.exists'             => 'Admin Masjid tidak ditemukan',
        'userId.required'           => 'Wajib memilih Admin/User',
        'logo_masjid.image'         => 'File harus berupa gambar',
        'logo_masjid.mimes'         => 'Format file tidak valid. File harus berupa gambar jpeg,jpg,png,gif!',
        'logo_masjid.max'           => 'File gambar terlalu besar. Ukuran file maksimal 1MB!',
        'logo_pemerintah.image'     => 'File harus berupa gambar',
        'logo_pemerintah.mimes'     => 'Format file tidak valid. File harus berupa gambar jpeg,jpg,png,gif!',
        'logo_pemerintah.max'       => 'File gambar terlalu besar. Ukuran file maksimal 1MB!',
        'phone.max'                 => 'Nomor telepon maksimal 15 karakter!',
        'phone.required'            => 'Nomor telepon wajib diisi!',
        'phone.numeric'             => 'Nomor telepon harus berupa angka!',
        'address.required'          => 'Alamat wajib diisi!',
        'name.required'             => 'Nama Masjid wajib diisi!',
        'name.max'                  => 'Nama Masjid maksimal 255 karakter!',
        'name.string'               => 'Nama Masjid harus berupa teks!',
    ];

    public function mount()
    {
        $this->search = '';
        $this->paginate = 5;

        // If user is not admin
        if (Auth::user()->role !== 'Admin') {
            $profil = Profil::where('user_id', Auth::id())->first();

            // Always show form for non-admin users
            $this->showForm = true;
            // Set user ID for new profiles
            $this->userId = Auth::id();

            if ($profil) {
                // If profile exists, load the data
                $this->profileId                = $profil->id;
                $this->name                     = $profil->name;
                $this->address                  = $profil->address;
                $this->phone                    = $profil->phone;
                $this->temp_logo                = $profil->logo_masjid;
                $this->temp_logo_pemerintah     = $profil->logo_pemerintah;
                $this->isEdit                   = true;
            } else {
                // For new profiles, set isEdit to false
                $this->isEdit = false;
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->showForm = false;
        $this->resetValidation();
        $this->reset(
            [
                'profileId',
                'userId',
                'name',
                'address',
                'phone',
                'logo_masjid',
                'temp_logo',
                'logo_pemerintah',
                'temp_logo_pemerintah'
            ]
        );
    }

    public function render()
    {
        // Get current user and role
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'Admin';

        // Query builder for profiles
        $query = Profil::with('user')
            ->select('id', 'user_id', 'name', 'slug', 'address', 'phone', 'logo_masjid', 'logo_pemerintah');

        // If user is not admin, only show their own profile
        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            // Admin can search through all profiles
            $query->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $profilList = $query->orderBy('name', 'asc')
            ->paginate($this->paginate);

        // Only admin can see list of users for assignment
        $users = $isAdmin ? User::orderBy('name')->get() : collect([]);

        return view('livewire.profil.profil-masjid', [
            'profilList' => $profilList,
            'users' => $users
        ]);
    }

    // Show the form for adding a new profile
    public function showAddForm()
    {
        // Only admin can add new profiles
        if (Auth::user()->role !== 'Admin') {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah profil masjid!');
            return;
        }

        $this->resetValidation();
        $this->reset(
            [
                'profileId',
                'userId',
                'name',
                'address',
                'phone',
                'logo_masjid',
                'temp_logo',
                'logo_pemerintah',
                'temp_logo_pemerintah'
            ]
        );
        $this->isEdit   = false;
        $this->showForm = true;
    }

    // Show the form for editing an existing profile
    public function edit($id)
    {
        $this->resetValidation();

        $profil = Profil::findOrFail($id);

        // Check if user has permission to edit this profile
        if (Auth::user()->role !== 'Admin' && Auth::id() !== $profil->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit profil ini!');
            return;
        }

        $this->profileId            = $profil->id;
        $this->userId               = $profil->user_id;
        $this->name                 = $profil->name;
        $this->address              = $profil->address;
        $this->phone                = $profil->phone;
        $this->temp_logo            = $profil->logo_masjid;
        $this->temp_logo_pemerintah = $profil->logo_pemerintah;

        $this->isEdit               = true;
        $this->showForm             = true;
    }

    // Hide the form
    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->reset(
            [
                'profileId',
                'userId',
                'name',
                'address',
                'phone',
                'logo_masjid',
                'temp_logo',
                'logo_pemerintah',
                'temp_logo_pemerintah'
            ]
        );
    }

    /**
     * Generate a unique slug from the given name
     */
    private function generateSlug($name, $id = null)
    {
        // Generate base slug
        $slug = Str::slug($name);

        // Check if slug exists
        $query = Profil::where('slug', $slug);

        // Exclude current profile when updating
        if ($id) {
            $query->where('id', '!=', $id);
        }

        // If slug exists, append a unique identifier
        if ($query->exists()) {
            $count = 1;
            $originalSlug = $slug;

            // Keep incrementing until we find a unique slug
            while ($query->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }

        return $slug;
    }

    // Save the profile (create or update)
    public function save()
    {
        $currentUser = Auth::user();

        // If user is not admin, force userId to be their own id
        if ($currentUser->role !== 'Admin') {
            $this->userId = $currentUser->id;
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                $profil = Profil::findOrFail($this->profileId);
                // Check if user has permission to edit this profile
                if ($currentUser->role !== 'Admin' && $currentUser->id !== $profil->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit profil ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own profile
                if ($currentUser->role !== 'Admin' && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat profil untuk user lain!');
                    return;
                }
                $profil = new Profil();
            }

            $profil->user_id = $this->userId;
            $profil->name    = $this->name;
            $profil->address = $this->address;
            $profil->phone   = $this->phone;

            // Generate slug saat pembuatan dan pembaruan
            // $profil->slug = $this->generateSlug($this->name, $this->isEdit ? $this->profileId : null);

            // Jika edit, tidak perlu generate slug
            if (!$this->isEdit) {
                $profil->slug = $this->generateSlug($this->name);
            }

            // Handle logo masjid upload
            if ($this->logo_masjid) {
                // Delete old logo if it exists
                if ($this->isEdit && $profil->logo_masjid && file_exists(public_path($profil->logo_masjid))) {
                    File::delete(public_path($profil->logo_masjid));
                }

                // Save new logo
                $fileName = time() . '_masjid_' . $this->logo_masjid->getClientOriginalName();
                $this->logo_masjid->storeAs('', $fileName, 'public_images');
                $profil->logo_masjid = 'images/logo/' . $fileName;
            }

            // Handle logo pemerintah upload
            if ($this->logo_pemerintah) {
                // Delete old logo if it exists
                if ($this->isEdit && $profil->logo_pemerintah && file_exists(public_path($profil->logo_pemerintah))) {
                    File::delete(public_path($profil->logo_pemerintah));
                }

                // Save new logo
                $fileName = time() . '_pemerintah_' . $this->logo_pemerintah->getClientOriginalName();
                $this->logo_pemerintah->storeAs('', $fileName, 'public_images');
                $profil->logo_pemerintah = 'images/logo/' . $fileName;
            }

            $profil->save();

            $this->dispatch('success', $this->isEdit ? 'Profil masjid berhasil diperbarui!' : 'Profil masjid berhasil ditambahkan!');

            // Only hide form and reset fields if user is admin
            if (Auth::user()->role === 'Admin') {
                $this->showForm = false;
                $this->reset(
                    [
                        'profileId',
                        'userId',
                        'name',
                        'address',
                        'phone',
                        'logo_masjid',
                        'temp_logo',
                        'logo_pemerintah',
                        'temp_logo_pemerintah'
                    ]
                );
            } else {
                // For regular users, keep the form visible and reload their data
                $this->showForm = true;
                $profil = Profil::where('user_id', Auth::id())->first();
                if ($profil) {
                    $this->profileId            = $profil->id;
                    $this->userId               = $profil->user_id;
                    $this->name                 = $profil->name;
                    $this->address              = $profil->address;
                    $this->phone                = $profil->phone;
                    $this->temp_logo            = $profil->logo_masjid;
                    $this->temp_logo_pemerintah = $profil->logo_pemerintah;
                    $this->isEdit               = true;
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;

        $profil = Profil::findOrFail($id);
        $this->deleteProfileId = $profil->id;
        $this->deleteProfileName = $profil->name;
    }

    // Delete the profile
    public function destroyProfile()
    {
        try {
            $profil = Profil::findOrFail($this->deleteProfileId);

            if ($profil->logo_masjid && file_exists(public_path($profil->logo_masjid))) {
                File::delete(public_path($profil->logo_masjid));
            }

            if ($profil->logo_pemerintah && file_exists(public_path($profil->logo_pemerintah))) {
                File::delete(public_path($profil->logo_pemerintah));
            }

            $profil->delete();

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Profil masjid berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
