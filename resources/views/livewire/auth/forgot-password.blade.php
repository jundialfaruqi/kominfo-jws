<div class="card card-md shadow-sm rounded-4 border-0">
    <div class="card-body">
        <h2 class="h2 text-center mb-1">Lupa Password</h2>
        <div class="text-muted small text-center mb-5">Masukkan email Anda untuk mendapatkan link reset password.</div>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="sendResetLink" autocomplete="off" novalidate>
            <div class="mb-3">
                <label class="form-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                        <path d="M3 7l9 6l9 -6" />
                    </svg>
                    Email
                </label>
                <input wire:model="email" type="email"
                    class="form-control rounded-3 @error('email') is-invalid @enderror"
                    placeholder="Masukkan Alamat Email" autocomplete="off">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-footer">
                <button wire:click="sendResetLink" wire:loading.attr="disabled" type="submit"
                    class="btn btn-primary w-100 rounded-3">
                    <span wire:loading.remove wire:target="sendResetLink" class="fw-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 14l11 -11" />
                            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                        </svg>
                        KIRIM LINK RESET
                    </span>
                    <span wire:loading wire:target="sendResetLink">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </span>
                </button>
            </div>
        </form>
    </div>
    <div class="hr-text">atau</div>
    <div class="card-footer bg-transparent border-0 text-center pb-4">
        <a wire:navigate href="{{ route('login') }}" class="text-decoration-none">Kembali ke Login</a>
    </div>
</div>
