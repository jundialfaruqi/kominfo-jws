@if ($showTable)
    {{-- Pagination & Search Controls --}}
    <div class="card-body border-bottom py-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-auto">
                <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                    <span class="input-group-text rounded-start-4 gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
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
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-search">
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
        <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
            <thead>
                <tr>
                    <th class="w-1">No.</th>
                    <th>Nama Kategori</th>
                    <th>Dibuat Oleh</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categoriesList as $category)
                    <tr>
                        <td class="text-center text-muted">
                            {{ $loop->iteration + ($categoriesList->currentPage() - 1) * $categoriesList->perPage() }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->user->name ?? '-' }}</td>
                        <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <button wire:click="edit('{{ $category->id }}')"
                                class="btn py-2 px-2 rounded-4 shadow-sm">
                                <span wire:loading.remove wire:target="edit('{{ $category->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-0">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path
                                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                    Ubah
                                </span>
                                <span wire:loading wire:target="edit('{{ $category->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span class="small">Loading...</span>
                                </span>
                            </button>
                            <button wire:click="delete('{{ $category->id }}')"
                                class="btn py-2 px-2 rounded-4 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                                <span wire:loading.remove wire:target="delete('{{ $category->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-0">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                    Hapus
                                </span>
                                <span wire:loading wire:target="delete('{{ $category->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span class="small">Loading...</span>
                                </span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Data tidak ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
        {{ $categoriesList->links(data: ['scrollTo' => false]) }}
    </div>
@endif
