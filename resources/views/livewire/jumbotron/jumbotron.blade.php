<div>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm" x-data="{
                        currentTab: '{{ $activeTab }}',
                        switchTab(tab) {
                            this.currentTab = tab;
                            const url = new URL(window.location);
                            url.searchParams.set('tab', tab);
                            window.history.replaceState({}, '', url);
                            if ($wire.activeTab !== tab) {
                                $wire.setTab(tab);
                            }
                        }
                    }" x-init="window.addEventListener('popstate', () => {
                        const urlTab = new URLSearchParams(window.location.search).get('tab');
                        if (urlTab && ['image', 'video'].includes(urlTab)) {
                            currentTab = urlTab;
                            if ($wire.activeTab !== urlTab) {
                                $wire.setTab(urlTab);
                            }
                        }
                    });
                    $wire.$watch('activeTab', (val) => {
                        if (val && ['image', 'video'].includes(val)) {
                            currentTab = val;
                            const url = new URL(window.location);
                            url.searchParams.set('tab', val);
                            window.history.replaceState({}, '', url);
                        }
                    });">

                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <ul class="nav nav-tabs card-header-tabs" wire:ignore role="tablist">
                                <li class="nav-item">
                                    <button type="button"
                                        class="nav-link {{ $activeTab === 'image' ? 'active fw-bold' : '' }}"
                                        :class="{ 'active': currentTab === 'image', 'fw-bold': currentTab === 'image' }"
                                        @click="switchTab('image')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <rect width="18" height="18" x="3" y="3" rx="2"
                                                ry="2" />
                                            <circle cx="9" cy="9" r="2" />
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                        </svg>
                                        Jumbotron Gambar
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button"
                                        class="nav-link {{ $activeTab === 'video' ? 'active fw-bold' : '' }}"
                                        :class="{ 'active': currentTab === 'video', 'fw-bold': currentTab === 'video' }"
                                        @click="switchTab('video')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <path d="m22 8-6 4 6 4V8Z" />
                                            <rect width="14" height="12" x="2" y="6" rx="2"
                                                ry="2" />
                                        </svg>
                                        Jumbotron Video
                                    </button>
                                </li>
                            </ul>
                            <div class="card-actions" x-show="$wire.showTable"
                                @if (!$showTable) style="display: none;" @endif
                                wire:key="card-actions-create">
                                @can('create-jumbotron')
                                    <button wire:click="showAddForm(currentTab)"
                                        class="btn py-2 px-2 rounded-3 shadow-sm btn-primary">
                                        <span wire:loading.remove wire:target="showAddForm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                <path d="M13.5 6.5l4 4" />
                                                <path d="M16 19h6" />
                                                <path d="M19 16v6" />
                                            </svg>
                                            Tambah Jumbotron <span
                                                x-text="currentTab === 'video' ? 'Video' : 'Gambar'">{{ $activeTab === 'video' ? 'Video' : 'Gambar' }}</span>
                                        </span>
                                        <span wire:loading wire:target="showAddForm">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Loading...</span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </div>

                        <div wire:key="jumbotron-form-section" x-show="$wire.showForm"
                            @if (!$showForm) style="display: none;" @endif>
                            @include('livewire.jumbotron.form')
                        </div>

                        <div x-show="$wire.showTable" @if (!$showTable) style="display: none;" @endif
                            wire:key="jumbotron-table-section">
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

                            @include('livewire.jumbotron.table')

                            <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                                <div wire:key="pagination-video-container" x-show="currentTab === 'video'" x-cloak>
                                    {{ $jumboVideoList->links() }}
                                </div>
                                <div wire:key="pagination-image-container" x-show="currentTab === 'image'" x-cloak>
                                    {{ $jumboImageList->links() }}
                                </div>
                            </div>
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

            window.handleJumbotronVideoSelect = function(el, event) {
                const f = el.files && el.files[0];
                const err = document.getElementById('video_file-client-error');
                const show = (m) => {
                    el.classList.add('is-invalid');
                    if (err) {
                        err.textContent = m;
                        err.style.display = 'block';
                    }
                    if (window.iziToast) {
                        iziToast.error({
                            title: 'Batas Ukuran Video',
                            message: m,
                            position: 'topRight'
                        });
                    }
                };
                const hide = () => {
                    el.classList.remove('is-invalid');
                    if (err) {
                        err.style.display = 'none';
                        err.textContent = '';
                    }
                };

                if (!f) {
                    hide();
                    return;
                }

                const name = (f.name || '').toLowerCase();
                const validExt = name.endsWith('.mp4') || name.endsWith('.webm');
                if (!validExt) {
                    if (event) event.stopImmediatePropagation();
                    el.value = '';
                    show('Format file harus MP4 atau WebM.');
                    return;
                }

                const maxSize = 50 * 1024 * 1024; // 50 MB
                if (f.size > maxSize) {
                    if (event) event.stopImmediatePropagation();
                    el.value = '';
                    const sizeMB = (f.size / (1024 * 1024)).toFixed(1);
                    show('Ukuran file video maksimal 50 MB (File Anda: ' + sizeMB +
                        ' MB). Silakan kompresi video terlebih dahulu.');
                    return;
                }

                hide();

                // Deteksi durasi video seketika di peramban (client-side)
                try {
                    const tempV = document.createElement('video');
                    tempV.preload = 'metadata';
                    const objUrl = URL.createObjectURL(f);
                    tempV.onloadedmetadata = function() {
                        window.URL.revokeObjectURL(objUrl);
                        const sec = Math.ceil(tempV.duration) || Math.round(tempV.duration);
                        if (sec > 0) {
                            window._jwsDetectedDuration = sec;

                            // 1. Langsung isi input durasi seketika
                            const durInput = document.getElementById('video_duration_input');
                            if (durInput) {
                                durInput.value = sec;
                                durInput.dispatchEvent(new Event('input', {
                                    bubbles: true
                                }));
                                durInput.dispatchEvent(new Event('change', {
                                    bubbles: true
                                }));
                            }

                            // 2. Langsung tampilkan badge visual
                            const badge = document.getElementById('video_duration_badge');
                            const badgeText = document.getElementById('video_duration_badge_text');
                            if (badge && badgeText) {
                                const m = Math.floor(sec / 60);
                                const s = sec % 60;
                                badgeText.textContent = 'Terdeteksi: ' + (m > 0 ? (m + 'm ' + s + 's') : (s + 's'));
                                badge.style.display = 'inline-flex';
                            }

                            // 3. Tampilkan teks konfirmasi
                            const infoText = document.getElementById('video_duration_info');
                            if (infoText) {
                                infoText.innerHTML = '<small class="text-success fw-semibold">Durasi ' + sec +
                                    ' detik terisi otomatis dari metadata video. Bisa diubah manual jika perlu.</small>';
                            }

                            // 4. Sinkronkan ke Livewire
                            const wireEl = el.closest('[wire\\:id]');
                            if (wireEl && window.Livewire) {
                                const comp = Livewire.find(wireEl.getAttribute('wire:id'));
                                if (comp) {
                                    comp.set('video_duration', sec);
                                }
                            }
                        }
                    };
                    tempV.onerror = function() {
                        window.URL.revokeObjectURL(objUrl);
                    };
                    tempV.src = objUrl;
                    tempV.load();
                } catch (_) {}
            };

            window.resetJumbotronVideoDurationUI = function() {
                window._jwsDetectedDuration = 0;
                const badge = document.getElementById('video_duration_badge');
                if (badge) badge.style.display = 'none';
                const info = document.getElementById('video_duration_info');
                if (info) {
                    info.innerHTML =
                        '<small class="text-muted">Akan terisi otomatis saat video dipilih. Jika tidak terisi, isi manual dalam satuan detik.</small>';
                }
            };

            window.compressImageToWebP = function(file, maxSizeBytes = 80 * 1024) {
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
                                const ratio = Math.min(maxInitialWidth / curWidth, maxInitialHeight /
                                    curHeight);
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
                                        blob = await getBlob(curWidth, curHeight, Math.max(quality,
                                            0.20));
                                    }

                                    let attempts = 0;
                                    while (blob && blob.size > maxSizeBytes && attempts < 10) {
                                        curWidth = Math.round(curWidth * 0.85);
                                        curHeight = Math.round(curHeight * 0.85);
                                        quality = 0.75;
                                        blob = await getBlob(curWidth, curHeight, quality);

                                        while (blob && blob.size > maxSizeBytes && quality > 0.25) {
                                            quality -= 0.10;
                                            blob = await getBlob(curWidth, curHeight, Math.max(
                                                quality, 0.20));
                                        }
                                        attempts++;
                                    }

                                    if (!blob) {
                                        return reject(new Error(
                                            'Gagal mengompres gambar ke format WebP.'));
                                    }

                                    const originalName = file.name.replace(/\.[^/.]+$/, '');
                                    const safeName = originalName.replace(/[^a-zA-Z0-9_-]/g, '_');
                                    const webpFile = new File([blob],
                                        `${safeName || 'jumbo'}.webp`, {
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
            };

            window.handleJumboUpload = function(inputEl, propertyName) {
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
                    statusEl.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengompres gambar ke WebP (maks 80KB)...';
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

                window.compressImageToWebP(file, 80 * 1024)
                    .then((result) => {
                        const {
                            file: compressedFile,
                            originalSize,
                            compressedSize
                        } = result;
                        const origKb = (originalSize / 1024).toFixed(1);
                        const compKb = (compressedSize / 1024).toFixed(1);

                        if (statusEl) {
                            statusEl.innerHTML =
                                `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengunggah WebP (${compKb} KB)...`;
                        }

                        component.upload(
                            propertyName,
                            compressedFile,
                            () => {
                                if (statusEl) {
                                    statusEl.className = 'small text-success mt-1';
                                    statusEl.style.display = 'block';
                                    statusEl.innerHTML =
                                        `✓ Berhasil dikompres ke WebP: <strong>${compKb} KB</strong> (dari ${origKb} KB)`;
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
                                    statusEl.innerHTML =
                                        `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengunggah WebP (${compKb} KB) ${percent}%...`;
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
            };

            window.resetJumboInput = function(name) {
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
            };

            $wire.on('resetFileInput', (data) => {
                const name = data?.inputName || (Array.isArray(data) && data[0]?.inputName) || data;
                if (window.resetJumboInput) {
                    window.resetJumboInput(name);
                }
                const fileInput = document.querySelector(`input[wire\\:model="${name}"]`);
                if (fileInput) {
                    fileInput.value = '';
                    fileInput.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }
            });
        </script>
    @endscript
</div>
