@if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) && $showTable)
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

    <div class="table-responsive">
        <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
            <thead>
                <tr>
                    <th class="w-1">No.</th>
                    <th>Admin Masjid</th>
                    <th class="text-center">Audio 1</th>
                    <th class="text-center">Audio 2</th>
                    <th class="text-center">Audio 3</th>
                    <th class="text-center">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($audioList as $audio)
                    <tr>
                        <td class="text-center text-muted">
                            {{ $loop->iteration + ($audioList->currentPage() - 1) * $audioList->perPage() }}
                        </td>
                        <td class="text-wrap">{{ $audio->user->name ?? '-' }}</td>
                        {{-- Audio 1 --}}
                        <td class="text-center">
                            @if ($audio->audio1_url)
                                <div
                                    class="text-wrap text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    {{ pathinfo($audio->audio1, PATHINFO_BASENAME) }}
                                </div>
                                <audio controls class="w-50 rounded-3">
                                    <source src="{{ $audio->audio1_url }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- Audio 2 --}}
                        <td class="text-center">
                            @if ($audio->audio2_url)
                                <div
                                    class="text-wrap text-center mb-2 small d-flex align-items-center justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    {{ pathinfo($audio->audio2, PATHINFO_BASENAME) }}
                                </div>
                                <audio controls class="w-50 rounded-3">
                                    <source src="{{ $audio->audio2_url }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- Audio 3 --}}
                        <td class="text-center">
                            @if ($audio->audio3_url)
                                <div
                                    class="text-wrap text-center mb-2 small d-flex align-items-center justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    {{ pathinfo($audio->audio3, PATHINFO_BASENAME) }}
                                </div>
                                <audio controls class="w-50 rounded-3">
                                    <source src="{{ $audio->audio3_url }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $audio->status ? 'bg-primary-lt' : 'bg-danger-lt' }}">
                                {{ $audio->status ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button wire:click="edit('{{ $audio->id }}')" class="btn py-2 px-2 rounded-3 shadow-sm">
                                <span wire:loading.remove wire:target="edit('{{ $audio->id }}')">
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
                                <span wire:loading wire:target="edit('{{ $audio->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span class="small">Loading...</span>
                                </span>
                            </button>
                            <button wire:click="delete('{{ $audio->id }}')"
                                class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                                <span wire:loading.remove wire:target="delete('{{ $audio->id }}')">
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
                                <span wire:loading wire:target="delete('{{ $audio->id }}')">
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
        {{ $audioList->links() }}
    </div>
@endif
