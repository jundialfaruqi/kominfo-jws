<?php

namespace App\Livewire\Tema;

use App\Models\Theme;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Tema extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Pengaturan Tema')]

    public $search;
    public $paginate = 10;
    protected $paginationTheme = 'bootstrap';

    public $themeId;
    public $name;
    public $preview_image;
    public $temp_preview_image;
    public $css_file;
    public $temp_css_file;
    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteThemeId;
    public $deleteThemeName;
    public $selectedThemeId;
    public $initialThemeId;

    protected $rules = [
        'name' => 'required|string|max:255',
        'preview_image' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'css_file' => 'nullable|file|max:1000|mimes:css',
    ];

    protected $messages = [
        'name.required' => 'Nama tema wajib diisi.',
        'name.max' => 'Nama tema maksimal 255 karakter.',
        'preview_image.image' => 'File harus berupa gambar.',
        'preview_image.mimes' => 'Format file gambar tidak valid (jpg, jpeg, png, webp, gif).',
        'preview_image.max' => 'Ukuran gambar maksimal 1MB.',
        'css_file.mimes' => 'File harus berupa file CSS.',
        'css_file.max' => 'Ukuran file CSS maksimal 1MB.',
    ];

    public function mount()
    {
        $this->search = '';
        $this->showForm = false;
        $this->selectedThemeId = Auth::check() ? Auth::user()->theme_id : null; // Ambil preferensi tema dari user
        $this->initialThemeId = Auth::check() ? Auth::user()->theme_id : null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->showForm = false;
        $this->resetValidation();
        $this->reset([
            'themeId',
            'name',
            'preview_image',
            'temp_preview_image',
            'css_file',
            'temp_css_file',
        ]);
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        $query = Theme::select('id', 'name', 'preview_image', 'css_file');

        if ($isAdmin) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $themeList = $query->orderBy('name', 'asc')->paginate($this->paginate);
        $availableThemes = Theme::select('id', 'name', 'preview_image')->orderBy('name', 'asc')->get();

        return view('livewire.tema.tema', [
            'themeList' => $themeList,
            'availableThemes' => $availableThemes,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function selectTempTheme($themeId)
    {
        $this->selectedThemeId = $themeId; // Ubah selectedThemeId sementara
    }

    public function cancelSelection()
    {
        $this->selectedThemeId = $this->initialThemeId; // Kembalikan ke tema awal
    }

    public function showAddForm()
    {
        if (!in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah tema!');
            return;
        }

        $this->resetValidation();
        $this->reset([
            'themeId',
            'name',
            'preview_image',
            'temp_preview_image',
            'css_file',
            'temp_css_file',
        ]);
        $this->isEdit = false;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function edit($id)
    {
        if (!in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit tema!');
            return;
        }

        $this->resetValidation();

        $theme = Theme::findOrFail($id);

        $this->themeId = $theme->id;
        $this->name = $theme->name;
        $this->temp_preview_image = $theme->preview_image;
        $this->temp_css_file = $theme->css_file;

        $this->isEdit = true;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->showTable = true;
        $this->resetValidation();
        $this->reset([
            'themeId',
            'name',
            'preview_image',
            'temp_preview_image',
            'css_file',
            'temp_css_file',
        ]);
    }

    private function saveProcessedImage($uploadedFile)
    {
        try {
            $image = Image::read($uploadedFile->getRealPath());

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_preview_' . $originalName . '.webp';
            $filePath = public_path('images/themes/' . $fileName);

            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $image->toWebp(85)->save($filePath);

            return '/images/themes/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    private function saveCssFile($uploadedFile)
    {
        try {
            $tempPath = $uploadedFile->getRealPath();
            Log::info('Temporary file path:', [$tempPath]);

            if (!file_exists($tempPath)) {
                throw new \Exception('File sementara tidak ditemukan di: ' . $tempPath);
            }

            $content = file_get_contents($tempPath);
            Log::info('File content read:', [strlen($content) . ' bytes']);

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_css_' . $originalName . '.css';

            Log::info('Saving to public_css disk with filename:', [$fileName]);
            $path = Storage::disk('public_css')->putFileAs('', $uploadedFile, $fileName);

            if (!$path) {
                throw new \Exception('Gagal menyimpan file ke disk public_css.');
            }

            Log::info('File saved successfully:', [$path]);
            return '/css/themes/' . $fileName;
        } catch (\Exception $e) {
            Log::error('CSS upload failed:', [$e->getMessage()]);
            throw new \Exception('Gagal menyimpan file CSS: ' . $e->getMessage());
        }
    }

    public function clearPreviewImage()
    {
        try {
            if ($this->isEdit && $this->temp_preview_image) {
                $filePath = public_path($this->temp_preview_image);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->themeId) {
                    $theme = Theme::find($this->themeId);
                    if ($theme) {
                        $theme->preview_image = null;
                        $theme->save();
                    }
                }
            }

            $this->preview_image = null;
            $this->temp_preview_image = null;
            $this->resetValidation(['preview_image']);
            $this->dispatch('resetFileInput', ['inputName' => 'preview_image']);

            $this->dispatch('success', 'Gambar pratinjau berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearCssFile()
    {
        try {
            if ($this->isEdit && $this->temp_css_file) {
                $filePath = public_path($this->temp_css_file);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                if ($this->themeId) {
                    $theme = Theme::find($this->themeId);
                    if ($theme) {
                        $theme->css_file = null;
                        $theme->save();
                    }
                }
            }

            $this->css_file = null;
            $this->temp_css_file = null;
            $this->resetValidation(['css_file']);
            $this->dispatch('resetFileInput', ['inputName' => 'css_file']);

            $this->dispatch('success', 'File CSS berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus file CSS: ' . $e->getMessage());
        }
    }

    public function save()
    {
        if (!in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menyimpan tema!');
            return;
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                $theme = Theme::findOrFail($this->themeId);
            } else {
                $theme = new Theme();
            }

            $theme->name = $this->name;

            if ($this->preview_image) {
                if ($this->isEdit && $theme->preview_image && file_exists(public_path($theme->preview_image))) {
                    File::delete(public_path($theme->preview_image));
                }
                $theme->preview_image = $this->saveProcessedImage($this->preview_image);
            } else {
                $theme->preview_image = $this->temp_preview_image;
            }

            if ($this->css_file) {
                if ($this->isEdit && $theme->css_file && file_exists(public_path($theme->css_file))) {
                    File::delete(public_path($theme->css_file));
                }
                $theme->css_file = $this->saveCssFile($this->css_file);
            } else {
                $theme->css_file = $this->temp_css_file;
            }

            $theme->save();

            $this->dispatch('success', $this->isEdit ? 'Tema berhasil diperbarui!' : 'Tema berhasil ditambahkan!');
            $this->showTable = true;

            $this->showForm = false;
            $this->reset([
                'themeId',
                'name',
                'preview_image',
                'temp_preview_image',
                'css_file',
                'temp_css_file',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function selectTheme()
    {
        try {
            $currentUser = Auth::user();
            $theme = Theme::find($this->selectedThemeId);

            if ($theme) {
                $currentUser->theme_id = $theme->id; // Simpan preferensi tema di tabel users
                $currentUser->save();
                $this->dispatch('success', 'Tema berhasil dipilih!');
            } else {
                $currentUser->theme_id = null;
                $currentUser->save();
                $this->dispatch('success', 'Tema dihapus!');
            }

            $this->selectedThemeId = $currentUser->theme_id;
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (Auth::user()->role !== 'Super Admin') {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menghapus tema!');
            return;
        }

        $this->showForm = false;

        $theme = Theme::findOrFail($id);
        $this->deleteThemeId = $theme->id;
        $this->deleteThemeName = $theme->name;
    }

    public function destroyTheme()
    {
        if (Auth::user()->role !== 'Super Admin') {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menghapus tema!');
            return;
        }

        try {
            $theme = Theme::findOrFail($this->deleteThemeId);

            if ($theme->preview_image && file_exists(public_path($theme->preview_image))) {
                File::delete(public_path($theme->preview_image));
            }

            if ($theme->css_file && file_exists(public_path($theme->css_file))) {
                File::delete(public_path($theme->css_file));
            }

            $theme->delete();

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Tema berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadCssFile($id)
    {
        try {
            if (!in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
                $this->dispatch('error', 'Anda tidak memiliki akses untuk mengunduh file CSS!');
                return;
            }

            $theme = Theme::findOrFail($id);
            if (!$theme->css_file || !file_exists(public_path($theme->css_file))) {
                $this->dispatch('error', 'File CSS tidak ditemukan!');
                return;
            }

            return response()->download(public_path($theme->css_file), basename($theme->css_file));
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat mengunduh file CSS: ' . $e->getMessage());
        }
    }
}
