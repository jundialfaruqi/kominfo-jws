<div>
    <div class="row min-vh-100 g-0">
        <div class="col-12 col-lg-6 col-xl-4 d-flex flex-column justify-content-center align-items-center">
            <div class="w-100 px-4" style="max-width: 100%; width: 100%;">
                <div class="text-center">
                    {{-- <a href="." class="navbar-brand navbar-brand-autodark"><img
                            src="{{ asset('theme/static/logo.png') }}" height="150" alt="Logo"></a> --}}
                </div>
                <h1 class="h1 text-center mb-3 px-3">
                    JWS Login
                </h1>
                <div class="text-center small text-muted mb-5">
                    {{-- Jl. Abdul Rahman Hamid, Bencah Lesung, Kec. Tenayan Raya, Pekanbaru, Riau --}}
                </div>
                <form wire:submit="login" autocomplete="off" novalidate>
                    <div class="mb-3 px-3">
                        <label class="form-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                <path d="M3 7l9 6l9 -6" />
                            </svg>
                            Alamat Email
                        </label>
                        <input wire:model="email" type="email"
                            class="form-control rounded-4 @error('email') is-invalid @enderror"
                            placeholder="Masukkan Alamat Email" autocomplete="off">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="px-3">
                        <label class="form-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-lock-open">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                <path d="M8 11v-5a4 4 0 0 1 8 0" />
                            </svg>
                            Password
                        </label>
                        <div class="mb-3">
                            <input wire:model="password" id="password-field" type="password"
                                class="form-control rounded-4 @error('password') is-invalid @enderror"
                                placeholder="Masukkan Password" autocomplete="off">
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-footer px-3 text-end">
                        <button wire:click="login" wire:loading.attr="disabled" type="submit"
                            class="btn btn-primary py-2 rounded-4">
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
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-8 d-none d-lg-block">
            {{-- Photo --}}
            <div class="bg-cover h-100 min-vh-100"
                style="background-image: url({{ asset('theme/static/photos/login-cover.jpg') }})">
            </div>
        </div>
    </div>
</div>
