<?php

namespace App\Livewire\AdzanAudio;

use App\Models\AdzanAudio as AdzanAudioModel;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;

class AdzanAudio extends Component
{
    use WithPagination, WithFileUploads;

    #[Title('Audio Adzan')]

    public $search;
    public $paginate;
    protected $paginationTheme = 'bootstrap';

    public $adzanAudioId;
    public $userId;
    public $audioadzan;
    public $tmp_audioadzan;
    public $adzanshubuh;
    public $tmp_adzanshubuh;
    public $status = 1; // Default status aktif

    public $isEdit = false;
    public $showForm = false;
    public $deleteAdzanAudioId;
    public $deleteAdzanAudioName;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'audioadzan' => 'nullable|file|mimes:mp3,wav|max:10240', // Maks 10MB
        'adzanshubuh' => 'nullable|file|mimes:mp3,wav|max:10240', // Maks 10MB
        'status' => 'required|boolean',
    ];

    protected $messages = [
        'userId.required' => 'Admin Masjid wajib diisi',
        'userId.exists'   => 'Admin Masjid tidak ditemukan',
        'audioadzan.file'     => 'File harus berupa audio',
        'audioadzan.mimes'    => 'File harus berupa audio mp3 atau wav',
        'audioadzan.max'      => 'Ukuran file maksimal 10MB',
        'adzanshubuh.file'    => 'File harus berupa audio',
        'adzanshubuh.mimes'   => 'File harus berupa audio mp3 atau wav',
        'adzanshubuh.max'     => 'Ukuran file maksimal 10MB',
        'status.required' => 'Status wajib diisi',
        'status.boolean'  => 'Status harus berupa aktif atau tidak aktif',
    ];

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

    private function saveCloudinaryAudio($uploadedFile, $fieldName = 'audioadzan')
    {
        try {
            $this->checkCloudinaryConfig();

            if (!$uploadedFile || !$uploadedFile->isValid()) {
                throw new \Exception('File audio tidak valid atau tidak ditemukan.');
            }

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $uploadedFile->getClientOriginalExtension();
            $slugName = Str::slug($originalName);
            $timestamp = time();

            $folder = config('filesystems.disks.cloudinary.prefix', 'masjid_adzan_audios');
            $publicId = "{$timestamp}_{$fieldName}_{$slugName}";

            $result = Cloudinary::uploadApi()->upload($uploadedFile->getRealPath(), [
                'resource_type' => 'video',
                'folder' => 'Masjid Adzan Audios/' . User::find($this->userId)->name,
                'public_id' => $publicId,
                'overwrite' => true,
            ]);

            $fieldLabel = $fieldName === 'adzanshubuh' ? 'Audio Adzan Shubuh' : 'Audio Adzan';
            Log::info("✅ {$fieldLabel} berhasil diupload ke Cloudinary", [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
            ]);

            return $result['public_id'];
        } catch (\Exception $e) {
            $fieldLabel = $fieldName === 'adzanshubuh' ? 'audio adzan shubuh' : 'audio adzan';
            Log::error("❌ Gagal mengupload {$fieldLabel} ke Cloudinary", [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Upload gagal: ' . $e->getMessage());
        }
    }

    public function generateCloudinaryUrl($publicId)
    {
        $cloudName = config('filesystems.disks.cloudinary.cloud');
        return "https://res.cloudinary.com/{$cloudName}/video/upload/{$publicId}";
    }

    public function clearAudioAdzan()
    {
        try {
            $this->checkCloudinaryConfig();

            if ($this->isEdit && $this->tmp_audioadzan) {
                $deleteCloudinaryFile = function ($publicId, $field) {
                    if (!$publicId) {
                        return true;
                    }

                    try {
                        $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'raw']);
                        if ($result['result'] === 'ok') {
                            Log::info("Menghapus {$field} dari Cloudinary", [
                                'public_id' => $publicId,
                                'result' => $result,
                            ]);
                            return true;
                        }

                        $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'video']);
                        if ($result['result'] === 'ok') {
                            Log::info("Menghapus {$field} dari Cloudinary sebagai video", [
                                'public_id' => $publicId,
                                'result' => $result,
                            ]);
                            return true;
                        }

                        Log::warning("Gagal menghapus {$field} dari Cloudinary", [
                            'public_id' => $publicId,
                            'result' => $result,
                        ]);
                        return false;
                    } catch (\Exception $ex) {
                        Log::error("Gagal menghapus {$field}", [
                            'public_id' => $publicId,
                            'error' => $ex->getMessage(),
                        ]);
                        return false;
                    }
                };

                $publicId = $this->tmp_audioadzan;
                if (!$deleteCloudinaryFile($publicId, 'audioadzan')) {
                    $this->dispatch('error', 'Gagal menghapus audio adzan dari Cloudinary.');
                    return;
                }

                if ($this->adzanAudioId) {
                    $adzanAudio = AdzanAudioModel::find($this->adzanAudioId);
                    if ($adzanAudio) {
                        $adzanAudio->audioadzan = null;
                        $adzanAudio->save();
                    }
                }
            }

            $this->audioadzan = null;
            $this->tmp_audioadzan = null;
            $this->resetValidation(['audioadzan']);
            $this->dispatch('resetFileInput', ['inputName' => 'audioadzan']);
            $this->dispatch('success', 'Audio Adzan berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus audio adzan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus audio: ' . $e->getMessage());
        }
    }
    
    public function clearAdzanShubuh()
    {
        try {
            $this->checkCloudinaryConfig();

            if ($this->isEdit && $this->tmp_adzanshubuh) {
                $deleteCloudinaryFile = function ($publicId, $field) {
                    if (!$publicId) {
                        return true;
                    }

                    try {
                        $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'raw']);
                        if ($result['result'] === 'ok') {
                            Log::info("Menghapus {$field} dari Cloudinary", [
                                'public_id' => $publicId,
                                'result' => $result,
                            ]);
                            return true;
                        }

                        $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'video']);
                        if ($result['result'] === 'ok') {
                            Log::info("Menghapus {$field} dari Cloudinary sebagai video", [
                                'public_id' => $publicId,
                                'result' => $result,
                            ]);
                            return true;
                        }

                        Log::warning("Gagal menghapus {$field} dari Cloudinary", [
                            'public_id' => $publicId,
                            'result' => $result,
                        ]);
                        return false;
                    } catch (\Exception $ex) {
                        Log::error("Gagal menghapus {$field}", [
                            'public_id' => $publicId,
                            'error' => $ex->getMessage(),
                        ]);
                        return false;
                    }
                };

                $publicId = $this->tmp_adzanshubuh;
                if (!$deleteCloudinaryFile($publicId, 'adzanshubuh')) {
                    $this->dispatch('error', 'Gagal menghapus audio adzan shubuh dari Cloudinary.');
                    return;
                }

                if ($this->adzanAudioId) {
                    $adzanAudio = AdzanAudioModel::find($this->adzanAudioId);
                    if ($adzanAudio) {
                        $adzanAudio->adzanshubuh = null;
                        $adzanAudio->save();
                    }
                }
            }

            $this->adzanshubuh = null;
            $this->tmp_adzanshubuh = null;
            $this->resetValidation(['adzanshubuh']);
            $this->dispatch('resetFileInput', ['inputName' => 'adzanshubuh']);
            $this->dispatch('success', 'Audio Adzan Shubuh berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus audio adzan shubuh', [
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

        try {
            $this->checkCloudinaryConfig();
        } catch (\Exception $e) {
            Log::error('Gagal memeriksa konfigurasi Cloudinary saat inisialisasi', [
                'error' => $e->getMessage(),
            ]);
        }

        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $adzanAudio = AdzanAudioModel::where('user_id', Auth::id())->first();
            $this->showForm = true;
            $this->userId = Auth::id();

            if ($adzanAudio) {
                $this->adzanAudioId    = $adzanAudio->id;
                $this->tmp_audioadzan = $adzanAudio->audioadzan;
                $this->tmp_adzanshubuh = $adzanAudio->adzanshubuh;
                $this->status     = $adzanAudio->status ? 1 : 0;
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
            'adzanAudioId',
            'userId',
            'audioadzan',
            'tmp_audioadzan',
            'adzanshubuh',
            'tmp_adzanshubuh',
            'status'
        ]);
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        $query = AdzanAudioModel::with('user')
            ->select('id', 'user_id', 'audioadzan', 'adzanshubuh', 'status', 'created_at');

        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            $query->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        $adzanAudioList = $query->orderBy('id', 'asc')->paginate($this->paginate);

        foreach ($adzanAudioList as $adzanAudio) {
            $adzanAudio->audioadzan_url = $adzanAudio->audioadzan ? $this->generateCloudinaryUrl($adzanAudio->audioadzan) : null;
            $adzanAudio->adzanshubuh_url = $adzanAudio->adzanshubuh ? $this->generateCloudinaryUrl($adzanAudio->adzanshubuh) : null;
        }

        $users = collect([]);
        if ($isAdmin) {
            $usersWithAudios = AdzanAudioModel::pluck('user_id')->toArray();
            $usersQuery = User::whereNotIn('id', $usersWithAudios);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }
            $users = $usersQuery->orderBy('name')->get();
        }

        return view('livewire.adzan-audio.adzan-audio', [
            'adzanAudioList' => $adzanAudioList,
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
            'adzanAudioId',
            'userId',
            'audioadzan',
            'tmp_audioadzan',
            'adzanshubuh',
            'tmp_adzanshubuh',
            'status'
        ]);

        $this->isEdit = false;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $adzanAudio = AdzanAudioModel::findOrFail($id);

        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $adzanAudio->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit audio ini!');
            return;
        }

        $this->adzanAudioId    = $adzanAudio->id;
        $this->userId     = $adzanAudio->user_id;
        $this->tmp_audioadzan = $adzanAudio->audioadzan;
        $this->tmp_adzanshubuh = $adzanAudio->adzanshubuh;
        $this->status     = $adzanAudio->status ? 1 : 0;

        $this->isEdit     = true;
        $this->showForm   = true;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->reset([
            'adzanAudioId',
            'userId',
            'audioadzan',
            'tmp_audioadzan',
            'adzanshubuh',
            'tmp_adzanshubuh',
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
            $existingAudio = AdzanAudioModel::where('user_id', $this->userId)->first();
            if ($existingAudio) {
                $this->dispatch('error', 'User ini sudah memiliki audio adzan!');
                return;
            }
        } else {
            $existingAudio = AdzanAudioModel::where('user_id', $this->userId)
                ->where('id', '!=', $this->adzanAudioId)
                ->first();
            if ($existingAudio) {
                $this->dispatch('error', 'User ini sudah memiliki audio adzan!');
                return;
            }
        }

        $this->validate();

        try {
            $this->checkCloudinaryConfig();

            if ($this->isEdit) {
                $adzanAudio = AdzanAudioModel::findOrFail($this->adzanAudioId);
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $adzanAudio->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit audio ini!');
                    return;
                }
            } else {
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat audio untuk user lain!');
                    return;
                }
                $adzanAudio = new AdzanAudioModel();
            }

            $adzanAudio->user_id = $this->userId;
            $adzanAudio->status = $this->status ? 1 : 0;

            $deleteCloudinaryFile = function ($publicId, $field) {
                if (!$publicId) {
                    return true;
                }

                try {
                    $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'raw']);
                    if ($result['result'] === 'ok') {
                        Log::info("Menghapus {$field} dari Cloudinary", [
                            'public_id' => $publicId,
                            'result' => $result,
                        ]);
                        return true;
                    }

                    $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'video']);
                    if ($result['result'] === 'ok') {
                        Log::info("Menghapus {$field} dari Cloudinary sebagai video", [
                            'public_id' => $publicId,
                            'result' => $result,
                        ]);
                        return true;
                    }

                    Log::warning("Gagal menghapus {$field} dari Cloudinary", [
                        'public_id' => $publicId,
                        'result' => $result,
                    ]);
                    return false;
                } catch (\Exception $ex) {
                    Log::error("Gagal menghapus {$field}", [
                        'public_id' => $publicId,
                        'error' => $ex->getMessage(),
                    ]);
                    return false;
                }
            };

            if ($this->audioadzan) {
                if ($this->isEdit && $adzanAudio->audioadzan) {
                    $publicId = $adzanAudio->audioadzan;
                    if (!$deleteCloudinaryFile($publicId, 'audioadzan')) {
                        $this->dispatch('error', 'Gagal menghapus audio adzan lama dari Cloudinary.');
                        return;
                    }
                }
                $adzanAudio->audioadzan = $this->saveCloudinaryAudio($this->audioadzan, 'audioadzan');
            } else {
                $adzanAudio->audioadzan = $this->tmp_audioadzan;
            }
            
            if ($this->adzanshubuh) {
                if ($this->isEdit && $adzanAudio->adzanshubuh) {
                    $publicId = $adzanAudio->adzanshubuh;
                    if (!$deleteCloudinaryFile($publicId, 'adzanshubuh')) {
                        $this->dispatch('error', 'Gagal menghapus audio adzan shubuh lama dari Cloudinary.');
                        return;
                    }
                }
                $adzanAudio->adzanshubuh = $this->saveCloudinaryAudio($this->adzanshubuh, 'adzanshubuh');
            } else {
                $adzanAudio->adzanshubuh = $this->tmp_adzanshubuh;
            }

            $adzanAudio->save();

            $this->dispatch('success', $this->isEdit ? 'Audio Adzan berhasil diubah!' : 'Audio Adzan berhasil ditambahkan!');
            $this->dispatch('resetFileInput', ['inputName' => 'audioadzan']);
            $this->dispatch('resetFileInput', ['inputName' => 'adzanshubuh']);
            $this->dispatch('fileSelected', ['inputName' => 'audioadzan']);
            $this->dispatch('fileSelected', ['inputName' => 'adzanshubuh']);

            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
                $this->showForm = false;
                $this->reset([
                    'adzanAudioId',
                    'userId',
                    'audioadzan',
                    'tmp_audioadzan',
                    'adzanshubuh',
                    'tmp_adzanshubuh',
                    'status'
                ]);
            } else {
                $this->showForm = true;
                $adzanAudio = AdzanAudioModel::where('user_id', Auth::id())->first();
                if ($adzanAudio) {
                    $this->adzanAudioId    = $adzanAudio->id;
                    $this->userId     = $adzanAudio->user_id;
                    $this->tmp_audioadzan = $adzanAudio->audioadzan;
                    $this->tmp_adzanshubuh = $adzanAudio->adzanshubuh;
                    $this->status     = $adzanAudio->status ? 1 : 0;
                    $this->isEdit     = true;
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan audio adzan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menghapus audio!');
            return;
        }
        
        $adzanAudio = AdzanAudioModel::findOrFail($id);
        $this->deleteAdzanAudioId = $adzanAudio->id;
        $this->deleteAdzanAudioName = $adzanAudio->user->name;
    }

    public function destroyAdzanAudio()
    {
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menghapus audio!');
            return;
        }

        try {
            $this->checkCloudinaryConfig();

            $adzanAudio = AdzanAudioModel::findOrFail($this->deleteAdzanAudioId);
            $allDeleted = true;

            // Hapus audioadzan
            $publicId = $adzanAudio->audioadzan;

            if ($publicId) {
                try {
                    // Coba hapus sebagai RAW
                    $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'raw']);

                    if ($result['result'] !== 'ok') {
                        // Jika gagal sebagai RAW, coba sebagai VIDEO
                        $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'video']);
                        if ($result['result'] !== 'ok') {
                            $allDeleted = false;
                            Log::warning("Gagal menghapus file dari Cloudinary", [
                                'field' => 'audioadzan',
                                'public_id' => $publicId,
                                'result' => $result
                            ]);
                        }
                    }
                } catch (\Exception $ex) {
                    $allDeleted = false;
                    Log::error("Gagal menghapus audioadzan", [
                        'public_id' => $publicId,
                        'error' => $ex->getMessage()
                    ]);
                }
            }
            
            // Hapus adzanshubuh
            $publicId = $adzanAudio->adzanshubuh;

            if ($publicId) {
                try {
                    // Coba hapus sebagai RAW
                    $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'raw']);

                    if ($result['result'] !== 'ok') {
                        // Jika gagal sebagai RAW, coba sebagai VIDEO
                        $result = Cloudinary::uploadApi()->destroy($publicId, ['resource_type' => 'video']);
                        if ($result['result'] !== 'ok') {
                            $allDeleted = false;
                            Log::warning("Gagal menghapus file dari Cloudinary", [
                                'field' => 'adzanshubuh',
                                'public_id' => $publicId,
                                'result' => $result
                            ]);
                        }
                    }
                } catch (\Exception $ex) {
                    $allDeleted = false;
                    Log::error("Gagal menghapus adzanshubuh", [
                        'public_id' => $publicId,
                        'error' => $ex->getMessage()
                    ]);
                }
            }

            if ($allDeleted) {
                $adzanAudio->delete();
                $this->dispatch('closeDeleteModal');
                $this->dispatch('success', 'Audio Adzan berhasil dihapus!');
                $this->reset(['deleteAdzanAudioId', 'deleteAdzanAudioName']);
            } else {
                $this->dispatch('closeDeleteModal');
                $this->dispatch('error', 'File gagal dihapus dari Cloudinary. Data tidak dihapus.');
            }
        } catch (\Exception $e) {
            Log::error('❌ Gagal menjalankan destroyAdzanAudio()', [
                'audio_id' => $this->deleteAdzanAudioId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('error', 'Terjadi kesalahan saat menghapus audio: ' . $e->getMessage());
        }
    }
}