@if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
    @if ($showTable)
        {{-- Pagination & Search Controls --}}
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="text-secondary">
                    Lihat
                    <div class="mx-2 d-inline-block">
                        <select wire:model.live="paginate" class="form-select form-select rounded-4">
                            <option>5</option>
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                    </div>
                </div>
                <div class="input-group align-items-center rounded-4 w-auto ms-auto">
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
        {{-- Table of mosque profiles --}}
        <div class="table-responsive">
            <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th class="w-1">No.</th>
                        <th>Masjid & Admin</th>
                        <th>Alamat</th>
                        <th>No Hp</th>
                        <th>Logo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($profilList as $profil)
                        <tr>
                            <td class="text-center text-muted">
                                {{ $loop->iteration + ($profilList->currentPage() - 1) * $profilList->perPage() }}</td>
                            <td class="text-wrap">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        @if ($profil->slug)
                                            <a href="{{ route('firdaus', $profil->slug) }}"
                                                class="btn btn-icon bg-blue-lt text-blue-lt-fg rounded-circle border-0 shadow-sm"
                                                target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-external-link">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                    <path d="M11 13l9 -9" />
                                                    <path d="M15 4h5v5" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div>{{ $profil->name }}</div>
                                        <div class="text-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                                <path d="M3 7l9 6l9 -6" />
                                            </svg>
                                            {{ $profil->user->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-wrap">{{ $profil->address }}</td>
                            <td class="text-wrap">{{ $profil->phone }}</td>
                            <td>
                                @if ($profil->logo_masjid)
                                    <img src="{{ asset($profil->logo_masjid) }}" width="40" class="img-thumbnail">
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button wire:click="edit('{{ $profil->id }}')"
                                    class="btn py-2 px-2 rounded-4 shadow-sm">
                                    <span wire:loading.remove wire:target="edit('{{ $profil->id }}')">
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
                                    <span wire:loading wire:target="edit('{{ $profil->id }}')">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <span class="small">Loading...</span>
                                    </span>
                                </button>
                                <button wire:click="delete('{{ $profil->id }}')"
                                    class="btn py-2 px-2 rounded-4 shadow-sm " data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    <span wire:loading.remove wire:target="delete('{{ $profil->id }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
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
                                    <span wire:loading wire:target="delete('{{ $profil->id }}')">
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
            {{ $profilList->links(data: ['scrollTo' => false]) }}
        </div>
    @endif
@endif
