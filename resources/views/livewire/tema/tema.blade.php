<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                @if ($isAdmin && $showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Tema' : 'Tambah Tema Baru' }}
                                @else
                                    {{ $isAdmin ? 'Daftar Tema' : 'Tema' }}
                                @endif
                            </h3>
                            @if ($isAdmin && !$showForm)
                                <div class="card-actions">
                                    <button wire:click="showAddForm" class="btn py-2 px-2 rounded-3 shadow-sm"
                                        @if (Auth::user()->role !== 'Super Admin') style="display: none;" @endif>
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
                                            Tambah Tema
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
                        @if ($isAdmin && $showForm)
                            <form wire:submit.prevent="save">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-2">
                                                    <label class="form-label required">Nama Tema</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="text"
                                                        class="form-control rounded-3 @error('name') is-invalid @enderror"
                                                        wire:model="name" placeholder="Masukkan nama tema">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Gambar Pratinjau</label>
                                                    @if ($preview_image)
                                                        <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                                            <div class="img-responsive img-responsive-21x9"
                                                                style="background-image: url('{{ $preview_image->temporaryUrl() }}'); background-size: contain; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif ($temp_preview_image)
                                                        <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                                            <div class="img-responsive img-responsive-21x9"
                                                                style="background-image: url('{{ asset($temp_preview_image) }}'); background-size: contain; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="preview_image"
                                                        class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-danger">*</small>
                                                        <small class="text-muted">Format: jpg, jpeg, png, webp, gif.
                                                            Maks: 1MB</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('preview_image') is-invalid @enderror"
                                                            wire:model="preview_image" accept="image/*">
                                                        @if ($preview_image || $temp_preview_image)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearPreviewImage" title="Hapus gambar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M4 7l16 0" />
                                                                    <path d="M10 11l0 6" />
                                                                    <path d="M14 11l0 6" />
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path
                                                                        d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                </svg>
                                                                Reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('preview_image')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">File CSS</label>
                                                    @if ($css_file)
                                                        <div class="card p-2 rounded-3 shadow-sm border mb-2">
                                                            <span class="text-muted">File:
                                                                {{ $css_file->getClientOriginalName() }}</span>
                                                        </div>
                                                    @elseif ($temp_css_file)
                                                        <div class="card p-2 rounded-3 shadow-sm border mb-2">
                                                            <span class="text-muted">File:
                                                                {{ basename($temp_css_file) }}</span>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="css_file" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-danger">*</small>
                                                        <small class="text-muted">Format: .css. Maks: 1MB</small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file"
                                                            class="form-control my-2 rounded-4 @error('css_file') is-invalid @enderror"
                                                            wire:model="css_file" accept=".css">
                                                        @if ($css_file || $temp_css_file)
                                                            <button type="button"
                                                                class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                                wire:click="clearCssFile" title="Hapus file">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M4 7l16 0" />
                                                                    <path d="M10 11l0 6" />
                                                                    <path d="M14 11l0 6" />
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path
                                                                        d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                </svg>
                                                                Reset
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('css_file')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer rounded-bottom border-0 sticky-bottom"
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
                                                        d="M4.012 16.737a2 2 0 0 1 -1.012 -1.737v-10 c0 -1.1 .9 -2 2 -2h10 c.75 0 1.158 .385 1.5 1" />
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
                        @elseif (!$isAdmin)
                            <!-- Form untuk user memilih tema -->
                            <form wire:submit.prevent="selectTheme">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-2">
                                                    <label class="form-label required">Pilih Tema</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <select
                                                        class="form-select rounded-3 @error('selectedThemeId') is-invalid @enderror"
                                                        wire:model.live="selectedThemeId">
                                                        <option value="">Tidak ada tema (default)</option>
                                                        @foreach ($availableThemes as $theme)
                                                            <option value="{{ $theme->id }}">
                                                                {{ $theme->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('selectedThemeId')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cards">
                                        @foreach ($availableThemes as $theme)
                                            <div class="col-md-4" wire:key="theme-{{ $theme->id }}">
                                                <div
                                                    class="card mb-3 rounded-4 pt-3 shadow-sm {{ $selectedThemeId == $theme->id ? 'border-primary bg-light shadow-sm' : 'shadow-sm' }}">
                                                    <div class="position-relative">
                                                        <img src="{{ $theme->preview_image ? asset($theme->preview_image) : asset('images/other/default-theme.jpg') }}"
                                                            class="card-img-top rounded-4 {{ $selectedThemeId == $theme->id ? 'opacity-50' : 'opacity-100' }}"
                                                            alt="{{ $theme->name }}"
                                                            style="height: 200px; object-fit: cover;">
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
                                                                        d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
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
                                <div class="card-footer rounded-4 border-0 sticky-bottom"
                                    style="background-color: rgba(255, 255, 255, 0.9);">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if ($selectedThemeId !== $initialThemeId)
                                            <button type="button" wire:click="cancelSelection"
                                                class="btn py-2 px-2 rounded-3 shadow-sm"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="cancelSelection">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M18 6l-12 12" />
                                                        <path d="M6 6l12 12" />
                                                    </svg>
                                                    Batal
                                                </span>
                                                <span wire:loading wire:target="cancelSelection">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Loading...</span>
                                                </span>
                                            </button>
                                        @endif
                                        <button type="submit" class="btn py-2 px-2 rounded-3 shadow-sm"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="selectTheme">
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
                                            <span wire:loading wire:target="selectTheme">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="small">Menyimpan...</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        {{-- Table Section --}}
                        @include('livewire.tema.table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($isAdmin)
        {{-- Delete Modal --}}
        @include('livewire.tema.delete')
    @endif

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

            $wire.on('resetFileInput', event => {
                const input = document.querySelector(`input[name="${event.inputName}"]`);
                if (input) {
                    input.value = '';
                }
            });
        </script>
    @endscript
</div>
