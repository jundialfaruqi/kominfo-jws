<?php

namespace App\Livewire\Jumbotron;

use App\Models\Jumbotron as ModelsJumbotron;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;

class Jumbotron extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Jumbotron')]

    public $search;
    public $paginate;
    protected $paginationTheme = 'bootstrap';

    public $jumboId;
    public $jumbo1;
    public $tmp_jumbo1;
    public $jumbo2;
    public $tmp_jumbo2;
    public $jumbo3;
    public $tmp_jumbo3;
    public $jumbo4;
    public $tmp_jumbo4;
    public $jumbo5;
    public $tmp_jumbo5;
    public $jumbo6;
    public $tmp_jumbo6;
    public $is_active = true;

    public $isEdit = false;
    public $showForm = false;
    public $deleteJumboId;
    public $deleteJumboName;

    protected $rules = [
        'jumbo1' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbo2' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbo3' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbo4' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbo5' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbo6' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'jumbo1.image' => 'File harus berupa gambar',
        'jumbo2.image' => 'File harus berupa gambar',
        'jumbo3.image' => 'File harus berupa gambar',
        'jumbo4.image' => 'File harus berupa gambar',
        'jumbo5.image' => 'File harus berupa gambar',
        'jumbo6.image' => 'File harus berupa gambar',
        'jumbo1.mimes' => 'File harus berupa gambar jpg,png,jpeg,webp',
        'jumbo2.mimes' => 'File harus berupa gambar jpg,png,jpeg,webp',
        'jumbo3.mimes' => 'File harus berupa gambar jpg,png,jpeg,webp',
        'jumbo4.mimes' => 'File harus berupa gambar jpg,png,jpeg,webp',
        'jumbo5.mimes' => 'File harus berupa gambar jpg,png,jpeg,webp',
        'jumbo6.mimes' => 'File harus berupa gambar jpg,png,jpeg,webp',
        'jumbo1.max'   => 'Ukuran file gambar tidak boleh lebih dari 1000 KB',
        'jumbo2.max'   => 'Ukuran file gambar tidak boleh lebih dari 1000 KB',
        'jumbo3.max'   => 'Ukuran file gambar tidak boleh lebih dari 1000 KB',
        'jumbo4.max'   => 'Ukuran file gambar tidak boleh lebih dari 1000 KB',
        'jumbo5.max'   => 'Ukuran file gambar tidak boleh lebih dari 1000 KB',
        'jumbo6.max'   => 'Ukuran file gambar tidak boleh lebih dari 1000 KB',
    ];

    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 990)
    {
        try {
            $maxSizeBytes = $maxSizeKB * 1024;
            $image = Image::read($uploadedFile->getRealPath());
            $targetRatio = 16 / 9;
            $targetWidth = 1920;
            $targetHeight = 1080;

            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $originalRatio = $originalWidth / $originalHeight;

            if (abs($originalRatio - $targetRatio) > 0.01) {
                if ($originalRatio > $targetRatio) {
                    $newWidth = (int)($originalHeight * $targetRatio);
                    $x = (int)(($originalWidth - $newWidth) / 2);
                    $image->crop($newWidth, $originalHeight, $x, 0);
                } else {
                    $newHeight = (int)($originalWidth / $targetRatio);
                    $y = (int)(($originalHeight - $newHeight) / 2);
                    $image->crop($originalWidth, $newHeight, 0, $y);
                }
            }

            $image->resize($targetWidth, $targetHeight);
            $quality = 95;
            $minQuality = 20;

            do {
                $encoded = $image->toJpeg($quality);
                $currentSize = strlen($encoded);

                if ($currentSize <= $maxSizeBytes) {
                    break;
                }

                if ($currentSize > $maxSizeBytes * 1.5) {
                    $quality -= 10;
                } elseif ($currentSize > $maxSizeBytes * 1.2) {
                    $quality -= 5;
                } else {
                    $quality -= 2;
                }
            } while ($quality >= $minQuality);

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

    private function saveProcessedImage($uploadedFile, $jumboNumber)
    {
        try {
            $processedImage = $this->resizeImageToLimit($uploadedFile);
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_jumbo' . $jumboNumber . '_' . $originalName . '.jpg';
            $filePath = public_path('images/jumbotrons/' . $fileName);

            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $maxSizeBytes = 990 * 1024;
            $quality = 95;

            do {
                $encoded = $processedImage->toJpeg($quality);
                $currentSize = strlen($encoded);

                if ($currentSize <= $maxSizeBytes) {
                    break;
                }

                $quality -= 1;
            } while ($quality >= 60);

            $processedImage->toJpeg($quality)->save($filePath);

            $finalSize = filesize($filePath);
            if ($finalSize > $maxSizeBytes) {
                throw new \Exception("Ukuran file masih terlalu besar: " . round($finalSize / 1024, 2) . "KB");
            }

            return '/images/jumbotrons/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    public function clearJumbo1()
    {
        try {
            if ($this->isEdit && $this->tmp_jumbo1) {
                $filePath = public_path($this->tmp_jumbo1);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->jumboId) {
                    $jumbo = ModelsJumbotron::find($this->jumboId);
                    if ($jumbo) {
                        $jumbo->jumbo1 = null;
                        $jumbo->save();
                    }
                }
            }

            $this->jumbo1 = null;
            $this->tmp_jumbo1 = null;
            $this->resetValidation(['jumbo1']);
            $this->dispatch('resetFileInput', ['inputName' => 'jumbo1']);
            $this->dispatch('success', 'Gambar Jumbotron 1 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearJumbo2()
    {
        try {
            if ($this->isEdit && $this->tmp_jumbo2) {
                $filePath = public_path($this->tmp_jumbo2);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->jumboId) {
                    $jumbo = ModelsJumbotron::find($this->jumboId);
                    if ($jumbo) {
                        $jumbo->jumbo2 = null;
                        $jumbo->save();
                    }
                }
            }

            $this->jumbo2 = null;
            $this->tmp_jumbo2 = null;
            $this->resetValidation(['jumbo2']);
            $this->dispatch('resetFileInput', ['inputName' => 'jumbo2']);
            $this->dispatch('success', 'Gambar Jumbotron 2 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearJumbo3()
    {
        try {
            if ($this->isEdit && $this->tmp_jumbo3) {
                $filePath = public_path($this->tmp_jumbo3);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->jumboId) {
                    $jumbo = ModelsJumbotron::find($this->jumboId);
                    if ($jumbo) {
                        $jumbo->jumbo3 = null;
                        $jumbo->save();
                    }
                }
            }

            $this->jumbo3 = null;
            $this->tmp_jumbo3 = null;
            $this->resetValidation(['jumbo3']);
            $this->dispatch('resetFileInput', ['inputName' => 'jumbo3']);
            $this->dispatch('success', 'Gambar Jumbotron 3 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearJumbo4()
    {
        try {
            if ($this->isEdit && $this->tmp_jumbo4) {
                $filePath = public_path($this->tmp_jumbo4);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->jumboId) {
                    $jumbo = ModelsJumbotron::find($this->jumboId);
                    if ($jumbo) {
                        $jumbo->jumbo4 = null;
                        $jumbo->save();
                    }
                }
            }

            $this->jumbo4 = null;
            $this->tmp_jumbo4 = null;
            $this->resetValidation(['jumbo4']);
            $this->dispatch('resetFileInput', ['inputName' => 'jumbo4']);
            $this->dispatch('success', 'Gambar Jumbotron 4 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearJumbo5()
    {
        try {
            if ($this->isEdit && $this->tmp_jumbo5) {
                $filePath = public_path($this->tmp_jumbo5);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->jumboId) {
                    $jumbo = ModelsJumbotron::find($this->jumboId);
                    if ($jumbo) {
                        $jumbo->jumbo5 = null;
                        $jumbo->save();
                    }
                }
            }

            $this->jumbo5 = null;
            $this->tmp_jumbo5 = null;
            $this->resetValidation(['jumbo5']);
            $this->dispatch('resetFileInput', ['inputName' => 'jumbo5']);
            $this->dispatch('success', 'Gambar Jumbotron 5 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearJumbo6()
    {
        try {
            if ($this->isEdit && $this->tmp_jumbo6) {
                $filePath = public_path($this->tmp_jumbo6);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->jumboId) {
                    $jumbo = ModelsJumbotron::find($this->jumboId);
                    if ($jumbo) {
                        $jumbo->jumbo6 = null;
                        $jumbo->save();
                    }
                }
            }

            $this->jumbo6 = null;
            $this->tmp_jumbo6 = null;
            $this->resetValidation(['jumbo6']);
            $this->dispatch('resetFileInput', ['inputName' => 'jumbo6']);
            $this->dispatch('success', 'Gambar Jumbotron 6 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // Check access using both role and permission
        $this->checkAccess();
    }

    /**
     * Check if user has access to jumbotron
     */
    private function checkAccess()
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized - Not authenticated');
        }

        // Check legacy role-based access OR Spatie permission (salah satu saja)
        $hasRoleAccess = in_array(Auth::user()->role, ['Super Admin', 'Admin']);
        $hasPermission = Auth::user()->can('view-jumbotron');

        // User hanya perlu memiliki SALAH SATU akses (role ATAU permission)
        if (!$hasRoleAccess && !$hasPermission) {
            abort(403, 'Unauthorized - Anda tidak memiliki akses ke halaman jumbotron');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->showForm = false;
        $this->resetValidation();
        $this->reset([
            'jumboId',
            'jumbo1',
            'tmp_jumbo1',
            'jumbo2',
            'tmp_jumbo2',
            'jumbo3',
            'tmp_jumbo3',
            'jumbo4',
            'tmp_jumbo4',
            'jumbo5',
            'tmp_jumbo5',
            'jumbo6',
            'tmp_jumbo6',
            'is_active',
        ]);
    }

    public function render()
    {
        $query = ModelsJumbotron::with('user')
            ->select('id', 'user_id', 'jumbo1', 'jumbo2', 'jumbo3', 'jumbo4', 'jumbo5', 'jumbo6', 'is_active')
            ->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });

        $jumboList = $query->orderBy('id', 'asc')->paginate($this->paginate);

        return view('livewire.jumbotron.jumbotron', [
            'jumboList' => $jumboList,
        ]);
    }

    public function showAddForm()
    {
        $this->resetValidation();
        $this->reset([
            'jumboId',
            'jumbo1',
            'tmp_jumbo1',
            'jumbo2',
            'tmp_jumbo2',
            'jumbo3',
            'tmp_jumbo3',
            'jumbo4',
            'tmp_jumbo4',
            'jumbo5',
            'tmp_jumbo5',
            'jumbo6',
            'tmp_jumbo6',
            'is_active',
        ]);

        $this->isEdit = false;
        $this->showForm = true;
        $this->is_active = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $jumbo = ModelsJumbotron::findOrFail($id);

        $this->jumboId = $jumbo->id;
        $this->tmp_jumbo1 = $jumbo->jumbo1;
        $this->tmp_jumbo2 = $jumbo->jumbo2;
        $this->tmp_jumbo3 = $jumbo->jumbo3;
        $this->tmp_jumbo4 = $jumbo->jumbo4;
        $this->tmp_jumbo5 = $jumbo->jumbo5;
        $this->tmp_jumbo6 = $jumbo->jumbo6;
        $this->is_active = (bool) $jumbo->is_active;

        $this->isEdit = true;
        $this->showForm = true;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->reset([
            'jumboId',
            'jumbo1',
            'tmp_jumbo1',
            'jumbo2',
            'tmp_jumbo2',
            'jumbo3',
            'tmp_jumbo3',
            'jumbo4',
            'tmp_jumbo4',
            'jumbo5',
            'tmp_jumbo5',
            'jumbo6',
            'tmp_jumbo6',
            'is_active',
        ]);
    }

    public function save()
    {
        $this->validate();
        $currentUser = Auth::user();

        try {
            if ($this->isEdit) {
                $jumbo = ModelsJumbotron::findOrFail($this->jumboId);
            } else {
                $jumbo = new ModelsJumbotron();
                $jumbo->user_id = $currentUser->id;
            }

            if ($this->jumbo1) {
                if ($this->isEdit && $jumbo->jumbo1 && file_exists(public_path($jumbo->jumbo1))) {
                    File::delete(public_path($jumbo->jumbo1));
                }
                $jumbo->jumbo1 = $this->saveProcessedImage($this->jumbo1, 1);
            } else {
                $jumbo->jumbo1 = $this->tmp_jumbo1;
            }

            if ($this->jumbo2) {
                if ($this->isEdit && $jumbo->jumbo2 && file_exists(public_path($jumbo->jumbo2))) {
                    File::delete(public_path($jumbo->jumbo2));
                }
                $jumbo->jumbo2 = $this->saveProcessedImage($this->jumbo2, 2);
            } else {
                $jumbo->jumbo2 = $this->tmp_jumbo2;
            }

            if ($this->jumbo3) {
                if ($this->isEdit && $jumbo->jumbo3 && file_exists(public_path($jumbo->jumbo3))) {
                    File::delete(public_path($jumbo->jumbo3));
                }
                $jumbo->jumbo3 = $this->saveProcessedImage($this->jumbo3, 3);
            } else {
                $jumbo->jumbo3 = $this->tmp_jumbo3;
            }

            if ($this->jumbo4) {
                if ($this->isEdit && $jumbo->jumbo4 && file_exists(public_path($jumbo->jumbo4))) {
                    File::delete(public_path($jumbo->jumbo4));
                }
                $jumbo->jumbo4 = $this->saveProcessedImage($this->jumbo4, 4);
            } else {
                $jumbo->jumbo4 = $this->tmp_jumbo4;
            }

            if ($this->jumbo5) {
                if ($this->isEdit && $jumbo->jumbo5 && file_exists(public_path($jumbo->jumbo5))) {
                    File::delete(public_path($jumbo->jumbo5));
                }
                $jumbo->jumbo5 = $this->saveProcessedImage($this->jumbo5, 5);
            } else {
                $jumbo->jumbo5 = $this->tmp_jumbo5;
            }

            if ($this->jumbo6) {
                if ($this->isEdit && $jumbo->jumbo6 && file_exists(public_path($jumbo->jumbo6))) {
                    File::delete(public_path($jumbo->jumbo6));
                }
                $jumbo->jumbo6 = $this->saveProcessedImage($this->jumbo6, 6);
            } else {
                $jumbo->jumbo6 = $this->tmp_jumbo6;
            }

            $jumbo->is_active = $this->is_active;
            $jumbo->save();

            $this->dispatch('success', $this->isEdit ? 'Jumbotron berhasil diubah!' : 'Jumbotron berhasil ditambahkan!');
            $this->showForm = false;
            $this->reset([
                'jumboId',
                'jumbo1',
                'tmp_jumbo1',
                'jumbo2',
                'tmp_jumbo2',
                'jumbo3',
                'tmp_jumbo3',
                'jumbo4',
                'tmp_jumbo4',
                'jumbo5',
                'tmp_jumbo5',
                'jumbo6',
                'tmp_jumbo6',
                'is_active',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan jumbotron: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;
        $jumbo = ModelsJumbotron::findOrFail($id);
        $this->deleteJumboId = $jumbo->id;
        $this->deleteJumboName = $jumbo->user->name;
    }

    public function destroyJumbo()
    {
        try {
            $jumbo = ModelsJumbotron::findOrFail($this->deleteJumboId);

            foreach (['jumbo1', 'jumbo2', 'jumbo3', 'jumbo4', 'jumbo5', 'jumbo6'] as $field) {
                if ($jumbo->$field && file_exists(public_path($jumbo->$field))) {
                    File::delete(public_path($jumbo->$field));
                }
            }

            $jumbo->delete();

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Jumbotron berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
