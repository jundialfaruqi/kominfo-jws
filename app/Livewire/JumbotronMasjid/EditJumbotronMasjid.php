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
use Illuminate\Support\Facades\File;

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
        'jumbotron_masjid_1' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:300',
        'jumbotron_masjid_2' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:300',
        'jumbotron_masjid_3' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:300',
        'jumbotron_masjid_4' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:300',
        'jumbotron_masjid_5' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:300',
        'jumbotron_masjid_6' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:300',
        'is_active' => 'required|boolean'
    ];

    protected $messages = [
        'jumbotron_masjid_1.image'  => 'Jumbotron Masjid 1 harus berupa gambar',
        'jumbotron_masjid_1.max'    => 'Jumbotron Masjid 1 maksimal 300KB',
        'jumbotron_masjid_1.mimes'  => 'Jumbotron Masjid 1 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_2.image'  => 'Jumbotron Masjid 2 harus berupa gambar',
        'jumbotron_masjid_2.max'    => 'Jumbotron Masjid 2 maksimal 300KB',
        'jumbotron_masjid_2.mimes'  => 'Jumbotron Masjid 2 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_3.image'  => 'Jumbotron Masjid 3 harus berupa gambar',
        'jumbotron_masjid_3.max'    => 'Jumbotron Masjid 3 maksimal 300KB',
        'jumbotron_masjid_3.mimes'  => 'Jumbotron Masjid 3 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_4.image'  => 'Jumbotron Masjid 4 harus berupa gambar',
        'jumbotron_masjid_4.max'    => 'Jumbotron Masjid 4 maksimal 300KB',
        'jumbotron_masjid_4.mimes'  => 'Jumbotron Masjid 4 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_5.image'  => 'Jumbotron Masjid 5 harus berupa gambar',
        'jumbotron_masjid_5.max'    => 'Jumbotron Masjid 5 maksimal 300KB',
        'jumbotron_masjid_5.mimes'  => 'Jumbotron Masjid 5 harus berupa gambar jpg, png, jpeg, webp',
        'jumbotron_masjid_6.image'  => 'Jumbotron Masjid 6 harus berupa gambar',
        'jumbotron_masjid_6.max'    => 'Jumbotron Masjid 6 maksimal 300KB',
        'jumbotron_masjid_6.mimes'  => 'Jumbotron Masjid 6 harus berupa gambar jpg, png, jpeg, webp',
    ];

    public function updated($name)
    {
        if (in_array($name, [
            'jumbotron_masjid_1', 'jumbotron_masjid_2', 'jumbotron_masjid_3',
            'jumbotron_masjid_4', 'jumbotron_masjid_5', 'jumbotron_masjid_6'
        ])) {
            $this->validateOnly($name);
        }
    }

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

    /**
     * Method untuk menyimpan gambar jumbotron masjid yang sudah dikompresi di sisi client browser (format WebP).
     * Server backend berfungsi sebagai validator (tipe file & maks 300KB) dan menyimpan langsung file tanpa kompresi ulang.
     */
    private function saveProcessedImage($uploadedFile, $jumboNumber)
    {
        try {
            // Validasi batas maksimal 300KB di server sebagai penjaga pintu
            $maxSizeBytes = 300 * 1024;
            $fileSize = $uploadedFile->getSize();
            if ($fileSize > $maxSizeBytes) {
                throw new \Exception("Ukuran file melebihi batas maksimal server (300KB): " . round($fileSize / 1024, 2) . "KB");
            }

            $directory = public_path('images/jumbotrons');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Dapatkan ekstensi asli (default webp dari kompresi browser)
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            if (empty($extension) || $extension === 'tmp') {
                $extension = 'webp';
            }

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = \Illuminate\Support\Str::slug($originalName);
            $fileName = time() . '_jumbo_masjid' . $jumboNumber . '_' . ($safeName ?: 'jumbo') . '.' . $extension;
            $filePath = $directory . '/' . $fileName;

            // Simpan langsung file dari temporary ke public/images/jumbotrons tanpa kompresi ulang
            File::copy($uploadedFile->getRealPath(), $filePath);

            return '/images/jumbotrons/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    public function clearJumbotronMasjid1()
    {
        $this->deleteJumbotronFile('jumbotron_masjid_1');
    }
    public function clearJumbotronMasjid2()
    {
        $this->deleteJumbotronFile('jumbotron_masjid_2');
    }
    public function clearJumbotronMasjid3()
    {
        $this->deleteJumbotronFile('jumbotron_masjid_3');
    }
    public function clearJumbotronMasjid4()
    {
        $this->deleteJumbotronFile('jumbotron_masjid_4');
    }
    public function clearJumbotronMasjid5()
    {
        $this->deleteJumbotronFile('jumbotron_masjid_5');
    }
    public function clearJumbotronMasjid6()
    {
        $this->deleteJumbotronFile('jumbotron_masjid_6');
    }

    private function deleteJumbotronFile($field)
    {
        try {
            $profil = Profil::where('user_id', Auth::id())->first();
            if (!$profil) {
                $this->dispatch('error', 'Profil masjid untuk user ini tidak ditemukan');
                return;
            }

            $existing = null;
            if ($this->jumbotronMasjidId) {
                $existing = ModelsJumbotronMasjid::find($this->jumbotronMasjidId);
            }
            if (!$existing) {
                $existing = ModelsJumbotronMasjid::where('masjid_id', $profil->id)
                    ->where('created_by', Auth::id())
                    ->first();
            }

            $tmpField = 'tmp_' . $field;
            $path = null;
            if (property_exists($this, $tmpField) && !empty($this->$tmpField)) {
                $path = $this->$tmpField;
            } elseif ($existing) {
                $path = $existing->$field ?? null;
            }
            if ($path) {
                $this->deleteByPathOrSuffix($path);
            }

            if ($existing) {
                ModelsJumbotronMasjid::where('id', $existing->id)->update([$field => null]);
            }

            $this->$field = null;
            if (property_exists($this, $tmpField)) {
                $this->$tmpField = null;
            }

            event(new ContentUpdatedEvent($profil->slug, 'jumbotron_masjid'));
            $this->dispatch('resetFileInput', ['inputName' => $field]);
            $this->dispatch('reset_success', 'Gambar jumbotron berhasil direset');
        } catch (\Exception $e) {
            $this->dispatch('reset_error', 'Gagal mereset gambar jumbotron: ' . $e->getMessage());
        }
    }

    private function deleteByPathOrSuffix(string $path): void
    {
        $relative = ltrim($path, '/');
        $basename = basename($relative);
        $deleted = false;

        $candidates = [public_path($relative), public_path('/' . $relative)];
        $real = realpath(public_path($relative));
        if ($real) $candidates[] = $real;
        foreach (array_unique($candidates) as $candidate) {
            if (is_file($candidate)) {
                try { File::delete($candidate); } catch (\Exception $e) {}
                if (file_exists($candidate)) { @unlink($candidate); }
                if (!file_exists($candidate)) $deleted = true;
            }
        }

        if (!$deleted && $basename) {
            $suffix = preg_replace('/^\d+_/', '', $basename);
            $dir = public_path('images/jumbotrons');
            if (is_dir($dir) && $suffix) {
                $matches = glob($dir . '/*' . $suffix);
                if (is_array($matches)) {
                    foreach ($matches as $file) {
                        if (is_file($file)) {
                            try { File::delete($file); } catch (\Exception $e) {}
                            if (file_exists($file)) { @unlink($file); }
                        }
                    }
                }
            }
        }
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
                if ($this->tmp_jumbotron_masjid_1) {
                    $this->deleteByPathOrSuffix($this->tmp_jumbotron_masjid_1);
                }
                $paths['jumbotron_masjid_1'] = $this->saveProcessedImage($this->jumbotron_masjid_1, 1);
            }
            if ($this->jumbotron_masjid_2) {
                if ($this->tmp_jumbotron_masjid_2) {
                    $this->deleteByPathOrSuffix($this->tmp_jumbotron_masjid_2);
                }
                $paths['jumbotron_masjid_2'] = $this->saveProcessedImage($this->jumbotron_masjid_2, 2);
            }
            if ($this->jumbotron_masjid_3) {
                if ($this->tmp_jumbotron_masjid_3) {
                    $this->deleteByPathOrSuffix($this->tmp_jumbotron_masjid_3);
                }
                $paths['jumbotron_masjid_3'] = $this->saveProcessedImage($this->jumbotron_masjid_3, 3);
            }
            if ($this->jumbotron_masjid_4) {
                if ($this->tmp_jumbotron_masjid_4) {
                    $this->deleteByPathOrSuffix($this->tmp_jumbotron_masjid_4);
                }
                $paths['jumbotron_masjid_4'] = $this->saveProcessedImage($this->jumbotron_masjid_4, 4);
            }
            if ($this->jumbotron_masjid_5) {
                if ($this->tmp_jumbotron_masjid_5) {
                    $this->deleteByPathOrSuffix($this->tmp_jumbotron_masjid_5);
                }
                $paths['jumbotron_masjid_5'] = $this->saveProcessedImage($this->jumbotron_masjid_5, 5);
            }
            if ($this->jumbotron_masjid_6) {
                if ($this->tmp_jumbotron_masjid_6) {
                    $this->deleteByPathOrSuffix($this->tmp_jumbotron_masjid_6);
                }
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
