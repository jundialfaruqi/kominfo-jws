<?php

namespace App\Livewire\Durasi;

use App\Events\ContentUpdatedEvent;
use App\Models\Durasi as ModelsDurasi;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Durasi extends Component
{
    use WithPagination;

    #[Title('Pengaturan Durasi')]

    public $paginate;
    public $search;
    protected $paginationTheme = 'bootstrap';

    public $durasiId;
    public $userId;
    public $adzan_shuruq;
    public $adzan_dhuha;
    public $adzan_shubuh;
    public $iqomah_shubuh;
    public $final_shubuh;
    public $adzan_dzuhur;
    public $iqomah_dzuhur;
    public $final_dzuhur;
    public $jumat_slide;
    public $adzan_ashar;
    public $iqomah_ashar;
    public $final_ashar;
    public $adzan_maghrib;
    public $iqomah_maghrib;
    public $final_maghrib;
    public $adzan_isya;
    public $iqomah_isya;
    public $final_isya;

    public $showForm = false;
    public $isEdit = false;
    public $showTable = true;
    public $deleteDurasiId;
    public $deleteDurasiName;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'adzan_shubuh' => 'required|numeric|min:1',
        'adzan_shuruq' => 'required|numeric|min:1',
        'adzan_dhuha' => 'required|numeric|min:1',
        'iqomah_shubuh' => 'required|numeric|min:1',
        'final_shubuh' => 'required|numeric|min:1',
        'adzan_dzuhur' => 'required|numeric|min:1',
        'iqomah_dzuhur' => 'required|numeric|min:1',
        'final_dzuhur' => 'required|numeric|min:1',
        'jumat_slide' => 'required|numeric|min:1',
        'adzan_ashar' => 'required|numeric|min:1',
        'iqomah_ashar' => 'required|numeric|min:1',
        'final_ashar' => 'required|numeric|min:1',
        'adzan_maghrib' => 'required|numeric|min:1',
        'iqomah_maghrib' => 'required|numeric|min:1',
        'final_maghrib' => 'required|numeric|min:1',
        'adzan_isya' => 'required|numeric|min:1',
        'iqomah_isya' => 'required|numeric|min:1',
        'final_isya' => 'required|numeric|min:1',
    ];

    protected $messages = [
        'userId.required' => 'Admin Masjid wajib diisi',
        'userId.exists' => 'Admin Masjid tidak ditemukan',
        'adzan_shuruq.required' => 'Durasi Shuruq wajib diisi',
        'adzan_shuruq.numeric' => 'Durasi Shuruq harus berupa angka',
        'adzan_shuruq.min' => 'Durasi Shuruq minimal 1 menit',
        'adzan_dhuha.required' => 'Durasi Dhuha wajib diisi',
        'adzan_dhuha.numeric' => 'Durasi Dhuha harus berupa angka',
        'adzan_dhuha.min' => 'Durasi Dhuha minimal 1 menit',
        'adzan_shubuh.required' => 'Durasi adzan Shubuh wajib diisi',
        'adzan_shubuh.numeric' => 'Durasi adzan Shubuh harus berupa angka',
        'adzan_shubuh.min' => 'Durasi adzan Shubuh minimal 1 menit',
        'iqomah_shubuh.required' => 'Durasi iqomah Shubuh wajib diisi',
        'iqomah_shubuh.numeric' => 'Durasi iqomah Shubuh harus berupa angka',
        'iqomah_shubuh.min' => 'Durasi iqomah Shubuh minimal 1 menit',
        'final_shubuh.required' => 'Durasi final Shubuh wajib diisi',
        'final_shubuh.numeric' => 'Durasi final Shubuh harus berupa angka',
        'final_shubuh.min' => 'Durasi final Shubuh minimal 1 detik',
        'adzan_dzuhur.required' => 'Durasi adzan Dzuhur wajib diisi',
        'adzan_dzuhur.numeric' => 'Durasi adzan Dzuhur harus berupa angka',
        'adzan_dzuhur.min' => 'Durasi adzan Dzuhur minimal 1 menit',
        'iqomah_dzuhur.required' => 'Durasi iqomah Dzuhur wajib diisi',
        'iqomah_dzuhur.numeric' => 'Durasi iqomah Dzuhur harus berupa angka',
        'iqomah_dzuhur.min' => 'Durasi iqomah Dzuhur minimal 1 menit',
        'final_dzuhur.required' => 'Durasi final Dzuhur wajib diisi',
        'final_dzuhur.numeric' => 'Durasi final Dzuhur harus berupa angka',
        'final_dzuhur.min' => 'Durasi final Dzuhur minimal 1 detik',
        'jumat_slide.required' => 'Durasi slide Jum\'at wajib diisi',
        'jumat_slide.numeric' => 'Durasi slide Jum\'at harus berupa angka',
        'jumat_slide.min' => 'Durasi slide Jum\'at minimal 1 menit',
        'adzan_ashar.required' => 'Durasi adzan Ashar wajib diisi',
        'adzan_ashar.numeric' => 'Durasi adzan Ashar harus berupa angka',
        'adzan_ashar.min' => 'Durasi adzan Ashar minimal 1 menit',
        'iqomah_ashar.required' => 'Durasi iqomah Ashar wajib diisi',
        'iqomah_ashar.numeric' => 'Durasi iqomah Ashar harus berupa angka',
        'iqomah_ashar.min' => 'Durasi iqomah Ashar minimal 1 menit',
        'final_ashar.required' => 'Durasi final Ashar wajib diisi',
        'final_ashar.numeric' => 'Durasi final Ashar harus berupa angka',
        'final_ashar.min' => 'Durasi final Ashar minimal 1 detik',
        'adzan_maghrib.required' => 'Durasi adzan Maghrib wajib diisi',
        'adzan_maghrib.numeric' => 'Durasi adzan Maghrib harus berupa angka',
        'adzan_maghrib.min' => 'Durasi adzan Maghrib minimal 1 menit',
        'iqomah_maghrib.required' => 'Durasi iqomah Maghrib wajib diisi',
        'iqomah_maghrib.numeric' => 'Durasi iqomah Maghrib harus berupa angka',
        'iqomah_maghrib.min' => 'Durasi iqomah Maghrib minimal 1 menit',
        'final_maghrib.required' => 'Durasi final Maghrib wajib diisi',
        'final_maghrib.numeric' => 'Durasi final Maghrib harus berupa angka',
        'final_maghrib.min' => 'Durasi final Maghrib minimal 1 detik',
        'adzan_isya.required' => 'Durasi adzan Isya wajib diisi',
        'adzan_isya.numeric' => 'Durasi adzan Isya harus berupa angka',
        'adzan_isya.min' => 'Durasi adzan Isya minimal 1 menit',
        'iqomah_isya.required' => 'Durasi iqomah Isya wajib diisi',
        'iqomah_isya.numeric' => 'Durasi iqomah Isya harus berupa angka',
        'iqomah_isya.min' => 'Durasi iqomah Isya minimal 1 menit',
        'final_isya.required' => 'Durasi final Isya wajib diisi',
        'final_isya.numeric' => 'Durasi final Isya harus berupa angka',
        'final_isya.min' => 'Durasi final Isya minimal 1 detik',
    ];

    public function mount()
    {
        $this->paginate = 10;
        $this->search = '';

        // Jika user bukan admin
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $durasi = ModelsDurasi::where('user_id', Auth::user()->id)->first();

            // Selalu tampilkan form untuk non-admin
            $this->showForm = true;
            // Set user ID untuk durasi baru
            $this->userId = Auth::id();

            if ($durasi) {
                // Jika durasi sudah ada, muat data
                $this->durasiId = $durasi->id;
                $this->adzan_shuruq = $durasi->adzan_shuruq;
                $this->adzan_dhuha = $durasi->adzan_dhuha;
                $this->adzan_shubuh = $durasi->adzan_shubuh;
                $this->iqomah_shubuh = $durasi->iqomah_shubuh;
                $this->final_shubuh = $durasi->final_shubuh;
                $this->adzan_dzuhur = $durasi->adzan_dzuhur;
                $this->iqomah_dzuhur = $durasi->iqomah_dzuhur;
                $this->final_dzuhur = $durasi->final_dzuhur;
                $this->jumat_slide = $durasi->jumat_slide;
                $this->adzan_ashar = $durasi->adzan_ashar;
                $this->iqomah_ashar = $durasi->iqomah_ashar;
                $this->final_ashar = $durasi->final_ashar;
                $this->adzan_maghrib = $durasi->adzan_maghrib;
                $this->iqomah_maghrib = $durasi->iqomah_maghrib;
                $this->final_maghrib = $durasi->final_maghrib;
                $this->adzan_isya = $durasi->adzan_isya;
                $this->iqomah_isya = $durasi->iqomah_isya;
                $this->final_isya = $durasi->final_isya;
                $this->isEdit = true;
            } else {
                // Untuk durasi baru, set isEdit ke false
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
            'durasiId',
            'userId',
            'adzan_shuruq',
            'adzan_dhuha',
            'adzan_shubuh',
            'iqomah_shubuh',
            'final_shubuh',
            'adzan_dzuhur',
            'iqomah_dzuhur',
            'final_dzuhur',
            'jumat_slide',
            'adzan_ashar',
            'iqomah_ashar',
            'final_ashar',
            'adzan_maghrib',
            'iqomah_maghrib',
            'final_maghrib',
            'adzan_isya',
            'iqomah_isya',
            'final_isya',
        ]);
    }

    public function render()
    {
        // Dapatkan user saat ini dan peran
        $currentUser = Auth::user();
        $isAdmin = in_array($currentUser->role, ['Super Admin', 'Admin']);
        $isSuperAdmin = $currentUser->role === 'Super Admin';

        // Query builder untuk durasi
        $query = ModelsDurasi::with('user')
            ->select(
                'id',
                'user_id',
                'adzan_shuruq',
                'adzan_dhuha',
                'adzan_shubuh',
                'iqomah_shubuh',
                'final_shubuh',
                'adzan_dzuhur',
                'iqomah_dzuhur',
                'final_dzuhur',
                'jumat_slide',
                'adzan_ashar',
                'iqomah_ashar',
                'final_ashar',
                'adzan_maghrib',
                'iqomah_maghrib',
                'final_maghrib',
                'adzan_isya',
                'iqomah_isya',
                'final_isya'
            );

        // Jika bukan Super Admin, filter durasi dan kecualikan user dengan role 'Super Admin' atau 'Admin'
        if (!$isSuperAdmin) {
            $query->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['Super Admin', 'Admin']);
            });
        }

        // Jika bukan admin, hanya tampilkan durasi milik mereka sendiri
        if (!$isAdmin) {
            $query->where('user_id', $currentUser->id);
        } else {
            // Admin dapat mencari semua durasi
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        $durasiList = $query->orderBy('id', 'asc')
            ->paginate($this->paginate);

        // Hanya admin yang dapat melihat daftar user untuk penugasan
        $users = collect([]);
        if ($isAdmin) {
            $usersWithDurasi = ModelsDurasi::pluck('user_id')->toArray();
            // Jika bukan Super Admin, kecualikan user dengan role 'Super Admin' atau 'Admin'
            $usersQuery = User::whereNotIn('id', $usersWithDurasi);
            if (!$isSuperAdmin) {
                $usersQuery->whereNotIn('role', ['Super Admin', 'Admin']);
            }
            $users = $usersQuery->orderBy('name')
                ->get();
        }

        return view('livewire.durasi.durasi', [
            'durasiList' => $durasiList,
            'users' => $users,
        ]);
    }

    public function showAddForm()
    {
        // Hanya admin yang dapat menambah durasi baru
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk menambah durasi!');
            return;
        }

        $this->resetValidation();
        $this->reset([
            'durasiId',
            'userId',
            'adzan_shubuh',
            'iqomah_shubuh',
            'final_shubuh',
            'adzan_shuruq',
            'adzan_dhuha',
            'adzan_dzuhur',
            'iqomah_dzuhur',
            'final_dzuhur',
            'jumat_slide',
            'adzan_ashar',
            'iqomah_ashar',
            'final_ashar',
            'adzan_maghrib',
            'iqomah_maghrib',
            'final_maghrib',
            'adzan_isya',
            'iqomah_isya',
            'final_isya',
        ]);
        $this->isEdit = false;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function edit($id)
    {
        $this->resetValidation();

        $durasi = ModelsDurasi::findOrFail($id);

        // Periksa apakah user memiliki izin untuk mengedit durasi ini
        if (Auth::check() && !in_array(Auth::user()->role, ['Super Admin', 'Admin']) && Auth::id() !== $durasi->user_id) {
            $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit durasi ini!');
            return;
        }

        $this->durasiId = $durasi->id;
        $this->userId = $durasi->user_id;
        $this->adzan_shuruq = $durasi->adzan_shuruq;
        $this->adzan_dhuha = $durasi->adzan_dhuha;
        $this->adzan_shubuh = $durasi->adzan_shubuh;
        $this->iqomah_shubuh = $durasi->iqomah_shubuh;
        $this->final_shubuh = $durasi->final_shubuh;
        $this->adzan_dzuhur = $durasi->adzan_dzuhur;
        $this->iqomah_dzuhur = $durasi->iqomah_dzuhur;
        $this->final_dzuhur = $durasi->final_dzuhur;
        $this->jumat_slide = $durasi->jumat_slide;
        $this->adzan_ashar = $durasi->adzan_ashar;
        $this->iqomah_ashar = $durasi->iqomah_ashar;
        $this->final_ashar = $durasi->final_ashar;
        $this->adzan_maghrib = $durasi->adzan_maghrib;
        $this->iqomah_maghrib = $durasi->iqomah_maghrib;
        $this->final_maghrib = $durasi->final_maghrib;
        $this->adzan_isya = $durasi->adzan_isya;
        $this->iqomah_isya = $durasi->iqomah_isya;
        $this->final_isya = $durasi->final_isya;

        $this->showForm = true;
        $this->isEdit = true;
        $this->showTable = false;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->showTable = true;
        $this->resetValidation();
        $this->reset([
            'durasiId',
            'userId',
            'adzan_shuruq',
            'adzan_dhuha',
            'adzan_shubuh',
            'iqomah_shubuh',
            'final_shubuh',
            'adzan_dzuhur',
            'iqomah_dzuhur',
            'final_dzuhur',
            'jumat_slide',
            'adzan_ashar',
            'iqomah_ashar',
            'final_ashar',
            'adzan_maghrib',
            'iqomah_maghrib',
            'final_maghrib',
            'adzan_isya',
            'iqomah_isya',
            'final_isya',
        ]);
    }

    public function save()
    {
        $currentUser = Auth::user();

        // Jika bukan admin, paksa userId menjadi ID mereka sendiri
        if (!in_array($currentUser->role, ['Super Admin', 'Admin'])) {
            $this->userId = $currentUser->id;
        }

        // Validasi tambahan untuk satu profil per user
        if (!$this->isEdit) {
            // Periksa apakah user yang dipilih sudah memiliki durasi
            $existingDurasi = ModelsDurasi::where('user_id', $this->userId)->first();
            if ($existingDurasi) {
                $this->dispatch('error', 'User ini sudah memiliki pengaturan durasi!');
                return;
            }
        } else {
            // Saat mengedit, pastikan tidak mengubah ke user yang sudah memiliki durasi
            $existingDurasi = ModelsDurasi::where('user_id', $this->userId)
                ->where('id', '!=', $this->durasiId)
                ->first();
            if ($existingDurasi) {
                $this->dispatch('error', 'User ini sudah memiliki pengaturan durasi!');
                return;
            }
        }

        $this->validate();

        try {
            if ($this->isEdit) {
                $durasi = ModelsDurasi::findOrFail($this->durasiId);
                // Periksa apakah user memiliki izin untuk mengedit durasi ini
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $currentUser->id !== $durasi->user_id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk mengedit durasi ini!');
                    return;
                }
            } else {
                // Izinkan non-admin membuat durasi mereka sendiri
                if (!in_array($currentUser->role, ['Super Admin', 'Admin']) && $this->userId !== $currentUser->id) {
                    $this->dispatch('error', 'Anda tidak memiliki akses untuk membuat durasi untuk user lain!');
                    return;
                }
                $durasi = new ModelsDurasi();
            }

            $durasi->user_id = $this->userId;
            $durasi->adzan_shuruq = $this->adzan_shuruq;
            $durasi->adzan_dhuha = $this->adzan_dhuha;
            $durasi->adzan_shubuh = $this->adzan_shubuh;
            $durasi->iqomah_shubuh = $this->iqomah_shubuh;
            $durasi->final_shubuh = $this->final_shubuh;
            $durasi->adzan_dzuhur = $this->adzan_dzuhur;
            $durasi->iqomah_dzuhur = $this->iqomah_dzuhur;
            $durasi->final_dzuhur = $this->final_dzuhur;
            $durasi->jumat_slide = $this->jumat_slide;
            $durasi->adzan_ashar = $this->adzan_ashar;
            $durasi->iqomah_ashar = $this->iqomah_ashar;
            $durasi->final_ashar = $this->final_ashar;
            $durasi->adzan_maghrib = $this->adzan_maghrib;
            $durasi->iqomah_maghrib = $this->iqomah_maghrib;
            $durasi->final_maghrib = $this->final_maghrib;
            $durasi->adzan_isya = $this->adzan_isya;
            $durasi->iqomah_isya = $this->iqomah_isya;
            $durasi->final_isya = $this->final_isya;
            $durasi->save();

            // Trigger event
            $profil = Profil::where('user_id', $this->userId)->first();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'adzan'));

            $this->dispatch('success', $this->isEdit ? 'Durasi berhasil diubah!' : 'Durasi berhasil ditambahkan!');
            $this->showTable = true;

            // Hanya sembunyikan form dan reset field jika user adalah admin
            if (in_array(Auth::user()->role, ['Super Admin', 'Admin'])) {
                $this->showForm = false;
                $this->reset([
                    'durasiId',
                    'userId',
                    'adzan_shuruq',
                    'adzan_dhuha',
                    'adzan_shubuh',
                    'iqomah_shubuh',
                    'final_shubuh',
                    'adzan_dzuhur',
                    'iqomah_dzuhur',
                    'final_dzuhur',
                    'jumat_slide',
                    'adzan_ashar',
                    'iqomah_ashar',
                    'final_ashar',
                    'adzan_maghrib',
                    'iqomah_maghrib',
                    'final_maghrib',
                    'adzan_isya',
                    'iqomah_isya',
                    'final_isya',
                ]);
            } else {
                // Untuk user biasa, tetap tampilkan form dan muat ulang data mereka
                $this->showForm = true;
                $durasi = ModelsDurasi::where('user_id', Auth::id())->first();
                if ($durasi) {
                    $this->durasiId = $durasi->id;
                    $this->adzan_shuruq = $durasi->adzan_shuruq;
                    $this->adzan_dhuha = $durasi->adzan_dhuha;
                    $this->adzan_shubuh = $durasi->adzan_shubuh;
                    $this->iqomah_shubuh = $durasi->iqomah_shubuh;
                    $this->final_shubuh = $durasi->final_shubuh;
                    $this->adzan_dzuhur = $durasi->adzan_dzuhur;
                    $this->iqomah_dzuhur = $durasi->iqomah_dzuhur;
                    $this->final_dzuhur = $durasi->final_dzuhur;
                    $this->jumat_slide = $durasi->jumat_slide;
                    $this->adzan_ashar = $durasi->adzan_ashar;
                    $this->iqomah_ashar = $durasi->iqomah_ashar;
                    $this->final_ashar = $durasi->final_ashar;
                    $this->adzan_maghrib = $durasi->adzan_maghrib;
                    $this->iqomah_maghrib = $durasi->iqomah_maghrib;
                    $this->final_maghrib = $durasi->final_maghrib;
                    $this->adzan_isya = $durasi->adzan_isya;
                    $this->iqomah_isya = $durasi->iqomah_isya;
                    $this->final_isya = $durasi->final_isya;
                    $this->isEdit = true;
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan durasi: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $this->showForm = false;

        $durasi = ModelsDurasi::findOrFail($id);
        $this->deleteDurasiId = $durasi->id;
        $this->deleteDurasiName = $durasi->user->name;
    }

    public function destroyDurasi()
    {
        try {
            $durasi = ModelsDurasi::findOrFail($this->deleteDurasiId);
            $profil = $durasi->user->profil;
            $durasi->delete();
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'adzan'));
            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Durasi berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menghapus durasi: ' . $e->getMessage());
        }
    }
}
