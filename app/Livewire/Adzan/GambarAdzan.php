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
use Intervention\Image\Laravel\Facades\Image;

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
    public $showTable = true;
    public $deleteAdzanId;
    public $deleteAdzanName;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'adzan1' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan2' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan3' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan4' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan5' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan6' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan7' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan8' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan9' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan10' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan11' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan12' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan13' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan14' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'adzan15' => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
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
        'adzan1.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan2.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan3.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan4.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan5.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan6.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan7.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan8.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan9.max'      => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan10.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan11.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan12.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan13.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan14.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan15.max'     => 'Ukuran gambar terlalu besar. Ukuran file maksimal 1MB!',
        'adzan1.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan2.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan3.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan4.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan5.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan6.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan7.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan8.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan9.mimes'    => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan10.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan11.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan12.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan13.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan14.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
        'adzan15.mimes'   => 'Format gambar harus JPG, PNG, JPEG, GIF, WEBP',
    ];

    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 990)
    {
        try {
            // Konversi ke bytes
            $maxSizeBytes = $maxSizeKB * 1024;

            // Baca gambar menggunakan Intervention Image
            $image = Image::read($uploadedFile->getRealPath());

            // Dapatkan dimensi asli
            $originalWidth = $image->width();
            $originalHeight = $image->height();

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

                // Jika masih terlalu besar dengan kualitas minimum, resize lebih kecil
                if ($quality < $minQuality && strlen($image->toJpeg($minQuality)) > $maxSizeBytes) {
                    $scaleFactor = 0.9;
                    while (strlen($image->toJpeg($minQuality)) > $maxSizeBytes && $scaleFactor > 0.5) {
                        $newWidth = (int)($originalWidth * $scaleFactor);
                        $newHeight = (int)($originalHeight * $scaleFactor);
                        $image->resize($newWidth, $newHeight);
                        $scaleFactor -= 0.05;
                    }
                }
            } while ($quality >= $minQuality);

            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menyimpan gambar yang sudah diproses
     */
    private function saveProcessedImage($uploadedFile, $adzanNumber)
    {
        try {
            // Proses resize gambar dengan ukuran maksimal 990KB
            $processedImage = $this->resizeImageToLimit($uploadedFile);

            // Generate nama file dengan ekstensi .jpg (karena kita convert ke JPEG)
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_adzan' . $adzanNumber . '_' . $originalName . '.jpg';
            $filePath = public_path('images/adzan/' . $fileName);

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

            return '/images/adzan/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan1()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan1) {
                $filePath = public_path($this->tmp_adzan1);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan1 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan1 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan1 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan1']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan1']);

            // $this->showForm = true;

            $this->dispatch('success', 'Gambar Adzan 1 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan2()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan2) {
                $filePath = public_path($this->tmp_adzan2);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan2 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan2 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan2 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan2']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan2']);

            $this->dispatch('success', 'Gambar Adzan 2 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan3()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan3) {
                $filePath = public_path($this->tmp_adzan3);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan3 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan3 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan3 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan3']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan3']);

            $this->dispatch('success', 'Gambar Adzan 3 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan4()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan4) {
                $filePath = public_path($this->tmp_adzan4);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan4 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan4 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan4 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan4']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan4']);

            $this->dispatch('success', 'Gambar Adzan 4 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan5()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan5) {
                $filePath = public_path($this->tmp_adzan5);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan5 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan5 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan5 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan5']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan5']);

            $this->dispatch('success', 'Gambar Adzan 5 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan6()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan6) {
                $filePath = public_path($this->tmp_adzan6);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan6 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan6 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan6 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan6']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan6']);

            $this->dispatch('success', 'Gambar Adzan 6 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan7()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan7) {
                $filePath = public_path($this->tmp_adzan7);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan7 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan7 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan7 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan7']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan7']);

            $this->dispatch('success', 'Gambar Adzan 7 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan8()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan8) {
                $filePath = public_path($this->tmp_adzan8);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan8 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan8 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan8 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan8']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan8']);

            $this->dispatch('success', 'Gambar Adzan 8 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan9()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan9) {
                $filePath = public_path($this->tmp_adzan9);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan9 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan9 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan9 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan9']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan9']);

            $this->dispatch('success', 'Gambar Adzan 9 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan10()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan10) {
                $filePath = public_path($this->tmp_adzan10);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan10 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan10 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan10 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan10']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan10']);

            $this->dispatch('success', 'Gambar Adzan 10 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan11()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan11) {
                $filePath = public_path($this->tmp_adzan11);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan11 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan11 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan11 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan11']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan11']);

            $this->dispatch('success', 'Gambar Adzan 11 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan12()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan12) {
                $filePath = public_path($this->tmp_adzan12);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan12 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan12 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan12 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan12']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan12']);

            $this->dispatch('success', 'Gambar Adzan 12 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan13()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan13) {
                $filePath = public_path($this->tmp_adzan13);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan13 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan13 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan13 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan13']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan13']);

            $this->dispatch('success', 'Gambar Adzan 13 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan14()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan14) {
                $filePath = public_path($this->tmp_adzan14);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan14 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan14 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan14 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan14']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan14']);

            $this->dispatch('success', 'Gambar Adzan 14 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function clearAdzan15()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_slide1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_adzan15) {
                $filePath = public_path($this->tmp_adzan15);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->adzanId) {
                    $adzan = Adzan::find($this->adzanId);
                    if ($adzan) {
                        $adzan->adzan15 = null;
                        $adzan->save();
                    }
                }
            }

            // Reset property slide1 (file yang diupload)
            $this->adzan15 = null;

            // Reset property tmp_slide1 (gambar yang sudah tersimpan)
            $this->tmp_adzan15 = null;

            // Reset validation error untuk slide1
            $this->resetValidation(['adzan15']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'adzan15']);

            $this->dispatch('success', 'Gambar Adzan 15 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus gambar: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // jika user bukan admin
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
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
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        // query builder for adzan
        $query = Adzan::with('user')
            ->select('id', 'user_id', 'adzan1', 'adzan2', 'adzan3', 'adzan4', 'adzan5', 'adzan6', 'adzan7', 'adzan8', 'adzan9', 'adzan10', 'adzan11', 'adzan12', 'adzan13', 'adzan14', 'adzan15');

        // if user is not Super Admin, filter adzan and exclude users with 'Super Admin' or 'Admin' roles
        if (!$isSuperAdmin) {
            $query->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['Super Admin', 'Admin']);
            });
        }

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
        $users = collect([]);
        if ($isAdmin) {
            $usersWithAdzan = Adzan::pluck('user_id')->toArray();

            // If not Super Admin, exclude users with 'Super Admin' or 'Admin' roles
            $usersQuery = User::whereNotIn('id', $usersWithAdzan);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }

            $users = $usersQuery->orderBy('name')
                ->get();
        }

        return view('livewire.adzan.gambar-adzan', [
            'adzanList' => $adzanList,
            'users' => $users,
        ]);
    }

    public function showAddForm()
    {

        // only admin can add new adzan
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
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
        $this->showTable = false;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $adzan = Adzan::findOrFail($id);

        // Check if user has permission to edit this adzan
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $adzan->user_id) {
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
        $this->showTable = false;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->showTable = true;
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
        if (!in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            $this->userId = $currentUser->id;
        }

        // Additional validation for one adzan per user
        if (!$this->isEdit) {
            // Check if the selected user already has an adzan
            $existingAdzan = Adzan::where('user_id', $this->userId)->first();
            if ($existingAdzan) {
                $this->dispatch('error', 'User ini sudah memiliki adzan!');
                return;
            }
        } else {
            // When editing, make sure we're not changing to a user who already has an adzan
            $existingAdzan = Adzan::where('user_id', $this->userId)
                ->where('id', '!=', $this->adzanId)
                ->first();
            if ($existingAdzan) {
                $this->dispatch('error', 'User ini sudah memiliki adzan!');
                return;
            }
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                $adzan = Adzan::findOrFail($this->adzanId);
                // Check if user has permission to edit this adzan
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $adzan->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit adzan ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own adzan
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat adzan untuk user lain!');
                    return;
                }
                $adzan = new Adzan();
            }

            $adzan->user_id = $this->userId;

            // Handle adzan 1 upload
            if ($this->adzan1) {
                // Delete old adzan 1 if exists
                if ($this->isEdit && $adzan->adzan1 && file_exists(public_path($adzan->adzan1))) {
                    File::delete(public_path($adzan->adzan1));
                }

                // Save new adzan 1 dengan resize otomatis
                $adzan->adzan1 = $this->saveProcessedImage($this->adzan1, 1);
            } else {
                $adzan->adzan1 = $this->tmp_adzan1;
            }

            // Handle adzan 2 upload
            if ($this->adzan2) {
                // Delete old adzan 2 if exists
                if ($this->isEdit && $adzan->adzan2 && file_exists(public_path($adzan->adzan2))) {
                    File::delete(public_path($adzan->adzan2));
                }

                // Save new adzan 2 dengan resize otomatis
                $adzan->adzan2 = $this->saveProcessedImage($this->adzan2, 2);
            } else {
                $adzan->adzan2 = $this->tmp_adzan2;
            }

            // Handle adzan 3 upload
            if ($this->adzan3) {
                // delete old adzan 3 if exists
                if ($this->isEdit && $adzan->adzan3 && file_exists(public_path($adzan->adzan3))) {
                    File::delete(public_path($adzan->adzan3));
                }

                // Save new adzan 3 dengan resize otomatis
                $adzan->adzan3 = $this->saveProcessedImage($this->adzan3, 3);
            } else {
                $adzan->adzan3 = $this->tmp_adzan3;
            }

            // Handle adzan 4 upload
            if ($this->adzan4) {
                // delete old adzan 4 if exists
                if ($this->isEdit && $adzan->adzan4 && file_exists(public_path($adzan->adzan4))) {
                    File::delete(public_path($adzan->adzan4));
                }

                // Save new adzan 4 dengan resize otomatis
                $adzan->adzan4 = $this->saveProcessedImage($this->adzan4, 4);
            } else {
                $adzan->adzan4 = $this->tmp_adzan4;
            }

            // Handle adzan 5 upload
            if ($this->adzan5) {
                // delete old adzan 5 if exists
                if ($this->isEdit && $adzan->adzan5 && file_exists(public_path($adzan->adzan5))) {
                    File::delete(public_path($adzan->adzan5));
                }

                // Save new adzan 5 dengan resize otomatis
                $adzan->adzan5 = $this->saveProcessedImage($this->adzan5, 5);
            } else {
                $adzan->adzan5 = $this->tmp_adzan5;
            }

            // Handle adzan 6 upload            
            if ($this->adzan6) {
                // delete old adzan 6 if exists
                if ($this->isEdit && $adzan->adzan6 && file_exists(public_path($adzan->adzan6))) {
                    File::delete(public_path($adzan->adzan6));
                }

                // Save new adzan 6 dengan resize otomatis
                $adzan->adzan6 = $this->saveProcessedImage($this->adzan6, 6);
            } else {
                $adzan->adzan6 = $this->tmp_adzan6;
            }

            // Handle adzan 7 upload            
            if ($this->adzan7) {
                // delete old adzan 7 if exists
                if ($this->isEdit && $adzan->adzan7 && file_exists(public_path($adzan->adzan7))) {
                    File::delete(public_path($adzan->adzan7));
                }

                // Save new adzan 7 dengan resize otomatis
                $adzan->adzan7 = $this->saveProcessedImage($this->adzan7, 7);
            } else {
                $adzan->adzan7 = $this->tmp_adzan7;
            }

            // Handle adzan 8 upload            
            if ($this->adzan8) {
                // delete old adzan 8 if exists
                if ($this->isEdit && $adzan->adzan8 && file_exists(public_path($adzan->adzan8))) {
                    File::delete(public_path($adzan->adzan8));
                }

                // Save new adzan 8 dengan resize otomatis
                $adzan->adzan8 = $this->saveProcessedImage($this->adzan8, 8);
            } else {
                $adzan->adzan8 = $this->tmp_adzan8;
            }

            // Handle adzan 9 upload            
            if ($this->adzan9) {
                // delete old adzan 9 if exists
                if ($this->isEdit && $adzan->adzan9 && file_exists(public_path($adzan->adzan9))) {
                    File::delete(public_path($adzan->adzan9));
                }

                // Save new adzan 9 dengan resize otomatis
                $adzan->adzan9 = $this->saveProcessedImage($this->adzan9, 9);
            } else {
                $adzan->adzan9 = $this->tmp_adzan9;
            }

            // Handle adzan 10 upload            
            if ($this->adzan10) {
                // delete old adzan 10 if exists
                if ($this->isEdit && $adzan->adzan10 && file_exists(public_path($adzan->adzan10))) {
                    File::delete(public_path($adzan->adzan10));
                }

                // Save new adzan 10 dengan resize otomatis
                $adzan->adzan10 = $this->saveProcessedImage($this->adzan10, 10);
            } else {
                $adzan->adzan10 = $this->tmp_adzan10;
            }

            // Handle adzan 11 upload            
            if ($this->adzan11) {
                // delete old adzan 11 if exists
                if ($this->isEdit && $adzan->adzan11 && file_exists(public_path($adzan->adzan11))) {
                    File::delete(public_path($adzan->adzan11));
                }

                // Save new adzan 11 dengan resize otomatis
                $adzan->adzan11 = $this->saveProcessedImage($this->adzan11, 11);
            } else {
                $adzan->adzan11 = $this->tmp_adzan11;
            }

            // Handle adzan 12 upload            
            if ($this->adzan12) {
                // delete old adzan 12 if exists
                if ($this->isEdit && $adzan->adzan12 && file_exists(public_path($adzan->adzan12))) {
                    File::delete(public_path($adzan->adzan12));
                }

                // Save new adzan 12 dengan resize otomatis
                $adzan->adzan12 = $this->saveProcessedImage($this->adzan12, 12);
            } else {
                $adzan->adzan12 = $this->tmp_adzan12;
            }

            // Handle adzan 13 upload            
            if ($this->adzan13) {
                // delete old adzan 13 if exists
                if ($this->isEdit && $adzan->adzan13 && file_exists(public_path($adzan->adzan13))) {
                    File::delete(public_path($adzan->adzan13));
                }

                // Save new adzan 13 dengan resize otomatis
                $adzan->adzan13 = $this->saveProcessedImage($this->adzan13, 13);
            } else {
                $adzan->adzan13 = $this->tmp_adzan13;
            }

            // Handle adzan 14 upload            
            if ($this->adzan14) {
                // delete old adzan 14 if exists
                if ($this->isEdit && $adzan->adzan14 && file_exists(public_path($adzan->adzan14))) {
                    File::delete(public_path($adzan->adzan14));
                }

                // Save new adzan 14 dengan resize otomatis
                $adzan->adzan14 = $this->saveProcessedImage($this->adzan14, 14);
            } else {
                $adzan->adzan14 = $this->tmp_adzan14;
            }

            // Handle adzan 15 upload            
            if ($this->adzan15) {
                // delete old adzan 15 if exists
                if ($this->isEdit && $adzan->adzan15 && file_exists(public_path($adzan->adzan15))) {
                    File::delete(public_path($adzan->adzan15));
                }

                // Save new adzan 15 dengan resize otomatis
                $adzan->adzan15 = $this->saveProcessedImage($this->adzan15, 15);
            } else {
                $adzan->adzan15 = $this->tmp_adzan15;
            }

            $adzan->save();

            $this->dispatch('success', $this->isEdit ? 'Adzan berhasil diubah!' : 'Adzan berhasil ditambahkan!');
            $this->showTable = true;

            // only hide form and reset fields if user is not admin
            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
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
