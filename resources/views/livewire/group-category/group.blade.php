<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                {{ Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) ? 'Daftar Group Category' : 'Group Category Saya' }}
                            </h3>
                            @can('create-group-category')
                                <div class="card-actions">
                                    <a href="{{ route('group-category.create') }}" class="btn py-2 px-2 rounded-3 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                            <path d="M13.5 6.5l4 4" />
                                            <path d="M16 19h6" />
                                            <path d="M19 16v6" />
                                        </svg>
                                        Tambah Group Category
                                    </a>
                                </div>
                            @endcan
                        </div>

                        @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) && $showTable)
                            <div class="card-body border-bottom py-3">
                                <div class="d-flex">
                                    <div class="text-secondary">
                                        Lihat
                                        <div class="mx-2 d-inline-block">
                                            <select wire:model.live="paginate"
                                                class="form-select form-select py-1 rounded-3">
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
                                            <input wire:model.live="search" type="text"
                                                class="form-control form-control py-1 rounded-3"
                                                placeholder="Ketik disini">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        @if ($isAdmin)
                                            <th>Nama Masjid</th>
                                        @endif
                                        <th>Nama Group</th>
                                        <th class="w-1">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($groupList as $group)
                                        <tr>
                                            <td class="text-center text-muted">
                                                {{ $loop->iteration + ($groupList->currentPage() - 1) * $groupList->perPage() }}
                                            </td>
                                            @if ($isAdmin)
                                                <td class="text-wrap">
                                                    {{ optional($group->profil)->name }}
                                                </td>
                                            @endif
                                            <td class="text-wrap">
                                                {{ $group->name }}
                                            </td>
                                            <td class="text-center">
                                                @can('edit-group-category')
                                                    <a wire:navigate href="{{ route('group-category.edit', $group->id) }}"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm" title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                            <path d="M13.5 6.5l4 4" />
                                                        </svg>
                                                        Edit
                                                    </a>
                                                @endcan
                                                @can('delete-group-category')
                                                    <button wire:click="delete('{{ $group->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm ms-2" title="Hapus"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M4 7h16" />
                                                            <path d="M10 11v6" />
                                                            <path d="M14 11v6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7V4h6v3" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $isAdmin ? 4 : 3 }}" class="text-center text-muted">Tidak
                                                ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                            {{ $groupList->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.group-category.delete')

    @script
        <script>
            $wire.on('closeDeleteModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) {
                    modal.hide();
                }
            });
        </script>
    @endscript
</div>
