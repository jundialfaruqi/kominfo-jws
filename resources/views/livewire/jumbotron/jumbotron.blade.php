<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                @if ($showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Jumbotron' : 'Tambah Jumbotron Baru' }}
                                @else
                                    Daftar Jumbotron
                                @endif
                            </h3>
                            @if (!$showForm)
                                <div class="card-actions">
                                    <button wire:click="showAddForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                                        <span wire:loading.remove wire:target="showAddForm">
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
                                            Tambah Jumbotron
                                        </span>
                                        <span wire:loading wire:target="showAddForm">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Loading...</span>
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if ($showForm)
                            <form wire:submit.prevent="save">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Status Aktif</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="is_active" wire:change="$refresh"
                                                        id="is_active" {{ $is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">
                                                        {{ $is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Gambar Jumbotron 1</label>
                                                    @if ($jumbo1)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $jumbo1->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_jumbo1)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_jumbo1) }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="jumbo1" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi
                                                            untuk
                                                            memilih gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                                            WEBP Maksimal 1MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Rasio gambar 16:9
                                                            (Rekomendasi: 1920x1080 Piksel)</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('jumbo1') is-invalid @enderror"
                                                            wire:model="jumbo1" accept="image/*">
                                                        @if ($jumbo1 || $tmp_jumbo1)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearJumbo1" title="Hapus gambar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-1">
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                    </path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                    </path>
                                                                </svg>
                                                                reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('jumbo1')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Gambar Jumbotron 2</label>
                                                    @if ($jumbo2)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $jumbo2->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_jumbo2)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_jumbo2) }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="jumbo2" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi
                                                            untuk
                                                            memilih gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                                            WEBP Maksimal 1MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Rasio gambar 16:9
                                                            (Rekomendasi: 1920x1080 Piksel)</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('jumbo2') is-invalid @enderror"
                                                            wire:model="jumbo2" accept="image/*">
                                                        @if ($jumbo2 || $tmp_jumbo2)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearJumbo2" title="Hapus gambar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-1">
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                    </path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                    </path>
                                                                </svg>
                                                                reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('jumbo2')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Gambar Jumbotron 3</label>
                                                    @if ($jumbo3)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $jumbo3->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_jumbo3)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_jumbo3) }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="jumbo3" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi
                                                            untuk
                                                            memilih gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                                            WEBP Maksimal 1MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Rasio gambar 16:9
                                                            (Rekomendasi: 1920x1080 Piksel)</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('jumbo3') is-invalid @enderror"
                                                            wire:model="jumbo3" accept="image/*">
                                                        @if ($jumbo3 || $tmp_jumbo3)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearJumbo3" title="Hapus gambar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-1">
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                    </path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                    </path>
                                                                </svg>
                                                                reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('jumbo3')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Gambar Jumbotron 4</label>
                                                    @if ($jumbo4)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $jumbo4->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_jumbo4)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_jumbo4) }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="jumbo4" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi
                                                            untuk
                                                            memilih gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                                            WEBP Maksimal 1MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Rasio gambar 16:9
                                                            (Rekomendasi: 1920x1080 Piksel)</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('jumbo4') is-invalid @enderror"
                                                            wire:model="jumbo4" accept="image/*">
                                                        @if ($jumbo4 || $tmp_jumbo4)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearJumbo4" title="Hapus gambar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-1">
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                    </path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                    </path>
                                                                </svg>
                                                                reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('jumbo4')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Gambar Jumbotron 5</label>
                                                    @if ($jumbo5)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $jumbo5->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_jumbo5)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_jumbo5) }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="jumbo5" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi
                                                            untuk
                                                            memilih gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                                            WEBP Maksimal 1MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Rasio gambar 16:9
                                                            (Rekomendasi: 1920x1080 Piksel)</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('jumbo5') is-invalid @enderror"
                                                            wire:model="jumbo5" accept="image/*">
                                                        @if ($jumbo5 || $tmp_jumbo5)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearJumbo5" title="Hapus gambar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 22" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-1">
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                    </path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                    </path>
                                                                </svg>
                                                                reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('jumbo5')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Gambar Jumbotron 6</label>
                                                    @if ($jumbo6)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $jumbo6->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_jumbo6)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_jumbo6) }}'); background-size: cover; background-position: center; height: 150px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="jumbo6" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi
                                                            untuk
                                                            memilih gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                                            WEBP Maksimal 1MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Rasio gambar 16:9
                                                            (Rekomendasi: 1920x1080 Piksel)</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('jumbo6') is-invalid @enderror"
                                                            wire:model="jumbo6" accept="image/*">
                                                        @if ($jumbo6 || $tmp_jumbo6)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearJumbo6" title="Hapus gambar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-1">
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                    </path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                    </path>
                                                                </svg>
                                                                reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('jumbo6')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer rounded-bottom-4 border-0 sticky-bottom"
                                    style="background-color: rgba(255, 255, 255, 0.9);">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" wire:click="cancelForm"
                                            class="btn py-2 px-2 rounded-3 shadow-sm">
                                            <span wire:loading.remove wire:target="cancelForm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-copy-x">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                                    <path
                                                        d="M4.012 16.737a2 2 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                                                    <path d="M11.5 11.5l4.9 5" />
                                                    <path d="M16.5 11.5l-5.1 5" />
                                                </svg>
                                                Tutup
                                            </span>
                                            <span wire:loading wire:target="cancelForm">
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
                                                {{ $isEdit ? 'Perbarui' : 'Simpan' }}
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

                        <div class="card-body border-bottom py-3">
                            <div class="d-flex">
                                <div class="text-secondary">
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
                                        <th>Pengunggah</th>
                                        <th>Jumbotron 1</th>
                                        <th>Jumbotron 2</th>
                                        <th>Jumbotron 3</th>
                                        <th>Jumbotron 4</th>
                                        <th>Jumbotron 5</th>
                                        <th>Jumbotron 6</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jumboList as $jumbo)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-wrap">{{ $jumbo->user->name ?? '-' }}</td>
                                            <td>
                                                @if ($jumbo->jumbo1)
                                                    <img src="{{ asset($jumbo->jumbo1) }}" width="60"
                                                        class="img-thumbnail">
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($jumbo->jumbo2)
                                                    <img src="{{ asset($jumbo->jumbo2) }}" width="60"
                                                        class="img-thumbnail">
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($jumbo->jumbo3)
                                                    <img src="{{ asset($jumbo->jumbo3) }}" width="60"
                                                        class="img-thumbnail">
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($jumbo->jumbo4)
                                                    <img src="{{ asset($jumbo->jumbo4) }}" width="60"
                                                        class="img-thumbnail">
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($jumbo->jumbo5)
                                                    <img src="{{ asset($jumbo->jumbo5) }}" width="60"
                                                        class="img-thumbnail">
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($jumbo->jumbo6)
                                                    <img src="{{ asset($jumbo->jumbo6) }}" width="60"
                                                        class="img-thumbnail">
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $jumbo->is_active ? 'bg-primary-lt' : 'bg-danger-lt' }}">
                                                    {{ $jumbo->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <button wire:click="edit('{{ $jumbo->id }}')"
                                                    class="btn py-2 px-2 rounded-3 shadow-sm">
                                                    <span wire:loading.remove
                                                        wire:target="edit('{{ $jumbo->id }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                            <path
                                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                            <path d="M16 5l3 3" />
                                                        </svg>
                                                        Ubah
                                                    </span>
                                                    <span wire:loading wire:target="edit('{{ $jumbo->id }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </span>
                                                </button>
                                                <button wire:click="delete('{{ $jumbo->id }}')"
                                                    class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal">
                                                    <span wire:loading.remove
                                                        wire:target="delete('{{ $jumbo->id }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M4 7l16 0" />
                                                            <path d="M10 11l0 6" />
                                                            <path d="M14 11l0 6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                        </svg>
                                                        Hapus
                                                    </span>
                                                    <span wire:loading wire:target="delete('{{ $jumbo->id }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="card-footer d-flex align-items-center justify-content-end pb-0 rounded-4 shadow-sm">
                            {{ $jumboList->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.jumbotron.delete')

    @script
        <script>
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

        <script>
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('resetFileInput', (data) => {
                    const inputName = data.inputName;
                    const fileInput = document.querySelector(`input[wire\\:model="${inputName}"]`);
                    if (fileInput) {
                        fileInput.value = '';
                        fileInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });

                // Sinkronkan status toggle setelah pembaruan Livewire
                Livewire.on('updated', (data) => {
                    const isActiveInput = document.getElementById('is_active');
                    if (isActiveInput) {
                        isActiveInput.checked = @json($is_active);
                    }
                });
            });
        </script>
    @endscript
</div>
