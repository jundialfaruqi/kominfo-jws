<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4 shadow-sm">
                <div class="row g-0">
                    <div class="col-12 col-md-3 border-end d-none d-md-block">
                        <div class="card-body">
                            <h4 class="subheader align-items-center d-flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                </svg>
                                Pengaturan
                            </h4>
                            <div class="list-group list-group-transparent">
                                <a wire:navigate href="{{ route('updateprofile.index') }}"
                                    class="list-group-item list-group-item-action d-flex align-items-center active">
                                    Profil Akun
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-9 d-flex flex-column">
                        <div class="card-body">
                            <h2 class="mb-4">Profil Saya</h2>
                            <h3 class="card-title">Foto Profil</h3>
                            <div class="row align-items-center">
                                @if ($photo)
                                    <div class="col-auto">
                                        <div class="card p-2 rounded-3 border-0 shadow-sm">
                                            <span class="avatar avatar-xl rounded-3"
                                                style="background-image: url('{{ $photo->temporaryUrl() }}')">
                                            </span>
                                        </div>
                                    </div>
                                @elseif($temp_photo)
                                    <div class="col-auto">
                                        <div class="card p-2 rounded-3 border-0 shadow-sm">
                                            <span class="avatar avatar-xl rounded-3"
                                                style="background-image: url('{{ asset($temp_photo) }}')">
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-auto">
                                        <div class="card p-2 rounded-3 border-0 shadow-sm">
                                            <span class="avatar avatar-xl rounded-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-1">
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-auto py-2">
                                    <div class="d-flex gap-2">
                                        <label for="photo-upload" class="btn rounded-3 shadow-sm">
                                            Ubah Foto
                                            <input type="file" id="photo-upload" wire:model="photo" accept="image/*"
                                                style="display: none;">
                                        </label>
                                        @if ($photo)
                                            <button type="button" class="btn btn-primary rounded-3 shadow-sm"
                                                wire:click="uploadPhoto" wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="uploadPhoto">Simpan</span>
                                                <span wire:loading wire:target="uploadPhoto">
                                                    <span class="spinner-border spinner-border-sm me-1"
                                                        role="status"></span>
                                                    Menyimpan...
                                                </span>
                                            </button>
                                        @endif
                                    </div>
                                    @error('photo')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if ($temp_photo)
                                    <div class="col-auto py-2">
                                        <button type="button" class="btn btn-danger rounded-3 shadow-sm"
                                            wire:click="clearPhoto" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="clearPhoto">Hapus</span>
                                            <span wire:loading wire:target="clearPhoto">
                                                <span class="spinner-border spinner-border-sm me-1"
                                                    role="status"></span>
                                                Menghapus...
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <h3 class="card-title mt-4">Detail Profil</h3>
                            <div class="row g-3">
                                <div class="col-md">
                                    <div class="form-label">Nama <span class="text-danger">*</span></div>
                                    <input type="text"
                                        class="form-control rounded-3 @error('name') is-invalid @enderror"
                                        wire:model="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md">
                                    <div class="form-label">No. Telp</div>
                                    <input type="text"
                                        class="form-control rounded-3 @error('phone') is-invalid @enderror"
                                        wire:model="phone">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md">
                                    <div class="form-label">Alamat</div>
                                    <input type="text"
                                        class="form-control rounded-3 @error('address') is-invalid @enderror"
                                        wire:model="address">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h3 class="card-title mt-4">Email</h3>
                            <p class="card-subtitle">Email ini akan digunakan untuk login ke dalam Aplikasi.</p>
                            <div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="email"
                                            class="form-control rounded-3 @error('email') is-invalid @enderror"
                                            wire:model.live="email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-primary rounded-3"
                                            wire:click="showEmailConfirmation"
                                            @if ($email === Auth::user()->email) disabled @endif>
                                            Ubah
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <h3 class="card-title mt-4">Password</h3>
                            <p class="card-subtitle">Biarkan kosong jika tidak ingin mengubah password.</p>
                            <div class="col-md-6 mb-3">
                                <div class="form-label">Password Lama</div>
                                <input type="password"
                                    class="form-control rounded-3 @error('password_old') is-invalid @enderror"
                                    wire:model="password_old" autocomplete="new-password"
                                    placeholder="Masukkan Password Lama">
                                @error('password_old')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-label">Password Baru</div>
                                <input type="password"
                                    class="form-control rounded-3 @error('password_new') is-invalid @enderror"
                                    wire:model="password_new" placeholder="Masukkan Password Baru"
                                    autocomplete="new-password">
                                @error('password_new')
                                    <div class="invalid-feedback">{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-label">Konfirmasi Password Baru</div>
                                <input type="password"
                                    class="form-control rounded-3 @error('password_new_confirmation') is-invalid @enderror"
                                    wire:model="password_new_confirmation"
                                    placeholder="Masukkan Konfirmasi Password Baru" autocomplete="new-password">
                                @error('password_new_confirmation')
                                    <div class="invalid-feedback">{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer bg-transparent mt-auto">
                            <div class="btn-list justify-content-end">
                                <a wire:navigate href="{{ route('dashboard.index') }}" class="btn rounded-3">
                                    Tutup
                                </a>
                                <button type="button" wire:click="update" class="btn btn-primary rounded-3"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Simpan</span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Email -->
    @if ($show_email_confirmation)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1" role="dialog"
            aria-modal="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Ubah Email</h5>
                    </div>
                    <div class="modal-body">
                        <p>Untuk mengubah email dari <strong>{{ Auth::user()->email }}</strong> ke
                            <strong>{{ $new_email }}</strong>, masukkan password Anda:
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                class="form-control rounded-3 @error('email_confirmation_password') is-invalid @enderror"
                                wire:model="email_confirmation_password" placeholder="Masukkan password Anda">
                            @error('email_confirmation_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="alert alert-warning rounded-3" role="alert">
                            <strong>Perhatian!</strong> Jangan berikan informasi <strong>email</strong> dan
                            <strong>password</strong> akun Anda kepada siapapun.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto rounded-3"
                            wire:click="cancelEmailChange">Batal</button>
                        <button type="button" class="btn btn-primary rounded-3" wire:click="confirmEmailChange"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmEmailChange">Konfirmasi</span>
                            <span wire:loading wire:target="confirmEmailChange">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @script
        <script>
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

            // $wire.on('logout-user', () => {
            //     setTimeout(() => {
            //         window.location.href = '/logout';
            //     }, 2000);
            // });

            $wire.on('resetFileInput', (data) => {
                const input = document.querySelector(`input[name="${data.inputName}"]`);
                if (input) {
                    input.value = '';
                }
            });
        </script>
    @endscript
</div>
