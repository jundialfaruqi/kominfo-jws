<?php

namespace App\Livewire\Slides;

use App\Models\Slides;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class Slide extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Slider Utama')]

    public $search;
    public $paginate;
    protected $paginationTheme = 'bootstrap';

    public $slideId;
    public $userId;
    public $slide1;
    public $tmp_slide1;
    public $slide2;
    public $tmp_slide2;
    public $slide3;
    public $tmp_slide3;
    public $slide4;
    public $tmp_slide4;
    public $slide5;
    public $tmp_slide5;
    public $slide6;
    public $tmp_slide6;

    public $isEdit = false;
    public $showForm = false;
    public $deleteSlideId;
    public $deleteSlideName;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'slide1' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'slide2' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'slide3' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'slide4' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'slide5' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
        'slide6' => 'nullable|image|max:5000|mimes:jpg,png,jpeg,webp,gif,svg',
    ];

    protected $messages = [
        'userId.required' => 'Admin Masjid wajib diisi',
        'userId.exists'   => 'Admin Masjid tidak ditemukan',
        'slide1.image'    => 'File harus berupa gambar',
        'slide2.image'    => 'File harus berupa gambar',
        'slide3.image'    => 'File harus berupa gambar',
        'slide4.image'    => 'File harus berupa gambar',
        'slide5.image'    => 'File harus berupa gambar',
        'slide6.image'    => 'File harus berupa gambar',
        'slide1.max'      => 'File gambar terlalu besar. Ukuran file maksimal 5MB!',
        'slide2.max'      => 'File gambar terlalu besar. Ukuran file maksimal 5MB!',
        'slide3.max'      => 'File gambar terlalu besar. Ukuran file maksimal 5MB!',
        'slide4.max'      => 'File gambar terlalu besar. Ukuran file maksimal 5MB!',
        'slide5.max'      => 'File gambar terlalu besar. Ukuran file maksimal 5MB!',
        'slide6.max'      => 'File gambar terlalu besar. Ukuran file maksimal 5MB!',
        'slide1.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif,svg',
        'slide2.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif,svg',
        'slide3.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif,svg',
        'slide4.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif,svg',
        'slide5.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif,svg',
        'slide6.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif,svg',
    ];

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // If user is not admin
        if (Auth::user()->role !== 'Admin') {
            $slide = Slides::where('user_id', Auth::id())->first();

            // Always show form for non-admin users
            $this->showForm = true;
            // Set user ID for new slides
            $this->userId = Auth::id();

            if ($slide) {
                // If slides exists, load the data
                $this->slideId     = $slide->id;
                $this->tmp_slide1  = $slide->slide1;
                $this->tmp_slide2  = $slide->slide2;
                $this->tmp_slide3  = $slide->slide3;
                $this->tmp_slide4  = $slide->slide4;
                $this->tmp_slide5  = $slide->slide5;
                $this->tmp_slide6  = $slide->slide6;
                $this->isEdit      = true;
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
                'slideId',
                'userId',
                'slide1',
                'tmp_slide1',
                'slide2',
                'tmp_slide2',
                'slide3',
                'tmp_slide3',
                'slide4',
                'tmp_slide4',
                'slide5',
                'tmp_slide5',
                'slide6',
                'tmp_slide6'
            ]
        );
    }

    public function render()
    {
        // Get current user and role
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role === 'Admin';

        // Query builder for slides
        $query = Slides::with('user')
            ->select('id', 'user_id', 'slide1', 'slide2', 'slide3', 'slide4', 'slide5', 'slide6');

        // If user is not admin, only show their own slides
        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            // Admin can search through all slides
            $query->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        $slideList = $query->orderBy('id', 'asc')
            ->paginate($this->paginate);

        // Only admin can see list of users for assignment
        $users = $isAdmin ? User::orderBy('name')->get() : collect([]);

        return view('livewire.slides.slide', [
            'slideList' => $slideList,
            'users' => $users
        ]);
    }

    public function showAddForm()
    {

        // Only admin can add new slides
        if (Auth::user()->role !== 'Admin') {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah slide!');
            return;
        }

        $this->resetValidation();
        $this->reset(
            [
                'slideId',
                'userId',
                'slide1',
                'tmp_slide1',
                'slide2',
                'tmp_slide2',
                'slide3',
                'tmp_slide3',
                'slide4',
                'tmp_slide4',
                'slide5',
                'tmp_slide5',
                'slide6',
                'tmp_slide6'
            ]
        );

        $this->isEdit = false;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $slide = Slides::findOrFail($id);

        // Check if user has permission to edit this slide
        if (Auth::user()->role !== 'Admin' && Auth::id() !== $slide->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit slide ini!');
            return;
        }

        $this->slideId    = $slide->id;
        $this->userId     = $slide->user_id;
        $this->tmp_slide1 = $slide->slide1;
        $this->tmp_slide2 = $slide->slide2;
        $this->tmp_slide3 = $slide->slide3;
        $this->tmp_slide4 = $slide->slide4;
        $this->tmp_slide5 = $slide->slide5;
        $this->tmp_slide6 = $slide->slide6;

        $this->isEdit       = true;
        $this->showForm     = true;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->reset(
            [
                'slideId',
                'userId',
                'slide1',
                'tmp_slide1',
                'slide2',
                'tmp_slide2',
                'slide3',
                'tmp_slide3',
                'slide4',
                'tmp_slide4',
                'slide5',
                'tmp_slide5',
                'slide6',
                'tmp_slide6'
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
                $slide = Slides::findOrFail($this->slideId);
                // Check if user has permission to edit this slide
                if ($currentUser->role !== 'Admin' && $currentUser->id !== $slide->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit slide ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own slide
                if ($currentUser->role !== 'Admin' && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat slide untuk user lain!');
                    return;
                }
                $slide = new Slides();
            }

            $slide->user_id = $this->userId;
            $slide->slide1  = $this->tmp_slide1;
            $slide->slide2  = $this->tmp_slide2;
            $slide->slide3  = $this->tmp_slide3;
            $slide->slide4  = $this->tmp_slide4;
            $slide->slide5  = $this->tmp_slide5;
            $slide->slide6  = $this->tmp_slide6;

            // Handle slide1 upload
            if ($this->slide1) {
                // Delete old slide1 if exists
                if ($this->isEdit && $slide->slide1 && file_exists(public_path($slide->slide1))) {
                    File::delete(public_path($slide->slide1));
                }

                // Save new slide1
                $fileName = time() . '_slide1_' . $this->slide1->getClientOriginalName();
                $this->slide1->storeAs('', $fileName, 'public_images_slides');
                $slide->slide1 = '/images/slides/' . $fileName;
            }

            // Handle slide2 upload
            if ($this->slide2) {
                // Delete old slide2 if exists
                if ($this->isEdit && $slide->slide2 && file_exists(public_path($slide->slide2))) {
                    File::delete(public_path($slide->slide2));
                }

                // Save new slide2
                $fileName = time() . '_slide2_' . $this->slide2->getClientOriginalName();
                $this->slide2->storeAs('', $fileName, 'public_images_slides');
                $slide->slide2 = '/images/slides/' . $fileName;
            }

            // Handle slide3 upload
            if ($this->slide3) {
                // Delete old slide3 if exists
                if ($this->isEdit && $slide->slide3 && file_exists(public_path($slide->slide3))) {
                    File::delete(public_path($slide->slide3));
                }

                // Save new slide3
                $fileName = time() . '_slide3_' . $this->slide3->getClientOriginalName();
                $this->slide3->storeAs('', $fileName, 'public_images_slides');
                $slide->slide3 = '/images/slides/' . $fileName;
            }

            // Handle slide4 upload
            if ($this->slide4) {
                // Delete old slide4 if exists
                if ($this->isEdit && $slide->slide4 && file_exists(public_path($slide->slide4))) {
                    File::delete(public_path($slide->slide4));
                }

                // Save new slide4
                $fileName = time() . '_slide4_' . $this->slide4->getClientOriginalName();
                $this->slide4->storeAs('', $fileName, 'public_images_slides');
                $slide->slide4 = '/images/slides/' . $fileName;
            }

            // Handle slide5 upload
            if ($this->slide5) {
                // Delete old slide5 if exists
                if ($this->isEdit && $slide->slide5 && file_exists(public_path($slide->slide5))) {
                    File::delete(public_path($slide->slide5));
                }

                // Save new slide5
                $fileName = time() . '_slide5_' . $this->slide5->getClientOriginalName();
                $this->slide5->storeAs('', $fileName, 'public_images_slides');
                $slide->slide5 = '/images/slides/' . $fileName;
            }

            // Handle slide6 upload
            if ($this->slide6) {
                // Delete old slide6 if exists
                if ($this->isEdit && $slide->slide6 && file_exists(public_path($slide->slide6))) {
                    File::delete(public_path($slide->slide6));
                }

                // Save new slide6
                $fileName = time() . '_slide6_' . $this->slide6->getClientOriginalName();
                $this->slide6->storeAs('', $fileName, 'public_images_slides');
                $slide->slide6 = '/images/slides/' . $fileName;
            }

            $slide->save();

            $this->dispatch('success', $this->isEdit ? 'Gambar Slide berhasil diubah!' : 'Gambar Slide berhasil ditambahkan!');

            // Only hide form and reset fields if user is not admin
            if (Auth::user()->role === 'Admin') {
                $this->showForm = false;
                $this->reset(
                    [
                        'slideId',
                        'userId',
                        'tmp_slide1',
                        'tmp_slide2',
                        'tmp_slide3',
                        'tmp_slide4',
                        'tmp_slide5',
                        'tmp_slide6'
                    ]
                );
            } else {
                // For regular users, keep the form visible and reload their data
                $this->showForm = true;
                $slide = Slides::where('user_id', Auth::id())->first();
                if ($slide) {
                    $this->slideId    = $slide->id;
                    $this->userId     = $slide->user_id;
                    $this->tmp_slide1 = $slide->slide1;
                    $this->tmp_slide2 = $slide->slide2;
                    $this->tmp_slide3 = $slide->slide3;
                    $this->tmp_slide4 = $slide->slide4;
                    $this->tmp_slide5 = $slide->slide5;
                    $this->tmp_slide6 = $slide->slide6;
                    $this->isEdit     = true;
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan slide: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;
        $slide = Slides::findOrFail($id);
        $this->deleteSlideId = $slide->id;
        $this->deleteSlideName = $slide->user->name;
    }

    public function destroySlide()
    {
        try {
            $slide = Slides::findOrFail($this->deleteSlideId);

            if ($slide->slide1 && file_exists(public_path($slide->slide1))) {
                File::delete(public_path($slide->slide1));
            }

            if ($slide->slide2 && file_exists(public_path($slide->slide2))) {
                File::delete(public_path($slide->slide2));
            }

            if ($slide->slide3 && file_exists(public_path($slide->slide3))) {
                File::delete(public_path($slide->slide3));
            }

            if ($slide->slide4 && file_exists(public_path($slide->slide4))) {
                File::delete(public_path($slide->slide4));
            }

            if ($slide->slide5 && file_exists(public_path($slide->slide5))) {
                File::delete(public_path($slide->slide5));
            }

            if ($slide->slide6 && file_exists(public_path($slide->slide6))) {
                File::delete(public_path($slide->slide6));
            }

            $slide->delete();

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Slide berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
