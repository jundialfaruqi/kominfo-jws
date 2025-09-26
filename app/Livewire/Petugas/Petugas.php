<?php

namespace App\Livewire\Petugas;

use App\Events\ContentUpdatedEvent;
use App\Models\Petugas as ModelsPetugas;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Petugas extends Component
{
    use WithPagination;

    #[Title('Petugas Jum\'at')]

    public $paginate;
    public $search;
    protected $paginationTheme = 'bootstrap';

    public $petugasId;
    public $userId;
    public $hari;
    public $khatib;
    public $imam;
    public $muadzin;

    public $showForm = false;
    public $isEdit = false;
    public $showTable = true;
    public $deletePetugasId;
    public $deletePetugasName;

    protected $rules = [
        'userId'  => 'required|exists:users,id',
        'hari'    => 'required',
        'khatib'  => 'required',
        'imam'    => 'required',
        'muadzin' => 'required',
    ];

    protected $messages = [
        'userId.required'  => 'Admin Masjid wajib diisi',
        'userId.exists'    => 'Admin Masjid tidak ditemukan',
        'hari.required'    => 'Hari wajib diisi',
        'khatib.required'  => 'Khatib wajib diisi',
        'imam.required'    => 'Imam wajib diisi',
        'muadzin.required' => 'Muadzin wajib diisi',
    ];

    public $hariError = '';

    public function updatedHari()
    {
        $this->hariError = '';
        $this->validateFridayDate();
    }

    private function validateFridayDate()
    {
        if ($this->hari) {
            $dayOfWeek = date('N', strtotime($this->hari));
            if ($dayOfWeek != 5) { // 5 = Friday
                $this->hariError = 'Tanggal yang dipilih bukan Hari Jum\'at, harap pilih tanggal di Hari Jum\'at';
            }
        }
    }

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // For non-admin users, show table by default instead of form
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->showForm = false;
            $this->showTable = true;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->showForm = false;
        $this->resetValidation();
        $this->reset(
            [
                'petugasId',
                'userId',
                'hari',
                'khatib',
                'imam',
                'muadzin',
                'hariError',
            ]
        );
    }

    public function render()
    {
        // Get current user and role
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        // Query builder for petugas
        $query = ModelsPetugas::with('user')
            ->select('id', 'user_id', 'hari', 'khatib', 'imam', 'muadzin');

        // If user is not Super Admin, filter petugas and exclude users with 'Super Admin' or 'Admin' roles
        if (!$isSuperAdmin) {
            $query->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['Super Admin', 'Admin']);
            });
        }

        // If user is not admin, only show their own petugas
        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            // Admin can search through all petugas
            $query->where(function ($query) {
                $query->where('hari', 'like', '%' . $this->search . '%')
                    ->orWhere('khatib', 'like', '%' . $this->search . '%')
                    ->orWhere('imam', 'like', '%' . $this->search . '%')
                    ->orWhere('muadzin', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $petugasList = $query->orderBy('id', 'asc')
            ->paginate($this->paginate);

        // Only admin can see list of users for assignment
        $users = collect([]);
        if ($isAdmin) {
            $usersWithPetugas = ModelsPetugas::pluck('user_id')->toArray();
            // If not Super Admin, exclude users with 'Super Admin' or 'Admin' roles
            $usersQuery = User::whereNotIn('id', $usersWithPetugas);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }
            $users = $usersQuery->orderBy('name')
                ->get();
        }

        return view('livewire.petugas.petugas', [
            'petugasList' => $petugasList,
            'users' => $users,
        ]);
    }

    public function showAddForm()
    {
        $this->resetValidation();
        $this->reset(
            [
                'petugasId',
                'userId',
                'hari',
                'khatib',
                'imam',
                'muadzin',
                'hariError',
            ]
        );

        // For non-admin users, automatically set their user ID
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->userId = Auth::id();
        }

        $this->isEdit = false;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $petugas = ModelsPetugas::findOrFail($id);

        // Check if user has permission to edit this petugas
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $petugas->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit petugas ini!');
            return;
        }

        $this->petugasId = $petugas->id;
        $this->userId    = $petugas->user_id;
        $this->hari      = $petugas->hari;
        $this->khatib    = $petugas->khatib;
        $this->imam      = $petugas->imam;
        $this->muadzin   = $petugas->muadzin;

        $this->showForm  = true;
        $this->isEdit    = true;
        $this->showTable = false;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->showTable = true;
        $this->resetValidation();
        $this->reset(
            [
                'petugasId',
                'userId',
                'hari',
                'khatib',
                'imam',
                'muadzin',
                'hariError'
            ]
        );
    }

    public function save()
    {
        $currentUser = Auth::user();

        // If user is not admin, force userId to be their own id
        if (!in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            $this->userId = $currentUser->id;
        }

        // For admin users, still validate one profile per user
        if (in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            if (!$this->isEdit) {
                // Check if the selected user already has a profile
                $existingPetugas = ModelsPetugas::where('user_id', $this->userId)->first();
                if ($existingPetugas) {
                    $this->dispatch('error', 'User ini sudah memiliki petugas!');
                    return;
                }
            } else {
                // When editing, make sure we're not changing to a user who already has a profile
                $existingPetugas = ModelsPetugas::where('user_id', $this->userId)
                    ->where('id', '!=', $this->petugasId)
                    ->first();
                if ($existingPetugas) {
                    $this->dispatch('error', 'User ini sudah memiliki petugas!');
                    return;
                }
            }
        }

        // Validate that the selected date is a Friday
        if ($this->hari) {
            $dayOfWeek = date('N', strtotime($this->hari));
            if ($dayOfWeek != 5) { // 5 = Friday
                $this->hariError = 'Tanggal yang dipilih bukan Hari Jum\'at, harap pilih tanggal di Hari Jum\'at';
                return;
            }
        }

        // Check for duplicate hari for the same user
        $existingHari = ModelsPetugas::where('user_id', $this->userId)
            ->where('hari', $this->hari);

        if ($this->isEdit) {
            $existingHari->where('id', '!=', $this->petugasId);
        }

        if ($existingHari->exists()) {
            $this->hariError = 'Tanggal ini tidak bisa digunakan karena sudah digunakan sebelumnya! Silahkan pilih hari Jumat di tanggal lain.';
            return;
        }

        $this->hariError = '';
        $this->validate();

        try {
            if ($this->isEdit) {
                $petugas = ModelsPetugas::findOrFail($this->petugasId);
                // Check if user has permission to edit this petugas
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $petugas->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit petugas ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own petugas
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat petugas untuk user lain!');
                    return;
                }
                $petugas = new ModelsPetugas();
            }

            $petugas->user_id = $this->userId;
            $petugas->hari = $this->hari;
            $petugas->khatib = $this->khatib;
            $petugas->imam = $this->imam;
            $petugas->muadzin = $this->muadzin;
            $petugas->save();

            // Trigger event 
            $profil = Profil::where('user_id', $this->userId)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'petugas'));

            $this->dispatch('success', $this->isEdit ? 'Petugas berhasil diubah!' : 'Petugas berhasil ditambahkan!');

            // Hide form and show table for all users after successful save
            $this->showForm = false;
            $this->showTable = true;
            $this->reset(
                [
                    'petugasId',
                    'userId',
                    'hari',
                    'khatib',
                    'imam',
                    'muadzin',
                    'hariError'
                ]
            );
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan petugas: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;

        $petugas = ModelsPetugas::findOrFail($id);
        $this->deletePetugasId = $petugas->id;
        $this->deletePetugasName = $petugas->user->name;
    }

    public function destroyPetugas()
    {
        try {
            $petugas = ModelsPetugas::findOrFail($this->deletePetugasId);
            $profil = $petugas->user->profil;
            $petugas->delete();

            // Trigger event 
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'petugas'));

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Petugas berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus petugas: ' . $e->getMessage());
        }
    }
}
