<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                Assign Roles to Users
                            </h3>
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
                            <table
                                class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        <th>Nama & Email</th>
                                        <th>Legacy Role</th>
                                        <th>Spatie Roles</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td class="text-center text-muted">
                                                {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                            </td>
                                            <td>
                                                <div>{{ $user->name }}</div>
                                                <div class="text-muted">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                                        <path d="M3 7l9 6l9 -6" />
                                                    </svg>
                                                    {{ $user->email }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($user->role == 'Super Admin')
                                                    <span class="badge bg-purple-lt">{{ $user->role }}</span>
                                                @elseif ($user->role == 'Admin')
                                                    <span class="badge bg-blue-lt">{{ $user->role }}</span>
                                                @else
                                                    <span class="badge bg-green-lt">{{ $user->role }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->roles->count() > 0)
                                                    @foreach ($user->roles as $role)
                                                        <span
                                                            class="badge bg-info-lt d-inline-flex align-items-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-shield-cog">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path
                                                                    d="M12 21a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3c.568 1.933 .635 3.957 .223 5.89" />
                                                                <path
                                                                    d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                                <path d="M19.001 15.5v1.5" />
                                                                <path d="M19.001 21v1.5" />
                                                                <path d="M22.032 17.25l-1.299 .75" />
                                                                <path d="M17.27 20l-1.3 .75" />
                                                                <path d="M15.97 17.25l1.3 .75" />
                                                                <path d="M20.733 20l1.3 .75" />
                                                            </svg>
                                                            {{ $role->name }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-gray-lt">No roles</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->status == 'Active')
                                                    <span class="badge bg-green-lt">{{ $user->status }}</span>
                                                @else
                                                    <span class="badge bg-red-lt">{{ $user->status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button wire:click="assignRole('{{ $user->id }}')"
                                                    class="btn py-2 px-2 rounded-3 shadow-sm" data-bs-toggle="modal"
                                                    data-bs-target="#assignModal">
                                                    <span wire:loading.remove
                                                        wire:target="assignRole('{{ $user->id }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-cog">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                            <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" />
                                                            <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                            <path d="M19.001 15.5v1.5" />
                                                            <path d="M19.001 21v1.5" />
                                                            <path d="M22.032 17.25l-1.299 .75" />
                                                            <path d="M17.27 20l-1.3 .75" />
                                                            <path d="M15.97 17.25l1.3 .75" />
                                                            <path d="M20.733 20l1.3 .75" />
                                                        </svg>
                                                        <span class="small">Assign Role</span>
                                                    </span>
                                                    <span wire:loading wire:target="assignRole('{{ $user->id }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-users-off mb-2">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 7a4 4 0 1 0 4 4m2 -2a4 4 0 0 0 4 4" />
                                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4m4 0a4 4 0 0 1 4 4v2" />
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                                    <path d="M3 3l18 18" />
                                                </svg>
                                                <div>Tidak ada data user</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Assign Role Modal --}}
    <div wire:ignore.self class="modal modal-blur fade" id="assignModal" tabindex="-1" role="dialog"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Roles to {{ $selectedUserName }}</h5>
                    <button wire:click="cancel" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Roles</label>
                        <div class="mb-2">
                            @if (!empty($currentUserRoles))
                                @foreach ($currentUserRoles as $role)
                                    <span class="badge bg-secondary me-1 text-white">{{ $role }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No roles assigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select New Roles</label>
                        <div class="form-text mb-2">
                            <small class="text-muted">Pilih role yang akan diberikan ke user ini</small>
                        </div>

                        @if ($availableRoles->count() > 0)
                            <div class="row">
                                @foreach ($availableRoles as $role)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <label class="form-check">
                                            <input wire:model="selectedRoles" type="checkbox"
                                                value="{{ $role->name }}" class="form-check-input">
                                            <span class="form-check-label">{{ $role->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <div>Tidak ada role yang tersedia untuk Anda assign.</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <a wire:click="cancel" href="#" class="btn btn-link link-secondary"
                        data-bs-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path stroke="none" d="M0 0h24v24H0z" />
                            <path
                                d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                            <path
                                d="M4.012 16.737a2 2 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                            <path d="M11.5 11.5l4.9 5" />
                            <path d="M16.5 11.5l-5.1 5" />
                        </svg>
                        Batal
                    </a>
                    <button wire:loading.attr="disabled" wire:click="updateRoles" type="button"
                        class="btn ms-auto rounded-3">
                        <span wire:loading.remove wire:target="updateRoles">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-user-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                <path d="M15 19l2 2l4 -4" />
                            </svg>
                            Update Roles
                        </span>
                        <span wire:loading wire:target="updateRoles">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @script
        <script>
            $wire.on('closeAssignModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('assignModal'));
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
