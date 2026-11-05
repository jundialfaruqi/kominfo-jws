@if ($showForm)
    <form wire:submit.prevent="save">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">Nama Masjid</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror"
                                wire:model="name"
                                @if (!$isEdit) wire:keyup="generateSlugFromName" @endif
                                placeholder="Masukkan nama masjid">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">ID JWS</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control rounded-3 @error('slug') is-invalid @enderror"
                                wire:model="slug" placeholder="Masukkan ID JWS (URL-friendly)">
                            <div class="form-text">
                                <small class="text-muted">ID JWS akan digunakan untuk URL. Hanya boleh mengandung huruf
                                    kecil, angka, dan tanda hubung (-)</small>
                                @if ($isEdit)
                                    <br><small class="text-info">Saat edit, ID JWS tidak akan otomatis berubah meskipun
                                        nama masjid diubah</small>
                                @else
                                    <br><small class="text-success">ID JWS akan otomatis dibuat dari nama masjid</small>
                                @endif
                            </div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <label class="form-label">Admin Masjid</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-select rounded-3 @error('userId') is-invalid @enderror"
                                    wire:model="userId">
                                    <option class="dropdown-header" value="">Pilih
                                        Admin
                                        Masjid</option>

                                    {{-- Jika sedang edit dan user sudah dipilih, tampilkan user tersebut --}}
                                    @if ($isEdit && $userId)
                                        @php
                                            $selectedUser = \App\Models\User::find($userId);
                                        @endphp
                                        @if ($selectedUser && (Auth::user()->role === 'Super Admin' || !in_array($selectedUser->role, ['Super Admin', 'Admin'])))
                                            <option value="{{ $selectedUser->id }}" selected>
                                                {{ $selectedUser->name }} (Dipilih)
                                            </option>
                                        @endif
                                    @endif

                                    {{-- Tampilkan user yang belum memiliki profil --}}
                                    @foreach ($users as $user)
                                        {{-- Jangan tampilkan user yang sudah dipilih saat edit --}}
                                        @if (!($isEdit && $userId == $user->id))
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach

                                    {{-- Jika tidak ada user yang tersedia --}}
                                    @if ($users->isEmpty() && !($isEdit && $userId))
                                        <option disabled>Tidak ada user yang tersedia
                                        </option>
                                    @endif
                                </select>
                                @error('userId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Informasi tambahan --}}
                                @if ($users->isEmpty() && !$isEdit)
                                    <div class="form-text">
                                        <small class="text-muted">Semua user sudah memiliki
                                            profil masjid</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">No HP</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control rounded-3 @error('phone') is-invalid @enderror"
                                wire:model="phone" placeholder="Masukkan nomor HP">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">Alamat Masjid</label>
                        </div>
                        <div class="col-md-10">
                            <textarea class="form-control rounded-3 @error('address') is-invalid @enderror" wire:model="address" rows="3"
                                placeholder="Masukkan alamat masjid"></textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-10 offset-md-2">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Logo Masjid</label>
                            {{-- Photo logo masjid --}}
                            @if ($logo_masjid)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ $logo_masjid->temporaryUrl() }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @elseif($temp_logo)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ asset($temp_logo) }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="logo_masjid" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png, webp, gif
                                    (Otomatis dikompres ke WebP maks 70KB)</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran persegi untuk
                                    kualitas
                                    gambar terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="logo_masjid-input"
                                    class="form-control my-2 rounded-4 @error('logo_masjid') is-invalid @enderror"
                                    accept="image/*" onchange="handleLogoUpload(this, 'logo_masjid')">
                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($logo_masjid || $temp_logo)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearLogoMasjid" title="Hapus gambar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                            <path d="M4 7l16 0"></path>
                                            <path d="M10 11l0 6"></path>
                                            <path d="M14 11l0 6"></path>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                            </path>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                            </path>
                                        </svg>
                                        reset
                                    </button>
                                @endif
                            </div>
                            <div wire:ignore>
                                <div id="logo_masjid-progress-wrap" class="mt-2" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span id="logo_masjid-progress-label" class="small text-primary">
                                            Mengompres gambar...
                                        </span>
                                        <span id="logo_masjid-progress-percent" class="small fw-bold text-primary">0%</span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 8px;">
                                        <div id="logo_masjid-progress-bar"
                                            class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div id="logo_masjid-compress-status" class="small text-success mt-1" style="display: none;"></div>
                                <div id="logo_masjid-upload-error" class="invalid-feedback" style="display: none;"></div>
                            </div>
                            @error('logo_masjid')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Logo Instansi</label>
                            {{-- Photo logo pemerintah --}}
                            @if ($logo_pemerintah)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ $logo_pemerintah->temporaryUrl() }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @elseif($temp_logo_pemerintah)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ asset($temp_logo_pemerintah) }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="logo_pemerintah" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png, webp, gif
                                    (Otomatis dikompres ke WebP maks 70KB)</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran persegi untuk
                                    kualitas
                                    gambar terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="logo_pemerintah-input"
                                    class="form-control my-2 rounded-4 @error('logo_pemerintah') is-invalid @enderror"
                                    accept="image/*" onchange="handleLogoUpload(this, 'logo_pemerintah')">
                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($logo_pemerintah || $temp_logo_pemerintah)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearLogoPemerintah" title="Hapus gambar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-1">
                                            <path d="M4 7l16 0"></path>
                                            <path d="M10 11l0 6"></path>
                                            <path d="M14 11l0 6"></path>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                            </path>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                            </path>
                                        </svg>
                                        reset
                                    </button>
                                @endif
                            </div>
                            <div wire:ignore>
                                <div id="logo_pemerintah-progress-wrap" class="mt-2" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span id="logo_pemerintah-progress-label" class="small text-primary">
                                            Mengompres gambar...
                                        </span>
                                        <span id="logo_pemerintah-progress-percent" class="small fw-bold text-primary">0%</span>
                                    </div>
                                    <div class="progress rounded-pill" style="height: 8px;">
                                        <div id="logo_pemerintah-progress-bar"
                                            class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div id="logo_pemerintah-compress-status" class="small text-success mt-1" style="display: none;"></div>
                                <div id="logo_pemerintah-upload-error" class="invalid-feedback" style="display: none;"></div>
                            </div>
                            @error('logo_pemerintah')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer rounded-bottom-4 border-0">
            <div class="d-flex justify-content-end gap-2">
                @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                    <button type="button" wire:click="cancelForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                        <span wire:loading.remove wire:target="cancelForm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-copy-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path stroke="none" d="M0 0h24v24H0z" />
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
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="small">Loading...</span>
                        </span>
                    </button>
                @endif
                <button type="submit" id="profil-submit-btn" class="btn py-2 px-2 rounded-3 shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                            <path d="M6.5 12h14.5" />
                        </svg>
                        {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                    </span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="small">Menyimpan...</span>
                    </span>
                </button>
            </div>
        </div>
    </form>
@endif

{{-- Script Kompresi Logo Client-Side ke WebP (Maks 70KB) dengan Progress Bar & Fallback ke Server --}}
<script>
    /**
     * Format ukuran byte menjadi teks KB / MB yang mudah dibaca.
     */
    function formatLogoFileSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }
        return (bytes / 1024).toFixed(1) + ' KB';
    }

    /**
     * Update UI progress loading bar untuk properti logo tertentu.
     */
    function updateLogoProgressUI(propertyName, percent, labelText, barClass = 'bg-primary') {
        const wrapEl = document.getElementById(`${propertyName}-progress-wrap`);
        const labelEl = document.getElementById(`${propertyName}-progress-label`);
        const percentEl = document.getElementById(`${propertyName}-progress-percent`);
        const barEl = document.getElementById(`${propertyName}-progress-bar`);

        const clamped = Math.max(0, Math.min(100, Math.round(percent)));

        if (wrapEl) {
            wrapEl.style.display = 'block';
        }
        if (labelEl && labelText) {
            labelEl.textContent = labelText;
        }
        if (percentEl) {
            percentEl.textContent = `${clamped}%`;
        }
        if (barEl) {
            barEl.className = `progress-bar progress-bar-striped progress-bar-animated ${barClass}`;
            barEl.style.width = `${clamped}%`;
            barEl.setAttribute('aria-valuenow', String(clamped));
        }
    }

    function hideLogoProgressUI(propertyName) {
        const wrapEl = document.getElementById(`${propertyName}-progress-wrap`);
        if (wrapEl) {
            wrapEl.style.display = 'none';
        }
    }

    /**
     * Kompres gambar logo di browser ke WebP (crop persegi 1:1 di tengah) dengan batas maksimal 70KB (71.680 bytes).
     * Mendukung file gambar ukuran besar (mis. 10MB+) tanpa batas 1MB saat pemilihan file.
     */
    function compressLogoToWebP(file, maxSizeBytes = 70 * 1024, onProgress = null) {
        return new Promise((resolve, reject) => {
            if (!file || !file.type || !file.type.match(/^image\//i)) {
                return reject(new Error('File yang dipilih bukan gambar yang valid.'));
            }

            if (typeof onProgress === 'function') {
                onProgress(10, 'Membaca file gambar...');
            }

            const objectUrl = URL.createObjectURL(file);
            const img = new Image();

            img.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                reject(new Error('Gagal memuat file gambar di browser.'));
            };

            img.onload = () => {
                URL.revokeObjectURL(objectUrl);

                if (typeof onProgress === 'function') {
                    onProgress(20, 'Menyiapkan kanvas & memotong rasio 1:1...');
                }

                const srcW = img.naturalWidth || img.width;
                const srcH = img.naturalHeight || img.height;
                if (!srcW || !srcH) {
                    return reject(new Error('Dimensi gambar tidak valid.'));
                }

                // Crop tengah 1:1 persegi dan batasi ukuran awal maksimal 800x800 px
                const cropSize = Math.min(srcW, srcH);
                const sx = Math.floor((srcW - cropSize) / 2);
                const sy = Math.floor((srcH - cropSize) / 2);

                const maxInitialDim = 800;
                let curSize = Math.min(cropSize, maxInitialDim);

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    return reject(new Error('Canvas 2D tidak didukung di browser ini.'));
                }

                const getBlob = (size, q, mimeType) => {
                    return new Promise((res) => {
                        canvas.width = size;
                        canvas.height = size;
                        ctx.imageSmoothingEnabled = true;
                        ctx.imageSmoothingQuality = 'high';
                        if (mimeType === 'image/jpeg') {
                            // Isi background putih untuk JPEG agar area transparan (mis. PNG) tidak menjadi hitam di Safari
                            ctx.fillStyle = '#ffffff';
                            ctx.fillRect(0, 0, size, size);
                        } else {
                            ctx.clearRect(0, 0, size, size);
                        }
                        ctx.drawImage(img, sx, sy, cropSize, cropSize, 0, 0, size, size);
                        canvas.toBlob((blob) => {
                            res(blob);
                        }, mimeType, q);
                    });
                };

                const nextFrame = () => new Promise((r) => setTimeout(r, 15));

                (async () => {
                    try {
                        let quality = 0.85;
                        let targetMime = 'image/webp';
                        let targetExt = 'webp';
                        let formatLabel = 'WebP';

                        if (typeof onProgress === 'function') {
                            onProgress(30, 'Mengompres gambar ke WebP (maks 70KB)...');
                        }
                        await nextFrame();

                        let blob = await getBlob(curSize, quality, targetMime);

                        // Antisipasi Safari: jika browser tidak mendukung encoding WebP (mengembalikan image/png atau null),
                        // otomatis konversi dan kompres ke JPEG (image/jpeg) di sisi client
                        if (!blob || blob.type !== 'image/webp') {
                            targetMime = 'image/jpeg';
                            targetExt = 'jpg';
                            formatLabel = 'JPEG';
                            if (typeof onProgress === 'function') {
                                onProgress(32, 'Safari terdeteksi: Mengonversi & mengompres ke JPEG (maks 70KB)...');
                            }
                            await nextFrame();
                            blob = await getBlob(curSize, quality, targetMime);
                        }

                        if (!blob || (blob.type !== 'image/webp' && blob.type !== 'image/jpeg')) {
                            return reject(new Error('Browser tidak mendukung kompresi Canvas WebP/JPEG.'));
                        }

                        // Tahap 1: Turunkan kualitas bertahap jika ukuran masih > 70KB
                        let step = 0;
                        while (blob && blob.size > maxSizeBytes && quality > 0.25) {
                            quality = Math.max(0.20, +(quality - 0.10).toFixed(2));
                            step++;
                            if (typeof onProgress === 'function') {
                                const prog = Math.min(50, 32 + step * 4);
                                onProgress(prog, `Mengompres kualitas ${formatLabel} (${formatLogoFileSize(blob.size)})...`);
                            }
                            await nextFrame();
                            blob = await getBlob(curSize, quality, targetMime);
                        }

                        // Tahap 2: Jika masih > 70KB, turunkan resolusi secara bertahap
                        let attempts = 0;
                        while (blob && blob.size > maxSizeBytes && attempts < 12 && curSize > 64) {
                            curSize = Math.max(64, Math.round(curSize * 0.85));
                            quality = 0.75;
                            attempts++;

                            if (typeof onProgress === 'function') {
                                const prog = Math.min(68, 50 + attempts * 2);
                                onProgress(prog, `Menyesuaikan dimensi ${formatLabel} ${curSize}x${curSize}px...`);
                            }
                            await nextFrame();

                            blob = await getBlob(curSize, quality, targetMime);
                            while (blob && blob.size > maxSizeBytes && quality > 0.25) {
                                quality = Math.max(0.20, +(quality - 0.12).toFixed(2));
                                blob = await getBlob(curSize, quality, targetMime);
                            }
                        }

                        if (!blob || blob.size > maxSizeBytes) {
                            return reject(new Error('Hasil kompresi di browser masih melebihi 70KB.'));
                        }

                        if (typeof onProgress === 'function') {
                            onProgress(70, `Kompresi ${formatLabel} selesai, mengunggah ke server...`);
                        }

                        const originalName = file.name.replace(/\.[^/.]+$/, '');
                        const safeName = originalName.replace(/[^a-zA-Z0-9_-]/g, '_');
                        const outputFile = new File([blob], `${safeName || 'logo'}.${targetExt}`, {
                            type: targetMime,
                            lastModified: Date.now()
                        });

                        resolve({
                            file: outputFile,
                            formatLabel,
                            originalSize: file.size,
                            compressedSize: blob.size,
                            width: curSize,
                            height: curSize
                        });
                    } catch (err) {
                        reject(err);
                    }
                })();
            };

            img.src = objectUrl;
        });
    }

    /**
     * Handler onchange saat file dipilih pada input logo_masjid / logo_pemerintah.
     * 1. Kompresi langsung di browser menjadi WebP <= 70KB dengan progress bar.
     * 2. Jika kompresi client gagal, otomatis fallback mengunggah ke server agar dikompres oleh backend.
     */
    function handleLogoUpload(inputEl, propertyName) {
        const file = inputEl.files && inputEl.files[0];
        if (!file) return;

        const statusEl = document.getElementById(`${propertyName}-compress-status`);
        const errorEl = document.getElementById(`${propertyName}-upload-error`);
        const submitBtn = document.getElementById('profil-submit-btn');

        inputEl.classList.remove('is-invalid');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
        if (statusEl) {
            statusEl.style.display = 'none';
            statusEl.textContent = '';
        }

        if (!file.type || !file.type.match(/^image\//i)) {
            inputEl.classList.add('is-invalid');
            if (errorEl) {
                errorEl.textContent = 'File harus berupa gambar (jpg, jpeg, png, webp, atau gif).';
                errorEl.style.display = 'block';
            }
            return;
        }

        const container = inputEl.closest('[wire\\:id]');
        const wireId = container ? container.getAttribute('wire:id') : null;
        const component = (window.Livewire && wireId) ? window.Livewire.find(wireId) : null;

        if (!component) {
            if (errorEl) {
                errorEl.textContent = 'Komponen Livewire tidak ditemukan.';
                errorEl.style.display = 'block';
            }
            return;
        }

        if (submitBtn) submitBtn.disabled = true;
        updateLogoProgressUI(propertyName, 5, 'Memulai kompresi gambar di browser...');

        compressLogoToWebP(file, 70 * 1024, (percent, label) => {
                updateLogoProgressUI(propertyName, percent, label, 'bg-primary');
            })
            .then((result) => {
                const {
                    file: compressedFile,
                    formatLabel = 'WebP',
                    originalSize,
                    compressedSize
                } = result;
                const origFormatted = formatLogoFileSize(originalSize);
                const compFormatted = formatLogoFileSize(compressedSize);

                updateLogoProgressUI(propertyName, 72, `Mengunggah ${formatLabel} (${compFormatted}) ke server...`, 'bg-primary');

                component.upload(
                    propertyName,
                    compressedFile,
                    () => {
                        updateLogoProgressUI(propertyName, 100, 'Selesai!', 'bg-success');
                        setTimeout(() => {
                            hideLogoProgressUI(propertyName);
                            if (statusEl) {
                                statusEl.className = 'small text-success mt-1';
                                statusEl.style.display = 'block';
                                statusEl.innerHTML =
                                    `✓ Berhasil dikompres ke ${formatLabel}: <strong>${compFormatted}</strong> (dari ${origFormatted})`;
                            }
                        }, 250);
                        inputEl.classList.remove('is-invalid');
                        if (errorEl) errorEl.style.display = 'none';
                        if (submitBtn) submitBtn.disabled = false;
                    },
                    (error) => {
                        hideLogoProgressUI(propertyName);
                        inputEl.classList.add('is-invalid');
                        if (errorEl) {
                            errorEl.textContent = error || 'Gagal mengunggah gambar ke server.';
                            errorEl.style.display = 'block';
                        }
                        if (submitBtn) submitBtn.disabled = false;
                    },
                    (event) => {
                        const uploadPercent = event?.detail?.progress || 0;
                        const totalPercent = 70 + Math.round(uploadPercent * 0.30);
                        updateLogoProgressUI(
                            propertyName,
                            totalPercent,
                            `Mengunggah ${formatLabel} (${compFormatted}) ${uploadPercent}%...`,
                            'bg-primary'
                        );
                    }
                );
            })
            .catch(() => {
                // Fallback sesuai spesifikasi #3:
                // Jika kompresi di sisi client gagal, kirim file ke server agar backend yang melakukan kompresi (<= 70KB)
                const origFormatted = formatLogoFileSize(file.size);
                updateLogoProgressUI(
                    propertyName,
                    30,
                    `Kompresi browser gagal, mengalihkan kompresi ke server (${origFormatted})...`,
                    'bg-warning'
                );

                component.upload(
                    propertyName,
                    file,
                    () => {
                        updateLogoProgressUI(propertyName, 100, 'Dikompresi oleh server!', 'bg-success');
                        setTimeout(() => {
                            hideLogoProgressUI(propertyName);
                            if (statusEl) {
                                statusEl.className = 'small text-success mt-1';
                                statusEl.style.display = 'block';
                                statusEl.innerHTML =
                                    `✓ Dikompres otomatis oleh server ke WebP (maks 70 KB, dari ${origFormatted})`;
                            }
                        }, 250);
                        inputEl.classList.remove('is-invalid');
                        if (errorEl) errorEl.style.display = 'none';
                        if (submitBtn) submitBtn.disabled = false;
                    },
                    (error) => {
                        hideLogoProgressUI(propertyName);
                        inputEl.classList.add('is-invalid');
                        if (errorEl) {
                            errorEl.textContent = error || 'Gagal memproses dan mengunggah gambar ke server.';
                            errorEl.style.display = 'block';
                        }
                        if (submitBtn) submitBtn.disabled = false;
                    },
                    (event) => {
                        const uploadPercent = event?.detail?.progress || 0;
                        const totalPercent = 30 + Math.round(uploadPercent * 0.70);
                        updateLogoProgressUI(
                            propertyName,
                            totalPercent,
                            `Mengunggah ke server untuk kompresi fallback ${uploadPercent}%...`,
                            'bg-warning'
                        );
                    }
                );
            });
    }

    /**
     * Reset input file, progress bar, dan status ketika gambar dihapus / form direset.
     */
    function resetLogoInput(name) {
        if (!name) return;
        const input = document.getElementById(`${name}-input`);
        const statusEl = document.getElementById(`${name}-compress-status`);
        const errorEl = document.getElementById(`${name}-upload-error`);

        hideLogoProgressUI(name);

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
                resetLogoInput(name);
            });
        }
    });

    window.addEventListener('resetFileInput', (event) => {
        const detail = event?.detail;
        const name = detail?.inputName || (Array.isArray(detail) && detail[0]?.inputName) || detail;
        resetLogoInput(name);
    });
</script>
