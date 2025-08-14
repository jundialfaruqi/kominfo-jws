<div wire:ignore.self class="modal modal-blur fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button wire:click="cancel" type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama<span class="text-danger">*</span></label>
                    <input wire:model="name" type="text"
                        class="form-control rounded-3 @error('name') is-invalid @enderror" placeholder="Masukkan Nama">
                    @error('name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email<span class="text-danger">*</span></label>
                    <input wire:model="email" type="email"
                        class="form-control rounded-3 @error('email') is-invalid @enderror"
                        placeholder="Masukkan Email">
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Telepon<span class="text-danger">*</span></label>
                    <input wire:model="phone" type="text"
                        class="form-control rounded-3 @error('phone') is-invalid @enderror"
                        placeholder="Masukkan Telepon">
                    @error('phone')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat<span class="text-danger">*</span></label>
                    <input wire:model="address" type="text"
                        class="form-control rounded-3 @error('address') is-invalid @enderror"
                        placeholder="Masukkan Alamat">
                    @error('address')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Role<span class="text-danger">*</span></label>
                    <select wire:model="role" class="form-select rounded-3 @error('role') is-invalid @enderror">
                        <option value="" selected>Pilih Role</option>
                        @if (auth()->user()->role === 'Super Admin')
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin">Admin</option>
                        @endif
                        <option value="User">User</option>
                    </select>
                    @error('role')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input wire:model="password" type="password" id="password-create"
                            class="form-control rounded-start-3 @error('password') is-invalid @enderror"
                            placeholder="Masukkan Password">
                        <button type="button" class="btn btn-outline-secondary rounded-end-3"
                            onclick="togglePassword('password-create', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon-eye">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon-eye-off" style="display: none;">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                </path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input wire:model="password_confirmation" type="password" id="password-confirmation-create"
                            class="form-control rounded-start-3 @error('password_confirmation') is-invalid @enderror"
                            placeholder="Masukkan Konfirmasi Password">
                        <button type="button" class="btn btn-outline-secondary rounded-end-3"
                            onclick="togglePassword('password-confirmation-create', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="icon-eye">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="icon-eye-off"
                                style="display: none;">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                </path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Spatie Roles</label>
                    <div class="form-text mb-2">
                        <small class="text-muted">Pilih role yang akan diberikan ke user (menggunakan Spatie
                            Permission)</small>
                    </div>

                    @if ($availableRoles->count() > 0)
                        <div class="row">
                            @foreach ($availableRoles as $role)
                                <div class="col-md-6 mb-2">
                                    <label class="form-check">
                                        <input wire:model="selectedRoles" type="checkbox" value="{{ $role->name }}"
                                            class="form-check-input">
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

                <div class="mb-3">
                    <label class="form-label">Status<span class="text-danger">*</span></label>
                    <select wire:model="status" class="form-select rounded-3 @error('status') is-invalid @enderror">
                        <option value="" selected>Pilih Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    @error('status')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
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
                <button wire:loading.attr="disabled" wire:click="store" type="button"
                    class="btn ms-auto rounded-3">
                    <span wire:loading.remove wire:target="store">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                            <path d="M6.5 12h14.5" />
                        </svg>
                        Simpan
                    </span>
                    <span wire:loading wire:target="store">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
