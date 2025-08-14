<div wire:ignore.self class="modal modal-blur fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button wire:click="cancel" type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Role<span class="text-danger">*</span></label>
                    <input wire:model="name" type="text"
                        class="form-control rounded-3 @error('name') is-invalid @enderror"
                        placeholder="Masukkan Nama Role">
                    @error('name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Guard Name<span class="text-danger">*</span></label>
                    <select wire:model="guard_name"
                        class="form-select rounded-3 @error('guard_name') is-invalid @enderror">
                        <option value="web">Web</option>
                        <option value="api">API</option>
                    </select>
                    @error('guard_name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="form-text mb-2">
                        <small class="text-muted">Pilih permissions yang akan diberikan ke role ini</small>
                    </div>

                    @if ($permissions->count() > 0)
                        <div class="row">
                            @foreach ($permissions as $permission)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <label class="form-check">
                                        <input wire:model="selectedPermissions" type="checkbox"
                                            value="{{ $permission->id }}" class="form-check-input">
                                        <span class="form-check-label">{{ $permission->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon alert-icon">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <div>Belum ada permission yang tersedia. Silakan buat permission terlebih dahulu.</div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <a wire:click="cancel" href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
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
                <button wire:loading.attr="disabled" wire:click="update" type="button" class="btn ms-auto rounded-3">
                    <span wire:loading.remove wire:target="update">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                            <path d="M6.5 12h14.5" />
                        </svg>
                        Perbarui
                    </span>
                    <span wire:loading wire:target="update">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Memperbarui...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
