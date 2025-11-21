<div>
    <div class="page-body">
        <div class="container-xl">

            <h3>Statistik User</h3>
            @include('livewire.admin.user.statistic')

            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                Daftar User
                            </h3>
                            <div class="card-actions">
                                @if (auth()->user()->role === 'Super Admin' || auth()->user()->role === 'Admin')
                                    <button wire:loading.attr="disabled" wire:click="add" type="button"
                                        class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                        data-bs-target="#createModal">
                                        <span wire:loading.remove wire:target="add">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                <path d="M13.5 6.5l4 4" />
                                                <path d="M16 19h6" />
                                                <path d="M19 16v6" />
                                            </svg>
                                            Tambah User Baru
                                        </span>
                                        <span wire:loading wire:target="add">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            Loading...
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                                <div class="text-secondary d-flex align-items-center gap-2 flex-grow-1">
                                    Lihat
                                    <div class="mx-2 d-inline-block">
                                        <select wire:model.live="paginate"
                                            class="form-select form-select py-1 rounded-3">
                                            <option>5</option>
                                            <option>10</option>
                                            <option>25</option>
                                            <option>50</option>
                                            <option>100</option>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <a href="{{ route('admin.user.pdf', ['role' => 'Admin Masjid']) }}"
                                            class="btn py-1 btn-primary rounded-3">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                                                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                                                    <path d="M17 18h2" />
                                                    <path d="M20 15h-3v6" />
                                                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                                                </svg>
                                                <span>Export PDF User</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-secondary d-flex align-items-center ms-md-auto">
                                    <span>Cari</span>
                                    <div class="ms-2 d-inline-block">
                                        <input wire:model.live="search" type="search" name="q" id="q"
                                            inputmode="search" autocapitalize="none" spellcheck="false"
                                            class="form-control form-control py-1 rounded-3 w-auto" autocomplete="off"
                                            placeholder="Ketik disini">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('livewire.admin.user.table')

                        <!-- Ket Aktivitas-->
                        <div class="card-body border-top-0 border-0 py-3 small"
                            style="border-bottom: 1px solid #dee2e6;">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="text-secondary">Tingkat Aktivitas User:</span>
                                <span class="badge bg-green-lt">Aktif ≤ 30 hari</span>
                                <span class="badge bg-yellow-lt">Kurang (31–90 hari)</span>
                                <span class="badge bg-red-lt">Tidak aktif > 3 bulan</span>
                                <span class="badge bg-gray-lt">Tidak ada aktivitas</span>
                            </div>
                        </div>

                        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                            {{ $user->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    @include('livewire.admin.user.create')

    {{-- Edit Modal --}}
    @include('livewire.admin.user.edit')

    {{-- Delete Modal --}}
    @include('livewire.admin.user.delete')

    {{-- Scripts --}}
    @script
        <script>
            $wire.on('closeCreateModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
                if (modal) {
                    modal.hide();
                }
            });
            $wire.on('closeEditModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                if (modal) {
                    modal.hide();
                }
            });
            $wire.on('closeDeleteModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) {
                    modal.hide();
                }
            });

            $wire.on('success', message => {
                iziToast.success({
                    title: 'Berhasil',
                    message,
                    position: 'topRight'
                });
            });

            $wire.on('error', message => {
                iziToast.error({
                    title: 'Gagal',
                    message,
                    position: 'topRight'
                });
            });
        </script>
    @endscript
</div>
