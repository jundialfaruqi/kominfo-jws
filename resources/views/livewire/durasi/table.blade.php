<div class="table-responsive">
    <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
        <thead>
            <tr>
                <th class="w-1">No</th>
                <th>Nama Admin Masjid</th>
                <th class="text-center">Syuruq</th>
                <th class="text-center">Shubuh (A/I/F)</th>
                <th class="text-center">Dzuhur (A/I/F)</th>
                <th class="text-center">Jum'at (Slide)</th>
                <th class="text-center">Ashar (A/I/F)</th>
                <th class="text-center">Maghrib (A/I/F)</th>
                <th class="text-center">Isya (A/I/F)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($durasiList as $durasi)
                <tr>
                    <td class="text-center text-muted">
                        {{ $loop->iteration + ($durasiList->currentPage() - 1) * $durasiList->perPage() }}
                    </td>
                    <td class="text-wrap">{{ $durasi->user->name }}</td>
                    <td class="text-center">{{ $durasi->adzan_shuruq }}
                    </td>
                    <td class="text-center">
                        {{ $durasi->adzan_shubuh }}/{{ $durasi->iqomah_shubuh }}/{{ $durasi->final_shubuh }}
                    </td>
                    <td class="text-center">
                        {{ $durasi->adzan_dzuhur }}/{{ $durasi->iqomah_dzuhur }}/{{ $durasi->final_dzuhur }}
                    </td>
                    <td class="text-center">{{ $durasi->jumat_slide }}</td>
                    <td class="text-center">
                        {{ $durasi->adzan_ashar }}/{{ $durasi->iqomah_ashar }}/{{ $durasi->final_ashar }}
                    </td>
                    <td class="text-center">
                        {{ $durasi->adzan_maghrib }}/{{ $durasi->iqomah_maghrib }}/{{ $durasi->final_maghrib }}
                    </td>
                    <td class="text-center">
                        {{ $durasi->adzan_isya }}/{{ $durasi->iqomah_isya }}/{{ $durasi->final_isya }}
                    </td>
                    <td class="text-end">
                        <button wire:click="edit({{ $durasi->id }})" class="btn py-2 px-2 rounded-3 shadow-sm">
                            <span wire:loading.remove wire:target="edit({{ $durasi->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                Ubah
                            </span>
                            <span wire:loading wire:target="edit({{ $durasi->id }})"><span
                                    class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">loading...</span>
                            </span>
                        </button>
                        <button wire:click="delete('{{ $durasi->id }}')" class="btn py-2 px-2 rounded-3 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <span wire:loading.remove wire:target="delete('{{ $durasi->id }}')">
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
                            <span wire:loading wire:target="delete('{{ $durasi->id }}')">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">loading...</span>
                            </span>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
