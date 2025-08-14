<div class="card-header">
    <h3 class="card-title d-none d-md-block">
        @if ($showForm)
            {{ $isEdit ? 'Ubah Pengaturan Profil Masjid' : 'Tambah Profil Masjid Baru' }}
        @else
            {{ Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) ? 'Daftar Profil Masjid' : 'Ubah Pengaturan Profil Masjid' }}
        @endif
    </h3>
    @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) && !$showForm)
        <div class="card-actions">
            <button wire:click="showAddForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                <span wire:loading.remove wire:target="showAddForm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                        <path d="M13.5 6.5l4 4" />
                        <path d="M16 19h6" />
                        <path d="M19 16v6" />
                    </svg>
                    Tambah Profil Masjid
                </span>
                <span wire:loading wire:target="showAddForm">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="small">Loading...</span>
                </span>
            </button>
        </div>
    @endif
</div>
