<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header bg-dark text-white rounded-top-4">
                            <h3 class="card-title d-none d-md-block">
                                Daftar Jumbotron
                            </h3>
                        </div>
                        <form wire:submit.prevent="save">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label">Status Jumbotron Masjid</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="is_active" wire:change="$refresh" id="is_active"
                                                    {{ $is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    {{ $is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 1</label>
                                                @if ($jumbotron_masjid_1)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_1->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_1)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_1) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_1"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP. Otomatis dikompres ke WebP (maks 80KB) di browser sebelum diunggah.</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_1-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_1') is-invalid @enderror"
                                                        accept="image/*"
                                                        onchange="handleJumbotronMasjidUpload(this, 'jumbotron_masjid_1')">
                                                    @if ($jumbotron_masjid_1 || $tmp_jumbotron_masjid_1)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid1"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                <div id="jumbotron_masjid_1-compress-status" class="small text-primary mt-1" style="display: none;"></div>
                                                <div id="jumbotron_masjid_1-upload-error" class="invalid-feedback" style="display: none;"></div>
                                                @error('jumbotron_masjid_1')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 2</label>
                                                @if ($jumbotron_masjid_2)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_2->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_2)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_2) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_2"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP. Otomatis dikompres ke WebP (maks 80KB) di browser sebelum diunggah.</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_2-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_2') is-invalid @enderror"
                                                        accept="image/*"
                                                        onchange="handleJumbotronMasjidUpload(this, 'jumbotron_masjid_2')">
                                                    @if ($jumbotron_masjid_2 || $tmp_jumbotron_masjid_2)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid2"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                <div id="jumbotron_masjid_2-compress-status" class="small text-primary mt-1" style="display: none;"></div>
                                                <div id="jumbotron_masjid_2-upload-error" class="invalid-feedback" style="display: none;"></div>
                                                @error('jumbotron_masjid_2')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 3</label>
                                                @if ($jumbotron_masjid_3)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_3->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_3)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_3) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_3"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP. Otomatis dikompres ke WebP (maks 80KB) di browser sebelum diunggah.</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_3-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_3') is-invalid @enderror"
                                                        accept="image/*"
                                                        onchange="handleJumbotronMasjidUpload(this, 'jumbotron_masjid_3')">
                                                    @if ($jumbotron_masjid_3 || $tmp_jumbotron_masjid_3)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid3"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                <div id="jumbotron_masjid_3-compress-status" class="small text-primary mt-1" style="display: none;"></div>
                                                <div id="jumbotron_masjid_3-upload-error" class="invalid-feedback" style="display: none;"></div>
                                                @error('jumbotron_masjid_3')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 4</label>
                                                @if ($jumbotron_masjid_4)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_4->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_4)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_4) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_4"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP. Otomatis dikompres ke WebP (maks 80KB) di browser sebelum diunggah.</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_4-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_4') is-invalid @enderror"
                                                        accept="image/*"
                                                        onchange="handleJumbotronMasjidUpload(this, 'jumbotron_masjid_4')">
                                                    @if ($jumbotron_masjid_4 || $tmp_jumbotron_masjid_4)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid4"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                <div id="jumbotron_masjid_4-compress-status" class="small text-primary mt-1" style="display: none;"></div>
                                                <div id="jumbotron_masjid_4-upload-error" class="invalid-feedback" style="display: none;"></div>
                                                @error('jumbotron_masjid_4')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 5</label>
                                                @if ($jumbotron_masjid_5)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_5->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_5)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_5) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_5"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP. Otomatis dikompres ke WebP (maks 80KB) di browser sebelum diunggah.</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_5-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_5') is-invalid @enderror"
                                                        accept="image/*"
                                                        onchange="handleJumbotronMasjidUpload(this, 'jumbotron_masjid_5')">
                                                    @if ($jumbotron_masjid_5 || $tmp_jumbotron_masjid_5)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid5"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                <div id="jumbotron_masjid_5-compress-status" class="small text-primary mt-1" style="display: none;"></div>
                                                <div id="jumbotron_masjid_5-upload-error" class="invalid-feedback" style="display: none;"></div>
                                                @error('jumbotron_masjid_5')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 6</label>
                                                @if ($jumbotron_masjid_6)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_6->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_6)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_6) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_6"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP. Otomatis dikompres ke WebP (maks 80KB) di browser sebelum diunggah.</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_6-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_6') is-invalid @enderror"
                                                        accept="image/*"
                                                        onchange="handleJumbotronMasjidUpload(this, 'jumbotron_masjid_6')">
                                                    @if ($jumbotron_masjid_6 || $tmp_jumbotron_masjid_6)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid6"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                <div id="jumbotron_masjid_6-compress-status" class="small text-primary mt-1" style="display: none;"></div>
                                                <div id="jumbotron_masjid_6-upload-error" class="invalid-feedback" style="display: none;"></div>
                                                @error('jumbotron_masjid_6')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer rounded-bottom-4 border-0">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('jumbotron-masjid.index') }}" wire:navigate
                                        class="btn py-2 px-2 rounded-3 shadow-sm">
                                        <span class="d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left-dashed">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 12h6m3 0h1.5m3 0h.5" />
                                                <path d="M5 12l6 6" />
                                                <path d="M5 12l6 -6" />
                                            </svg>
                                            Batal
                                        </span>
                                    </a>
                                    <button type="submit" class="btn btn-primary py-2 px-2 rounded-3 shadow-sm"
                                        wire:loading.attr="disabled">
                                        <span class="d-flex align-items-center" wire:target="save"
                                            wire:loading.class="d-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                <path d="M14 4l0 4l-6 0l0 -4" />
                                            </svg>
                                            Simpan
                                        </span>
                                        <span class="d-none d-flex align-items-center" wire:target="save"
                                            wire:loading.class.remove="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Menyimpan...</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Script Kompresi Client-Side ke WebP (Maks 80KB) & Upload Livewire untuk Jumbotron Gambar Masjid --}}
