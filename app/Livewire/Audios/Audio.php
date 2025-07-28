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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
    public $status = 1; // Default status aktif

    // Upload validation success indicators
    public $audio1Uploaded = false;
    public $audio2Uploaded = false;
    public $audio3Uploaded = false;

    public $isEdit = false;
    public $showForm = false;
    public $deleteAudioId;
    public $deleteAudioName;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'audio1' => 'nullable|file|mimes:mp3,wav|max:51200', // Maks 50MB
        'audio2' => 'nullable|file|mimes:mp3,wav|max:51200', // Maks 50MB
        'audio3' => 'nullable|file|mimes:mp3,wav|max:51200', // Maks 50MB
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
        'audio1.max'      => 'Ukuran file maksimal 50MB',
        'audio2.max'      => 'Ukuran file maksimal 50MB',
        'audio3.max'      => 'Ukuran file maksimal 50MB',
        'status.required' => 'Status wajib diisi',
        'status.boolean'  => 'Status harus berupa aktif atau tidak aktif',
    ];

    /**
     * Method untuk memeriksa direktori penyimpanan lokal
     */
    private function checkLocalStorageConfig()
    {
        $audioPath = public_path('sounds/musik');
        
        if (!file_exists($audioPath)) {
            mkdir($audioPath, 0755, true);
            Log::info('Direktori audio berhasil dibuat', ['path' => $audioPath]);
        }
        
        if (!is_writable($audioPath)) {
            throw new \Exception('Direktori audio tidak dapat ditulis: ' . $audioPath);
        }
        
        Log::info('Konfigurasi penyimpanan lokal valid', ['path' => $audioPath]);
    }

    /**
     * Method untuk menyimpan audio secara lokal
     */
    private function saveLocalAudio($uploadedFile, $audioNumber)
    {
        try {
            $this->checkLocalStorageConfig();

            if (!$uploadedFile || !$uploadedFile->isValid()) {
                throw new \Exception('File audio tidak valid atau tidak ditemukan.');
            }

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $uploadedFile->getClientOriginalExtension();
            $slugName = Str::slug($originalName);
            $timestamp = time();

            // Generate nama file
            $fileName = "{$timestamp}_audio{$audioNumber}_{$slugName}.{$extension}";
            $filePath = public_path('sounds/musik/' . $fileName);

            // Pastikan directory ada
            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Pindahkan file ke direktori tujuan menggunakan Livewire method
            $uploadedFile->storeAs('', $fileName, 'public_sounds_musik');
            
            // Verifikasi file berhasil dipindahkan
            if (!file_exists($filePath)) {
                throw new \Exception('Gagal memindahkan file audio.');
            }

            Log::info('✅ Audio berhasil disimpan secara lokal', [
                'audio_number' => $audioNumber,
                'file_name' => $fileName,
                'file_path' => $filePath,
            ]);

            return '/sounds/musik/' . $fileName; // ← Simpan path relatif ke database
        } catch (\Exception $e) {
            Log::error('❌ Gagal menyimpan audio secara lokal', [
                'audio_number' => $audioNumber,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Upload gagal: ' . $e->getMessage());
        }
    }



    /**
     * Method untuk menghasilkan URL lokal dari path file
     */
    public function generateLocalUrl($filePath)
    {
        if (!$filePath) {
            return null;
        }
        
        // Jika sudah berupa URL lengkap, return as is
        if (str_starts_with($filePath, 'http')) {
            return $filePath;
        }
        
        // Jika path dimulai dengan /, hapus leading slash untuk menghindari double slash
        $cleanPath = ltrim($filePath, '/');
        
        return url($cleanPath);
    }


    /**
     * Method untuk menghapus audio1 dan mereset input file
     */
    public function clearAudio1()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_audio1, hapus file fisiknya
            if ($this->isEdit && $this->tmp_audio1) {
                $filePath = public_path($this->tmp_audio1);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->audioId) {
                    $audio = Audios::find($this->audioId);
                    if ($audio) {
                        $audio->audio1 = null;
                        $audio->save();
                    }
                }
            }

            // Reset property audio1 (file yang diupload)
            $this->audio1 = null;

            // Reset property tmp_audio1 (audio yang sudah tersimpan)
            $this->tmp_audio1 = null;

            // Reset validation error untuk audio1
            $this->resetValidation(['audio1']);

            // Reset upload indicator
            $this->audio1Uploaded = false;

            // Dispatch event untuk reset input file di browser
            $this->dispatch('resetFileInput', ['inputName' => 'audio1']);

            $this->dispatch('success', 'Audio 1 berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus audio: ' . $e->getMessage());
        }
    }


    /**
     * Method untuk menghapus audio2 dan mereset input file
     */
    public function clearAudio2()
    {
        try {
            // Jika sedang edit dan ada file lama di tmp_audio2, hapus file fisiknya
            if ($this->isEdit && $this->tmp_audio2) {
                $filePath = public_path($this->tmp_audio2);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->audioId) {
                    $audio = Audios::find($this->audioId);
                    if ($audio) {
                        $audio->audio2 = null;
                        $audio->save();
                    }
                }
            }

            // Reset property audio2 (file yang diupload)
            $this->audio2 = null;

            // Reset property tmp_audio2 (audio yang sudah tersimpan)
            $this->tmp_audio2 = null;

            // Reset upload indicator
            $this->audio2Uploaded = false;

            // Reset validation error untuk audio2
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
            // Jika sedang edit dan ada file lama di tmp_audio3, hapus file fisiknya
            if ($this->isEdit && $this->tmp_audio3) {
                $filePath = public_path($this->tmp_audio3);
                if (file_exists($filePath)) {
                    File::delete($filePath);
                }

                // Update database untuk menghapus referensi file
                if ($this->audioId) {
                    $audio = Audios::find($this->audioId);
                    if ($audio) {
                        $audio->audio3 = null;
                        $audio->save();
                    }
                }
            }

            // Reset property audio3 (file yang diupload)
            $this->audio3 = null;

            // Reset property tmp_audio3 (audio yang sudah tersimpan)
            $this->tmp_audio3 = null;

            // Reset upload indicator
            $this->audio3Uploaded = false;

            // Reset validation error untuk audio3
            $this->resetValidation(['audio3']);

            // Reset file input di frontend
            $this->dispatch('resetFileInput', ['inputName' => 'audio3']);

            // Dispatch success message
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

        try {
            $this->checkLocalStorageConfig();
        } catch (\Exception $e) {
            Log::error('Gagal memeriksa konfigurasi penyimpanan lokal saat inisialisasi', [
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
                $this->status     = $audio->status ? 1 : 0;
                $this->isEdit     = true;
                $this->audio1Uploaded = false;
                $this->audio2Uploaded = false;
                $this->audio3Uploaded = false;
            } else {
                $this->isEdit = false;
                $this->audio1Uploaded = false;
                $this->audio2Uploaded = false;
                $this->audio3Uploaded = false;
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
            'status',
            'audio1Uploaded',
            'audio2Uploaded',
            'audio3Uploaded'
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
            $audio->audio1_url = $audio->audio1 ? $this->generateLocalUrl($audio->audio1) : null;
            $audio->audio2_url = $audio->audio2 ? $this->generateLocalUrl($audio->audio2) : null;
            $audio->audio3_url = $audio->audio3 ? $this->generateLocalUrl($audio->audio3) : null;
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
            'status',
            'audio1Uploaded',
            'audio2Uploaded',
            'audio3Uploaded'
        ]);

        $this->isEdit = false;
        $this->showForm = true;
        $this->status = 0;
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
        $this->status     = $audio->status ? 1 : 0;
        $this->audio1Uploaded = false;
        $this->audio2Uploaded = false;
        $this->audio3Uploaded = false;

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
            'status',
            'audio1Uploaded',
            'audio2Uploaded',
            'audio3Uploaded'
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
            // Periksa konfigurasi penyimpanan lokal sebelum menyimpan
            $this->checkLocalStorageConfig();

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
            $audio->status = $this->status ? 1 : 0;

            // Fungsi untuk menghapus file lokal
            $deleteLocalFile = function ($filePath, $field) {
                if (!$filePath) {
                    return true; // Tidak ada file untuk dihapus
                }

                try {
                    $fullPath = public_path($filePath);
                    if (file_exists($fullPath)) {
                        File::delete($fullPath);
                        Log::info("Menghapus {$field} dari penyimpanan lokal", [
                            'file_path' => $filePath,
                        ]);
                    }
                    return true;
                } catch (\Exception $ex) {
                    Log::error("Gagal menghapus {$field}", [
                        'file_path' => $filePath,
                        'error' => $ex->getMessage(),
                    ]);
                    return false;
                }
            };

            // Handle audio1 upload
            if ($this->audio1) {
                if ($this->isEdit && $audio->audio1) {
                    $filePath = $audio->audio1; // Gunakan file path langsung dari database
                    if (!$deleteLocalFile($filePath, 'audio1')) {
                        $this->dispatch('error', 'Gagal menghapus audio1 lama dari penyimpanan lokal.');
                        return;
                    }
                }
                $audio->audio1 = $this->saveLocalAudio($this->audio1, 1);
                $this->audio1Uploaded = true;
            } else {
                $audio->audio1 = $this->tmp_audio1;
            }

            // Handle audio2 upload
            if ($this->audio2) {
                if ($this->isEdit && $audio->audio2) {
                    $filePath = $audio->audio2; // Gunakan file path langsung dari database
                    if (!$deleteLocalFile($filePath, 'audio2')) {
                        $this->dispatch('error', 'Gagal menghapus audio2 lama dari penyimpanan lokal.');
                        return;
                    }
                }
                $audio->audio2 = $this->saveLocalAudio($this->audio2, 2);
                $this->audio2Uploaded = true;
            } else {
                $audio->audio2 = $this->tmp_audio2;
            }

            // Handle audio3 upload
            if ($this->audio3) {
                if ($this->isEdit && $audio->audio3) {
                    $filePath = $audio->audio3; // Gunakan file path langsung dari database
                    if (!$deleteLocalFile($filePath, 'audio3')) {
                        $this->dispatch('error', 'Gagal menghapus audio3 lama dari penyimpanan lokal.');
                        return;
                    }
                }
                $audio->audio3 = $this->saveLocalAudio($this->audio3, 3);
                $this->audio3Uploaded = true;
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
                    'audio1',
                    'audio2',
                    'audio3',
                    'tmp_audio1',
                    'tmp_audio2',
                    'tmp_audio3',
                    'status',
                    'audio1Uploaded',
                    'audio2Uploaded',
                    'audio3Uploaded'
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
                    $this->status     = $audio->status ? 1 : 0;
                    $this->isEdit     = true;

                    $this->audio1 = null;
                    $this->audio2 = null;
                    $this->audio3 = null;
                    $this->audio1Uploaded = false;
                    $this->audio2Uploaded = false;
                    $this->audio3Uploaded = false;
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

    public function updated($propertyName)
    {
        if ($propertyName === 'audio1') {
            Log::debug('audio1 updated', [
                'audio1' => $this->audio1 ? get_class($this->audio1) : null,
                'tmp_audio1' => $this->tmp_audio1,
            ]);
            if ($this->audio1) {
                $this->validateOnly('audio1');
                // Set success indicator after validation passes
                if (!$this->getErrorBag()->has('audio1')) {
                    $this->audio1Uploaded = true;
                }
            } else {
                $this->audio1Uploaded = false;
            }
        }
        if ($propertyName === 'audio2') {
            Log::debug('audio2 updated', [
                'audio2' => $this->audio2 ? get_class($this->audio2) : null,
                'tmp_audio2' => $this->tmp_audio2,
            ]);
            if ($this->audio2) {
                $this->validateOnly('audio2');
                // Set success indicator after validation passes
                if (!$this->getErrorBag()->has('audio2')) {
                    $this->audio2Uploaded = true;
                }
            } else {
                $this->audio2Uploaded = false;
            }
        }
        if ($propertyName === 'audio3') {
            Log::debug('audio3 updated', [
                'audio3' => $this->audio3 ? get_class($this->audio3) : null,
                'tmp_audio3' => $this->tmp_audio3,
            ]);
            if ($this->audio3) {
                $this->validateOnly('audio3');
                // Set success indicator after validation passes
                if (!$this->getErrorBag()->has('audio3')) {
                    $this->audio3Uploaded = true;
                }
            } else {
                $this->audio3Uploaded = false;
            }
        }
    }

    public function refreshAudio($data)
    {
        $this->dispatch('refreshAudio', inputName: $data['inputName']);
    }

    public function fileSelected($data)
    {
        // Log untuk debugging
        Log::debug('fileSelected called', [
            'inputName' => $data['inputName'],
            'audio1' => $this->audio1 ? get_class($this->audio1) : null,
            'audio2' => $this->audio2 ? get_class($this->audio2) : null,
            'audio3' => $this->audio3 ? get_class($this->audio3) : null,
        ]);

        // Memaksa render ulang dengan mengirim event kembali ke JavaScript
        $this->dispatch('fileSelected', inputName: $data['inputName']);
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
            $audio = Audios::findOrFail($this->deleteAudioId);
            $audioFields = ['audio1', 'audio2', 'audio3'];
            $allDeleted = true;

            foreach ($audioFields as $field) {
                $filePath = $audio->$field;

                if (!$filePath) {
                    continue;
                }

                try {
                    $fullPath = public_path($filePath);
                    if (file_exists($fullPath)) {
                        File::delete($fullPath);
                        Log::info("Berhasil menghapus file {$field} dari penyimpanan lokal", [
                            'field' => $field,
                            'file_path' => $filePath,
                        ]);
                    }
                } catch (\Exception $ex) {
                    $allDeleted = false;
                    Log::error("Gagal menghapus {$field}", [
                        'file_path' => $filePath,
                        'error' => $ex->getMessage()
                    ]);
                }
            }

            if ($allDeleted) {
                $audio->delete();
                $this->dispatch('closeDeleteModal');
                $this->dispatch('success', 'Audio dan data berhasil dihapus.');
            } else {
                $this->dispatch('closeDeleteModal');
                $this->dispatch('error', 'Sebagian file gagal dihapus dari penyimpanan lokal. Data tidak dihapus.');
            }
        } catch (\Exception $e) {
            Log::error('❌ Gagal menjalankan destroyAudio()', [
                'audio_id' => $this->deleteAudioId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('error', 'Terjadi kesalahan saat menghapus audio: ' . $e->getMessage());
        }
    }
}
