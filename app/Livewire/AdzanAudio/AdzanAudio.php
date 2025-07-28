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

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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

    // Properti untuk menandai file berhasil diupload
    public $audioadzanUploaded = false;
    public $adzanshubuhUploaded = false;

    public $isEdit = false;
    public $showForm = false;
    public $deleteAdzanAudioId;
    public $deleteAdzanAudioName;

    protected function rules()
    {
        $rules = [
            'audioadzan' => 'nullable|file|mimes:mp3,wav|max:10240', // Maks 10MB
            'adzanshubuh' => 'nullable|file|mimes:mp3,wav|max:10240', // Maks 10MB
            'status' => 'required|boolean',
        ];

        if (Auth::user()->role === 'Super Admin' || Auth::user()->role === 'Admin') {
            if ($this->isEdit) {
                $rules['userId'] = 'required|exists:users,id';
            } else {
                $rules['userId'] = 'required|exists:users,id|unique:adzan_audio,user_id';
            }
        }

        return $rules;
    }

    protected $messages = [
        'userId.required' => 'Admin Masjid wajib diisi',
        'userId.exists'   => 'Admin Masjid tidak ditemukan',
        'userId.unique'   => 'Admin Masjid sudah memiliki audio adzan',
        'audioadzan.file'     => 'File harus berupa audio',
        'audioadzan.mimes'    => 'File harus berupa audio mp3 atau wav',
        'audioadzan.max'      => 'Ukuran file maksimal 10MB',
        'adzanshubuh.file'    => 'File harus berupa audio',
        'adzanshubuh.mimes'   => 'File harus berupa audio mp3 atau wav',
        'adzanshubuh.max'     => 'Ukuran file maksimal 10MB',
        'status.required' => 'Status wajib diisi',
        'status.boolean'  => 'Status harus berupa aktif atau tidak aktif',
    ];



    /**
     * Method untuk mengecek konfigurasi penyimpanan lokal
     */
    private function checkLocalStorageConfig()
    {
        $audioPath = public_path('sounds/adzan');
        
        if (!file_exists($audioPath)) {
            mkdir($audioPath, 0755, true);
        }
        
        if (!is_writable($audioPath)) {
            throw new \Exception('Direktori audio adzan tidak dapat ditulis: ' . $audioPath);
        }
        
        Log::info('Konfigurasi penyimpanan lokal adzan valid', ['path' => $audioPath]);
    }

    /**
     * Method untuk menyimpan audio adzan secara lokal
     */
    private function saveLocalAdzanAudio($uploadedFile, $audioType)
    {
        try {
            $this->checkLocalStorageConfig();

            if (!$uploadedFile || !$uploadedFile->isValid()) {
                throw new \Exception('File audio adzan tidak valid atau tidak ditemukan.');
            }

            $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $uploadedFile->getClientOriginalExtension();
            $slugName = Str::slug($originalName);
            $timestamp = time();

            // Generate nama file
            $fileName = "{$timestamp}_{$audioType}_{$slugName}.{$extension}";
            $filePath = public_path('sounds/adzan/' . $fileName);

            // Pastikan directory ada
            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Pindahkan file ke direktori tujuan menggunakan Livewire method
            $uploadedFile->storeAs('', $fileName, 'public_sounds_adzan');
            
            // Verifikasi file berhasil dipindahkan
            if (!file_exists($filePath)) {
                throw new \Exception('Gagal memindahkan file audio adzan.');
            }

            Log::info('✅ Audio adzan berhasil disimpan secara lokal', [
                'audio_type' => $audioType,
                'file_name' => $fileName,
                'file_path' => $filePath,
            ]);

            return '/sounds/adzan/' . $fileName; // ← Simpan path relatif ke database
        } catch (\Exception $e) {
            Log::error('❌ Gagal menyimpan audio adzan secara lokal', [
                'audio_type' => $audioType,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Upload gagal: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menghasilkan URL lokal dari path file adzan
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

    public function clearAudioAdzan()
    {
        try {
            $this->checkLocalStorageConfig();

            if ($this->isEdit && $this->tmp_audioadzan) {
                $deleteLocalFile = function ($filename, $field) {
                    if (!$filename) {
                        return true;
                    }

                    try {
                        $filePath = public_path('sounds/adzan/' . $filename);
                        if (file_exists($filePath)) {
                            if (unlink($filePath)) {
                                Log::info("Menghapus {$field} dari penyimpanan lokal", [
                                    'filename' => $filename,
                                    'path' => $filePath,
                                ]);
                                return true;
                            } else {
                                Log::warning("Gagal menghapus {$field} dari penyimpanan lokal", [
                                    'filename' => $filename,
                                    'path' => $filePath,
                                ]);
                                return false;
                            }
                        } else {
                            Log::info("File {$field} tidak ditemukan, mungkin sudah dihapus", [
                                'filename' => $filename,
                                'path' => $filePath,
                            ]);
                            return true;
                        }
                    } catch (\Exception $ex) {
                        Log::error("Gagal menghapus {$field}", [
                            'filename' => $filename,
                            'error' => $ex->getMessage(),
                        ]);
                        return false;
                    }
                };

                $filename = $this->tmp_audioadzan;
                if (!$deleteLocalFile($filename, 'audioadzan')) {
                    $this->dispatch('error', 'Gagal menghapus audio adzan dari penyimpanan lokal.');
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
            $this->audioadzanUploaded = false;
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
            $this->checkLocalStorageConfig();

            if ($this->isEdit && $this->tmp_adzanshubuh) {
                $deleteLocalFile = function ($filename, $field) {
                    if (!$filename) {
                        return true;
                    }

                    try {
                        $filePath = public_path('sounds/adzan/' . $filename);
                        if (file_exists($filePath)) {
                            if (unlink($filePath)) {
                                Log::info("Menghapus {$field} dari penyimpanan lokal", [
                                    'filename' => $filename,
                                    'path' => $filePath,
                                ]);
                                return true;
                            } else {
                                Log::warning("Gagal menghapus {$field} dari penyimpanan lokal", [
                                    'filename' => $filename,
                                    'path' => $filePath,
                                ]);
                                return false;
                            }
                        } else {
                            Log::info("File {$field} tidak ditemukan, mungkin sudah dihapus", [
                                'filename' => $filename,
                                'path' => $filePath,
                            ]);
                            return true;
                        }
                    } catch (\Exception $ex) {
                        Log::error("Gagal menghapus {$field}", [
                            'filename' => $filename,
                            'error' => $ex->getMessage(),
                        ]);
                        return false;
                    }
                };

                $filename = $this->tmp_adzanshubuh;
                if (!$deleteLocalFile($filename, 'adzanshubuh')) {
                    $this->dispatch('error', 'Gagal menghapus audio adzan shubuh dari penyimpanan lokal.');
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
            $this->adzanshubuhUploaded = false;
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
        $this->audioadzanUploaded = false;
        $this->adzanshubuhUploaded = false;

        try {
            $this->checkLocalStorageConfig();
        } catch (\Exception $e) {
            Log::error('Gagal memeriksa konfigurasi penyimpanan lokal saat inisialisasi', [
                'error' => $e->getMessage(),
            ]);
        }

        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $adzanAudio = AdzanAudioModel::where('user_id', Auth::id())->first();
            $this->showForm = true;
            $this->userId = Auth::id();

            if ($adzanAudio) {
                $this->adzanAudioId = $adzanAudio->id;
                $this->tmp_audioadzan = $adzanAudio->audioadzan;
                $this->tmp_adzanshubuh = $adzanAudio->adzanshubuh;
                $this->status = $adzanAudio->status ? 1 : 0;
                $this->isEdit = true;
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
            'audioadzan',
            'tmp_audioadzan',
            'adzanshubuh',
            'tmp_adzanshubuh',
            'status'
        ]);
        $this->audioadzanUploaded = false;
        $this->adzanshubuhUploaded = false;
    }

    public function getUsersProperty()
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role === 'Super Admin';
        $isAdmin = $currentUser->role === 'Admin';

        // Jika bukan admin, kembalikan koleksi kosong
        if (!$isSuperAdmin && !$isAdmin) {
            return collect([]);
        }

        // Ambil ID user yang sudah memiliki audio adzan
        $usersWithAdzanAudio = AdzanAudioModel::pluck('user_id')->toArray();

        // Mulai dengan query dasar untuk user yang belum memiliki audio adzan
        $query = User::where('role', 'Admin Masjid');

        // Jika sedang edit, pastikan user yang sedang diedit tetap muncul di daftar
        if ($this->isEdit && $this->userId) {
            // Jika user ID saat ini tidak ada dalam daftar user dengan audio
            if (!in_array($this->userId, $usersWithAdzanAudio)) {
                // Tidak perlu melakukan apa-apa karena user akan tetap muncul dalam query
            } else {
                // Tambahkan user yang sedang diedit ke daftar
                $query->orWhere('id', $this->userId);
            }
        }

        // Jika bukan Super Admin (hanya Admin biasa), filter user yang sudah memiliki audio
        if (!$isSuperAdmin) {
            $query->whereNotIn('id', $usersWithAdzanAudio);
        }

        return $query->orderBy('name')->get();
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        $query = AdzanAudioModel::with('user')
            ->select('id', 'user_id', 'audioadzan', 'adzanshubuh', 'status');

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
            $adzanAudio->audioadzan_url = $adzanAudio->audioadzan ? $this->generateLocalUrl($adzanAudio->audioadzan) : null;
            $adzanAudio->adzanshubuh_url = $adzanAudio->adzanshubuh ? $this->generateLocalUrl($adzanAudio->adzanshubuh) : null;
        }

        // Ambil daftar users untuk dropdown
        $users = collect([]);
        if ($isAdmin) {
            $usersWithAdzanAudio = AdzanAudioModel::pluck('user_id')->toArray();
            $usersQuery = User::whereNotIn('id', $usersWithAdzanAudio);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }
            $users = $usersQuery->orderBy('name')->get();
        }

        return view('livewire.adzan-audio.adzan-audio', [
            'adzanAudioList' => $adzanAudioList,
            'users' => $users,
        ]);
    }

    public function showAddForm()
    {
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah audio adzan!');
            return;
        }

        $this->isEdit = false;
        $this->showForm = true;
        $this->reset(['adzanAudioId', 'userId', 'audioadzan', 'adzanshubuh', 'status', 'tmp_audioadzan', 'tmp_adzanshubuh']);
        $this->status = 0;
        $this->audioadzanUploaded = false;
        $this->adzanshubuhUploaded = false;
        $this->resetValidation();

        // Ambil daftar pengguna yang tersedia
        $users = $this->getUsersProperty();

        // Jika ada pengguna yang tersedia, atur userId ke pengguna pertama
        if ($users->isNotEmpty()) {
            $this->userId = $users->first()->id;
        } else {
            // Jika tidak ada pengguna yang tersedia, tampilkan pesan
            $this->dispatch('warning', 'Tidak ada Admin Masjid yang tersedia untuk ditambahkan audio.');
        }
    }

    public function edit($id)
    {
        $this->resetValidation();
        $adzanAudio = AdzanAudioModel::findOrFail($id);

        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $adzanAudio->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit audio adzan ini!');
            return;
        }

        $this->adzanAudioId = $adzanAudio->id;
        $this->userId = $adzanAudio->user_id;
        $this->tmp_audioadzan = $adzanAudio->audioadzan;
        $this->tmp_adzanshubuh = $adzanAudio->adzanshubuh;
        $this->status = $adzanAudio->status ? 1 : 0;
        $this->audioadzanUploaded = false;
        $this->adzanshubuhUploaded = false;

        $this->isEdit = true;
        $this->showForm = true;
        $this->resetValidation();
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
        $this->audioadzanUploaded = false;
        $this->adzanshubuhUploaded = false;
    }

    public function closeForm()
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
        $this->audioadzanUploaded = false;
        $this->adzanshubuhUploaded = false;
    }

    public function save()
    {
        $currentUser = Auth::user();

        if (!in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            $this->userId = $currentUser->id;
        }

        $this->validate();

        try {
            $data = [
                'user_id' => $this->userId,
                'status' => (int)$this->status,
            ];

            if ($this->audioadzan) {
                $data['audioadzan'] = $this->saveLocalAdzanAudio($this->audioadzan, 'audioadzan');
            }

            if ($this->adzanshubuh) {
                $data['adzanshubuh'] = $this->saveLocalAdzanAudio($this->adzanshubuh, 'adzanshubuh');
            }

            if ($this->isEdit) {
                $adzanAudio = AdzanAudioModel::find($this->adzanAudioId);
                if ($adzanAudio) {
                    $adzanAudio->update($data);
                    $this->dispatch('success', 'Audio Adzan berhasil diperbarui!');
                } else {
                    $this->dispatch('error', 'Audio Adzan tidak ditemukan!');
                    return;
                }
            } else {
                AdzanAudioModel::create($data);
                $this->dispatch('success', 'Audio Adzan berhasil ditambahkan!');
            }

            if (in_array($currentUser->role, ['Super Admin', 'Admin'])) {
                $this->cancelForm();
            } else {
                // Untuk non-admin, tetap tampilkan form dan perbarui data
                $this->showForm = true;
                $adzanAudio = AdzanAudioModel::where('user_id', Auth::id())->first();
                if ($adzanAudio) {
                    $this->adzanAudioId = $adzanAudio->id;
                    $this->userId = $adzanAudio->user_id;
                    $this->tmp_audioadzan = $adzanAudio->audioadzan;
                    $this->tmp_adzanshubuh = $adzanAudio->adzanshubuh;
                    $this->status = $adzanAudio->status ? 1 : 0;
                    $this->isEdit = true;

                    // Reset properti file setelah menyimpan untuk mencegah upload ulang
                    $this->audioadzan = null;
                    $this->adzanshubuh = null;
                    $this->audioadzanUploaded = false;
                    $this->adzanshubuhUploaded = false;
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
        $adzanAudio = AdzanAudioModel::findOrFail($id);
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $adzanAudio->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menghapus audio adzan ini!');
            return;
        }

        $this->deleteAdzanAudioId = $adzanAudio->id;
        $this->deleteAdzanAudioName = $adzanAudio->user->name;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'audioadzan') {
            Log::debug('audioadzan updated', [
                'audioadzan' => $this->audioadzan ? get_class($this->audioadzan) : null,
                'tmp_audioadzan' => $this->tmp_audioadzan,
            ]);
            if ($this->audioadzan) {
                $this->tmp_audioadzan = null;
                $this->audioadzanUploaded = true;
                $this->validateOnly('audioadzan');
            } else {
                $this->audioadzanUploaded = false;
            }
        }
        if ($propertyName === 'adzanshubuh') {
            Log::debug('adzanshubuh updated', [
                'adzanshubuh' => $this->adzanshubuh ? get_class($this->adzanshubuh) : null,
                'tmp_adzanshubuh' => $this->tmp_adzanshubuh,
            ]);
            if ($this->adzanshubuh) {
                $this->tmp_adzanshubuh = null;
                $this->adzanshubuhUploaded = true;
                $this->validateOnly('adzanshubuh');
            } else {
                $this->adzanshubuhUploaded = false;
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
            'audioadzan' => $this->audioadzan ? get_class($this->audioadzan) : null,
            'adzanshubuh' => $this->adzanshubuh ? get_class($this->adzanshubuh) : null,
        ]);

        // Memaksa render ulang dengan mengirim event kembali ke JavaScript
        $this->dispatch('fileSelected', inputName: $data['inputName']);
    }

    public function destroyAdzanAudio()
    {
        // Verifikasi bahwa hanya Super Admin dan Admin yang dapat menghapus audio adzan
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menghapus audio adzan!');
            return;
        }

        try {
            $this->checkLocalStorageConfig();

            $adzanAudio = AdzanAudioModel::findOrFail($this->deleteAdzanAudioId);

            // Verifikasi bahwa user hanya dapat menghapus audio adzan miliknya sendiri
            if (Auth::id() != $adzanAudio->user_id && Auth::user()->role !== 'Super Admin') {
                $this->dispatch('error', 'Anda hanya dapat menghapus audio adzan milik Anda sendiri!');
                return;
            }
            $allDeleted = true;

            // Hapus audioadzan
            $filename = $adzanAudio->audioadzan;

            if ($filename) {
                try {
                    $filePath = public_path('sounds/adzan/' . $filename);
                    if (file_exists($filePath)) {
                        if (!unlink($filePath)) {
                            $allDeleted = false;
                            Log::warning("Gagal menghapus file dari penyimpanan lokal", [
                                'field' => 'audioadzan',
                                'filename' => $filename,
                                'path' => $filePath
                            ]);
                        } else {
                            Log::info("Berhasil menghapus audioadzan dari penyimpanan lokal", [
                                'filename' => $filename,
                                'path' => $filePath
                            ]);
                        }
                    } else {
                        Log::info("File audioadzan tidak ditemukan, mungkin sudah dihapus", [
                            'filename' => $filename,
                            'path' => $filePath
                        ]);
                    }
                } catch (\Exception $ex) {
                    $allDeleted = false;
                    Log::error("Gagal menghapus audioadzan", [
                        'filename' => $filename,
                        'error' => $ex->getMessage()
                    ]);
                }
            }

            // Hapus adzanshubuh
            $filename = $adzanAudio->adzanshubuh;

            if ($filename) {
                try {
                    $filePath = public_path('sounds/adzan/' . $filename);
                    if (file_exists($filePath)) {
                        if (!unlink($filePath)) {
                            $allDeleted = false;
                            Log::warning("Gagal menghapus file dari penyimpanan lokal", [
                                'field' => 'adzanshubuh',
                                'filename' => $filename,
                                'path' => $filePath
                            ]);
                        } else {
                            Log::info("Berhasil menghapus adzanshubuh dari penyimpanan lokal", [
                                'filename' => $filename,
                                'path' => $filePath
                            ]);
                        }
                    } else {
                        Log::info("File adzanshubuh tidak ditemukan, mungkin sudah dihapus", [
                            'filename' => $filename,
                            'path' => $filePath
                        ]);
                    }
                } catch (\Exception $ex) {
                    $allDeleted = false;
                    Log::error("Gagal menghapus adzanshubuh", [
                        'filename' => $filename,
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
                $this->dispatch('error', 'File gagal dihapus dari penyimpanan lokal. Data tidak dihapus.');
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
