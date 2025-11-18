<?php

namespace App\Livewire\JumbotronMasjid;

use Livewire\Component;
use Livewire\Attributes\Title;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\WithFileUploads;
use App\Models\JumbotronMasjid as ModelsJumbotronMasjid;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;
use App\Events\ContentUpdatedEvent;

#[Title('Edit Jumbotron Masjid')]
class EditJumbotronMasjid extends Component
{
    use WithFileUploads;
    public $jumbotronMasjidId;
    public $jumbotron_masjid_1;
    public $tmp_jumbotron_masjid_1;
    public $jumbotron_masjid_2;
    public $tmp_jumbotron_masjid_2;
    public $jumbotron_masjid_3;
    public $tmp_jumbotron_masjid_3;
    public $jumbotron_masjid_4;
    public $tmp_jumbotron_masjid_4;
    public $jumbotron_masjid_5;
    public $tmp_jumbotron_masjid_5;
    public $jumbotron_masjid_6;
    public $tmp_jumbotron_masjid_6;
    public $is_active = false;

    protected $rules = [
        'jumbotron_masjid_1' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbotron_masjid_2' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbotron_masjid_3' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbotron_masjid_4' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbotron_masjid_5' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'jumbotron_masjid_6' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:1000',
        'is_active' => 'required|boolean'
    ];

    protected $messages = [
        'jumbotron_masjid_1.image'  => 'Jumbotron Masjid 1 harus berupa gambar',
        'jumbotron_masjid_1.max'    => 'Jumbotron Masjid 1 maksimal 1000KB',
        'jumbotron_masjid_1.mimes'  => 'Jumbotron Masjid 1 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_2.image'  => 'Jumbotron Masjid 2 harus berupa gambar',
        'jumbotron_masjid_2.max'    => 'Jumbotron Masjid 2 maksimal 1000KB',
        'jumbotron_masjid_2.mimes'  => 'Jumbotron Masjid 2 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_3.image'  => 'Jumbotron Masjid 3 harus berupa gambar',
        'jumbotron_masjid_3.max'    => 'Jumbotron Masjid 3 maksimal 1000KB',
        'jumbotron_masjid_3.mimes'  => 'Jumbotron Masjid 3 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_4.image'  => 'Jumbotron Masjid 4 harus berupa gambar',
        'jumbotron_masjid_4.max'    => 'Jumbotron Masjid 4 maksimal 1000KB',
        'jumbotron_masjid_4.mimes'  => 'Jumbotron Masjid 4 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_5.image'  => 'Jumbotron Masjid 5 harus berupa gambar',
        'jumbotron_masjid_5.max'    => 'Jumbotron Masjid 5 maksimal 1000KB',
        'jumbotron_masjid_5.mimes'  => 'Jumbotron Masjid 5 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_6.image'  => 'Jumbotron Masjid 6 harus berupa gambar',
        'jumbotron_masjid_6.max'    => 'Jumbotron Masjid 6 maksimal 1000KB',
        'jumbotron_masjid_6.mimes'  => 'Jumbotron Masjid 6 harus berupa gambar jpg, png, jpeg, webp',
    ];

