<tr>
    <td>
        {{-- <div class="card card-sm rounded-4 border-0 shadow-sm bg-body"> --}}
        <div class="card-body py-2">
            <div class="d-flex align-items-center justify-content-between">
                <span class="avatar border-0 rounded-3 shadow-none bg-transparent"
                    style="border:0 !important; box-shadow:none !important;">{{ $item['index'] }}</span>
                <div class="vr mx-2"></div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <span class="text-success fw-bold d-inline-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-lock-open-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M9 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                <path d="M13 11v-4a4 4 0 1 1 8 0v4" />
                            </svg>
                            {{ $item['model']->name }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-secondary small">
                            {{ $item['model']->created_at->format('d M Y H:i') }}
                            <span class="badge bg-purple-lt">
                                {{ $item['model']->roles->count() }}
                                roles
                            </span>
                            {{ $item['model']->guard_name }}
                        </span>
                    </div>
                </div>
                <div class="ms-2">
                    <div class="dropdown">
                        <button class="btn btn-icon rounded-circle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-caret-up-down">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 10l-6 -6l-6 6h12" />
                                <path d="M18 14l-6 6l-6 -6h12" />
                            </svg>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal"
                                wire:click="edit('{{ $item['model']->id }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                Ubah
                            </a>
                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                wire:click="delete('{{ $item['model']->id }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-2">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 7l16 0" />
                                    <path d="M10 11l0 6" />
                                    <path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                                Hapus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- </div> --}}
    </td>
</tr>
