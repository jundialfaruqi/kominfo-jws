<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link {{ $activeTab === 'image' ? 'active fw-bold' : '' }}" wire:click="setTab('image')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        Jumbotron Gambar
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link {{ $activeTab === 'video' ? 'active fw-bold' : '' }}" wire:click="setTab('video')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2" ry="2"/></svg>
                                        Jumbotron Video
                                    </button>
                                </li>
                            </ul>
                            @if (!$showForm)
                                <div class="card-actions">
                                    @can('create-jumbotron')
                                        <button wire:click="showAddForm" class="btn py-2 px-2 rounded-3 shadow-sm btn-primary">
                                            <span wire:loading.remove wire:target="showAddForm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                    <path d="M13.5 6.5l4 4" />
                                                    <path d="M16 19h6" />
                                                    <path d="M19 16v6" />
                                                </svg>
                                                Tambah Jumbotron {{ $activeTab === 'video' ? 'Video' : 'Gambar' }}
                                            </span>
                                            <span wire:loading wire:target="showAddForm">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="small">Loading...</span>
                                            </span>
                                        </button>
                                    @endcan
                                </div>
                            @endif
                        </div>

                        @include('livewire.jumbotron.form')

                        @if ($showTable)
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
                                {{ $jumboList->links() }}
                            </div>
                        @endif
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
                        iziToast.error({ title: 'Batas Ukuran Video', message: m, position: 'topRight' });
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
                    show('Ukuran file video maksimal 50 MB (File Anda: ' + sizeMB + ' MB). Silakan kompresi video terlebih dahulu.');
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
                                durInput.dispatchEvent(new Event('input', { bubbles: true }));
                                durInput.dispatchEvent(new Event('change', { bubbles: true }));
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
                                infoText.innerHTML = '<small class="text-success fw-semibold">Durasi ' + sec + ' detik terisi otomatis dari metadata video. Bisa diubah manual jika perlu.</small>';
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
                    info.innerHTML = '<small class="text-muted">Akan terisi otomatis saat video dipilih. Jika tidak terisi, isi manual dalam satuan detik.</small>';
                }
            };
        </script>
    @endscript
</div>
