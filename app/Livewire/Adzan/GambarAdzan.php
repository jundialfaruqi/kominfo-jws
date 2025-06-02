<?php

namespace App\Livewire\Adzan;

use App\Models\Adzan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GambarAdzan extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Gambar Slides')]

    public $search;
    public $paginate;
    protected $paginationTheme = 'bootstrap';

    public $adzanId;
    public $userId;
    public $adzan1;
    public $tmp_adzan1;
    public $adzan2;
    public $tmp_adzan2;
    public $adzan3;
    public $tmp_adzan3;
    public $adzan4;
    public $tmp_adzan4;
    public $adzan5;
    public $tmp_adzan5;
    public $adzan6;
    public $tmp_adzan6;
    public $adzan7;
    public $tmp_adzan7;
    public $adzan8;
    public $tmp_adzan8;
    public $adzan9;
    public $tmp_adzan9;
    public $adzan10;
    public $tmp_adzan10;
    public $adzan11;
    public $tmp_adzan11;
    public $adzan12;
    public $tmp_adzan12;
    public $adzan13;
    public $tmp_adzan13;
    public $adzan14;
    public $tmp_adzan14;
    public $adzan15;
    public $tmp_adzan15;

    public $user;

    public $isEdit = false;
    public $showForm = false;
    public $deleteAdzanId;
    public $deleteAdzanName;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'adzan1' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan2' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan3' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan4' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan5' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan6' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan7' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan8' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan9' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan10' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan11' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan12' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan13' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan14' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'adzan15' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
    ];

    public $messages = [
        'userId.required' => 'Pilih Admin Masjid terlebih dahulu',
        'userId.exists'   => 'Admin Masjdi tidak ditemukan',
        'adzan1.image'    => 'File harus berupa gambar',
        'adzan2.image'    => 'File harus berupa gambar',
        'adzan3.image'    => 'File harus berupa gambar',
        'adzan4.image'    => 'File harus berupa gambar',
        'adzan5.image'    => 'File harus berupa gambar',
        'adzan6.image'    => 'File harus berupa gambar',
        'adzan7.image'    => 'File harus berupa gambar',
        'adzan8.image'    => 'File harus berupa gambar',
        'adzan9.image'    => 'File harus berupa gambar',
        'adzan10.image'   => 'File harus berupa gambar',
        'adzan11.image'   => 'File harus berupa gambar',
        'adzan12.image'   => 'File harus berupa gambar',
        'adzan13.image'   => 'File harus berupa gambar',
        'adzan14.image'   => 'File harus berupa gambar',
        'adzan15.image'   => 'File harus berupa gambar',
        'adzan1.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan2.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan3.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan4.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan5.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan6.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan7.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan8.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan9.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan10.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan11.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan12.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan13.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan14.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan15.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 5MB!',
        'adzan1.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan2.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan3.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan4.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan5.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan6.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan7.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan8.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan9.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan10.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan11.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan12.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan13.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan14.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
        'adzan15.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP, SVG',
    ];

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // jika user bukan admin
        if (Auth::user()->role !== 'Admin') {
            $adzan = Adzan::where('user_id', Auth::user()->id)->first();

            // Always show form for non-admin users
            $this->showForm = true;
            // Set user ID for new adzan
            $this->userId = Auth::id();

            if ($adzan) {
                // If adzan exists, load the data
                $this->adzanId     = $adzan->id;
                $this->tmp_adzan1  = $adzan->adzan1;
                $this->tmp_adzan2  = $adzan->adzan2;
                $this->tmp_adzan3  = $adzan->adzan3;
                $this->tmp_adzan4  = $adzan->adzan4;
                $this->tmp_adzan5  = $adzan->adzan5;
                $this->tmp_adzan6  = $adzan->adzan6;
                $this->tmp_adzan7  = $adzan->adzan7;
                $this->tmp_adzan8  = $adzan->adzan8;
                $this->tmp_adzan9  = $adzan->adzan9;
                $this->tmp_adzan10 = $adzan->adzan10;
                $this->tmp_adzan11 = $adzan->adzan11;
                $this->tmp_adzan12 = $adzan->adzan12;
                $this->tmp_adzan13 = $adzan->adzan13;
                $this->tmp_adzan14 = $adzan->adzan14;
                $this->tmp_adzan15 = $adzan->adzan15;
                $this->isEdit      = true;
            } else {
                // For new adzan, set isEdit to false
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
                'adzanId',
                'userId',
                'adzan1',
                'tmp_adzan1',
                'adzan2',
                'tmp_adzan2',
                'adzan3',
                'tmp_adzan3',
                'adzan4',
                'tmp_adzan4',
                'adzan5',
                'tmp_adzan5',
                'adzan6',
                'tmp_adzan6',
                'adzan7',
                'tmp_adzan7',
                'adzan8',
                'tmp_adzan8',
                'adzan9',
                'tmp_adzan9',
                'adzan10',
                'tmp_adzan10',
                'adzan11',
                'tmp_adzan11',
                'adzan12',
                'tmp_adzan12',
                'adzan13',
                'tmp_adzan13',
                'adzan14',
                'tmp_adzan14',
                'adzan15',
                'tmp_adzan15'
            ]
        );
    }

    public function render()
    {
        // get current user and role
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'Admin';

        // query builder for adzan
        $query = Adzan::with('user')
            ->select('id', 'user_id', 'adzan1', 'adzan2', 'adzan3', 'adzan4', 'adzan5', 'adzan6', 'adzan7', 'adzan8', 'adzan9', 'adzan10', 'adzan11', 'adzan12', 'adzan13', 'adzan14', 'adzan15');

        // if user is not admin, only show their own adzan
        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            // admin can search through all adzan
            $query->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        $adzanList = $query->orderBy('id', 'asc')
            ->paginate($this->paginate);

        // only admin can see list of users for assignment
        $users = $isAdmin ? User::orderBy('name')->get() : collect([]);

        return view('livewire.adzan.gambar-adzan', [
            'adzanList' => $adzanList,
            'users' => $users,
        ]);
    }

    public function showAddForm()
    {

        // only admin can add new adzan
        if (Auth::user()->role !== 'Admin') {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah adzan!');
            return;
        }

        $this->resetValidation();
        $this->reset(
            [
                'adzanId',
                'userId',
                'adzan1',
                'tmp_adzan1',
                'adzan2',
                'tmp_adzan2',
                'adzan3',
                'tmp_adzan3',
                'adzan4',
                'tmp_adzan4',
                'adzan5',
                'tmp_adzan5',
                'adzan6',
                'tmp_adzan6',
                'adzan7',
                'tmp_adzan7',
                'adzan8',
                'tmp_adzan8',
                'adzan9',
                'tmp_adzan9',
                'adzan10',
                'tmp_adzan10',
                'adzan11',
                'tmp_adzan11',
                'adzan12',
                'tmp_adzan12',
                'adzan13',
                'tmp_adzan13',
                'adzan14',
                'tmp_adzan14',
                'adzan15',
                'tmp_adzan15'
            ]
        );
        $this->isEdit = false;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $adzan = Adzan::findOrFail($id);

        // Check if user has permission to edit this adzan
        if (Auth::user()->role !== 'Admin' && Auth::id() !== $adzan->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit adzan ini!');
            return;
        }

        $this->adzanId     = $adzan->id;
        $this->userId      = $adzan->user_id;
        $this->tmp_adzan1  = $adzan->adzan1;
        $this->tmp_adzan2  = $adzan->adzan2;
        $this->tmp_adzan3  = $adzan->adzan3;
        $this->tmp_adzan4  = $adzan->adzan4;
        $this->tmp_adzan5  = $adzan->adzan5;
        $this->tmp_adzan6  = $adzan->adzan6;
        $this->tmp_adzan7  = $adzan->adzan7;
        $this->tmp_adzan8  = $adzan->adzan8;
        $this->tmp_adzan9  = $adzan->adzan9;
        $this->tmp_adzan10 = $adzan->adzan10;
        $this->tmp_adzan11 = $adzan->adzan11;
        $this->tmp_adzan12 = $adzan->adzan12;
        $this->tmp_adzan13 = $adzan->adzan13;
        $this->tmp_adzan14 = $adzan->adzan14;
        $this->tmp_adzan15 = $adzan->adzan15;
        $this->isEdit      = true;
        $this->showForm    = true;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->reset(
            [
                'adzanId',
                'userId',
                'adzan1',
                'tmp_adzan1',
                'adzan2',
                'tmp_adzan2',
                'adzan3',
                'tmp_adzan3',
                'adzan4',
                'tmp_adzan4',
                'adzan5',
                'tmp_adzan5',
                'adzan6',
                'tmp_adzan6',
                'adzan7',
                'tmp_adzan7',
                'adzan8',
                'tmp_adzan8',
                'adzan9',
                'tmp_adzan9',
                'adzan10',
                'tmp_adzan10',
                'adzan11',
                'tmp_adzan11',
                'adzan12',
                'tmp_adzan12',
                'adzan13',
                'tmp_adzan13',
                'adzan14',
                'tmp_adzan14',
                'adzan15',
                'tmp_adzan15'
            ]
        );
    }

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
                $adzan = Adzan::findOrFail($this->adzanId);
                // Check if user has permission to edit this adzan
                if ($currentUser->role !== 'Admin' && $currentUser->id !== $adzan->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit adzan ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own adzan
                if ($currentUser->role !== 'Admin' && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat adzan untuk user lain!');
                    return;
                }
                $adzan = new Adzan();
            }

            $adzan->user_id = $this->userId;
            $adzan->adzan1  = $this->tmp_adzan1;
            $adzan->adzan2  = $this->tmp_adzan2;
            $adzan->adzan3  = $this->tmp_adzan3;
            $adzan->adzan4  = $this->tmp_adzan4;
            $adzan->adzan5  = $this->tmp_adzan5;
            $adzan->adzan6  = $this->tmp_adzan6;
            $adzan->adzan7  = $this->tmp_adzan7;
            $adzan->adzan8  = $this->tmp_adzan8;
            $adzan->adzan9  = $this->tmp_adzan9;
            $adzan->adzan10 = $this->tmp_adzan10;
            $adzan->adzan11 = $this->tmp_adzan11;
            $adzan->adzan12 = $this->tmp_adzan12;
            $adzan->adzan13 = $this->tmp_adzan13;
            $adzan->adzan14 = $this->tmp_adzan14;
            $adzan->adzan15 = $this->tmp_adzan15;

            // Handle adzan 1 upload
            if ($this->adzan1) {
                // delete old adzan 1 if exists
                if ($this->isEdit && $adzan->adzan1 && file_exists(public_path($adzan->adzan1))) {
                    File::delete(public_path($adzan->adzan1));
                }
                // save new adzan 1
                $fileName = time() . '_adzan1_' . $this->adzan1->getClientOriginalName();
                $this->adzan1->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan1 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 2 upload
            if ($this->adzan2) {
                // delete old adzan 2 if exists
                if ($this->isEdit && $adzan->adzan2 && file_exists(public_path($adzan->adzan2))) {
                    File::delete(public_path($adzan->adzan2));
                }
                // save new adzan 2
                $fileName = time() . '_adzan2_' . $this->adzan2->getClientOriginalName();
                $this->adzan2->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan2 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 3 upload
            if ($this->adzan3) {
                // delete old adzan 3 if exists
                if ($this->isEdit && $adzan->adzan3 && file_exists(public_path($adzan->adzan3))) {
                    File::delete(public_path($adzan->adzan3));
                }
                // save new adzan 3
                $fileName = time() . '_adzan3_' . $this->adzan3->getClientOriginalName();
                $this->adzan3->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan3 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 4 upload
            if ($this->adzan4) {
                // delete old adzan 4 if exists
                if ($this->isEdit && $adzan->adzan4 && file_exists(public_path($adzan->adzan4))) {
                    File::delete(public_path($adzan->adzan4));
                }
                // save new adzan 4
                $fileName = time() . '_adzan4_' . $this->adzan4->getClientOriginalName();
                $this->adzan4->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan4 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 5 upload
            if ($this->adzan5) {
                // delete old adzan 5 if exists
                if ($this->isEdit && $adzan->adzan5 && file_exists(public_path($adzan->adzan5))) {
                    File::delete(public_path($adzan->adzan5));
                }
                // save new adzan 5
                $fileName = time() . '_adzan5_' . $this->adzan5->getClientOriginalName();
                $this->adzan5->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan5 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 6 upload            
            if ($this->adzan6) {
                // delete old adzan 6 if exists
                if ($this->isEdit && $adzan->adzan6 && file_exists(public_path($adzan->adzan6))) {
                    File::delete(public_path($adzan->adzan6));
                }
                // save new adzan 6
                $fileName = time() . '_adzan6_' . $this->adzan6->getClientOriginalName();
                $this->adzan6->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan6 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 7 upload            
            if ($this->adzan7) {
                // delete old adzan 7 if exists
                if ($this->isEdit && $adzan->adzan7 && file_exists(public_path($adzan->adzan7))) {
                    File::delete(public_path($adzan->adzan7));
                }
                // save new adzan 7
                $fileName = time() . '_adzan7_' . $this->adzan7->getClientOriginalName();
                $this->adzan7->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan7 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 8 upload            
            if ($this->adzan8) {
                // delete old adzan 8 if exists
                if ($this->isEdit && $adzan->adzan8 && file_exists(public_path($adzan->adzan8))) {
                    File::delete(public_path($adzan->adzan8));
                }
                // save new adzan 8
                $fileName = time() . '_adzan8_' . $this->adzan8->getClientOriginalName();
                $this->adzan8->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan8 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 9 upload            
            if ($this->adzan9) {
                // delete old adzan 9 if exists
                if ($this->isEdit && $adzan->adzan9 && file_exists(public_path($adzan->adzan9))) {
                    File::delete(public_path($adzan->adzan9));
                }
                // save new adzan 9
                $fileName = time() . '_adzan9_' . $this->adzan9->getClientOriginalName();
                $this->adzan9->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan9 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 10 upload            
            if ($this->adzan10) {
                // delete old adzan 10 if exists
                if ($this->isEdit && $adzan->adzan10 && file_exists(public_path($adzan->adzan10))) {
                    File::delete(public_path($adzan->adzan10));
                }
                // save new adzan 10
                $fileName = time() . '_adzan10_' . $this->adzan10->getClientOriginalName();
                $this->adzan10->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan10 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 11 upload            
            if ($this->adzan11) {
                // delete old adzan 11 if exists
                if ($this->isEdit && $adzan->adzan11 && file_exists(public_path($adzan->adzan11))) {
                    File::delete(public_path($adzan->adzan11));
                }
                // save new adzan 11
                $fileName = time() . '_adzan11_' . $this->adzan11->getClientOriginalName();
                $this->adzan11->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan11 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 12 upload            
            if ($this->adzan12) {
                // delete old adzan 12 if exists
                if ($this->isEdit && $adzan->adzan12 && file_exists(public_path($adzan->adzan12))) {
                    File::delete(public_path($adzan->adzan12));
                }
                // save new adzan 12
                $fileName = time() . '_adzan12_' . $this->adzan12->getClientOriginalName();
                $this->adzan12->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan12 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 13 upload            
            if ($this->adzan13) {
                // delete old adzan 13 if exists
                if ($this->isEdit && $adzan->adzan13 && file_exists(public_path($adzan->adzan13))) {
                    File::delete(public_path($adzan->adzan13));
                }
                // save new adzan 13
                $fileName = time() . '_adzan13_' . $this->adzan13->getClientOriginalName();
                $this->adzan13->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan13 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 14 upload            
            if ($this->adzan14) {
                // delete old adzan 14 if exists
                if ($this->isEdit && $adzan->adzan14 && file_exists(public_path($adzan->adzan14))) {
                    File::delete(public_path($adzan->adzan14));
                }
                // save new adzan 14
                $fileName = time() . '_adzan14_' . $this->adzan14->getClientOriginalName();
                $this->adzan14->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan14 = 'images/adzan/' . $fileName;
            }

            // Handle adzan 15 upload            
            if ($this->adzan15) {
                // delete old adzan 15 if exists
                if ($this->isEdit && $adzan->adzan15 && file_exists(public_path($adzan->adzan15))) {
                    File::delete(public_path($adzan->adzan15));
                }
                // save new adzan 15
                $fileName = time() . '_adzan15_' . $this->adzan15->getClientOriginalName();
                $this->adzan15->storeAs('', $fileName, 'public_images_adzan');
                $adzan->adzan15 = 'images/adzan/' . $fileName;
            }

            $adzan->save();

            $this->dispatch('success', $this->isEdit ? 'Adzan berhasil diubah!' : 'Adzan berhasil ditambahkan!');

            // only hide form and reset fields if user is not admin
            if (Auth::user()->role === 'Admin') {
                $this->showForm = false;
                $this->resetValidation();
                $this->reset(
                    [
                        'adzanId',
                        'userId',
                        'adzan1',
                        'adzan2',
                        'adzan3',
                        'adzan4',
                        'adzan5',
                        'adzan6',
                        'adzan7',
                        'adzan8',
                        'adzan9',
                        'adzan10',
                        'adzan11',
                        'adzan12',
                        'adzan13',
                        'adzan14',
                        'adzan15',
                    ]
                );
            } else {
                // for regular users, keep the form visible and reload their data
                $this->showForm = true;
                $adzan = Adzan::where('user_id', Auth::user()->id)->first();
                if ($adzan) {
                    $this->adzanId     = $adzan->id;
                    $this->tmp_adzan1  = $adzan->adzan1;
                    $this->tmp_adzan2  = $adzan->adzan2;
                    $this->tmp_adzan3  = $adzan->adzan3;
                    $this->tmp_adzan4  = $adzan->adzan4;
                    $this->tmp_adzan5  = $adzan->adzan5;
                    $this->tmp_adzan6  = $adzan->adzan6;
                    $this->tmp_adzan7  = $adzan->adzan7;
                    $this->tmp_adzan8  = $adzan->adzan8;
                    $this->tmp_adzan9  = $adzan->adzan9;
                    $this->tmp_adzan10 = $adzan->adzan10;
                    $this->tmp_adzan11 = $adzan->adzan11;
                    $this->tmp_adzan12 = $adzan->adzan12;
                    $this->tmp_adzan13 = $adzan->adzan13;
                    $this->tmp_adzan14 = $adzan->adzan14;
                    $this->tmp_adzan15 = $adzan->adzan15;
                    $this->isEdit      = true;
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan adzan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;
        $adzan = Adzan::findOrFail($id);
        $this->deleteAdzanId = $adzan->id;
        $this->deleteAdzanName = $adzan->user->name;
    }

    public function destroyAdzan()
    {
        try {
            $adzan = Adzan::findOrFail($this->deleteAdzanId);
            if ($adzan->adzan1 && file_exists(public_path($adzan->adzan1))) {
                File::delete(public_path($adzan->adzan1));
            }
            if ($adzan->adzan2 && file_exists(public_path($adzan->adzan2))) {
                File::delete(public_path($adzan->adzan2));
            }
            if ($adzan->adzan3 && file_exists(public_path($adzan->adzan3))) {
                File::delete(public_path($adzan->adzan3));
            }
            if ($adzan->adzan4 && file_exists(public_path($adzan->adzan4))) {
                File::delete(public_path($adzan->adzan4));
            }
            if ($adzan->adzan5 && file_exists(public_path($adzan->adzan5))) {
                File::delete(public_path($adzan->adzan5));
            }
            if ($adzan->adzan6 && file_exists(public_path($adzan->adzan6))) {
                File::delete(public_path($adzan->adzan6));
            }
            if ($adzan->adzan7 && file_exists(public_path($adzan->adzan7))) {
                File::delete(public_path($adzan->adzan7));
            }
            if ($adzan->adzan8 && file_exists(public_path($adzan->adzan8))) {
                File::delete(public_path($adzan->adzan8));
            }
            if ($adzan->adzan9 && file_exists(public_path($adzan->adzan9))) {
                File::delete(public_path($adzan->adzan9));
            }
            if ($adzan->adzan10 && file_exists(public_path($adzan->adzan10))) {
                File::delete(public_path($adzan->adzan10));
            }
            if ($adzan->adzan11 && file_exists(public_path($adzan->adzan11))) {
                File::delete(public_path($adzan->adzan11));
            }
            if ($adzan->adzan12 && file_exists(public_path($adzan->adzan12))) {
                File::delete(public_path($adzan->adzan12));
            }
            if ($adzan->adzan13 && file_exists(public_path($adzan->adzan13))) {
                File::delete(public_path($adzan->adzan13));
            }
            if ($adzan->adzan14 && file_exists(public_path($adzan->adzan14))) {
                File::delete(public_path($adzan->adzan14));
            }
            if ($adzan->adzan15 && file_exists(public_path($adzan->adzan15))) {
                File::delete(public_path($adzan->adzan15));
            }
            $adzan->delete();

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Adzan berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus adzan: ' . $e->getMessage());
        }
    }
}
