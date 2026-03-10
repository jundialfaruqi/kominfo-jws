@if ($showTable)
    {{-- Pagination & Search Controls --}}
    <div class="card-body border-bottom py-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-auto">
                <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                    <span class="input-group-text rounded-start-4 gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-row">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                            <path d="M9 3l-6 6" />
                            <path d="M14 3l-7 7" />
                            <path d="M19 3l-7 7" />
                            <path d="M21 6l-4 4" />
                            <path d="M3 10h18" />
                            <path d="M10 10v11" />
                        </svg>
                        Tampilkan Baris
                    </span>
                    <select wire:model.live="paginate" class="form-select form-select rounded-end-4">
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                        Cari
                    </span>
                    <input wire:model.live="search" type="text" class="form-control rounded-end-4"
                        placeholder="Ketik disini" autocomplete="off" />
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table card-table table-vcenter table-hover datatable">
            <thead>
                <tr>
                    <th>Daftar Artikel</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($articlesList as $article)
                    <tr>
                        <td>
                            <div
                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 py-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span
                                            class="text-muted small">#{{ $loop->iteration + ($articlesList->currentPage() - 1) * $articlesList->perPage() }}</span>
                                        <div class="fw-bold h4 mb-0">{{ $article->title }}</div>
                                    </div>

                                    <div class="text-muted small mb-2 text-wrap" style="max-width: 600px;">
                                        {{ $article->description }}
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                        <span class="badge bg-blue-lt px-2 py-1 d-flex align-items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-category-2 me-1">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M14 4h6v6h-6z" />
                                                <path d="M4 14h6v6h-6z" />
                                                <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                <path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                            </svg>
                                            {{ $article->category->name ?? '-' }}
                                        </span>

                                        @if ($article->status === 'Published')
                                            <span class="badge bg-success-lt px-2 py-1 d-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check me-1">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                    <path d="M9 12l2 2l4 -4" />
                                                </svg>
                                                Published
                                            </span>
                                        @else
                                            <span class="badge bg-warning-lt px-2 py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-text me-1">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                    <path
                                                        d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                    <path d="M9 9l1 0" />
                                                    <path d="M9 13l6 0" />
                                                    <path d="M9 17l6 0" />
                                                </svg>
                                                Draft
                                            </span>
                                        @endif

                                        <span class="text-muted small d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event me-1">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                <path d="M16 3l0 4" />
                                                <path d="M8 3l0 4" />
                                                <path d="M4 11l16 0" />
                                                <path d="M8 15h2v2h-2z" />
                                            </svg>
                                            {{ $article->published_at ? $article->published_at->format('d M Y H:i') : '-' }}
                                        </span>

                                        <span class="text-muted small d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-user me-1">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                            </svg>
                                            {{ $article->user->name ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 ms-auto">
                                    <button wire:click="edit('{{ $article->id }}')"
                                        class="btn btn-icon d-flex align-items-center justify-content-center rounded-4 shadow-sm"
                                        style="width: 40px; height: 40px;" title="Ubah">
                                        <span wire:loading.remove wire:target="edit('{{ $article->id }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit m-0">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path
                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </span>
                                        <span wire:loading wire:target="edit('{{ $article->id }}')">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </span>
                                    </button>
                                    <button wire:click="delete('{{ $article->id }}')"
                                        class="btn btn-icon d-flex align-items-center justify-content-center rounded-4 shadow-sm text-danger"
                                        style="width: 40px; height: 40px;" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal" title="Hapus">
                                        <span wire:loading.remove wire:target="delete('{{ $article->id }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-trash m-0">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                        </span>
                                        <span wire:loading wire:target="delete('{{ $article->id }}')">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center text-muted py-5">
                            <div class="mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-news text-muted">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" />
                                    <path d="M8 8l4 0" />
                                    <path d="M8 12l4 0" />
                                    <path d="M8 16l4 0" />
                                </svg>
                            </div>
                            Data artikel tidak ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
        {{ $articlesList->links(data: ['scrollTo' => false]) }}
    </div>
@endif