    public function mount()
    {
        $profil = Profil::where('user_id', Auth::id())->first();
        if (!$profil) {
            $this->dispatch('error', 'Profil masjid untuk user ini tidak ditemukan');
            return;
        }
        $existing = ModelsJumbotronMasjid::where('masjid_id', $profil->id)->where('created_by', Auth::id())->first();
        if ($existing) {
            $this->jumbotronMasjidId = $existing->id;
            $this->tmp_jumbotron_masjid_1 = $existing->jumbotron_masjid_1;
            $this->tmp_jumbotron_masjid_2 = $existing->jumbotron_masjid_2;
            $this->tmp_jumbotron_masjid_3 = $existing->jumbotron_masjid_3;
            $this->tmp_jumbotron_masjid_4 = $existing->jumbotron_masjid_4;
            $this->tmp_jumbotron_masjid_5 = $existing->jumbotron_masjid_5;
            $this->tmp_jumbotron_masjid_6 = $existing->jumbotron_masjid_6;
            $this->is_active = (bool) $existing->aktif;
        }
    }

    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 990)
    {
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
    }

    private function saveProcessedImage($uploadedFile, $jumboNumber)
    {
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
    }

    public function clearJumbotronMasjid1()
    {
        $this->jumbotron_masjid_1 = null;
        $this->tmp_jumbotron_masjid_1 = null;
    }
    public function clearJumbotronMasjid2()
    {
        $this->jumbotron_masjid_2 = null;
        $this->tmp_jumbotron_masjid_2 = null;
    }
    public function clearJumbotronMasjid3()
    {
        $this->jumbotron_masjid_3 = null;
        $this->tmp_jumbotron_masjid_3 = null;
    }
    public function clearJumbotronMasjid4()
    {
        $this->jumbotron_masjid_4 = null;
        $this->tmp_jumbotron_masjid_4 = null;
    }
    public function clearJumbotronMasjid5()
    {
        $this->jumbotron_masjid_5 = null;
        $this->tmp_jumbotron_masjid_5 = null;
    }
    public function clearJumbotronMasjid6()
    {
        $this->jumbotron_masjid_6 = null;
        $this->tmp_jumbotron_masjid_6 = null;
    }

    public function cancelForm()
    {
        $this->reset([
            'jumbotronMasjidId',
            'jumbotron_masjid_1',
            'tmp_jumbotron_masjid_1',
            'jumbotron_masjid_2',
            'tmp_jumbotron_masjid_2',
            'jumbotron_masjid_3',
            'tmp_jumbotron_masjid_3',
            'jumbotron_masjid_4',
            'tmp_jumbotron_masjid_4',
            'jumbotron_masjid_5',
            'tmp_jumbotron_masjid_5',
            'jumbotron_masjid_6',
            'tmp_jumbotron_masjid_6',
            'is_active',
        ]);
        $this->is_active = true;
        $this->mount();
    }

    public function save()
    {
        try {
            $this->validate();
            $profil = Profil::where('user_id', Auth::id())->first();
            if (!$profil) {
                $this->dispatch('error', 'Profil masjid belum ada, silahkan buat profil terlebih dahulu.');
                return;
            }
            $paths = [];
            if ($this->jumbotron_masjid_1) {
                $paths['jumbotron_masjid_1'] = $this->saveProcessedImage($this->jumbotron_masjid_1, 1);
            }
            if ($this->jumbotron_masjid_2) {
                $paths['jumbotron_masjid_2'] = $this->saveProcessedImage($this->jumbotron_masjid_2, 2);
            }
            if ($this->jumbotron_masjid_3) {
                $paths['jumbotron_masjid_3'] = $this->saveProcessedImage($this->jumbotron_masjid_3, 3);
            }
            if ($this->jumbotron_masjid_4) {
                $paths['jumbotron_masjid_4'] = $this->saveProcessedImage($this->jumbotron_masjid_4, 4);
            }
            if ($this->jumbotron_masjid_5) {
                $paths['jumbotron_masjid_5'] = $this->saveProcessedImage($this->jumbotron_masjid_5, 5);
            }
            if ($this->jumbotron_masjid_6) {
                $paths['jumbotron_masjid_6'] = $this->saveProcessedImage($this->jumbotron_masjid_6, 6);
            }

            $model = $this->jumbotronMasjidId
                ? ModelsJumbotronMasjid::find($this->jumbotronMasjidId)
                : new ModelsJumbotronMasjid();
            if (!$model) {
                $model = new ModelsJumbotronMasjid();
            }
            $model->masjid_id = $profil->id;
            $model->created_by = Auth::id();
            $model->jumbotron_masjid_1 = $paths['jumbotron_masjid_1'] ?? $this->tmp_jumbotron_masjid_1;
            $model->jumbotron_masjid_2 = $paths['jumbotron_masjid_2'] ?? $this->tmp_jumbotron_masjid_2;
            $model->jumbotron_masjid_3 = $paths['jumbotron_masjid_3'] ?? $this->tmp_jumbotron_masjid_3;
            $model->jumbotron_masjid_4 = $paths['jumbotron_masjid_4'] ?? $this->tmp_jumbotron_masjid_4;
            $model->jumbotron_masjid_5 = $paths['jumbotron_masjid_5'] ?? $this->tmp_jumbotron_masjid_5;
            $model->jumbotron_masjid_6 = $paths['jumbotron_masjid_6'] ?? $this->tmp_jumbotron_masjid_6;
            $model->aktif = (bool) $this->is_active;
            $model->save();

            // Trigger event
            event(new ContentUpdatedEvent($profil->slug, 'jumbotron_masjid'));

            $this->jumbotronMasjidId = $model->id;
            session()->flash('success', 'Jumbotron masjid berhasil disimpan');
            return redirect()->route('jumbotron-masjid.index');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan jumbotron: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.jumbotron-masjid.edit-jumbotron-masjid');
    }
}
