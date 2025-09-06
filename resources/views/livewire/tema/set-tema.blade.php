<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                Set Tema Pengguna
                            </h3>
                        </div>
                        @if ($userId)
                            <form wire:submit.prevent="save">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-2">
                                                    <label class="form-label required">Nama Masjid</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control rounded-3"
                                                        value="{{ $profilName }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cards">
                                        @foreach ($availableThemes as $theme)
                                            <div class="col-md-4" wire:key="theme-{{ $theme->id }}">
                                                <div
                                                    class="card mb-3 rounded-4 pt-2 px-2 shadow-sm {{ $selectedThemeId == $theme->id ? 'border-primary bg-light shadow-sm' : 'shadow-sm' }}">
                                                    <div class="position-relative">
                                                        <img src="{{ $theme->preview_image ? asset($theme->preview_image) : asset('images/other/default-theme.jpg') }}"
                                                            class="card-img-top rounded-3 {{ $selectedThemeId == $theme->id ? 'opacity-50' : 'opacity-100' }}"
                                                            alt="{{ $theme->name }}"
                                                            style="height: 200px; object-fit: stright;">
                                                        @if ($selectedThemeId == $theme->id)
                                                            <div
                                                                class="position-absolute top-0 end-0 me-4 mt-2 text-success avatar rounded-circle shadow-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24"
                                                                    fill="currentColor"
                                                                    class="icon icon-tabler icons-tabler-filled icon-tabler-circle-check">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path
                                                                        d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 .083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <h5
                                                            class="card-title {{ $selectedThemeId == $theme->id ? 'text-primary' : '' }}">
                                                            {{ $theme->name }}
                                                        </h5>
                                                        <button type="button"
                                                            wire:click="selectTempTheme({{ $theme->id }})"
                                                            class="btn btn-primary rounded-3 shadow-sm {{ $selectedThemeId == $theme->id ? 'disabled' : '' }}">
                                                            {{ $selectedThemeId == $theme->id ? 'Dipilih' : 'Pilih Tema' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card-footer rounded-bottom-4 border-0 sticky-bottom"
                                    style="background-color: rgba(255, 255, 255, 0.9);">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" wire:click="cancel"
                                            class="btn py-2 px-2 rounded-3 shadow-sm">
                                            <span wire:loading.remove wire:target="cancel">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-copy-x">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                                    <path
                                                        d="M4.012 16.737a2 2 0 0 1 -1.012 -1.737v-10 c0 -1.1 .9 -2 2 -2h10 c.75 0 1.158 .385 1.5 1" />
                                                    <path d="M11.5 11.5l4.9 5" />
                                                    <path d="M16.5 11.5l-5.1 5" />
                                                </svg>
                                                Batal
                                            </span>
                                            <span wire:loading wire:target="cancel">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="small">Loading...</span>
                                            </span>
                                        </button>
                                        <button type="submit" class="btn py-2 px-2 rounded-3 shadow-sm"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="save">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                                                    <path d="M6.5 12h14.5" />
                                                </svg>
                                                Simpan
                                            </span>
                                            <span wire:loading wire:target="save">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="small">Menyimpan...</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        @if ($showTable)
                            <div class="card-body border-bottom py-3">
                                <div class="d-flex">
                                    <div class="text-secondary">
                                        Lihat
                                        <div class="mx-2 d-inline-block">
                                            <select wire:model.live="paginate"
                                                class="form-select form-select py-1 rounded-3">
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ms-auto text-secondary">
                                        <span>Cari</span>
                                        <div class="ms-2 d-inline-block">
                                            <input wire:model.live="search" type="text"
                                                class="form-control form-control py-1 rounded-3"
                                                placeholder="Ketik disini">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter table-hover text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th>Nama Masjid</th>
                                            <th>Tema Terpilih</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($profilList as $profil)
                                            <tr>
                                                <td class="text-center text-muted">
                                                    {{ $loop->iteration + ($profilList->currentPage() - 1) * $profilList->perPage() }}
                                                </td>
                                                <td class="text-wrap">{{ $profil->masjid_name }}</td>
                                                <td>
                                                    {{ $profil->theme_name }}
                                                </td>
                                                <td class="text-end">
                                                    <button wire:click="edit('{{ $profil->user_id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm">
                                                        <span wire:loading.remove
                                                            wire:target="edit('{{ $profil->user_id }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path
                                                                    d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                <path
                                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                <path d="M16 5l3 3" />
                                                            </svg>
                                                            Ubah
                                                        </span>
                                                        <span wire:loading
                                                            wire:target="edit('{{ $profil->user_id }}')">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="small">Loading...</span>
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                                {{ $profilList->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
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
