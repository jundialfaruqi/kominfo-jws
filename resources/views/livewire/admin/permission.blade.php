<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4 shadow-sm border-0 mb-3">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-auto">
                            @if (auth()->user()->role === 'Super Admin' || auth()->user()->role === 'Admin')
                                <button wire:loading.attr="disabled" wire:click="add" type="button"
                                    class="btn btn-primary py-2 rounded-4 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#createModal">
                                    <span wire:loading.remove wire:target="add">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-lock-open-2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M3 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                            <path d="M9 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                            <path d="M13 11v-4a4 4 0 1 1 8 0v4" />
                                        </svg>
                                        Tambah Permission
                                    </span>
                                    <span wire:loading wire:target="add">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Loading...
                                    </span>
                                </button>
                            @endif
                        </div>
                        <div class="col-12 col-md-auto">
                            <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                                <span class="input-group-text rounded-start-4 gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-table-row">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                        <path d="M9 3l-6 6" />
                                        <path d="M14 3l-7 7" />
                                        <path d="M19 3l-7 7" />
                                        <path d="M21 6l-4 4" />
                                        <path d="M3 10h18" />
                                        <path d="M10 10v11" />
                                    </svg>
                                    Tampilkan Baris
                                </span>
                                <select wire:model.live="paginate" class="form-select form-select rounded-end-4">
                                    <option>5</option>
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto ms-md-auto">
                            <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                                <span class="input-group-text rounded-start-4 gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-search">
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
                </div>
            </div>
            <div class="row row-deck row-cards">
                <div class="col-12">
                    @if (count($groupedPermissions))
                        <div class="row row-cards">
                            @foreach ($groupedPermissions as $groupKey => $items)
                                <div class="col-12 col-lg-6">
                                    <div class="card rounded-4 shadow-sm">
                                        <div
                                            class="card-header bg-dark text-white rounded-top-4 d-flex justify-content-between align-items-center">
                                            <h3 class="card-title">
                                                {{ $groupMeta[$groupKey]['name'] }}
                                            </h3>

                                        </div>
                                        <div class="table-responsive">
                                            <table
                                                class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Permission</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($items as $item)
                                                        @include('livewire.admin.permission.table-body', [
                                                            'item' => $item,
                                                        ])
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                                            {{ $groupPaginators[$groupKey]->links(data: ['scrollTo' => false]) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card rounded-4 shadow-sm">
                            <div class="card-body">
                                <div class="text-center text-muted py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-database-off mb-2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M12.983 8.978c3.955 -.182 7.017 -1.446 7.017 -2.978c0 -1.657 -3.582 -3 -8 -3c-1.661 0 -3.204 .19 -4.483 .515m-2.783 1.228c-.471 .382 -.734 .808 -.734 1.257c0 1.22 1.944 2.271 4.734 2.74" />
                                        <path
                                            d="M4 6v6c0 1.657 3.582 3 8 3c.986 0 1.93 -.067 2.802 -.19m3.187 -.82c1.251 -.53 2.011 -1.228 2.011 -1.99v-6" />
                                        <path
                                            d="M4 12v6c0 1.657 3.582 3 8 3c3.217 0 5.991 -.712 7.261 -1.74m.739 -3.26v-4" />
                                        <path d="M3 3l18 18" />
                                    </svg>
                                    <div>Tidak ada data permission</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    @include('livewire.admin.permission.create')

    {{-- Edit Modal --}}
    @include('livewire.admin.permission.edit')

    {{-- Delete Modal --}}
    @include('livewire.admin.permission.delete')

    {{-- Scripts --}}
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
