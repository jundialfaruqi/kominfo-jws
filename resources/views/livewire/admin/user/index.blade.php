<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                Daftar User
                            </h3>
                            <div class="card-actions">
                                <button wire:loading.attr="disabled" wire:click="add" type="button"
                                    class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#createModal">
                                    <span wire:loading.remove wire:target="add">
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
                                        Tambah User
                                    </span>
                                    <span wire:loading wire:target="add">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Loading...
                                    </span>
                                </button>
                            </div>
                        </div>
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
                                            class="form-control form-control py-1 rounded-3" placeholder="Ketik disini">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter table-hover text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user as $users)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $users->name }}</td>
                                            <td>{{ $users->email }}</td>
                                            <td>{{ $users->phone }}</td>
                                            @if ($users->role == 'Admin')
                                                <td>
                                                    <span class="badge bg-green-lt">
                                                        {{ $users->role }}
                                                    </span>
                                                </td>
                                            @else
                                                <td>
                                                    <span class="badge bg-red-lt">
                                                        {{ $users->role }}
                                                    </span>
                                                </td>
                                            @endif
                                            <td class="text-end">
                                                <button wire:click="edit('{{ $users->id }}')"
                                                    class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editModal">
                                                    <span wire:loading.remove wire:target="edit('{{ $users->id }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                            <path
                                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                            <path d="M16 5l3 3" />
                                                        </svg>
                                                        <span class="small">Ubah</span>
                                                    </span>
                                                    <span wire:loading wire:target="edit('{{ $users->id }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </span>
                                                </button>
                                                <button wire:click="delete('{{ $users->id }}')"
                                                    class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal">
                                                    <span wire:loading.remove
                                                        wire:target="delete('{{ $users->id }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
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
                                                    <span wire:loading wire:target="delete('{{ $users->id }}')">
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
                        <div class="card-footer d-flex align-items-center justify-content-end pb-0 rounded-4 shadow-sm">
                            {{ $user->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Create Modal --}}
    @include('livewire.admin.user.create')

    {{-- Edit Modal --}}
    @include('livewire.admin.user.edit')

    {{-- Delete Modal --}}
    @include('livewire.admin.user.delete')

    {{-- Close Create Modal --}}
    @script
        <script>
            $wire.on('closeCreateModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
                if (modal) {
                    modal.hide();
                }
            });
            $wire.on('closeEditModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                if (modal) {
                    modal.hide();
                }
            });
            $wire.on('closeDeleteModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) {
                    modal.hide();
                }
            });

            $wire.on('success', message => {
                iziToast.success({
                    title: 'Berhasil',
                    message,
                    position: 'topRight'
                });
            });

            $wire.on('error', message => {
                iziToast.error({
                    title: 'Gagal',
                    message,
                    position: 'topRight'
                });
            });
        </script>
    @endscript
</div>
