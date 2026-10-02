<div class="table-responsive">
    <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
        @if ($activeTab === 'video')
            <thead>
                <tr>
                    <th class="w-1">No.</th>
                    <th>Pengunggah</th>
                    <th>Berkas Video</th>
                    <th>Durasi</th>
                    <th>Audio</th>
                    <th>Status</th>
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jumboList as $jumbo)
                    <tr>
                        <td class="text-center text-muted">
                            {{ $loop->iteration + ($jumboList->currentPage() - 1) * $jumboList->perPage() }}
                        </td>
                        <td class="text-wrap">
                            <span class="fw-semibold">{{ $jumbo->user->name ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple flex-shrink-0"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2" ry="2"/></svg>
                                <div>
                                    @if ($jumbo->video_file)
                                        <a href="{{ asset($jumbo->video_file) }}" target="_blank" class="fw-bold text-decoration-none">
                                            {{ basename($jumbo->video_file) }}
                                        </a>
                                    @else
                                        <span class="text-muted">- Belum ada file video -</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($jumbo->video_duration)
                                <span class="badge bg-blue-lt fw-normal">{{ gmdate('i:s', $jumbo->video_duration) }} ({{ $jumbo->video_duration }}s)</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($jumbo->has_audio)
                                <span class="badge bg-teal-lt" title="Audio Aktif">Audio On</span>
                            @else
                                <span class="badge bg-secondary-lt" title="Mode Bisu">Muted</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $jumbo->is_active ? 'bg-primary-lt' : 'bg-danger-lt' }}">
                                {{ $jumbo->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                @can('edit-jumbotron')
                                    <button wire:click="edit('{{ $jumbo->id }}')" class="btn btn-sm py-1 px-2 rounded-3 shadow-sm" title="Ubah Video">
                                        <span wire:loading.remove wire:target="edit('{{ $jumbo->id }}')">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icon-tabler-edit m-0 align-middle">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                                <span class="lh-1">Ubah</span>
                                            </span>
                                        </span>
                                        <span wire:loading wire:target="edit('{{ $jumbo->id }}')">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </span>
                                    </button>
                                @endcan
                                @can('delete-jumbotron')
                                    <button wire:click="delete('{{ $jumbo->id }}')" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-3 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal" title="Hapus Video">
                                        <span wire:loading.remove wire:target="delete('{{ $jumbo->id }}')">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icon-tabler-trash m-0 align-middle">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                                <span class="lh-1">Hapus</span>
                                            </span>
                                        </span>
                                        <span wire:loading wire:target="delete('{{ $jumbo->id }}')">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="d-flex flex-column align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-2"><path d="m22 8-6 4 6 4V8Z"/><rect width="14" height="12" x="2" y="6" rx="2" ry="2"/></svg>
                                <span>Belum ada berkas video jumbotron yang ditambahkan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        @else
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
                    <th class="w-1 text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jumboList as $jumbo)
                    <tr>
                        <td class="text-center text-muted">
                            {{ $loop->iteration + ($jumboList->currentPage() - 1) * $jumboList->perPage() }}
                        </td>
                        <td class="text-wrap">
                            <span class="fw-semibold">{{ $jumbo->user->name ?? '-' }}</span>
                        </td>
                        <td>
                            @if ($jumbo->jumbo1)
                                <img src="{{ asset($jumbo->jumbo1) }}" width="60" class="img-thumbnail rounded-2">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($jumbo->jumbo2)
                                <img src="{{ asset($jumbo->jumbo2) }}" width="60" class="img-thumbnail rounded-2">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($jumbo->jumbo3)
                                <img src="{{ asset($jumbo->jumbo3) }}" width="60" class="img-thumbnail rounded-2">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($jumbo->jumbo4)
                                <img src="{{ asset($jumbo->jumbo4) }}" width="60" class="img-thumbnail rounded-2">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($jumbo->jumbo5)
                                <img src="{{ asset($jumbo->jumbo5) }}" width="60" class="img-thumbnail rounded-2">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($jumbo->jumbo6)
                                <img src="{{ asset($jumbo->jumbo6) }}" width="60" class="img-thumbnail rounded-2">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $jumbo->is_active ? 'bg-primary-lt' : 'bg-danger-lt' }}">
                                {{ $jumbo->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                @can('edit-jumbotron')
                                    <button wire:click="edit('{{ $jumbo->id }}')" class="btn btn-sm py-1 px-2 rounded-3 shadow-sm" title="Ubah Gambar">
                                        <span wire:loading.remove wire:target="edit('{{ $jumbo->id }}')">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icon-tabler-edit m-0 align-middle">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                                <span class="lh-1">Ubah</span>
                                            </span>
                                        </span>
                                        <span wire:loading wire:target="edit('{{ $jumbo->id }}')">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </span>
                                    </button>
                                @endcan
                                @can('delete-jumbotron')
                                    <button wire:click="delete('{{ $jumbo->id }}')" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-3 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal" title="Hapus Gambar">
                                        <span wire:loading.remove wire:target="delete('{{ $jumbo->id }}')">
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icon-tabler-trash m-0 align-middle">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                                <span class="lh-1">Hapus</span>
                                            </span>
                                        </span>
                                        <span wire:loading wire:target="delete('{{ $jumbo->id }}')">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </span>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <div class="d-flex flex-column align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                <span>Belum ada berkas gambar jumbotron yang ditambahkan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        @endif
    </table>
</div>
