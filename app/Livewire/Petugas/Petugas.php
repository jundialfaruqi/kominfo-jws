<?php

namespace App\Livewire\Petugas;

use App\Models\Petugas as ModelsPetugas;
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

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // jika user bukan admin
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $petugas = ModelsPetugas::where('user_id', Auth::user()->id)->first();

            // Always show form for non-admin users
            $this->showForm = true;
            // Set user ID for new petugas
            $this->userId = Auth::id();

            if ($petugas) {
                // If petugas exists, load the data
                $this->petugasId = $petugas->id;
                $this->hari = $petugas->hari;
                $this->khatib = $petugas->khatib;
                $this->imam = $petugas->imam;
                $this->muadzin = $petugas->muadzin;
                $this->isEdit = true;
            } else {
                // For new slides, set isEdit to false
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
                'petugasId',
                'userId',
                'hari',
                'khatib',
                'imam',
                'muadzin',
            ]
        );
    }

    public function render()
    {
        // Get current user and role
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);

        // Query builder for petugas
        $query = ModelsPetugas::with('user')
            ->select('id', 'user_id', 'hari', 'khatib', 'imam', 'muadzin');

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
        $users = $isAdmin ? User::orderBy('name')->get() : collect([]);

        return view('livewire.petugas.petugas', [
            'petugasList' => $petugasList,
            'users' => $users,
        ]);
    }

    public function showAddForm()
    {
        // Only admin can add new petugas
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah petugas!');
            return;
        }

        $this->resetValidation();
        $this->reset(
            [
                'petugasId',
                'userId',
                'hari',
                'khatib',
                'imam',
                'muadzin',
            ]
        );
        $this->isEdit = false;
        $this->showForm = true;
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
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->reset(
            [
                'petugasId',
                'userId',
                'hari',
                'khatib',
                'imam',
                'muadzin'
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

            $this->dispatch('success', $this->isEdit ? 'Petugas berhasil diubah!' : 'Petugas berhasil ditambahkan!');

            // only hide form and reset field if user is not admin
            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
                $this->showForm = false;
                $this->reset(
                    [
                        'petugasId',
                        'userId',
                        'hari',
                        'khatib',
                        'imam',
                        'muadzin'
                    ]
                );
            } else {
                // for regular users, keep the form visible and reload their data
                $this->showForm = true;
                $petugas = ModelsPetugas::where('user_id', Auth::id())->first();
                if ($petugas) {
                    $this->petugasId = $petugas->id;
                    $this->hari      = $petugas->hari;
                    $this->khatib    = $petugas->khatib;
                    $this->imam      = $petugas->imam;
                    $this->muadzin   = $petugas->muadzin;
                    $this->isEdit    = true;
                }
            }
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
            $petugas->delete();
            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Petugas berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus petugas: ' . $e->getMessage());
        }
    }
}
