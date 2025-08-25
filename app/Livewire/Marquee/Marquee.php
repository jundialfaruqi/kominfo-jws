<?php

namespace App\Livewire\Marquee;

use App\Models\Marquee as ModelsMarquee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Marquee extends Component
{
    use WithPagination;

    #[Title('Teks Berjalan (Marquee)')]

    public $paginate;
    public $search;
    protected $paginationTheme = 'bootstrap';

    public $marqueeId;
    public $userId;
    public $marquee1;
    public $marquee2;
    public $marquee3;
    public $marquee4;
    public $marquee5;
    public $marquee6;

    public $showForm = false;
    public $isEdit = false;
    public $showTable = true;
    public $deleteMarqueeId;
    public $deleteMarqueeName;

    protected $rules = [
        'userId'    => 'required|exists:users,id',
        'marquee1'  => 'required',
        'marquee2'  => 'required',
        'marquee3'  => 'required',
        'marquee4'  => 'required',
        'marquee5'  => 'required',
        'marquee6'  => 'required',
    ];

    protected $messages = [
        'userId.required'   => 'Pilih Admin Masjid terlebih dahulu',
        'userId.exists'     => 'Admin Masjid tidak ditemukan',
        'marquee1.required' => 'Teks Marquee 1 tidak boleh kosong',
        'marquee2.required' => 'Teks Marquee 2 tidak boleh kosong',
        'marquee3.required' => 'Teks Marquee 3 tidak boleh kosong',
        'marquee4.required' => 'Teks Marquee 4 tidak boleh kosong',
        'marquee5.required' => 'Teks Marquee 5 tidak boleh kosong',
        'marquee6.required' => 'Teks Marquee 6 tidak boleh kosong',
    ];

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // If user is not admin
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $marquee = ModelsMarquee::where('user_id', Auth::id())->first();

            // Always show form for non-admin users
            $this->showForm = true;
            // Set user ID for new marquee
            $this->userId = Auth::id();

            if ($marquee) {
                // If marquee exists, load the data
                $this->marqueeId = $marquee->id;
                $this->marquee1  = $marquee->marquee1;
                $this->marquee2  = $marquee->marquee2;
                $this->marquee3  = $marquee->marquee3;
                $this->marquee4  = $marquee->marquee4;
                $this->marquee5  = $marquee->marquee5;
                $this->marquee6  = $marquee->marquee6;
                $this->isEdit    = true;
            } else {
                // For new marquee, set isEdit to false
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
                'marqueeId',
                'userId',
                'marquee1',
                'marquee2',
                'marquee3',
                'marquee4',
                'marquee5',
                'marquee6',
            ]
        );
    }

    public function render()
    {
        // get current user and role
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        // Query builder for marquee
        $query = ModelsMarquee::with('user')
            ->select('id', 'user_id', 'marquee1', 'marquee2', 'marquee3', 'marquee4', 'marquee5', 'marquee6');

        // If user is not Super Admin, filter marquee and exclude users with 'Super Admin' or 'Admin' roles
        if (!$isSuperAdmin) {
            $query->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['Super Admin', 'Admin']);
            });
        }

        // If user is not admin, only show their own marquee
        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            // Admin can search through all marquee
            $query->where(function ($query) {
                $query->where('marquee1', 'like', '%' . $this->search . '%')
                    ->orWhere('marquee2', 'like', '%' . $this->search . '%')
                    ->orWhere('marquee3', 'like', '%' . $this->search . '%')
                    ->orWhere('marquee4', 'like', '%' . $this->search . '%')
                    ->orWhere('marquee5', 'like', '%' . $this->search . '%')
                    ->orWhere('marquee6', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $marqueeList = $query->orderBy('id', 'asc')
            ->paginate($this->paginate);

        // Only admin can see list of users for assignment
        $users = collect([]);
        if ($isAdmin) {
            $usersWithMarquee = ModelsMarquee::pluck('user_id')->toArray();

            // If not Super Admin, exclude users with 'Super Admin' or 'Admin' roles
            $usersQuery = User::whereNotIn('id', $usersWithMarquee);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }

            $users = $usersQuery->orderBy('name')
                ->get();
        }

        return view('livewire.marquee.marquee', [
            'marqueeList' => $marqueeList,
            'users' => $users
        ]);
    }

    public function showAddForm()
    {
        // Only admin can add new marquee
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah marquee!');
            return;
        }

        $this->resetValidation();
        $this->reset(
            [
                'marqueeId',
                'userId',
                'marquee1',
                'marquee2',
                'marquee3',
                'marquee4',
                'marquee5',
                'marquee6',
            ]
        );
        $this->isEdit = false;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $marquee = ModelsMarquee::findOrFail($id);

        // Check if user has permission to edit this marquee
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $marquee->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit marquee ini!');
            return;
        }

        $this->marqueeId = $marquee->id;
        $this->userId    = $marquee->user_id;
        $this->marquee1  = $marquee->marquee1;
        $this->marquee2  = $marquee->marquee2;
        $this->marquee3  = $marquee->marquee3;
        $this->marquee4  = $marquee->marquee4;
        $this->marquee5  = $marquee->marquee5;
        $this->marquee6  = $marquee->marquee6;

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
                'marqueeId',
                'userId',
                'marquee1',
                'marquee2',
                'marquee3',
                'marquee4',
                'marquee5',
                'marquee6',
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

        // Additional validation for one marquee per user
        if (!$this->isEdit) {
            // Check if the selected user already has a marquee
            $existingMarquee = ModelsMarquee::where('user_id', $this->userId)->first();
            if ($existingMarquee) {
                $this->dispatch('error', 'User ini sudah memiliki marquee!');
                return;
            }
        } else {
            // When editing, make sure we're not changing to a user who already has a marquee
            $existingMarquee = ModelsMarquee::where('user_id', $this->userId)
                ->where('id', '!=', $this->marqueeId)
                ->first();
            if ($existingMarquee) {
                $this->dispatch('error', 'User ini sudah memiliki marquee!');
                return;
            }
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                $marquee = ModelsMarquee::findOrFail($this->marqueeId);
                // Check if user has permission to edit this marquee
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $marquee->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit marquee ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own marquee
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat marquee untuk user lain!');
                    return;
                }
                $marquee = new ModelsMarquee();
            }

            $marquee->user_id = $this->userId;
            $marquee->marquee1 = $this->marquee1;
            $marquee->marquee2 = $this->marquee2;
            $marquee->marquee3 = $this->marquee3;
            $marquee->marquee4 = $this->marquee4;
            $marquee->marquee5 = $this->marquee5;
            $marquee->marquee6 = $this->marquee6;
            $marquee->save();

            $this->dispatch('success', $this->isEdit ? 'Marquee berhasil diubah!' : 'Marquee berhasil ditambahkan!');
            $this->showTable = true;

            //only hide form and reset fields if user is admin
            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
                $this->showForm = false;
                $this->reset(
                    [
                        'marqueeId',
                        'userId',
                        'marquee1',
                        'marquee2',
                        'marquee3',
                        'marquee4',
                        'marquee5',
                        'marquee6',
                    ]
                );
            } else {
                // for regular users, keep the form visible and reload their data
                $this->showForm = true;
                $marquee = ModelsMarquee::where('user_id', Auth::id())->first();
                if ($marquee) {
                    $this->marqueeId = $marquee->id;
                    $this->marquee1 = $marquee->marquee1;
                    $this->marquee2 = $marquee->marquee2;
                    $this->marquee3 = $marquee->marquee3;
                    $this->marquee4 = $marquee->marquee4;
                    $this->marquee5 = $marquee->marquee5;
                    $this->marquee6 = $marquee->marquee6;
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan marquee: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;
        $marquee = ModelsMarquee::findOrFail($id);
        $this->deleteMarqueeId = $marquee->id;
        $this->deleteMarqueeName = $marquee->user->name;
    }

    public function destroyMarquee()
    {
        try {
            $marquee = ModelsMarquee::findOrFail($this->deleteMarqueeId);
            $marquee->delete();
            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Marquee berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus marquee: ' . $e->getMessage());
        }
    }
}
