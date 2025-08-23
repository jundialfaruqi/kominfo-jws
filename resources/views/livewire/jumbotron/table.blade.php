<div class="table-responsive">
    <table class="table card-table table-vcenter table-hover text-nowrap datatable">
        <thead>
            <tr>
                <th class="w-1">No.</th>
                <th>Pengunggah</th>
                <th>Jumbotron 1</th>
                <th>Jumbotron 2</th>
                <th>Jumbotron 3</th>
                <th>Jumbotron 4</th>
                <th>Jumbotron 5</th>
                <th>Jumbotron 6</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jumboList as $jumbo)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-wrap">{{ $jumbo->user->name ?? '-' }}</td>
                    <td>
                        @if ($jumbo->jumbo1)
                            <img src="{{ asset($jumbo->jumbo1) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($jumbo->jumbo2)
                            <img src="{{ asset($jumbo->jumbo2) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($jumbo->jumbo3)
                            <img src="{{ asset($jumbo->jumbo3) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($jumbo->jumbo4)
                            <img src="{{ asset($jumbo->jumbo4) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($jumbo->jumbo5)
                            <img src="{{ asset($jumbo->jumbo5) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($jumbo->jumbo6)
                            <img src="{{ asset($jumbo->jumbo6) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $jumbo->is_active ? 'bg-primary-lt' : 'bg-danger-lt' }}">
                            {{ $jumbo->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td class="text-end">
                        @can('edit-jumbotron')
                            <button wire:click="edit('{{ $jumbo->id }}')" class="btn py-2 px-2 rounded-3 shadow-sm">
                                <span wire:loading.remove wire:target="edit('{{ $jumbo->id }}')">
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
                                <span wire:loading wire:target="edit('{{ $jumbo->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span class="small">Loading...</span>
                                </span>
                            </button>
                        @endcan
                        @can('delete-jumbotron')
                            <button wire:click="delete('{{ $jumbo->id }}')" class="btn py-2 px-2 rounded-3 shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <span wire:loading.remove wire:target="delete('{{ $jumbo->id }}')">
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
                                <span wire:loading wire:target="delete('{{ $jumbo->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    <span class="small">Loading...</span>
                                </span>
                            </button>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
