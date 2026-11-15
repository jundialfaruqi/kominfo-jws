<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm border-0">
                        <div class="card-header bg-dark text-white rounded-top-4">
                            <h3 class="card-title d-none d-md-block">
                                Daftar Jumbotron Masjid
                            </h3>
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
                                </div>

                                <div class="col-12 col-md-auto ms-md-auto">
                                    <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                                        <span class="input-group-text rounded-start-4 gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                <path d="M21 21l-6 -6" />
                                            </svg>
                                            Cari
                                        </span>
                                        <input wire:model.live="search" type="text"
                                            class="form-control rounded-end-4" placeholder="Cari admin atau nama masjid"
                                            autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        <th>Nama Masjid &amp; Admin</th>
                                        <th class="text-center">Status</th>
                                        @for ($i = 1; $i <= 6; $i++)
                                            <th class="text-center">Jumbotron Masjid {{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jumbotronMasjidsData as $jm)
                                        @php
                                            $masjidName = optional($jm->profilMasjid)->name ?? (optional($jm->user)->name ?? '-');
                                        @endphp
                                        <tr wire:key="admin-jm-row-{{ $jm->id }}" class="align-middle">
                                            <td class="text-center text-muted">
                                                {{ $loop->iteration + ($jumbotronMasjidsData->currentPage() - 1) * $jumbotronMasjidsData->perPage() }}
                                            </td>
                                            <td class="text-wrap">
                                                <div class="fw-bold">
                                                    {{ optional($jm->profilMasjid)->name ?? '-' }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ optional($jm->user)->name ?? '-' }}
                                                </div>
                                                <div class="small text-muted">
                                                    {{ optional($jm->user)->email ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $jm->aktif ? 'bg-green-lt' : 'bg-red-lt' }}">
                                                    {{ $jm->aktif ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </td>
                                            @for ($i = 1; $i <= 6; $i++)
                                                @php
                                                    $field = 'jumbotron_masjid_' . $i;
                                                    $imagePath = $jm->$field;
                                                @endphp
                                                <td class="text-center">
                                                    @if ($imagePath)
                                                        <div class="d-inline-flex flex-column align-items-center justify-content-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                height="20" viewBox="0 0 24 24" fill="none"
                                                                stroke="#000000" stroke-width="2.2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M5 12l5 5l10 -10" />
                                                            </svg>
                                                            <a href="javascript:void(0)"
                                                                onclick="openAdminJumbotronPreview(@js(asset($imagePath)), 'Jumbotron Masjid {{ $i }}', @js($masjidName))"
                                                                class="small text-decoration-underline text-reset mt-1 fw-medium"
                                                                style="cursor: pointer;">
                                                                Lihat Slide
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                            {{ $jumbotronMasjidsData->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Overlay Pratinjau Gambar Jumbotron Masjid --}}
    <div wire:ignore.self class="modal modal-blur fade" id="adminJumbotronPreviewModal" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header py-2 px-3 border-bottom">
                    <div class="modal-title text-truncate pe-2">
                        <span class="fw-bold" id="adminJumbotronPreviewTitle">Pratinjau Jumbotron Masjid</span>
                        <span class="text-muted small ms-1" id="adminJumbotronPreviewMasjid"></span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        title="Tutup"></button>
                </div>

                <div class="modal-body p-3 d-flex align-items-center justify-content-center position-relative"
                    style="min-height: 360px;">
                    <div id="adminJumbotronPreviewSkeleton" class="w-100 placeholder-glow"
                        style="aspect-ratio: 16 / 9; max-height: 70vh;">
                        <div class="placeholder col-12 w-100 h-100 rounded-3" style="min-height: 340px;"></div>
                    </div>

                    <div id="adminJumbotronPreviewError" class="text-center text-muted py-5" style="display: none;">
                        Gagal memuat gambar jumbotron masjid.
                    </div>

                    <img id="adminJumbotronPreviewImg" src="" alt="Pratinjau Jumbotron Masjid"
                        class="img-fluid rounded-3"
                        style="max-height: 72vh; width: auto; object-fit: contain; display: none;">
                </div>
            </div>
        </div>
    </div>

    <script>
        window.openAdminJumbotronPreview = function(url, title, masjid) {
            const modalEl = document.getElementById('adminJumbotronPreviewModal');
            if (!modalEl) return;

            const titleEl = document.getElementById('adminJumbotronPreviewTitle');
            const masjidEl = document.getElementById('adminJumbotronPreviewMasjid');
            const imgEl = document.getElementById('adminJumbotronPreviewImg');
            const skeletonEl = document.getElementById('adminJumbotronPreviewSkeleton');
            const errorEl = document.getElementById('adminJumbotronPreviewError');

            if (titleEl) titleEl.textContent = title || 'Pratinjau Jumbotron Masjid';
            if (masjidEl) masjidEl.textContent = masjid ? '— ' + masjid : '';

            if (errorEl) errorEl.style.display = 'none';
            if (skeletonEl) skeletonEl.style.display = 'block';

            if (imgEl) {
                imgEl.style.display = 'none';
                imgEl.onload = function() {
                    if (skeletonEl) skeletonEl.style.display = 'none';
                    imgEl.style.display = 'block';
                };
                imgEl.onerror = function() {
                    if (skeletonEl) skeletonEl.style.display = 'none';
                    if (errorEl) errorEl.style.display = 'block';
                };
                imgEl.src = url;
            }

            if (!modalEl._hasHideListener) {
                modalEl.addEventListener('hidden.bs.modal', function() {
                    if (imgEl) {
                        imgEl.removeAttribute('src');
                        imgEl.style.display = 'none';
                    }
                    if (skeletonEl) skeletonEl.style.display = 'block';
                    if (errorEl) errorEl.style.display = 'none';
                });
                modalEl._hasHideListener = true;
            }

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        };
    </script>
</div>
