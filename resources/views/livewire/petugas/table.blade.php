@if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) && $showTable)
    {{-- Pagination and search control --}}
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
                <div class="ms-2 d-inline-block">
                    <input wire:model.live="search" type="text" class="form-control form-control py-1 rounded-3"
                        placeholder="Ketik disini">
                </div>
            </div>
        </div>
    </div>
    {{-- Table petugas --}}
    <div class="table-responsive">
        <table class="table card-table table-vcenter table-hover text-nowrap datatable">
            <thead>
                <tr>
                    <th class="w-1">No</th>
                    <th>Nama Admin Masjid</th>
                    <th>Hari</th>
                    <th>Khatib</th>
                    <th>Imam</th>
                    <th>Muadzin</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($petugasList as $petugas)
                    <tr>
                        <td class="text-center text-muted">
                            {{ $loop->iteration + ($petugasList->currentPage() - 1) * $petugasList->perPage() }}
                        </td>
                        <td class="text-wrap">{{ $petugas->user->name }}</td>
                        <td>{{ $petugas->hari }}</td>
                        <td>{{ $petugas->khatib }}</td>
                        <td>{{ $petugas->imam }}</td>
                        <td>{{ $petugas->muadzin }}</td>
                        <td class="text-end">
                            <button wire:click="edit({{ $petugas->id }})" class="btn py-2 px-2 rounded-3 shadow-sm">
                                <span wire:loading.remove wire:target="edit({{ $petugas->id }})">
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
                                <span wire:loading wire:target="edit({{ $petugas->id }})"><span
                                        class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span class="small">loading...</span>
                                </span>
                            </button>
                            <button wire:click="delete('{{ $petugas->id }}')" class="btn py-2 px-2 rounded-3 shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <span wire:loading.remove wire:target="delete('{{ $petugas->id }}')">
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
                                <span wire:loading wire:target="delete('{{ $petugas->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span class="small">loading...</span>
                                </span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
        {{ $petugasList->links() }}
    </div>
@endif
