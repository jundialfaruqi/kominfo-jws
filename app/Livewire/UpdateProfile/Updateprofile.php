<?php

namespace App\Livewire\UpdateProfile;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class Updateprofile extends Component
{
    use WithFileUploads;

    #[Title('Pengaturan')]

    public $name;
    public $email;
    public $phone;
    public $address;
    public $photo;
    public $temp_photo;
    public $password_old;
    public $password_new;
    public $password_new_confirmation;

    // Properties untuk konfirmasi email
    public $show_email_confirmation = false;
    public $email_confirmation_password;
    public $new_email;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
            'password_old' => 'nullable|required_with:password_new|current_password',
            'password_new' => ['nullable', 'required_with:password_old', 'confirmed', Password::defaults()],
            'password_new_confirmation' => 'nullable|required_with:password_new',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama harus diisi.',
        'email.required' => 'Email harus diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'photo.image' => 'File harus berupa gambar.',
        'photo.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau GIF.',
        'photo.max' => 'Ukuran gambar maksimal 5MB.',
        'password_old.current_password' => 'Password lama tidak sesuai.',
        'password_new.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        'password_new.min' => 'Password baru minimal 8 karakter.',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->temp_photo = $user->photo;
    }

    /**
     * Method untuk resize gambar ke ratio 1:1 dan ukuran maksimal
     */
    private function resizeImageToSquare($uploadedFile, $maxSizeKB = 500)
    {
        try {
            // Konversi ke bytes
            $maxSizeBytes = $maxSizeKB * 1024;

            // Baca gambar menggunakan Intervention Image
            $image = Image::read($uploadedFile->getRealPath());

            // Target aspect ratio 1:1 (square)
            $targetSize = 400; // 400x400 pixels

            // Dapatkan dimensi asli
            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // Crop gambar ke aspect ratio 1:1 (square)
            $minDimension = min($originalWidth, $originalHeight);

            // Crop dari tengah untuk membuat square
            $x = (int)(($originalWidth - $minDimension) / 2);
            $y = (int)(($originalHeight - $minDimension) / 2);
            $image->crop($minDimension, $minDimension, $x, $y);

            // Resize ke dimensi target
            $image->resize($targetSize, $targetSize);

            // Mulai dengan kualitas tinggi dan turunkan sampai ukuran sesuai
            $quality = 95;
            $minQuality = 30;

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
                    $newSize = (int)($targetSize * $scaleFactor);
                    $image->resize($newSize, $newSize);
                    $scaleFactor -= 0.05;
                }
            }

            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menyimpan foto profil yang sudah diproses
     */
    private function saveProcessedPhoto($uploadedFile)
    {
        try {
            // Proses resize gambar dengan aspect ratio 1:1 dan ukuran maksimal 500KB
            $processedImage = $this->resizeImageToSquare($uploadedFile);

            // Generate nama file dengan ekstensi .jpg
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = 'profile_' . Auth::id() . '_' . time() . '_' . $originalName . '.jpg';
            $filePath = public_path('images/profiles/' . $fileName);

            // Pastikan directory ada
            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Tentukan kualitas optimal berdasarkan ukuran target
            $maxSizeBytes = 500 * 1024; // 500KB
            $quality = 85;

            // Fine-tune kualitas untuk mendekati 500KB
            do {
                $encoded = $processedImage->toJpeg($quality);
                $currentSize = strlen($encoded);

                if ($currentSize <= $maxSizeBytes) {
                    break;
                }

                $quality -= 1;
            } while ($quality >= 50);

            // Simpan gambar yang sudah diproses dengan kualitas optimal
            $processedImage->toJpeg($quality)->save($filePath);

            // Verifikasi ukuran file hasil akhir
            $finalSize = filesize($filePath);
            if ($finalSize > $maxSizeBytes) {
                throw new \Exception("Ukuran file masih terlalu besar: " . round($finalSize / 1024, 2) . "KB");
            }

            return '/images/profiles/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus foto profil
     */
    public function clearPhoto()
    {
        try {
            // Jika ada foto lama, hapus file fisiknya
            if ($this->temp_photo) {
                $filePath = public_path($this->temp_photo);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                $user = Auth::user();
                $user->photo = null;
                $user->save();
            }

            // Reset property photo (file yang diupload)
            $this->photo = null;

            // Reset property temp_photo (gambar yang sudah tersimpan)
            $this->temp_photo = null;

            // Reset validation error untuk photo
            $this->resetValidation(['photo']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'photo']);

            $this->dispatch('success', 'Foto profil berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus foto: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk upload foto profil
     */
    public function uploadPhoto()
    {
        $this->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        try {
            // Hapus foto lama jika ada
            if ($this->temp_photo) {
                $oldFilePath = public_path($this->temp_photo);
                if (file_exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            // Simpan foto baru
            $photoPath = $this->saveProcessedPhoto($this->photo);

            // Update database
            $user = Auth::user();
            $user->photo = $photoPath;
            $user->save();

            // Update temp_photo untuk preview
            $this->temp_photo = $photoPath;

            // Reset photo input
            $this->photo = null;
            $this->dispatch('resetFileInput', ['inputName' => 'photo']);

            $this->dispatch('success', 'Foto profil berhasil diupload!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Gagal mengupload foto: ' . $e->getMessage());
        }
    }

    public function showEmailConfirmation()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        $this->new_email = $this->email;
        $this->show_email_confirmation = true;
        $this->email_confirmation_password = '';
    }

    public function cancelEmailChange()
    {
        $this->show_email_confirmation = false;
        $this->email_confirmation_password = '';
        $this->new_email = '';
        $this->email = Auth::user()->email; // Reset ke email asli
    }

    public function confirmEmailChange()
    {
        $this->validate([
            'email_confirmation_password' => 'required',
            'new_email' => 'required|email|unique:users,email,' . Auth::id(),
        ], [
            'email_confirmation_password.required' => 'Password harus diisi untuk mengubah email.',
            'new_email.required' => 'Email baru harus diisi.',
            'new_email.email' => 'Format email tidak valid.',
            'new_email.unique' => 'Email sudah digunakan.',
        ]);

        // Verifikasi password
        if (!Hash::check($this->email_confirmation_password, Auth::user()->password)) {
            $this->addError('email_confirmation_password', 'Password tidak sesuai.');
            return;
        }

        try {
            DB::beginTransaction();

            // Update email
            $user = Auth::user();
            $user->email = $this->new_email;
            $user->save();

            DB::commit();

            $this->dispatch('success', 'Email berhasil diubah');
            $this->show_email_confirmation = false;
            // Logout user setelah 2 detik
            // $this->dispatch('logout-user');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', 'Terjadi kesalahan saat mengubah email.');
        }
    }

    public function update()
    {
        // Jika email berubah, tampilkan konfirmasi
        if ($this->email !== Auth::user()->email) {
            $this->showEmailConfirmation();
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $user->name = $this->name;
            $user->phone = $this->phone;
            $user->address = $this->address;

            // Update password jika diisi
            if ($this->password_old && $this->password_new) {
                $user->password = Hash::make($this->password_new);
                $this->password_old = '';
                $this->password_new = '';
                $this->password_new_confirmation = '';
            }

            $user->save();

            DB::commit();

            $this->dispatch('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', 'Terjadi kesalahan saat memperbarui profil.');
        }
    }

    public function render()
    {
        return view('livewire.update-profile.updateprofile');
    }
}
