<?php

namespace App\Livewire\Profil;

use App\Events\ContentUpdatedEvent;
use App\Models\Profil;
use App\Models\Slides;
use App\Models\Marquee;
use App\Models\Petugas;
use App\Models\Laporan;
use Carbon\Carbon;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProfilMasjid extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Profil Masjid')]

    public $search;
    public $paginate;
    protected $paginationTheme = 'bootstrap';

    public $profileId;
    public $userId;
    public $name;
    public $slug;
    public $address;
    public $phone;
    public $logo_masjid;
    public $temp_logo;
    public $logo_pemerintah;
    public $temp_logo_pemerintah;

    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteProfileId;
    public $deleteProfileName;

    protected $rules = [
        'userId'            => 'required|exists:users,id',
        'name'              => 'required|string|max:255',
        'slug'              => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:profils,slug',
        'address'           => 'required|string',
        'phone'             => 'required|string|max:15',
        'logo_masjid'       => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
        'logo_pemerintah'   => 'nullable|image|max:1000|mimes:jpg,png,jpeg,webp,gif',
    ];

    protected $messages = [
        'userId.exists'             => 'Admin Masjid tidak ditemukan',
        'userId.required'           => 'Wajib memilih Admin/User',
        'slug.required'             => 'ID JWS wajib diisi!',
        'slug.unique'               => 'ID JWS sudah digunakan, silakan gunakan ID JWS lain!',
        'slug.regex'                => 'ID JWS hanya boleh mengandung huruf kecil, angka, dan tanda hubung (-)!',
        'slug.max'                  => 'ID JWS maksimal 255 karakter!',
        'logo_masjid.image'         => 'File harus berupa gambar',
        'logo_masjid.mimes'         => 'Format file tidak valid. File harus berupa gambar jpg, jpeg, png, webp, atau gif!',
        'logo_masjid.max'           => 'File gambar terlalu besar. Ukuran file maksimal 1MB!',
        'logo_pemerintah.image'     => 'File harus berupa gambar',
        'logo_pemerintah.mimes'     => 'Format file tidak valid. File harus berupa gambar jpg, jpeg, png, webp, atau gif!',
        'logo_pemerintah.max'       => 'File gambar terlalu besar. Ukuran file maksimal 1MB!',
        'phone.max'                 => 'Nomor telepon maksimal 15 karakter!',
        'phone.required'            => 'Nomor telepon wajib diisi!',
        'address.required'          => 'Alamat wajib diisi!',
        'name.required'             => 'Nama Masjid wajib diisi!',
        'name.max'                  => 'Nama Masjid maksimal 255 karakter!',
        'name.string'               => 'Nama Masjid harus berupa teks!',
    ];

    public function mount()
    {
        $this->search = '';
        $this->paginate = 10;

        // If user is not admin
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $profil = Profil::where('user_id', Auth::id())->first();

            // Always show form for non-admin users
            $this->showForm = true;
            // Set user ID for new profiles
            $this->userId = Auth::id();

            if ($profil) {
                // If profile exists, load the data
                $this->profileId                = $profil->id;
                $this->name                     = $profil->name;
                $this->slug                     = $profil->slug;
                $this->address                  = $profil->address;
                $this->phone                    = $profil->phone;
                $this->temp_logo                = $profil->logo_masjid;
                $this->temp_logo_pemerintah     = $profil->logo_pemerintah;
                $this->isEdit                   = true;
            } else {
                // For new profiles, set isEdit to false
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
                'profileId',
                'userId',
                'name',
                'slug',
                'address',
                'phone',
                'logo_masjid',
                'temp_logo',
                'logo_pemerintah',
                'temp_logo_pemerintah'
            ]
        );
    }

    public function render()
    {
        // Get current user and role
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        // Query builder for profiles
        $query = Profil::with('user')
            ->select('id', 'user_id', 'name', 'slug', 'address', 'phone', 'logo_masjid', 'logo_pemerintah');

        // If user is not Super Admin, filter profiles and exclude users with 'Super Admin' or 'Admin' roles
        if (!$isSuperAdmin) {
            $query->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['Super Admin', 'Admin']);
            });
        }

        // If user is not admin, only show their own profile
        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            // Admin can search through profiles
            $query->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $profilList = $query->orderBy('name', 'asc')
            ->paginate($this->paginate);

        // Profil metrics (moved from Blade): total, active (slug not null), new this week
        $now = Carbon::now('Asia/Jakarta');
        $startWeek = $now->copy()->startOfWeek();
        $endWeek = $now->copy()->endOfWeek();

        $totalMasjid = Profil::count();
        $jwsAktif = Profil::whereNotNull('slug')->count();
        $baruMingguIni = Profil::whereBetween('created_at', [$startWeek, $endWeek])->count();

        // Aggregated content readiness metrics
        $profilesAll = Profil::select('id', 'user_id', 'name', 'slug', 'address', 'phone', 'logo_masjid', 'logo_pemerintah')->get();
        $readyMasjid = 0;
        $needsMasjid = 0;
        $profileCompletenessSum = 0.0;

        foreach ($profilesAll as $p) {
            // Slides & Marquee are per-user
            $slides = Slides::where('user_id', $p->user_id)->first();
            $marquee = Marquee::where('user_id', $p->user_id)->first();

            $slidesFilled = $slides
                ? collect([$slides->slide1, $slides->slide2, $slides->slide3, $slides->slide4, $slides->slide5, $slides->slide6])
                ->filter(fn($v) => !empty($v))
                ->count()
                : 0;
            $marqueeFilled = $marquee
                ? collect([$marquee->marquee1, $marquee->marquee2, $marquee->marquee3, $marquee->marquee4, $marquee->marquee5, $marquee->marquee6])
                ->filter(fn($v) => !empty($v))
                ->count()
                : 0;

            // Hanya gunakan Slides & Marquee
            $moduleCompletenessRatio = ((($slidesFilled / 6) + ($marqueeFilled / 6)) / 2);

            if ($slidesFilled === 6 && $marqueeFilled === 6) {
                $readyMasjid++;
            }
            if ($moduleCompletenessRatio < 0.5) {
                $needsMasjid++;
            }

            $profileFields = [$p->name, $p->address, $p->phone, $p->logo_masjid, $p->logo_pemerintah];
            $profileCompleteness = count($profileFields) > 0
                ? (collect($profileFields)->filter(fn($v) => !empty($v))->count() / count($profileFields))
                : 0;
            $profileCompletenessSum += $profileCompleteness;
        }

        $kontenSiapTayangPercent = $totalMasjid > 0 ? (int) round(($readyMasjid / $totalMasjid) * 100) : 0;
        $kelengkapanProfilRata = $totalMasjid > 0 ? (int) round(($profileCompletenessSum / $totalMasjid) * 100) : 0;
        $masjidPerluDilengkapi = $needsMasjid;

        // Changes in the last 24 hours across modules
        $since = Carbon::now('Asia/Jakarta')->subDay();
        $perubahan24JamTerakhir =
            Slides::where('updated_at', '>=', $since)->count()
            + Marquee::where('updated_at', '>=', $since)->count()
            + Petugas::where('updated_at', '>=', $since)->count()
            + Laporan::where('updated_at', '>=', $since)->count();

        // Only admin can see list of users for assignment
        $users = collect([]);
        if ($isAdmin) {
            $usersWithProfiles = Profil::pluck('user_id')->toArray();

            // If not Super Admin, exclude users with 'Super Admin' or 'Admin' roles
            $usersQuery = User::whereNotIn('id', $usersWithProfiles);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }

            $users = $usersQuery->orderBy('name')
                ->get();
        }

        return view('livewire.profil.profil-masjid', [
            'profilList' => $profilList,
            'users' => $users,
            'totalMasjid' => $totalMasjid,
            'jwsAktif' => $jwsAktif,
            'baruMingguIni' => $baruMingguIni,
            // Aggregated metrics for cards
            'kontenSiapTayangPercent' => $kontenSiapTayangPercent,
            'kelengkapanProfilRata' => $kelengkapanProfilRata,
            'masjidPerluDilengkapi' => $masjidPerluDilengkapi,
            'perubahan24JamTerakhir' => $perubahan24JamTerakhir,
        ]);
    }

    public function showAddForm()
    {
        // Only admin can add new profiles
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah profil masjid!');
            return;
        }

        $this->resetValidation();
        $this->reset(
            [
                'profileId',
                'userId',
                'name',
                'slug',
                'address',
                'phone',
                'logo_masjid',
                'temp_logo',
                'logo_pemerintah',
                'temp_logo_pemerintah'
            ]
        );
        $this->isEdit   = false;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $profil = Profil::findOrFail($id);

        // Check if user has permission to edit this profile
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $profil->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit profil ini!');
            return;
        }

        $this->profileId            = $profil->id;
        $this->userId               = $profil->user_id;
        $this->name                 = $profil->name;
        $this->slug                 = $profil->slug;
        $this->address              = $profil->address;
        $this->phone                = $profil->phone;
        $this->temp_logo            = $profil->logo_masjid;
        $this->temp_logo_pemerintah = $profil->logo_pemerintah;

        $this->isEdit               = true;
        $this->showForm             = true;
        $this->showTable            = false;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->showTable = true;
        $this->resetValidation();
        $this->reset(
            [
                'profileId',
                'userId',
                'name',
                'slug',
                'address',
                'phone',
                'logo_masjid',
                'temp_logo',
                'logo_pemerintah',
                'temp_logo_pemerintah'
            ]
        );
    }

    private function resizeImageToLimit($uploadedFile, $maxSizeKB = 990)
    {
        try {
            // Konversi ke bytes
            $maxSizeBytes = $maxSizeKB * 1024;

            // Baca gambar menggunakan Intervention Image
            $image = Image::read($uploadedFile->getRealPath());

            // Crop ke rasio 1:1
            $width = $image->width();
            $height = $image->height();
            $size = min($width, $height);
            $image->crop($size, $size, ($width - $size) / 2, ($height - $size) / 2);

            // Mulai dengan kualitas tinggi dan turunkan sampai ukuran sesuai
            $quality = 95;
            $minQuality = 20;

            do {
                // Encode dengan kualitas saat ini ke WebP
                $encoded = $image->toWebp($quality);
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
                if ($quality < $minQuality && strlen($image->toWebp($minQuality)) > $maxSizeBytes) {
                    $scaleFactor = 0.9;
                    while (strlen($image->toWebp($minQuality)) > $maxSizeBytes && $scaleFactor > 0.5) {
                        $newSize = (int)($size * $scaleFactor);
                        $image->resize($newSize, $newSize);
                        $scaleFactor -= 0.05;
                    }
                }
            } while ($quality >= $minQuality);

            return $image;
        } catch (\Exception $e) {
            throw new \Exception('Gagal memproses gambar: ' . $e->getMessage());
        }
    }

    private function saveProcessedImage($uploadedFile, $type)
    {
        try {
            // Proses resize gambar dengan ukuran maksimal 990KB
            $processedImage = $this->resizeImageToLimit($uploadedFile);

            // Generate nama file dengan ekstensi .webp
            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = time() . '_' . $type . '_' . $originalName . '.webp';
            $filePath = public_path('images/logo/' . $fileName);

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
                $encoded = $processedImage->toWebp($quality);
                $currentSize = strlen($encoded);

                if ($currentSize <= $maxSizeBytes) {
                    break;
                }

                $quality -= 1;
            } while ($quality >= 60);

            // Simpan gambar yang sudah diproses dengan kualitas optimal
            $processedImage->toWebp($quality)->save($filePath);

            // Verifikasi ukuran file hasil akhir
            $finalSize = filesize($filePath);
            if ($finalSize > $maxSizeBytes) {
                throw new \Exception("Ukuran file masih terlalu besar: " . round($finalSize / 1024, 2) . "KB");
            }

            return '/images/logo/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan gambar: ' . $e->getMessage());
        }
    }

    public function clearLogoMasjid()
    {
        try {
            // Jika sedang edit dan ada file lama di temp_logo, hapus file fisiknya
            if ($this->isEdit && $this->temp_logo) {
                $filePath = public_path($this->temp_logo);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->profileId) {
                    $profil = Profil::find($this->profileId);
                    if ($profil) {
                        $profil->logo_masjid = null;
                        $profil->save();
                    }
                }
            }

            // Reset property logo_masjid (file yang diupload)
            $this->logo_masjid = null;

            // Reset property temp_logo (gambar yang sudah tersimpan)
            $this->temp_logo = null;

            // Reset validation error untuk logo_masjid
            $this->resetValidation(['logo_masjid']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'logo_masjid']);

            $this->dispatch('success', 'Logo Masjid berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus logo: ' . $e->getMessage());
        }
    }

    public function clearLogoPemerintah()
    {
        try {
            // Jika sedang edit dan ada file lama di temp_logo_pemerintah, hapus file fisiknya
            if ($this->isEdit && $this->temp_logo_pemerintah) {
                $filePath = public_path($this->temp_logo_pemerintah);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->profileId) {
                    $profil = Profil::find($this->profileId);
                    if ($profil) {
                        $profil->logo_pemerintah = null;
                        $profil->save();
                    }
                }
            }

            // Reset property logo_pemerintah (file yang diupload)
            $this->logo_pemerintah = null;

            // Reset property temp_logo_pemerintah (gambar yang sudah tersimpan)
            $this->temp_logo_pemerintah = null;

            // Reset validation error untuk logo_pemerintah
            $this->resetValidation(['logo_pemerintah']);

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'logo_pemerintah']);

            $this->dispatch('success', 'Logo Instansi berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus logo: ' . $e->getMessage());
        }
    }

    public function generateSlugFromName()
    {
        // Hanya generate slug otomatis saat membuat profil baru (bukan edit)
        if ($this->name && !$this->isEdit) {
            $this->slug = Str::slug($this->name);
        }
    }

    private function validateUniqueSlug($slug, $id = null)
    {
        $query = Profil::where('slug', $slug);

        // Exclude current profile when updating
        if ($id) {
            $query->where('id', '!=', $id);
        }

        return !$query->exists();
    }

    private function generateSlug($name, $id = null)
    {
        // Generate base slug
        $slug = Str::slug($name);

        // Check if slug exists
        $query = Profil::where('slug', $slug);

        // Exclude current profile when updating
        if ($id) {
            $query->where('id', '!=', $id);
        }

        // If slug exists, append a unique identifier
        if ($query->exists()) {
            $count = 1;
            $originalSlug = $slug;

            // Keep incrementing until we find a unique slug
            while ($query->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }

        return $slug;
    }

    public function save()
    {
        $currentUser = Auth::user();

        // If user is not admin, force userId to be their own id
        if (!in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            $this->userId = $currentUser->id;
        }

        // Additional validation for one profile per user
        if (!$this->isEdit) {
            // Check if the selected user already has a profile
            $existingProfile = Profil::where('user_id', $this->userId)->first();
            if ($existingProfile) {
                $this->dispatch('error', 'User ini sudah memiliki profil masjid!');
                return;
            }
        } else {
            // When editing, make sure we're not changing to a user who already has a profile
            $existingProfile = Profil::where('user_id', $this->userId)
                ->where('id', '!=', $this->profileId)
                ->first();
            if ($existingProfile) {
                $this->dispatch('error', 'User ini sudah memiliki profil masjid!');
                return;
            }
        }

        // Custom validation for slug uniqueness
        $rules = $this->rules;
        if ($this->isEdit) {
            $rules['slug'] = 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:profils,slug,' . $this->profileId;
        }

        $this->validate($rules);

        try {
            if ($this->isEdit) {
                $profil = Profil::findOrFail($this->profileId);
                // Check if user has permission to edit this profile
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $profil->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit profil ini!');
                    return;
                }
            } else {
                // Allow non-admin users to create their own profile
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat profil untuk user lain!');
                    return;
                }
                $profil = new Profil();
            }

            $profil->user_id = $this->userId;
            $profil->name    = $this->name;
            $profil->slug    = $this->slug;
            $profil->address = $this->address;
            $profil->phone   = $this->phone;

            // Handle logo masjid upload
            if ($this->logo_masjid) {
                // Delete old logo if it exists
                if ($this->isEdit && $profil->logo_masjid && file_exists(public_path($profil->logo_masjid))) {
                    File::delete(public_path($profil->logo_masjid));
                }

                // Save new logo dengan resize otomatis
                $profil->logo_masjid = $this->saveProcessedImage($this->logo_masjid, 'masjid');
            } else {
                $profil->logo_masjid = $this->temp_logo;
            }

            // Handle logo pemerintah upload
            if ($this->logo_pemerintah) {
                // Delete old logo if it exists
                if ($this->isEdit && $profil->logo_pemerintah && file_exists(public_path($profil->logo_pemerintah))) {
                    File::delete(public_path($profil->logo_pemerintah));
                }

                // Save new logo dengan resize otomatis
                $profil->logo_pemerintah = $this->saveProcessedImage($this->logo_pemerintah, 'pemerintah');
            } else {
                $profil->logo_pemerintah = $this->temp_logo_pemerintah;
            }

            $profil->save();

            // Trigger event
            if ($this->isEdit) {
                event(new ContentUpdatedEvent($profil->slug, 'profil'));
            }

            $this->dispatch('success', $this->isEdit ? 'Profil masjid berhasil diperbarui!' : 'Profil masjid berhasil ditambahkan!');
            $this->showTable = true;

            // Only hide form and reset fields if user is admin
            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
                $this->showForm = false;
                $this->reset(
                    [
                        'profileId',
                        'userId',
                        'name',
                        'slug',
                        'address',
                        'phone',
                        'logo_masjid',
                        'temp_logo',
                        'logo_pemerintah',
                        'temp_logo_pemerintah'
                    ]
                );
            } else {
                // For regular users, keep the form visible and reload their data
                $this->showForm = true;
                $profil = Profil::where('user_id', Auth::id())->first();
                if ($profil) {
                    $this->profileId            = $profil->id;
                    $this->userId               = $profil->user_id;
                    $this->name                 = $profil->name;
                    $this->slug                 = $profil->slug;
                    $this->address              = $profil->address;
                    $this->phone                = $profil->phone;
                    $this->temp_logo            = $profil->logo_masjid;
                    $this->temp_logo_pemerintah = $profil->logo_pemerintah;
                    $this->isEdit               = true;
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;

        $profil = Profil::findOrFail($id);
        $this->deleteProfileId = $profil->id;
        $this->deleteProfileName = $profil->name;
    }

    public function destroyProfile()
    {
        try {
            $profil = Profil::findOrFail($this->deleteProfileId);

            if ($profil->logo_masjid && file_exists(public_path($profil->logo_masjid))) {
                File::delete(public_path($profil->logo_masjid));
            }

            if ($profil->logo_pemerintah && file_exists(public_path($profil->logo_pemerintah))) {
                File::delete(public_path($profil->logo_pemerintah));
            }

            $profil->delete();

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Profil masjid berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
