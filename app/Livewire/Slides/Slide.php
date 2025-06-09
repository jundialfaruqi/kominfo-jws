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
use Intervention\Image\Laravel\Facades\Image;

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
        'slide1' => 'nullable|image|mimes:jpg,png,jpeg,webp,gif',
        'slide2' => 'nullable|image|mimes:jpg,png,jpeg,webp,gif',
        'slide3' => 'nullable|image|mimes:jpg,png,jpeg,webp,gif',
        'slide4' => 'nullable|image|mimes:jpg,png,jpeg,webp,gif',
        'slide5' => 'nullable|image|mimes:jpg,png,jpeg,webp,gif',
        'slide6' => 'nullable|image|mimes:jpg,png,jpeg,webp,gif',
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
        'slide1.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif',
        'slide2.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif',
        'slide3.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif',
        'slide4.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif',
        'slide5.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif',
        'slide6.mimes'    => 'File harus berupa gambar jpg,png,jpeg,webp,gif',
    ];

    /**
     * Method untuk resize gambar dengan aspect ratio 16:9 dan ukuran maksimal 990KB
     */
    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 990)
    {
        try {
            // Konversi ke bytes
            $maxSizeBytes = $maxSizeKB * 1024;

            // Baca gambar menggunakan Intervention Image
            $image = Image::read($uploadedFile->getRealPath());

            // Target aspect ratio 16:9
            $targetRatio = 16 / 9;
            $targetWidth = 1920;
            $targetHeight = 1080;

            // Dapatkan dimensi asli
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $originalRatio = $originalWidth / $originalHeight;

            // Crop gambar ke aspect ratio 16:9 jika diperlukan
            if (abs($originalRatio - $targetRatio) > 0.01) {
                if ($originalRatio > $targetRatio) {
                    // Gambar terlalu lebar, crop dari kiri-kanan
                    $newWidth = (int)($originalHeight * $targetRatio);
                    $x = (int)(($originalWidth - $newWidth) / 2);
                    $image->crop($newWidth, $originalHeight, $x, 0);
                } else {
                    // Gambar terlalu tinggi, crop dari atas-bawah
                    $newHeight = (int)($originalWidth / $targetRatio);
                    $y = (int)(($originalHeight - $newHeight) / 2);
                    $image->crop($originalWidth, $newHeight, 0, $y);
                }
            }

            // Resize ke dimensi target 1920x1080
            $image->resize($targetWidth, $targetHeight);

            // Mulai dengan kualitas tinggi dan turunkan sampai ukuran sesuai
            $quality = 95;
            $minQuality = 20;

            do {
                // Encode dengan kualitas saat ini
                $encoded = $image->toJpeg($quality);
                $currentSize = strlen($encoded);

                // Jika ukuran sudah sesuai, keluar dari loop
                if ($currentSize <= $maxSizeBytes) {
                    break;
                }

                // Turunkan kualitas secara bertahap
                if ($currentSize > $maxSizeBytes * 1.5) {
                    $quality -= 10; // Penurunan cepat jika masih jauh dari target
                } elseif ($currentSize > $maxSizeBytes * 1.2) {
                    $quality -= 5;  // Penurunan sedang
                } else {
                    $quality -= 2;  // Penurunan halus untuk fine-tuning
                }
            } while ($quality >= $minQuality);

            // Jika masih terlalu besar dengan kualitas minimum, resize lebih kecil
            if (strlen($image->toJpeg($minQuality)) > $maxSizeBytes) {
                $scaleFactor = 0.9;
                while (strlen($image->toJpeg($minQuality)) > $maxSizeBytes && $scaleFactor > 0.5) {
                    $newWidth = (int)($targetWidth * $scaleFactor);
                    $newHeight = (int)($targetHeight * $scaleFactor);
                    $image->resize($newWidth, $newHeight);
                    $scaleFactor -= 0.05;
                }
            }

            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menyimpan gambar yang sudah diproses
     */
    private function saveProcessedImage($uploadedFile, $slideNumber)
    {
        try {
            // Proses resize gambar dengan aspect ratio 16:9 dan ukuran maksimal 990KB
            $processedImage = $this->resizeImageToLimit($uploadedFile);

            // Generate nama file dengan ekstensi .jpg (karena kita convert ke JPEG)
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_slide' . $slideNumber . '_' . $originalName . '.jpg';
            $filePath = public_path('images/slides/' . $fileName);

            // Pastikan directory ada
            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Tentukan kualitas optimal berdasarkan ukuran target
            $maxSizeBytes = 990 * 1024; // 990KB
            $quality = 95;

            // Fine-tune kualitas untuk mendekati 990KB
            do {
                $encoded = $processedImage->toJpeg($quality);
                $currentSize = strlen($encoded);

                if ($currentSize <= $maxSizeBytes) {
                    break;
                }

                $quality -= 1;
            } while ($quality >= 60);

            // Simpan gambar yang sudah diproses dengan kualitas optimal
            $processedImage->toJpeg($quality)->save($filePath);

            // Verifikasi ukuran file hasil akhir
            $finalSize = filesize($filePath);
            if ($finalSize > $maxSizeBytes) {
                throw new \Exception("Ukuran file masih terlalu besar: " . round($finalSize / 1024, 2) . "KB");
            }

            return '/images/slides/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus slide1 dan mereset input file
     */
    public function clearSlide1()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_slide1) {
                $filePath = public_path($this->tmp_slide1);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->slideId) {
                    $slide = Slides::find($this->slideId);
                    if ($slide) {
                        $slide->slide1 = null;
                        $slide->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->slide1 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_slide1 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['slide1']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'slide1']);

            $this->dispatch('success', 'Gambar Slide 1 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus slide2 dan mereset input file
     */
    public function clearSlide2()
    {
        try {
            if ($this->isEdit && $this->tmp_slide2) {
                $filePath = public_path($this->tmp_slide2);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->slideId) {
                    $slide = Slides::find($this->slideId);
                    if ($slide) {
                        $slide->slide2 = null;
                        $slide->save();
                    }
                }
            }

            $this->slide2 = null;
            $this->tmp_slide2 = null;
            $this->resetValidation(['slide2']);
            $this->dispatch('resetFileInput', ['inputName' => 'slide2']);
            $this->dispatch('success', 'Gambar Slide 2 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus slide3 dan mereset input file
     */
    public function clearSlide3()
    {
        try {
            if ($this->isEdit && $this->tmp_slide3) {
                $filePath = public_path($this->tmp_slide3);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->slideId) {
                    $slide = Slides::find($this->slideId);
                    if ($slide) {
                        $slide->slide3 = null;
                        $slide->save();
                    }
                }
            }

            $this->slide3 = null;
            $this->tmp_slide3 = null;
            $this->resetValidation(['slide3']);
            $this->dispatch('resetFileInput', ['inputName' => 'slide3']);
            $this->dispatch('success', 'Gambar Slide 3 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus slide4 dan mereset input file
     */
    public function clearSlide4()
    {
        try {
            if ($this->isEdit && $this->tmp_slide4) {
                $filePath = public_path($this->tmp_slide4);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->slideId) {
                    $slide = Slides::find($this->slideId);
                    if ($slide) {
                        $slide->slide4 = null;
                        $slide->save();
                    }
                }
            }

            $this->slide4 = null;
            $this->tmp_slide4 = null;
            $this->resetValidation(['slide4']);
            $this->dispatch('resetFileInput', ['inputName' => 'slide4']);
            $this->dispatch('success', 'Gambar Slide 4 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus slide5 dan mereset input file
     */
    public function clearSlide5()
    {
        try {
            if ($this->isEdit && $this->tmp_slide5) {
                $filePath = public_path($this->tmp_slide5);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->slideId) {
                    $slide = Slides::find($this->slideId);
                    if ($slide) {
                        $slide->slide5 = null;
                        $slide->save();
                    }
                }
            }

            $this->slide5 = null;
            $this->tmp_slide5 = null;
            $this->resetValidation(['slide5']);
            $this->dispatch('resetFileInput', ['inputName' => 'slide5']);
            $this->dispatch('success', 'Gambar Slide 5 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus slide6 dan mereset input file
     */
    public function clearSlide6()
    {
        try {
            if ($this->isEdit && $this->tmp_slide6) {
                $filePath = public_path($this->tmp_slide6);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->slideId) {
                    $slide = Slides::find($this->slideId);
                    if ($slide) {
                        $slide->slide6 = null;
                        $slide->save();
                    }
                }
            }

            $this->slide6 = null;
            $this->tmp_slide6 = null;
            $this->resetValidation(['slide6']);
            $this->dispatch('resetFileInput', ['inputName' => 'slide6']);
            $this->dispatch('success', 'Gambar Slide 6 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    // Method mount, render, dan method lainnya tetap sama seperti sebelumnya...
    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // If user is not admin
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
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
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);

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
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
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
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $slide->user_id) {
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
        if (!in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            $this->userId = $currentUser->id;
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                $slide = Slides::findOrFail($this->slideId);
                // Check if user has permission to edit this slide
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $slide->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit slide ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own slide
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat slide untuk user lain!');
                    return;
                }
                $slide = new Slides();
            }

            $slide->user_id = $this->userId;

            // Handle slide1 upload dengan resize otomatis
            if ($this->slide1) {
                // Delete old slide1 if exists
                if ($this->isEdit && $slide->slide1 && file_exists(public_path($slide->slide1))) {
                    File::delete(public_path($slide->slide1));
                }
                // Save new slide1 dengan resize otomatis
                $slide->slide1 = $this->saveProcessedImage($this->slide1, 1);
            } else {
                // Jika tidak ada file baru, gunakan nilai dari tmp_slide1
                // Jika tmp_slide1 null (dihapus via trash), maka slide1 akan jadi null
                $slide->slide1 = $this->tmp_slide1;
            }

            // Handle slide2 upload dengan resize otomatis
            if ($this->slide2) {
                // Delete old slide2 if exists
                if ($this->isEdit && $slide->slide2 && file_exists(public_path($slide->slide2))) {
                    File::delete(public_path($slide->slide2));
                }
                // Save new slide2 dengan resize otomatis
                $slide->slide2 = $this->saveProcessedImage($this->slide2, 2);
            } else {
                $slide->slide2 = $this->tmp_slide2;
            }

            // Handle slide3 upload dengan resize otomatis
            if ($this->slide3) {
                // Delete old slide3 if exists
                if ($this->isEdit && $slide->slide3 && file_exists(public_path($slide->slide3))) {
                    File::delete(public_path($slide->slide3));
                }
                // Save new slide3 dengan resize otomatis
                $slide->slide3 = $this->saveProcessedImage($this->slide3, 3);
            } else {
                $slide->slide3 = $this->tmp_slide3;
            }

            // Handle slide4 upload dengan resize otomatis
            if ($this->slide4) {
                // Delete old slide4 if exists
                if ($this->isEdit && $slide->slide4 && file_exists(public_path($slide->slide4))) {
                    File::delete(public_path($slide->slide4));
                }
                // Save new slide4 dengan resize otomatis
                $slide->slide4 = $this->saveProcessedImage($this->slide4, 4);
            } else {
                $slide->slide4 = $this->tmp_slide4;
            }

            // Handle slide5 upload dengan resize otomatis
            if ($this->slide5) {
                // Delete old slide5 if exists
                if ($this->isEdit && $slide->slide5 && file_exists(public_path($slide->slide5))) {
                    File::delete(public_path($slide->slide5));
                }
                // Save new slide5 dengan resize otomatis
                $slide->slide5 = $this->saveProcessedImage($this->slide5, 5);
            } else {
                $slide->slide5 = $this->tmp_slide5;
            }

            // Handle slide6 upload dengan resize otomatis
            if ($this->slide6) {
                // Delete old slide6 if exists
                if ($this->isEdit && $slide->slide6 && file_exists(public_path($slide->slide6))) {
                    File::delete(public_path($slide->slide6));
                }
                // Save new slide6 dengan resize otomatis
                $slide->slide6 = $this->saveProcessedImage($this->slide6, 6);
            } else {
                $slide->slide6 = $this->tmp_slide6;
            }

            $slide->save();

            $this->dispatch('success', $this->isEdit ? 'Gambar Slide berhasil diubah!' : 'Gambar Slide berhasil ditambahkan!');

            // Only hide form and reset fields if user is not admin
            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
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
