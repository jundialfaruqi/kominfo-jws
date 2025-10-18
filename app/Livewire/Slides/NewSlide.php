<?php

namespace App\Livewire\Slides;

use App\Models\NewSlider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class NewSlide extends Component
{
    use WithPagination, WithFileUploads;
    #[Title('New Slider')]

    // jumlah slide
    public Int $sliderFormCount, $maxAllowedSlides = 15;
    public array $slideImages = [];
    public array $existingSlides = [];

    protected $rules = [
        'slideImages.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
    ];

    protected $messages = [
        'slideImages.*.image' => 'File harus berupa gambar.',
        'slideImages.*.mimes' => 'Format gambar tidak valid. Gunakan JPG, JPEG, PNG, atau WEBP.',
        'slideImages.*.max' => 'Ukuran gambar maksimal 5MB.',
    ];

    public function mount()
    {
        $this->initializeSlides();
    }

    public function render()
    {
        return view('livewire.slides.new-slide');
    }

    private function initializeSlides(): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->existingSlides = [];
            $this->sliderFormCount = 1;
            $this->slideImages = array_fill(1, $this->sliderFormCount, null);

            return;
        }

        $slides = $user->newSliders()->orderBy('created_at')->get();

        $this->existingSlides = $slides->values()
            ->mapWithKeys(fn($slide, $index) => [
                $index + 1 => [
                    'id' => $slide->id,
                    'path' => $slide->path,
                ],
            ])
            ->toArray();

        $this->sliderFormCount = max(count($this->existingSlides), 1);
        $this->slideImages = array_fill(1, $this->sliderFormCount, null);
    }

    public function addSliderForm()
    {
        if ($this->sliderFormCount < $this->maxAllowedSlides) {
            $this->sliderFormCount += 1;
        } else {
            $this->dispatch('error', 'form tidak boleh melebihi ' . $this->maxAllowedSlides);
            return;
        }

        $this->slideImages[$this->sliderFormCount] = null;
        if (!array_key_exists($this->sliderFormCount, $this->existingSlides)) {
            $this->existingSlides[$this->sliderFormCount] = null;
        }
        $this->dispatch('slider-form-added');
    }

    public function saveSlide()
    {
        $this->validate();

        $files = collect($this->slideImages)
            ->filter(fn($file) => $file instanceof TemporaryUploadedFile);

        if ($files->isEmpty()) {
            $this->addError('slideImages', 'Minimal satu gambar perlu diunggah.');
            return;
        }

        $user = Auth::user();
        $profil = $user?->profil;

        if (!$profil) {
            $this->dispatch('error', 'Profil masjid belum tersedia. Silakan lengkapi data profil terlebih dahulu.');
            return;
        }

        try {
            DB::transaction(function () use ($files, $profil, $user) {
                foreach ($files as $file) {
                    $storedPath = $file->store('new-sliders/' . $profil->id, 'public');

                    NewSlider::create([
                        'masjid_id' => $profil->id,
                        'path' => 'storage/' . $storedPath,
                        'uploaded_by' => $user?->id,
                    ]);
                }
            });

            $this->dispatch('success', 'Slide baru berhasil disimpan.');
            $this->initializeSlides();
        } catch (\Throwable $th) {
            report($th);
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan slide. Silakan coba lagi.');
        }
    }

    public function removeSlideImage(int $index): void
    {
        $temporaryFile = $this->slideImages[$index] ?? null;

        if ($temporaryFile instanceof TemporaryUploadedFile) {
            $this->slideImages[$index] = null;
            return;
        }

        $existing = $this->existingSlides[$index] ?? null;

        if (!is_array($existing) || empty($existing['id'])) {
            $this->dispatch('error', 'Tidak ada gambar untuk dihapus pada slot ini.');
            return;
        }

        $slide = NewSlider::find($existing['id']);

        if (!$slide) {
            $this->dispatch('error', 'Slide tidak ditemukan.');
            $this->initializeSlides();
            return;
        }

        if ($slide->path) {
            $diskPath = str_starts_with($slide->path, 'storage/') ? substr($slide->path, 8) : $slide->path;
            Storage::disk('public')->delete($diskPath);
        }

        $slide->delete();

        $this->dispatch('success', 'Gambar slide berhasil dihapus.');
        $this->initializeSlides();
    }
}
