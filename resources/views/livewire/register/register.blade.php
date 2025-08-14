<div>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('theme/static/logo-pemko-kominfo.webp') }}" width="110" height="32"
                        alt="Tabler" class="navbar-brand-image">
                </a>
            </div>

            <!-- Success Message -->
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                        </div>
                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <!-- Error Message -->
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <path d="m12 9 0 4" />
                                <path d="m12 17 .01 0" />
                            </svg>
                        </div>
                        <div>
                            {{ session('error') }}
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            @endif

            <form wire:submit="register" class="card card-md rounded-4 shadow-sm" autocomplete="off" novalidate>
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Buat Akun JWS Diskominfo</h2>

                    <!-- Name Field -->
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" wire:model="name"
                            class="form-control rounded-4 @error('name') is-invalid @enderror"
                            placeholder="Masukkan Nama">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" wire:model="email"
                            class="form-control rounded-4 @error('email') is-invalid @enderror"
                            placeholder="Masukkan Email">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Masjid Field -->
                    <div class="mb-3">
                        <label class="form-label">Nama Masjid</label>
                        <input type="text" wire:model="masjid"
                            class="form-control rounded-4 @error('masjid') is-invalid @enderror"
                            placeholder="Masukkan Nama Masjid">
                        @error('masjid')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" wire:model="phone"
                            class="form-control rounded-4 @error('phone') is-invalid @enderror"
                            placeholder="Masukkan Nomor Telepon">
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Address Field -->
                    <div class="mb-3">
                        <label class="form-label">Alamat Masjid</label>
                        <textarea wire:model="address" class="form-control rounded-4 @error('address') is-invalid @enderror"
                            placeholder="Masukkan Alamat Masjid" rows="2"></textarea>
                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group input-group-flat rounded-4 border">
                            <input type="password" wire:model="password"
                                class="form-control rounded-4 border-0 @error('password') is-invalid @enderror"
                                placeholder="Masukkan Password" autocomplete="new-password" id="password">
                            <button type="button" class="btn btn-link rounded-4 border-0 p-1"
                                onclick="togglePassword('password', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    <path
                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye-off" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round"
                                    style="display: none;">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="input-group input-group-flat rounded-4 border">
                            <input type="password" wire:model="password_confirmation"
                                class="form-control rounded-4 border-0 @error('password_confirmation') is-invalid @enderror"
                                placeholder="Masukkan Konfirmasi Password" autocomplete="new-password"
                                id="password_confirmation">
                            <button type="button" class="btn btn-link rounded-4 border-0 p-1"
                                onclick="togglePassword('password_confirmation', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    <path
                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-eye-off" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round"
                                    style="display: none;">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" wire:model="terms_agreed"
                                class="form-check-input rounded-4 @error('terms_agreed') is-invalid @enderror" />
                            <span class="form-check-label">Saya Setuju dengan <a href="#" tabindex="-1">Syarat
                                    dan Ketentuan</a>.</span>
                        </label>
                        @error('terms_agreed')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100 rounded-4" wire:loading.attr="disabled">
                            <span wire:loading.remove>Buat Akun</span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Membuat Akun...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
            <div class="text-center text-secondary mt-3">
                Sudah Punya Akun? <a wire:navigate href="{{ route('login') }}" tabindex="-1">Masuk</a>
            </div>
        </div>
    </div>


</div>
