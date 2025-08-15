<div class="container container-normal py-6">
    <div class="row align-items-center g-4">
        <div class="col-lg">
            <div class="container-tight">
                <div class="text-center mb-4">
                    <a href="." class="navbar-brand navbar-brand-autodark"><img
                            src="{{ asset('theme/static/logo-pemko-kominfo.webp') }}" height="36" alt="">
                    </a>
                </div>
                <div class="card card-md rounded-4 shadow-sm">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">Login JWS Diskominfo</h2>
                        <form wire:submit="login" autocomplete="off" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Alamat Email</label>
                                <input wire:model="email" type="email"
                                    class="form-control rounded-4 @error('email') is-invalid @enderror"
                                    placeholder="Masukkan Alamat Email" autocomplete="off">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <label class="form-label">
                                    Password
                                    <span class="form-label-description">
                                        {{-- <a href="#">Lupa Password?</a> --}}
                                    </span>
                                </label>
                                <div class="input-group input-group-flat rounded-4 border">
                                    <input wire:model="password" type="password" id="password"
                                        class="form-control rounded-4 border-0 @error('password') is-invalid @enderror"
                                        placeholder="Masukkan Password" autocomplete="off">
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
                            <div class="form-footer">
                                <button wire:click="login" wire:loading.attr="disabled" type="submit"
                                    class="btn btn-primary w-100 rounded-4">
                                    <span wire:loading.remove wire:target="login">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-login-2">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                                            <path d="M3 12h13l-3 -3" />
                                            <path d="M13 15l3 -3" />
                                        </svg>
                                        MASUK
                                    </span>
                                    <span wire:loading wire:target="login">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Loading...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center text-secondary mt-3">
                    Belum Punya Akun? <a wire:navigate href="{{ route('register') }}" tabindex="-1">Daftar</a>
                </div>
            </div>
        </div>
        <div class="col-lg d-none d-lg-block">
            <div id="carousel-captions" class="carousel slide rounded-4 shadow-sm border-0" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100 rounded-4" alt=""
                            src="{{ asset('theme/static/illustrations/jws.webp') }}" />
                        <div class="carousel-caption-background d-none d-md-block rounded-4"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Jadwal Sholat</h3>
                            <p>Dengan tampilan yang responsif dan real-time.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100 rounded-4" alt=""
                            src="{{ asset('theme/static/illustrations/adzan.webp') }}" />
                        <div class="carousel-caption-background d-none d-md-block rounded-4"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Adzan</h3>
                            <p>Adzan yang terintegrasi dengan jadwal sholat.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100 rounded-4" alt=""
                            src="{{ asset('theme/static/illustrations/iqomah.webp') }}" />
                        <div class="carousel-caption-background d-none d-md-block rounded-4"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Iqomah</h3>
                            <p>Fitur Iqomah yang terintegrasi dengan jadwal sholat.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100 rounded-4" alt=""
                            src="{{ asset('theme/static/illustrations/after-iqomah.webp') }}" />
                        <div class="carousel-caption-background d-none d-md-block rounded-4"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Informatif</h3>
                            <p>Menampilkan informasi yang penting dan informatif.</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100 rounded-4" alt=""
                            src="{{ asset('theme/static/illustrations/jumat.webp') }}" />
                        <div class="carousel-caption-background d-none d-md-block rounded-4"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Juma'at</h3>
                            <p>Dengan penanganan khusus untuk waktu sholat juma'at.</p>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" data-bs-target="#carousel-captions" role="button"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next" data-bs-target="#carousel-captions" role="button"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </a>
            </div>
        </div>
    </div>


</div>
