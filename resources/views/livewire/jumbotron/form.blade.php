@if ($showForm)
    <form wire:submit.prevent="save">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h3 class="card-title fw-bold m-0">
                                {{ $isEdit ? 'Ubah Jumbotron ' . ($media_type === 'video' ? 'Video' : 'Gambar') : 'Tambah Jumbotron ' . ($media_type === 'video' ? 'Video' : 'Gambar') . ' Baru' }}
                            </h3>
                            <div class="text-muted small">
                                Pastikan data yang dimasukkan sudah benar sebelum disimpan.
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label fw-bold m-0">Status Aktif:</label>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" wire:model.live="is_active"
                                    wire:change="$refresh" id="is_active" {{ $is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <span class="badge {{ $is_active ? 'bg-primary-lt' : 'bg-danger-lt' }}">
                                        {{ $is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    @if ($media_type === 'video')
                        <div class="card p-3 rounded-4 shadow-sm border mb-4">
                            <div class="row g-3">
                                <div class="col-md-7" x-data="{ isUploading: false, progress: 0 }"
                                    x-on:livewire-upload-start="isUploading = true; progress = 0"
                                    x-on:livewire-upload-finish="isUploading = false"
                                    x-on:livewire-upload-error="isUploading = false; if (window.iziToast) iziToast.error({ title: 'Gagal Unggah', message: 'Koneksi terputus atau file gagal diunggah.', position: 'topRight' });"
                                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                                    <label class="form-label fw-bold">Unggah Berkas Video</label>
                                    @if ($video_file)
                                        <div class="alert alert-success d-flex align-items-center mb-2 py-2 px-3" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                            <div class="small">
                                                <strong>Video Terpilih:</strong> {{ $video_file->getClientOriginalName() }} ({{ round($video_file->getSize() / 1024 / 1024, 2) }} MB)
                                            </div>
                                        </div>
                                    @elseif ($tmp_video_file)
                                        <div class="mb-2">
                                            <video src="{{ asset($tmp_video_file) }}" controls class="rounded-3 w-100 shadow-sm" style="max-height: 220px; background-color: #000;"></video>
                                        </div>
                                    @endif

                                    {{-- Real-Time Upload Progress Bar --}}
                                    <div x-show="isUploading" class="my-2 p-2 bg-light border rounded-3" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-semibold text-primary">
                                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                Mengunggah video ke server...
                                            </span>
                                            <span class="small fw-bold text-primary" x-text="progress + '%'"></span>
                                        </div>
                                        <div class="progress rounded-pill" style="height: 8px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                role="progressbar"
                                                :style="`width: ${progress}%`"
                                                :aria-valuenow="progress"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <input type="file" id="video_file_input"
                                            class="form-control rounded-3 @error('video_file') is-invalid @enderror"
                                            wire:model="video_file" accept="video/mp4,video/webm"
                                            onchange="(function(el){
                                                const f = el.files[0];
                                                const err = document.getElementById('video_file-client-error');
                                                const show = (m) => {
                                                    el.classList.add('is-invalid');
                                                    if (err) { err.textContent = m; err.style.display = 'block'; }
                                                    if (window.iziToast) {
                                                        iziToast.error({ title: 'Batas Ukuran Video', message: m, position: 'topRight' });
                                                    }
                                                };
                                                const hide = () => {
                                                    el.classList.remove('is-invalid');
                                                    if (err) { err.style.display = 'none'; err.textContent = ''; }
                                                };
                                                if (!f) { hide(); return; }
                                                const name = (f.name || '').toLowerCase();
                                                const validExt = name.endsWith('.mp4') || name.endsWith('.webm');
                                                if (!validExt) {
                                                    event.stopImmediatePropagation();
                                                    el.value = '';
                                                    show('Format file harus MP4 atau WebM.');
                                                    return;
                                                }
                                                const maxSize = 50 * 1024 * 1024; // 50 MB
                                                if (f.size > maxSize) {
                                                    event.stopImmediatePropagation();
                                                    el.value = '';
                                                    const sizeMB = (f.size / (1024 * 1024)).toFixed(1);
                                                    show('Ukuran file video maksimal 50 MB (File Anda: ' + sizeMB + ' MB). Silakan kompresi video terlebih dahulu.');
                                                    return;
                                                }
                                                hide();
                                                try {
                                                    const tempV = document.createElement('video');
                                                    tempV.preload = 'metadata';
                                                    tempV.onloadedmetadata = function() {
                                                        window.URL.revokeObjectURL(tempV.src);
                                                        const sec = Math.round(tempV.duration);
                                                        if (sec > 0 && typeof @this !== 'undefined') {
                                                            @this.set('video_duration', sec);
                                                        }
                                                    };
                                                    tempV.src = URL.createObjectURL(f);
                                                } catch (_) {}
                                            })(this)">
                                        @if ($video_file || $tmp_video_file)
                                            <button type="button" class="btn btn-outline-danger rounded-3" wire:click="clearVideo" title="Hapus / Reset Video">
                                                Reset
                                            </button>
                                        @endif
                                    </div>
                                    <div id="video_file-client-error" class="invalid-feedback" style="display:none"></div>
                                    @error('video_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text mt-2">
                                        <small class="text-muted d-block"><span class="text-danger">*</span> Format: <strong>MP4 (H.264)</strong> atau <strong>WebM</strong>.</small>
                                        <small class="text-muted d-block"><span class="text-danger">*</span> Ukuran maksimal: <strong>50 MB</strong>. Rasio rekomendasi: <strong>16:9 (1080p)</strong>.</small>
                                        <small class="text-muted d-block"><span class="text-danger">*</span> Didukung pemutaran <em>streaming buffer (HTTP 206 Partial Content)</em> langsung di TV.</small>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="card p-3 rounded-3 bg-light border-0">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pengaturan Audio Video</label>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" wire:model.live="has_audio" id="has_audio">
                                                <label class="form-check-label fw-semibold" for="has_audio">
                                                    {{ $has_audio ? 'Suara Video Aktif' : 'Mode Bisu / Senyap (Muted)' }}
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    <strong>Mode Bisu (Direkomendasikan):</strong> Audio murottal masjid tetap berputar lembut di latar belakang.<br>
                                                    <strong>Suara Aktif:</strong> Audio murottal otomatis dijeda saat video tayang, lalu dilanjutkan kembali setelah video selesai.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label fw-bold">Durasi Video (Detik - Opsional)</label>
                                            <input type="number" class="form-control rounded-3" wire:model="video_duration" placeholder="Contoh: 329 (5m 29s)">
                                            <div class="form-text">
                                                <small class="text-muted">Catatan durasi video dalam satuan detik.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Panduan & Tata Cara Penggunaan Video Jumbotron (Tanpa Card, Tanpa Badge, Icon Tanpa BG) --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary me-2 flex-shrink-0">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                                <h4 class="m-0 fw-bold text-dark">Panduan & Tata Cara Penggunaan Jumbotron Video</h4>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary me-2 flex-shrink-0">
                                                <path d="M15 10l5 5-5 5"/>
                                                <path d="M4 4v7a4 4 0 0 0 4 4h12"/>
                                            </svg>
                                            <span class="fw-bold text-dark">1. Format & Spesifikasi Berkas Video</span>
                                        </div>
                                        <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.6;">
                                            <li><strong>Format Video:</strong> Wajib format <strong>MP4 (H.264 / AAC)</strong> atau <strong>WebM</strong> agar kompatibel dengan seluruh peramban web dan aplikasi Android TV.</li>
                                            <li><strong>Rasio & Resolusi:</strong> Direkomendasikan rasio layar <strong>16:9</strong> (resolusi Full HD <strong>1920x1080</strong> atau HD <strong>1280x720</strong>) agar pas satu layar penuh tanpa terpotong.</li>
                                            <li><strong>Ukuran Berkas:</strong> Maksimal <strong>50 MB</strong>. Dianjurkan mengompresi video terlebih dahulu agar video ringan, cepat dimuat, dan hemat kuota internet.</li>
                                        </ul>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-azure me-2 flex-shrink-0">
                                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                                                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                                                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                                            </svg>
                                            <span class="fw-bold text-dark">2. Pengaturan Audio / Suara Video</span>
                                        </div>
                                        <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.6;">
                                            <li><strong>Mode Bisu / Muted (Rekomendasi):</strong> Suara video dimatikan. Murottal/audio latar belakang masjid akan terus berputar lembut tanpa henti.</li>
                                            <li><strong>Suara Video Aktif:</strong> Jika diaktifkan, audio murottal otomatis dijeda saat video diputar, dan akan dilanjutkan kembali secara otomatis setelah video selesai.</li>
                                        </ul>
                                    </div>

                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success me-2 flex-shrink-0">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            <span class="fw-bold text-dark">3. Durasi & Siklus Penayangan</span>
                                        </div>
                                        <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.6;">
                                            <li><strong>Deteksi Durasi Fisik:</strong> Sistem mendeteksi durasi asli video secara otomatis saat diunggah dan saat diputar di TV.</li>
                                            <li><strong>Siklus Penayangan:</strong> Video berputar bergantian dengan slide informasi masjid setelah satu putaran slide selesai.</li>
                                            <li><strong>Jeda Antar-Tayang:</strong> Terdapat jeda minimal agar video tidak berputar berulang-ulang secara terus-menerus.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-warning me-2 flex-shrink-0">
                                                <path d="M12 2v4"/>
                                                <path d="M12 18v4"/>
                                                <path d="m4.93 4.93 2.83 2.83"/>
                                                <path d="m16.24 16.24 2.83 2.83"/>
                                                <path d="M2 12h4"/>
                                                <path d="M18 12h4"/>
                                                <path d="m4.93 19.07 2.83-2.83"/>
                                                <path d="m16.24 7.76 2.83-2.83"/>
                                            </svg>
                                            <span class="fw-bold text-dark">4. Keamanan Otomatis Waktu Sholat</span>
                                        </div>
                                        <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.6;">
                                            <li><strong>1 Menit Pra-Adzan:</strong> Video dan audio otomatis dihentikan/senyap seketika demi menjaga ketenangan masjid.</li>
                                            <li><strong>Saat Adzan & Iqomah:</strong> Pemutaran video diblokir penuh selama alarm beep, adzan, dan hitung mundur iqomah berlangsung.</li>
                                            <li><strong>Selama Layar Sholat:</strong> Layar fokus menampilkan lurus rapatkan shaf / waktu sholat tanpa gangguan video.</li>
                                            <li><strong>Selesai Sholat:</strong> Setelah layar sholat selesai ditutup dan kembali ke layar utama, pemutaran video dan murottal akan kembali beroperasi normal secara otomatis.</li>
                                        </ul>
                                    </div>

                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-info me-2 flex-shrink-0">
                                                <rect width="20" height="14" x="2" y="3" rx="2"/>
                                                <line x1="8" x2="16" y1="21" y2="21"/>
                                                <line x1="12" x2="12" y1="17" y2="21"/>
                                            </svg>
                                            <span class="fw-bold text-dark">5. Sinkronisasi Web & Android TV</span>
                                        </div>
                                        <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.6;">
                                            <li><strong>Sinkronisasi Real-Time:</strong> Pengunggahan berkas atau perubahan status aktif langsung terdistribusi ke seluruh layar TV melalui koneksi WebSocket.</li>
                                            <li><strong>Optimasi Android TV:</strong> TV mengunduh berkas ke penyimpanan lokal (*cache-first*) sehingga tidak buffering saat diputar berulang kali dan tetap dapat berjalan lancar.</li>
                                            <li><strong>Timer Pengaman (Watchdog):</strong> Dilengkapi pengaman otomatis sehingga jika video mengalami kendala jaringan atau macet, layar akan langsung beralih ke slide berikutnya.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
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
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP Maksimal 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
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
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP Maksimal 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
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
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP Maksimal 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
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
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP Maksimal 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
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
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP Maksimal 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 22" fill="none" stroke="currentColor"
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
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP Maksimal 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
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
                            @error('jumbo6')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer rounded-bottom-4 border-0">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" wire:click="cancelForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                    <span wire:loading.remove wire:target="cancelForm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy-x">
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
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="small">Loading...</span>
                    </span>
                </button>
                <button type="submit" class="btn py-2 px-2 rounded-3 shadow-sm" wire:loading.attr="disabled">
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
