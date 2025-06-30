<?php

namespace App\Livewire\Audios;

use App\Models\Audios;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Audio extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Audio Masjid')]

    public $search;
    public $paginate;
    protected $paginationTheme = 'bootstrap';

    public $audioId;
    public $userId;
    public $audio1;
    public $tmp_audio1;
    public $audio2;
    public $tmp_audio2;
    public $audio3;
    public $tmp_audio3;
    public $status = true; // Default status aktif

    public $isEdit = false;
    public $showForm = false;
    public $deleteAudioId;
    public $deleteAudioName;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'audio1' => 'nullable|file|mimes:mp3,wav|max:10240', // Maks 10MB
        'audio2' => 'nullable|file|mimes:mp3,wav|max:10240',
        'audio3' => 'nullable|file|mimes:mp3,wav|max:10240',
        'status' => 'required|boolean',
    ];

    protected $messages = [
        'userId.required' => 'Admin Masjid wajib diisi',
        'userId.exists'   => 'Admin Masjid tidak ditemukan',
        'audio1.file'     => 'File harus berupa audio',
        'audio2.file'     => 'File harus berupa audio',
        'audio3.file'     => 'File harus berupa audio',
        'audio1.mimes'    => 'File harus berupa audio mp3 atau wav',
        'audio2.mimes'    => 'File harus berupa audio mp3 atau wav',
        'audio3.mimes'    => 'File harus berupa audio mp3 atau wav',
        'audio1.max'      => 'Ukuran file maksimal 10MB',
        'audio2.max'      => 'Ukuran file maksimal 10MB',
        'audio3.max'      => 'Ukuran file maksimal 10MB',
        'status.required' => 'Status wajib diisi',
        'status.boolean'  => 'Status harus berupa aktif atau tidak aktif',
    ];

    /**
     * Method untuk memeriksa konfigurasi Cloudinary
     */
    private function checkCloudinaryConfig()
    {
        $config = config('filesystems.disks.cloudinary');
        if (empty($config['key']) || empty($config['secret']) || empty($config['cloud'])) {
            Log::error('Konfigurasi Cloudinary tidak lengkap', [
                'cloud_name' => $config['cloud'] ?? 'null',
                'api_key' => $config['key'] ?? 'null',
                'api_secret' => $config['secret'] ? 'set' : 'null',
                'url' => $config['url'] ?? 'null',
                'prefix' => $config['prefix'] ?? 'null',
            ]);
            throw new \Exception('Konfigurasi Cloudinary tidak lengkap. Pastikan CLOUDINARY_KEY, CLOUDINARY_SECRET, dan CLOUDINARY_CLOUD_NAME diatur di file .env.');
        }

        Log::info('Konfigurasi Cloudinary valid', [
            'cloud_name' => $config['cloud'],
            'api_key' => $config['key'],
            'prefix' => $config['prefix'] ?? 'null',
        ]);
    }

    /**
     * Method untuk menyimpan audio ke Cloudinary
     */
    private function saveCloudinaryAudio($uploadedFile, $audioNumber)
    {
        try {
            // Periksa konfigurasi Cloudinary
            $this->checkCloudinaryConfig();

            // Validasi apakah file valid
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                Log::error('File audio tidak valid atau tidak ditemukan', [
                    'audio_number' => $audioNumber,
                    'file' => $uploadedFile ? $uploadedFile->getClientOriginalName() : 'null',
                ]);
                throw new \Exception('File audio tidak valid atau tidak ditemukan.');
            }

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $uploadedFile->getClientOriginalExtension();
            $fileName = time() . '_audio' . $audioNumber . '_' . $originalName . '.' . $extension;

            // Log informasi file untuk debugging
            Log::info('Mengupload audio ke Cloudinary', [
                'audio_number' => $audioNumber,
                'original_name' => $originalName,
                'file_name' => $fileName,
                'file_path' => $uploadedFile->getRealPath(),
                'file_size' => $uploadedFile->getSize(),
                'mime_type' => $uploadedFile->getMimeType(),
            ]);

            // Ambil prefix dari konfigurasi, default ke kosong jika tidak ada
            $prefix = config('filesystems.disks.cloudinary.prefix', '');
            $folder = $prefix ? $prefix : 'masjid_audios';

            // Upload ke Cloudinary menggunakan disk cloudinary
            $uploadResult = Storage::disk('cloudinary')->putFileAs(
                $folder,
                $uploadedFile,
                $fileName
            );

            // Dapatkan public_id dari hasil upload
            $publicId = $folder . '/' . $fileName;

            // Log hasil upload
            Log::info('Audio berhasil diupload ke Cloudinary', [
                'audio_number' => $audioNumber,
                'public_id' => $publicId,
                'upload_result' => $uploadResult,
            ]);

            return $publicId;
        } catch (\Exception $e) {
            Log::error('Gagal mengupload audio ke Cloudinary', [
                'audio_number' => $audioNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Gagal mengupload audio ke Cloudinary: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghasilkan URL Cloudinary dari public_id
     */
    private function generateCloudinaryUrl($publicId)
    {
        $cloudName = config('filesystems.disks.cloudinary.cloud');
        return "https://res.cloudinary.com/{$cloudName}/raw/upload/{$publicId}";
    }

    /**
     * Method untuk menghapus audio1 dan mereset input file
     */
    public function clearAudio1()
    {
        try {
            $this->checkCloudinaryConfig();

            if ($this->isEdit && $this->tmp_audio1) {
                // Ambil public_id dari tmp_audio1
                $publicId = $this->tmp_audio1;

                // Periksa apakah file ada sebelum menghapus
                if (Storage::disk('cloudinary')->exists($publicId)) {
                    $result = Storage::disk('cloudinary')->delete($publicId);
                    Log::info('Menghapus audio1 dari Cloudinary', [
                        'public_id' => $publicId,
                        'result' => $result,
                    ]);
                } else {
                    Log::warning('File audio1 tidak ditemukan di Cloudinary', [
                        'public_id' => $publicId,
                    ]);
                }

                if ($this->audioId) {
                    $audio = Audios::find($this->audioId);
                    if ($audio) {
                        $audio->audio1 = null;
                        $audio->save();
                    }
                }
            }

            $this->audio1 = null;
            $this->tmp_audio1 = null;
            $this->resetValidation(['audio1']);
            $this->dispatch('resetFileInput', ['inputName' => 'audio1']);
            $this->dispatch('success', 'Audio 1 berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus audio1', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus audio: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus audio2 dan mereset input file
     */
    public function clearAudio2()
    {
        try {
            $this->checkCloudinaryConfig();

            if ($this->isEdit && $this->tmp_audio2) {
                // Ambil public_id dari tmp_audio2
                $publicId = $this->tmp_audio2;

                // Periksa apakah file ada sebelum menghapus
                if (Storage::disk('cloudinary')->exists($publicId)) {
                    $result = Storage::disk('cloudinary')->delete($publicId);
                    Log::info('Menghapus audio2 dari Cloudinary', [
                        'public_id' => $publicId,
                        'result' => $result,
                    ]);
                } else {
                    Log::warning('File audio2 tidak ditemukan di Cloudinary', [
                        'public_id' => $publicId,
                    ]);
                }

                if ($this->audioId) {
                    $audio = Audios::find($this->audioId);
                    if ($audio) {
                        $audio->audio2 = null;
                        $audio->save();
                    }
                }
            }

            $this->audio2 = null;
            $this->tmp_audio2 = null;
            $this->resetValidation(['audio2']);
            $this->dispatch('resetFileInput', ['inputName' => 'audio2']);
            $this->dispatch('success', 'Audio 2 berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus audio2', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus audio: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghapus audio3 dan mereset input file
     */
    public function clearAudio3()
    {
        try {
            $this->checkCloudinaryConfig();

            if ($this->isEdit && $this->tmp_audio3) {
                // Ambil public_id dari tmp_audio3
                $publicId = $this->tmp_audio3;

                // Periksa apakah file ada sebelum menghapus
                if (Storage::disk('cloudinary')->exists($publicId)) {
                    $result = Storage::disk('cloudinary')->delete($publicId);
                    Log::info('Menghapus audio3 dari Cloudinary', [
                        'public_id' => $publicId,
                        'result' => $result,
                    ]);
                } else {
                    Log::warning('File audio3 tidak ditemukan di Cloudinary', [
                        'public_id' => $publicId,
                    ]);
                }

                if ($this->audioId) {
                    $audio = Audios::find($this->audioId);
                    if ($audio) {
                        $audio->audio3 = null;
                        $audio->save();
                    }
                }
            }

            $this->audio3 = null;
            $this->tmp_audio3 = null;
            $this->resetValidation(['audio3']);
            $this->dispatch('resetFileInput', ['inputName' => 'audio3']);
            $this->dispatch('success', 'Audio 3 berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus audio3', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus audio: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // Log konfigurasi Cloudinary saat inisialisasi
        try {
            $this->checkCloudinaryConfig();
        } catch (\Exception $e) {
            Log::error('Gagal memeriksa konfigurasi Cloudinary saat inisialisasi', [
                'error' => $e->getMessage(),
            ]);
        }

        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $audio = Audios::where('user_id', Auth::id())->first();
            $this->showForm = true;
            $this->userId = Auth::id();

            if ($audio) {
                $this->audioId    = $audio->id;
                $this->tmp_audio1 = $audio->audio1;
                $this->tmp_audio2 = $audio->audio2;
                $this->tmp_audio3 = $audio->audio3;
                $this->status     = $audio->status;
                $this->isEdit     = true;
            } else {
                $this->isEdit = false;
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->showForm = false;
        $this->resetValidation();
        $this->reset([
            'audioId',
            'userId',
            'audio1',
            'tmp_audio1',
            'audio2',
            'tmp_audio2',
            'audio3',
            'tmp_audio3',
            'status'
        ]);
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        $query = Audios::with('user')
            ->select('id', 'user_id', 'audio1', 'audio2', 'audio3', 'status');

        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            $query->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        $audioList = $query->orderBy('id', 'asc')->paginate($this->paginate);

        // Generate URLs for audio files
        foreach ($audioList as $audio) {
            $audio->audio1_url = $audio->audio1 ? $this->generateCloudinaryUrl($audio->audio1) : null;
            $audio->audio2_url = $audio->audio2 ? $this->generateCloudinaryUrl($audio->audio2) : null;
            $audio->audio3_url = $audio->audio3 ? $this->generateCloudinaryUrl($audio->audio3) : null;
        }

        $users = collect([]);
        if ($isAdmin) {
            $usersWithAudios = Audios::pluck('user_id')->toArray();
            $usersQuery = User::whereNotIn('id', $usersWithAudios);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }
            $users = $usersQuery->orderBy('name')->get();
        }

        return view('livewire.audios.audio', [
            'audioList' => $audioList,
            'users' => $users
        ]);
    }

    public function showAddForm()
    {
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah audio!');
            return;
        }

        $this->resetValidation();
        $this->reset([
            'audioId',
            'userId',
            'audio1',
            'tmp_audio1',
            'audio2',
            'tmp_audio2',
            'audio3',
            'tmp_audio3',
            'status'
        ]);

        $this->isEdit = false;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $audio = Audios::findOrFail($id);

        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $audio->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit audio ini!');
            return;
        }

        $this->audioId    = $audio->id;
        $this->userId     = $audio->user_id;
        $this->tmp_audio1 = $audio->audio1;
        $this->tmp_audio2 = $audio->audio2;
        $this->tmp_audio3 = $audio->audio3;
        $this->status     = $audio->status;

        $this->isEdit     = true;
        $this->showForm   = true;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->reset([
            'audioId',
            'userId',
            'audio1',
            'tmp_audio1',
            'audio2',
            'tmp_audio2',
            'audio3',
            'tmp_audio3',
            'status'
        ]);
    }

    public function save()
    {
        $currentUser = Auth::user();

        if (!in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            $this->userId = $currentUser->id;
        }

        if (!$this->isEdit) {
            $existingAudio = Audios::where('user_id', $this->userId)->first();
            if ($existingAudio) {
                $this->dispatch('error', 'User ini sudah memiliki audio!');
                return;
            }
        } else {
            $existingAudio = Audios::where('user_id', $this->userId)
                ->where('id', '!=', $this->audioId)
                ->first();
            if ($existingAudio) {
                $this->dispatch('error', 'User ini sudah memiliki audio!');
                return;
            }
        }

        $this->validate();

        try {
            // Periksa konfigurasi Cloudinary sebelum menyimpan
            $this->checkCloudinaryConfig();

            if ($this->isEdit) {
                $audio = Audios::findOrFail($this->audioId);
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $audio->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit audio ini!');
                    return;
                }
            } else {
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat audio untuk user lain!');
                    return;
                }
                $audio = new Audios();
            }

            $audio->user_id = $this->userId;
            $audio->status = $this->status;

            // Ambil prefix dari konfigurasi
            $prefix = config('filesystems.disks.cloudinary.prefix', '');
            $folder = $prefix ? $prefix : 'masjid_audios';

            // Handle audio1 upload
            if ($this->audio1) {
                if ($this->isEdit && $audio->audio1) {
                    $publicId = $audio->audio1;
                    if (Storage::disk('cloudinary')->exists($publicId)) {
                        $result = Storage::disk('cloudinary')->delete($publicId);
                        Log::info('Menghapus audio1 lama dari Cloudinary', [
                            'public_id' => $publicId,
                            'result' => $result,
                        ]);
                    } else {
                        Log::warning('File audio1 lama tidak ditemukan di Cloudinary', [
                            'public_id' => $publicId,
                        ]);
                    }
                }
                $audio->audio1 = $this->saveCloudinaryAudio($this->audio1, 1);
            } else {
                $audio->audio1 = $this->tmp_audio1;
            }

            // Handle audio2 upload
            if ($this->audio2) {
                if ($this->isEdit && $audio->audio2) {
                    $publicId = $audio->audio2;
                    if (Storage::disk('cloudinary')->exists($publicId)) {
                        $result = Storage::disk('cloudinary')->delete($publicId);
                        Log::info('Menghapus audio2 lama dari Cloudinary', [
                            'public_id' => $publicId,
                            'result' => $result,
                        ]);
                    } else {
                        Log::warning('File audio2 lama tidak ditemukan di Cloudinary', [
                            'public_id' => $publicId,
                        ]);
                    }
                }
                $audio->audio2 = $this->saveCloudinaryAudio($this->audio2, 2);
            } else {
                $audio->audio2 = $this->tmp_audio2;
            }

            // Handle audio3 upload
            if ($this->audio3) {
                if ($this->isEdit && $audio->audio3) {
                    $publicId = $audio->audio3;
                    if (Storage::disk('cloudinary')->exists($publicId)) {
                        $result = Storage::disk('cloudinary')->delete($publicId);
                        Log::info('Menghapus audio3 lama dari Cloudinary', [
                            'public_id' => $publicId,
                            'result' => $result,
                        ]);
                    } else {
                        Log::warning('File audio3 lama tidak ditemukan di Cloudinary', [
                            'public_id' => $publicId,
                        ]);
                    }
                }
                $audio->audio3 = $this->saveCloudinaryAudio($this->audio3, 3);
            } else {
                $audio->audio3 = $this->tmp_audio3;
            }

            $audio->save();

            $this->dispatch('success', $this->isEdit ? 'Audio berhasil diubah!' : 'Audio berhasil ditambahkan!');

            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
                $this->showForm = false;
                $this->reset([
                    'audioId',
                    'userId',
                    'tmp_audio1',
                    'tmp_audio2',
                    'tmp_audio3',
                    'status'
                ]);
            } else {
                $this->showForm = true;
                $audio = Audios::where('user_id', Auth::id())->first();
                if ($audio) {
                    $this->audioId    = $audio->id;
                    $this->userId     = $audio->user_id;
                    $this->tmp_audio1 = $audio->audio1;
                    $this->tmp_audio2 = $audio->audio2;
                    $this->tmp_audio3 = $audio->audio3;
                    $this->status     = $audio->status;
                    $this->isEdit     = true;
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan audio', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan audio: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;
        $audio = Audios::findOrFail($id);
        $this->deleteAudioId = $audio->id;
        $this->deleteAudioName = $audio->user->name;
    }

    public function destroyAudio()
    {
        try {
            $this->checkCloudinaryConfig();

            $audio = Audios::findOrFail($this->deleteAudioId);

            // Ambil prefix dari konfigurasi
            $prefix = config('filesystems.disks.cloudinary.prefix', '');
            $folder = $prefix ? $prefix : 'masjid_audios';

            if ($audio->audio1) {
                $publicId = $audio->audio1;
                if (Storage::disk('cloudinary')->exists($publicId)) {
                    $result = Storage::disk('cloudinary')->delete($publicId);
                    Log::info('Menghapus audio1 dari Cloudinary', [
                        'public_id' => $publicId,
                        'result' => $result,
                    ]);
                } else {
                    Log::warning('File audio1 tidak ditemukan di Cloudinary', [
                        'public_id' => $publicId,
                    ]);
                }
            }

            if ($audio->audio2) {
                $publicId = $audio->audio2;
                if (Storage::disk('cloudinary')->exists($publicId)) {
                    $result = Storage::disk('cloudinary')->delete($publicId);
                    Log::info('Menghapus audio2 dari Cloudinary', [
                        'public_id' => $publicId,
                        'result' => $result,
                    ]);
                } else {
                    Log::warning('File audio2 tidak ditemukan di Cloudinary', [
                        'public_id' => $publicId,
                    ]);
                }
            }

            if ($audio->audio3) {
                $publicId = $audio->audio3;
                if (Storage::disk('cloudinary')->exists($publicId)) {
                    $result = Storage::disk('cloudinary')->delete($publicId);
                    Log::info('Menghapus audio3 dari Cloudinary', [
                        'public_id' => $publicId,
                        'result' => $result,
                    ]);
                } else {
                    Log::warning('File audio3 tidak ditemukan di Cloudinary', [
                        'public_id' => $publicId,
                    ]);
                }
            }

            $audio->delete();

            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Audio berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus audio', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
