<div class="table-responsive">
    <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
        <thead>
            <tr>
                <th class="w-1">No.</th>
                <th>Admin Masjid</th>
                <th>Slide 1</th>
                <th>Slide 2</th>
                <th>Slide 3</th>
                <th>Slide 4</th>
                <th>Slide 5</th>
                <th>Slide 6</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($slideList as $slide)
                <tr>
                    <td class="text-center text-muted">
                        {{ $loop->iteration + ($slideList->currentPage() - 1) * $slideList->perPage() }}
                    </td>
                    <td class="text-wrap">{{ $slide->user->name ?? '-' }}</td>
                    <td>
                        @if ($slide->slide1)
                            <img src="{{ asset($slide->slide1) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($slide->slide2)
                            <img src="{{ asset($slide->slide2) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($slide->slide3)
                            <img src="{{ asset($slide->slide3) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($slide->slide4)
                            <img src="{{ asset($slide->slide4) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($slide->slide5)
                            <img src="{{ asset($slide->slide5) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td>
                        @if ($slide->slide6)
                            <img src="{{ asset($slide->slide6) }}" width="60" class="img-thumbnail">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button wire:click="edit('{{ $slide->id }}')" class="btn py-2 px-2 rounded-3 shadow-sm">
                            <span wire:loading.remove wire:target="edit('{{ $slide->id }}')">
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
                            <span wire:loading wire:target="edit('{{ $slide->id }}')">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Loading...</span>
                            </span>
                        </button>
                        <button wire:click="delete('{{ $slide->id }}')" class="btn py-2 px-2 rounded-3 shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <span wire:loading.remove wire:target="delete('{{ $slide->id }}')">
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
                            <span wire:loading wire:target="delete('{{ $slide->id }}')">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Loading...</span>
                            </span>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
