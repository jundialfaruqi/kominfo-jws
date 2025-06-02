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
                        class="form-control rounded-3 @error('name')
                        is-invalid
                    @enderror"
                        placeholder="Masukkan Nama">
                    @error('name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email<span class="text-danger">*</span></label>
                    <input wire:model="email" type="email"
                        class="form-control rounded-3 @error('email')
                        is-invalid
                    @enderror"
                        placeholder="Masukkan Email">
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Telepon<span class="text-danger">*</span></label>
                    <input wire:model="phone" type="text"
                        class="form-control rounded-3 @error('phone')    
                        is-invalid
                    @enderror"
                        placeholder="Masukkan Telepon">
                    @error('phone')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat<span class="text-danger">*</span></label>
                    <input wire:model="address" type="text"
                        class="form-control rounded-3 @error('address')
                        is-invalid
                    @enderror"
                        placeholder="Masukkan Alamat">
                    @error('address')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Role<span class="text-danger">*</span></label>
                    <select wire:model="role"
                        class="form-select rounded-3 @error('role')
                        is-invalid
                    @enderror">
                        <option class="dropdown-header" selected>Pilih Role</option>
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                    @error('role')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password<span class="text-danger">*</span></label>
                    <input wire:model="password" type="password"
                        class="form-control rounded-3 @error('password')
                        is-invalid
                    @enderror"
                        placeholder="Masukkan Password">
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password<span class="text-danger">*</span></label>
                    <input wire:model="password_confirmation" type="password"
                        class="form-control rounded-3 @error('password_confirmation')
                        is-invalid
                    @enderror"
                        placeholder="Masukkan Konfirmasi Password">
                    @error('password_confirmation')
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
                    class="btn py-2 px-2 rounded-3 shadow-sm ms-auto">
                    <span wire:loading.remove wire:target="store">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                            <path d="M6.5 12h14.5" />
                        </svg>
                        Tambah User
                    </span>
                    <span wire:loading wire:target="store">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menyimpan...
                    </span>
                </button>
                </a>
            </div>
        </div>
    </div>
</div>
