@if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
    @if ($showTable)
        @php
            $slideColumns = [
                'adzan1'  => 'Iqomah 1',
                'adzan2'  => 'Iqomah 2',
                'adzan3'  => 'Iqomah 3',
                'adzan4'  => 'Iqomah 4',
                'adzan5'  => 'Iqomah 5',
                'adzan6'  => 'Iqomah 6',
                'adzan7'  => "Jum'at 1",
                'adzan8'  => "Jum'at 2",
                'adzan9'  => "Jum'at 3",
                'adzan10' => "Jum'at 4",
                'adzan11' => "Jum'at 5",
                'adzan12' => "Jum'at 6",
                'adzan15' => 'After Iqomah',
            ];
        @endphp

        {{-- Pagination & search control --}}
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="text-secondary">
                    Lihat
                    <div class="mx-2 d-inline-block">
                        <select wire:model.live="paginate" class="form-select form-select py-1 rounded-3">
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
                    <div class="ms-2 d-inline-block"
                        x-data="{
                            query: $wire.entangle('search'),
                            timer: null,
                            onInput(e) {
                                clearTimeout(this.timer);
                                const val = (e.target.value || '').trim();
                                this.timer = setTimeout(() => {
                                    if (val.length >= 3 || val.length === 0) {
                                        $wire.set('search', val);
                                    }
                                }, 500);
                            }
                        }">
                        <input type="search"
                            name="search_adzan_masjid"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            spellcheck="false"
                            :value="query"
                            @input="onInput($event)"
                            @search="onInput($event)"
                            class="form-control form-control py-1 rounded-3"
                            placeholder="Min. 3 karakter...">
                    </div>
                </div>
            </div>
        </div>

        {{-- table --}}
        <div class="table-responsive">
            <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th class="w-1">No.</th>
                        <th>Admin Masjid</th>
                        @foreach ($slideColumns as $key => $title)
                            <th class="text-center">{{ $title }}</th>
                        @endforeach
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($adzanList as $adzan)
                        @php
                            $masjidName = $adzan->user?->profil?->name ?? ($adzan->user?->name ?? '-');
                        @endphp
                        <tr wire:key="adzan-row-{{ $adzan->id }}">
                            <td class="text-center text-muted">
                                {{ $loop->iteration + ($adzanList->currentPage() - 1) * $adzanList->perPage() }}
                            </td>
                            <td class="text-nowrap">
                                <div class="fw-bold text-nowrap">{{ $adzan->user?->profil?->name ?? '-' }}</div>
                                <div class="text-muted small text-nowrap">{{ $adzan->user?->name ?? '-' }}</div>
                            </td>
                            @foreach ($slideColumns as $field => $label)
                                @php
                                    $slidePath = $adzan->$field;
                                @endphp
                                <td class="text-center">
                                    @if ($slidePath)
                                        <div class="d-inline-flex flex-column align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2.2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 12l5 5l10 -10" />
                                            </svg>
                                            <a href="javascript:void(0)"
                                                onclick="openAdzanSlidePreview(@js(asset($slidePath)), @js($label), @js($masjidName))"
                                                class="small text-decoration-underline text-reset mt-1 fw-medium"
                                                style="cursor: pointer;">
                                                Lihat Slide
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-end">
                                <button wire:click="edit('{{ $adzan->id }}')"
                                    class="btn py-2 px-2 rounded-3 shadow-sm">
                                    <span wire:loading.remove wire:target="edit('{{ $adzan->id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path
                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                        Ubah
                                    </span>
                                    <span wire:loading wire:target="edit('{{ $adzan->id }}')">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <span class="small">Loading...</span>
                                    </span>
                                </button>
                                <button wire:click="delete('{{ $adzan->id }}')"
                                    class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    <span wire:loading.remove wire:target="delete('{{ $adzan->id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
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
                                    <span wire:loading wire:target="delete('{{ $adzan->id }}')">
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

        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
            {{ $adzanList->links(data: ['scrollTo' => false]) }}
        </div>

        {{-- Modal Overlay Pratinjau Slide --}}
        <div wire:ignore.self class="modal modal-blur fade" id="adzanSlidePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content rounded-4 border-0 shadow">
                    {{-- Header Modal + Judul & Tombol Icon Close --}}
                    <div class="modal-header py-2 px-3 border-bottom">
                        <div class="modal-title text-truncate pe-2">
                            <span class="fw-bold" id="adzanSlidePreviewTitle">Pratinjau Slide</span>
                            <span class="text-muted small ms-1" id="adzanSlidePreviewMasjid"></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" title="Tutup"></button>
                    </div>

                    {{-- Body Modal + Skeleton Loading & Gambar --}}
                    <div class="modal-body p-3 d-flex align-items-center justify-content-center position-relative" style="min-height: 360px;">
                        {{-- Skeleton Loading --}}
                        <div id="adzanSlidePreviewSkeleton" class="w-100 placeholder-glow" style="aspect-ratio: 16 / 9; max-height: 70vh;">
                            <div class="placeholder col-12 w-100 h-100 rounded-3" style="min-height: 340px;"></div>
                        </div>

                        {{-- Pesan Gagal Muat --}}
                        <div id="adzanSlidePreviewError" class="text-center text-muted py-5" style="display: none;">
                            Gagal memuat gambar slide.
                        </div>

                        {{-- Gambar Slide --}}
                        <img id="adzanSlidePreviewImg" src="" alt="Pratinjau Slide" class="img-fluid rounded-3"
                            style="max-height: 72vh; width: auto; object-fit: contain; display: none;">
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openAdzanSlidePreview(url, title, masjid) {
                const modalEl = document.getElementById('adzanSlidePreviewModal');
                if (!modalEl) return;

                const titleEl = document.getElementById('adzanSlidePreviewTitle');
                const masjidEl = document.getElementById('adzanSlidePreviewMasjid');
                const imgEl = document.getElementById('adzanSlidePreviewImg');
                const skeletonEl = document.getElementById('adzanSlidePreviewSkeleton');
                const errorEl = document.getElementById('adzanSlidePreviewError');

                if (titleEl) titleEl.textContent = title || 'Pratinjau Slide';
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
                    modalEl.addEventListener('hidden.bs.modal', function () {
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
            }
        </script>
    @endif
@endif