<script>
    if (typeof compressImageToWebP !== 'function') {
        function compressImageToWebP(file, maxSizeBytes = 80 * 1024) {
            return new Promise((resolve, reject) => {
                if (!file || !file.type.match(/^image\//i)) {
                    return reject(new Error('File yang dipilih bukan gambar yang valid.'));
                }

                const reader = new FileReader();
                reader.onerror = () => reject(new Error('Gagal membaca file gambar.'));
                reader.onload = (e) => {
                    const img = new Image();
                    img.onerror = () => reject(new Error('Gagal memproses file gambar.'));
                    img.onload = () => {
                        const maxInitialWidth = 1920;
                        const maxInitialHeight = 1080;
                        let curWidth = img.naturalWidth || img.width;
                        let curHeight = img.naturalHeight || img.height;

                        if (curWidth > maxInitialWidth || curHeight > maxInitialHeight) {
                            const ratio = Math.min(maxInitialWidth / curWidth, maxInitialHeight / curHeight);
                            curWidth = Math.round(curWidth * ratio);
                            curHeight = Math.round(curHeight * ratio);
                        }

                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        if (!ctx) {
                            return reject(new Error('Canvas 2D tidak didukung di browser ini.'));
                        }

                        const getBlob = (w, h, q) => {
                            return new Promise((res) => {
                                canvas.width = w;
                                canvas.height = h;
                                ctx.imageSmoothingEnabled = true;
                                ctx.imageSmoothingQuality = 'high';
                                ctx.clearRect(0, 0, w, h);
                                ctx.drawImage(img, 0, 0, w, h);
                                canvas.toBlob((blob) => {
                                    res(blob);
                                }, 'image/webp', q);
                            });
                        };

                        (async () => {
                            try {
                                let quality = 0.85;
                                let blob = await getBlob(curWidth, curHeight, quality);

                                while (blob && blob.size > maxSizeBytes && quality > 0.30) {
                                    quality -= 0.10;
                                    blob = await getBlob(curWidth, curHeight, Math.max(quality, 0.20));
                                }

                                let attempts = 0;
                                while (blob && blob.size > maxSizeBytes && attempts < 10) {
                                    curWidth = Math.round(curWidth * 0.85);
                                    curHeight = Math.round(curHeight * 0.85);
                                    quality = 0.75;
                                    blob = await getBlob(curWidth, curHeight, quality);

                                    while (blob && blob.size > maxSizeBytes && quality > 0.25) {
                                        quality -= 0.10;
                                        blob = await getBlob(curWidth, curHeight, Math.max(quality, 0.20));
                                    }
                                    attempts++;
                                }

                                if (!blob) {
                                    return reject(new Error('Gagal mengompres gambar ke format WebP.'));
                                }

                                const originalName = file.name.replace(/\.[^/.]+$/, '');
                                const safeName = originalName.replace(/[^a-zA-Z0-9_-]/g, '_');
                                const webpFile = new File([blob], `${safeName || 'jumbo_masjid'}.webp`, {
                                    type: 'image/webp',
                                    lastModified: Date.now()
                                });

                                resolve({
                                    file: webpFile,
                                    originalSize: file.size,
                                    compressedSize: blob.size,
                                    width: curWidth,
                                    height: curHeight
                                });
                            } catch (err) {
                                reject(err);
                            }
                        })();
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    }

    function handleJumbotronMasjidUpload(inputEl, propertyName) {
        const file = inputEl.files && inputEl.files[0];
        if (!file) return;

        const statusEl = document.getElementById(`${propertyName}-compress-status`);
        const errorEl = document.getElementById(`${propertyName}-upload-error`);

        inputEl.classList.remove('is-invalid');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
        if (statusEl) {
            statusEl.className = 'small text-primary mt-1';
            statusEl.style.display = 'block';
            statusEl.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengompres gambar ke WebP (maks 80KB)...';
        }

        const container = inputEl.closest('[wire\\:id]');
        const wireId = container ? container.getAttribute('wire:id') : null;
        const component = (window.Livewire && wireId) ? window.Livewire.find(wireId) : null;

        if (!component) {
            if (statusEl) statusEl.style.display = 'none';
            if (errorEl) {
                errorEl.textContent = 'Komponen Livewire tidak ditemukan.';
                errorEl.style.display = 'block';
            }
            return;
        }

        compressImageToWebP(file, 80 * 1024)
            .then((result) => {
                const { file: compressedFile, originalSize, compressedSize } = result;
                const origKb = (originalSize / 1024).toFixed(1);
                const compKb = (compressedSize / 1024).toFixed(1);

                if (statusEl) {
                    statusEl.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengunggah WebP (${compKb} KB)...`;
                }

                component.upload(
                    propertyName,
                    compressedFile,
                    () => {
                        if (statusEl) {
                            statusEl.className = 'small text-success mt-1';
                            statusEl.style.display = 'block';
                            statusEl.innerHTML = `✓ Berhasil dikompres ke WebP: <strong>${compKb} KB</strong> (dari ${origKb} KB)`;
                        }
                        inputEl.classList.remove('is-invalid');
                        if (errorEl) errorEl.style.display = 'none';
                    },
                    (error) => {
                        if (statusEl) statusEl.style.display = 'none';
                        inputEl.classList.add('is-invalid');
                        if (errorEl) {
                            errorEl.textContent = error || 'Gagal mengunggah file ke server.';
                            errorEl.style.display = 'block';
                        }
                    },
                    (event) => {
                        const percent = event?.detail?.progress || 0;
                        if (statusEl) {
                            statusEl.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengunggah WebP (${compKb} KB) ${percent}%...`;
                        }
                    }
                );
            })
            .catch((err) => {
                if (statusEl) statusEl.style.display = 'none';
                inputEl.classList.add('is-invalid');
                if (errorEl) {
                    errorEl.textContent = err?.message || 'Terjadi kesalahan saat mengompres gambar.';
                    errorEl.style.display = 'block';
                }
            });
    }

    function resetJumbotronMasjidInput(name) {
        if (!name) return;
        const input = document.getElementById(`${name}-input`);
        const statusEl = document.getElementById(`${name}-compress-status`);
        const errorEl = document.getElementById(`${name}-upload-error`);

        if (input) {
            input.value = '';
            input.classList.remove('is-invalid');
        }
        if (statusEl) {
            statusEl.style.display = 'none';
            statusEl.textContent = '';
        }
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.Livewire) {
            window.Livewire.on('resetFileInput', (event) => {
                const name = event?.inputName || (Array.isArray(event) && event[0]?.inputName) || event;
                resetJumbotronMasjidInput(name);
            });
        }
    });

    window.addEventListener('resetFileInput', (event) => {
        const detail = event?.detail;
        const name = detail?.inputName || (Array.isArray(detail) && detail[0]?.inputName) || detail;
        resetJumbotronMasjidInput(name);
    });
</script>
@script
    <script>
        $wire.on('resetFileInput', (data) => {
            const name = data?.inputName || (Array.isArray(data) && data[0]?.inputName) || data;
            if (typeof resetJumbotronMasjidInput === 'function') {
                resetJumbotronMasjidInput(name);
            }
        });
        $wire.on('reset_success', message => {
            if (window.iziToast) {
                iziToast.success({
                    title: 'Berhasil',
                    message: Array.isArray(message) ? message[0] : message,
                    position: 'topRight'
                });
            }
        });
        $wire.on('reset_error', message => {
            if (window.iziToast) {
                iziToast.error({
                    title: 'Gagal',
                    message: Array.isArray(message) ? message[0] : message,
                    position: 'topRight'
                });
            }
        });
        $wire.on('success', message => {
            if (window.iziToast) {
                iziToast.success({
                    title: 'Berhasil',
                    message: Array.isArray(message) ? message[0] : message,
                    position: 'topRight'
                });
            }
        });
        $wire.on('error', message => {
            if (window.iziToast) {
                iziToast.error({
                    title: 'Gagal',
                    message: Array.isArray(message) ? message[0] : message,
                    position: 'topRight'
                });
            }
        });
    </script>
@endscript
