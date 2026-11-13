<div>
    <div class="page-body">
        <div class="container-xl">

            <div class="card mb-3 rounded-4 border-0 overflow-hidden">
                <div class="card-body"
                    style="background: linear-gradient(90deg, #0ea5a3 0%, #1f7ae0 60%, #3b82f6 100%); color: #fff;">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-7 mb-3 mb-md-0">
                            <h1 class="mb-1">Data User</h1>
                            <div class="text-white" style="opacity:.9;">Real-time monitoring data pengguna dan status
                                persetujuan</div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="d-flex flex-wrap justify-content-md-end">
                                <div class="text-center"
                                    style="border-right: 1px solid rgba(255, 255, 255, 0.498); padding-right: 1rem; margin-right: 1rem;">
                                    <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                        {{ $totalUsers }}
                                    </div>
                                    <div class="text-white small" style="opacity:.85;">
                                        Total User
                                    </div>
                                </div>
                                <div class="text-center"
                                    style="border-right: 1px solid rgba(255, 255, 255, 0.498); padding-right: 1rem; margin-right: 1rem;">
                                    <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                        {{ $activeUsers }}
                                    </div>
                                    <div class="text-white small" style="opacity:.85;" style="font-size: 2rem;">
                                        Disetujui
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                        {{ $inactiveUsers }}
                                    </div>
                                    <div class="text-white small" style="opacity:.85;">
                                        Belum Disetujui
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('livewire.admin.user.statistic')

            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm border-0">
                        <div class="card-header bg-dark text-white rounded-top-4">
                            <h3 class="card-title d-none d-md-block">
                                Daftar User
                            </h3>
                            <div class="card-actions">
                                @if (auth()->user()->role === 'Super Admin' || auth()->user()->role === 'Admin')
                                    <button wire:loading.attr="disabled" wire:click="add" type="button"
                                        class="btn btn-primary py-2 rounded-4 shadow-sm" data-bs-toggle="modal"
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
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-auto d-flex gap-2">
                                    <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                                        <span class="input-group-text rounded-start-4 gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-table-row">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                <path d="M9 3l-6 6" />
                                                <path d="M14 3l-7 7" />
                                                <path d="M19 3l-7 7" />
                                                <path d="M21 6l-4 4" />
                                                <path d="M3 10h18" />
                                                <path d="M10 10v11" />
                                            </svg>
                                            Tampilkan Baris
                                        </span>
                                        <select wire:model.live="paginate"
                                            class="form-select form-select rounded-end-4">
                                            <option>5</option>
                                            <option>10</option>
                                            <option>25</option>
                                            <option>50</option>
                                            <option>100</option>
                                        </select>
                                    </div>

                                    {{-- offcanvas --}}
                                    <a class="btn btn-icon btn-danger btn-pill" data-bs-toggle="offcanvas"
                                        href="#offcanvasEnd" role="button" aria-controls="offcanvasEnd">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                            <path d="M7 11l5 5l5 -5" />
                                            <path d="M12 4l0 12" />
                                        </svg>
                                    </a>

                                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd"
                                        aria-labelledby="offcanvasEndLabel">
                                        <div class="offcanvas-header">
                                            <h2 class="offcanvas-title" id="offcanvasEndLabel">Pilihan Export Data
                                            </h2>
                                            <button type="button" class="btn-close text-reset"
                                                data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <div>
                                                <b>Export User</b>
                                                <p><small class="mt-1">Export data pengguna admin masjid</small></p>
                                            </div>
                                            <div class="mt-1">
                                                <a href="{{ route('admin.user.pdf', ['role' => 'Admin Masjid']) }}"
                                                    class="btn btn-warning rounded-4">
                                                    <span class="d-inline-flex align-items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                            <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                                                            <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                                                            <path d="M17 18h2" />
                                                            <path d="M20 15h-3v6" />
                                                            <path
                                                                d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                                                        </svg>
                                                        <span>Export PDF User</span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-auto ms-md-auto"
                                    x-data="{
                                        query: @js($search ?? ''),
                                        lastSynced: @js($search ?? ''),
                                        timer: null,
                                        syncSearch(immediate = false) {
                                            clearTimeout(this.timer);
                                            const run = () => {
                                                const trimmed = (this.query || '').trim();
                                                if (trimmed.length >= 3) {
                                                    if (this.lastSynced !== trimmed) {
                                                        this.lastSynced = trimmed;
                                                        $wire.set('search', trimmed);
                                                    }
                                                } else if (this.lastSynced !== '') {
                                                    this.lastSynced = '';
                                                    $wire.set('search', '');
                                                }
                                            };
                                            if (immediate) {
                                                run();
                                            } else {
                                                this.timer = setTimeout(run, 350);
                                            }
                                        }
                                    }">
                                    <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                                        <span class="input-group-text rounded-start-4 gap-1">
                                            <span wire:loading.remove wire:target="search">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                    <path d="M21 21l-6 -6" />
                                                </svg>
                                            </span>
                                            <span wire:loading wire:target="search">
                                                <span class="spinner-border spinner-border-sm text-primary"
                                                    role="status" aria-hidden="true"></span>
                                            </span>
                                            Cari
                                        </span>
                                        <input wire:ignore x-model="query" x-on:input="syncSearch()" x-on:search="syncSearch(true)"
                                            x-on:keydown.enter.prevent="syncSearch(true)" type="search"
                                            name="search_user_query" class="form-control rounded-end-4"
                                            placeholder="Ketik min. 3 huruf" autocomplete="off" autocorrect="off"
                                            autocapitalize="off" spellcheck="false" readonly
                                            onfocus="this.removeAttribute('readonly');" />
                                    </div>
                                    <div x-cloak x-show="query.trim().length > 0 && query.trim().length < 3"
                                        class="text-muted small mt-1 ps-1">
                                        Ketik minimal 3 huruf untuk mulai mencari
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

    {{-- PDF Progress Modal --}}
    @include('livewire.admin.user.pdf-progress-modal')

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

            if (!window.__userPdfGeneratorBound) {
                window.__userPdfGeneratorBound = true;

                let pdfAbortController = null;
                let pdfCreepTimer = null;
                let lastPdfRequest = null;

                const stopCreep = () => {
                    if (pdfCreepTimer) {
                        clearInterval(pdfCreepTimer);
                        pdfCreepTimer = null;
                    }
                };

                const setStepState = (activeStep, isDone, isError) => {
                    const modalEl = document.getElementById('pdfProgressModal');
                    if (!modalEl) return;

                    modalEl.querySelectorAll('[data-pdf-step]').forEach((row) => {
                        const stepNum = parseInt(row.getAttribute('data-pdf-step'), 10);
                        const badge = row.querySelector('.badge');
                        if (!badge) return;

                        row.classList.remove('text-muted', 'text-dark', 'text-success', 'text-danger', 'fw-medium');
                        badge.className = 'badge rounded-pill';

                        if (isDone || stepNum < activeStep) {
                            row.classList.add('text-success');
                            badge.classList.add('bg-success-lt');
                            badge.textContent = '✓';
                        } else if (stepNum === activeStep) {
                            if (isError) {
                                row.classList.add('text-danger', 'fw-medium');
                                badge.classList.add('bg-danger-lt');
                                badge.textContent = '!';
                            } else {
                                row.classList.add('text-dark', 'fw-medium');
                                badge.classList.add('bg-primary-lt');
                                badge.textContent = String(stepNum);
                            }
                        } else {
                            row.classList.add('text-muted');
                            badge.classList.add('bg-secondary-lt');
                            badge.textContent = String(stepNum);
                        }
                    });
                };

                const startPdfGeneration = async (payload) => {
                    const modalEl = document.getElementById('pdfProgressModal');
                    if (!modalEl) return;

                    lastPdfRequest = payload;

                    if (pdfAbortController) {
                        pdfAbortController.abort();
                    }
                    stopCreep();

                    pdfAbortController = new AbortController();

                    const userNameEl = document.getElementById('pdfModalUserName');
                    const userEmailEl = document.getElementById('pdfModalUserEmail');
                    const userMosqueEl = document.getElementById('pdfModalUserMosque');
                    const statusEl = document.getElementById('pdfProgressStatus');
                    const percentEl = document.getElementById('pdfProgressPercent');
                    const barEl = document.getElementById('pdfProgressBar');
                    const successBox = document.getElementById('pdfSuccessBox');
                    const fileMetaEl = document.getElementById('pdfFileMeta');
                    const errorBox = document.getElementById('pdfErrorBox');
                    const errorMsgEl = document.getElementById('pdfErrorMessage');
                    const retryBtn = document.getElementById('pdfRetryBtn');
                    const downloadLink = document.getElementById('pdfDownloadLink');

                    userNameEl.textContent = payload.userName || '-';
                    userEmailEl.textContent = payload.userEmail || '-';
                    userMosqueEl.textContent = payload.userMosque && payload.userMosque !== '-'
                        ? 'Masjid / Instansi: ' + payload.userMosque
                        : 'Masjid / Instansi: -';

                    let displayProgress = 5;
                    let serverCeiling = 90;
                    let currentStep = 1;

                    const updateBar = (pct, mode = 'primary') => {
                        const clamped = Math.max(0, Math.min(100, Math.round(pct)));
                        barEl.style.width = clamped + '%';
                        barEl.setAttribute('aria-valuenow', String(clamped));
                        percentEl.textContent = clamped + '%';

                        barEl.classList.remove('bg-primary', 'bg-success', 'bg-danger', 'progress-bar-striped', 'progress-bar-animated');
                        percentEl.classList.remove('bg-primary-lt', 'bg-success-lt', 'bg-danger-lt');

                        if (mode === 'success') {
                            barEl.classList.add('bg-success');
                            percentEl.classList.add('bg-success-lt');
                        } else if (mode === 'error') {
                            barEl.classList.add('bg-danger');
                            percentEl.classList.add('bg-danger-lt');
                        } else {
                            barEl.classList.add('bg-primary', 'progress-bar-striped', 'progress-bar-animated');
                            percentEl.classList.add('bg-primary-lt');
                        }
                    };

                    statusEl.textContent = 'Memulai proses generate PDF...';
                    updateBar(displayProgress, 'primary');
                    setStepState(1, false, false);

                    successBox.classList.add('d-none');
                    errorBox.classList.add('d-none');
                    retryBtn.classList.add('d-none');
                    downloadLink.classList.add('d-none');
                    downloadLink.setAttribute('href', '#');
                    downloadLink.removeAttribute('download');

                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalInstance.show();

                    // Smooth realtime animation while waiting for server stream chunks
                    pdfCreepTimer = setInterval(() => {
                        if (displayProgress < serverCeiling) {
                            const remaining = serverCeiling - displayProgress;
                            const step = remaining > 20 ? 3 : (remaining > 8 ? 2 : 1);
                            displayProgress = Math.min(serverCeiling, displayProgress + step);
                            updateBar(displayProgress, 'primary');
                        }
                    }, 180);

                    const showError = (msg) => {
                        stopCreep();
                        updateBar(displayProgress, 'error');
                        setStepState(currentStep, false, true);
                        statusEl.textContent = 'Proses generate PDF gagal.';
                        errorMsgEl.textContent = msg || 'Terjadi kesalahan saat memproses PDF.';
                        errorBox.classList.remove('d-none');
                        retryBtn.classList.remove('d-none');
                    };

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const response = await fetch(payload.generateUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/x-ndjson',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            signal: pdfAbortController.signal
                        });

                        if (!response.ok || !response.body) {
                            throw new Error('Server mengembalikan status HTTP ' + response.status);
                        }

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder('utf-8');
                        let buffer = '';
                        let completedPayload = null;

                        const processLine = (line) => {
                            const trimmed = line.trim();
                            if (!trimmed) return;

                            let chunk = null;
                            try {
                                chunk = JSON.parse(trimmed);
                            } catch (e) {
                                return;
                            }

                            if (chunk.error) {
                                throw new Error(chunk.message || 'Gagal memproses file PDF.');
                            }

                            if (chunk.step) {
                                currentStep = chunk.step;
                            }

                            if (chunk.message) {
                                statusEl.textContent = chunk.message;
                            }

                            if (typeof chunk.progress === 'number') {
                                if (chunk.progress > displayProgress) {
                                    displayProgress = chunk.progress;
                                }
                                if (chunk.done) {
                                    serverCeiling = 100;
                                    completedPayload = chunk;
                                } else {
                                    serverCeiling = Math.max(serverCeiling, Math.min(94, chunk.progress + 18));
                                    setStepState(currentStep, false, false);
                                    updateBar(displayProgress, 'primary');
                                }
                            }
                        };

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;
                            buffer += decoder.decode(value, { stream: true });
                            const lines = buffer.split('\n');
                            buffer = lines.pop() || '';
                            for (const line of lines) {
                                processLine(line);
                            }
                        }

                        if (buffer.trim()) {
                            processLine(buffer);
                        }

                        if (!completedPayload || !completedPayload.download_url) {
                            throw new Error('Respon generate PDF tidak lengkap.');
                        }

                        stopCreep();
                        displayProgress = 100;
                        updateBar(100, 'success');
                        setStepState(5, true, false);
                        statusEl.textContent = completedPayload.message || 'File PDF selesai dibuat.';
                        fileMetaEl.textContent = `${completedPayload.filename} (${completedPayload.size_kb} KB)`;
                        successBox.classList.remove('d-none');

                        downloadLink.setAttribute('href', completedPayload.download_url);
                        downloadLink.setAttribute('download', completedPayload.filename);
                        downloadLink.classList.remove('d-none');
                    } catch (err) {
                        if (err && err.name === 'AbortError') {
                            stopCreep();
                            return;
                        }
                        showError(err ? err.message : 'Terjadi kesalahan jaringan.');
                    }
                };

                document.addEventListener('click', (event) => {
                    const btn = event.target.closest('[data-pdf-generate-btn]');
                    if (btn) {
                        startPdfGeneration({
                            generateUrl: btn.getAttribute('data-generate-url'),
                            userName: btn.getAttribute('data-user-name'),
                            userEmail: btn.getAttribute('data-user-email'),
                            userMosque: btn.getAttribute('data-user-mosque')
                        });
                        return;
                    }

                    if (event.target.closest('#pdfRetryBtn') && lastPdfRequest) {
                        startPdfGeneration(lastPdfRequest);
                    }
                });

                document.addEventListener('hidden.bs.modal', (event) => {
                    if (event.target && event.target.id === 'pdfProgressModal') {
                        if (pdfAbortController) {
                            pdfAbortController.abort();
                            pdfAbortController = null;
                        }
                        stopCreep();
                    }
                });
            }
        </script>
    @endscript
</div>
