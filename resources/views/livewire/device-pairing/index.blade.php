<div>
    <div class="page-body">
        <div class="container-xl">
            <!-- Banner Header -->
            <div class="card mb-3 rounded-4 border-0 overflow-hidden shadow-sm">
                <div class="card-body"
                    style="background: linear-gradient(90deg, #0ea5a3 0%, #1f7ae0 60%, #3b82f6 100%); color: #fff;">
                    <div class="row align-items-center">
                        <div class="col-12 mb-3 mb-md-0">
                            <h1 class="mb-1">Tautkan TV Masjid</h1>
                            <div class="text-white" style="opacity:.9;">
                                Manajemen kode aktivasi (Device Pairing) untuk menyambungkan TV layar masjid dengan profil admin.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card rounded-4 shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="mb-4">Kode Aktivasi TV</h2>
                    <p class="text-secondary mb-4">
                        Masukkan 6 digit kode yang tampil di layar TV untuk menyambungkan TV dengan profil masjid Anda secara instan.
                    </p>

                    @if ($message)
                        <div class="alert {{ $messageType === 'success' ? 'alert-success' : 'alert-danger' }} rounded-3" role="alert">
                            {{ $message }}
                        </div>
                    @endif

                    <form wire:submit.prevent="linkDevice">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <div class="form-label">Masukkan Kode 6 Digit</div>
                                <input 
                                    type="text" 
                                    wire:model="pairingCode" 
                                    placeholder="Contoh: X7B9KL" 
                                    class="form-control rounded-3 text-uppercase"
                                    maxlength="6"
                                    required
                                >
                            </div>
                            <div class="col-md-3">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary rounded-3 w-100"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove wire:target="linkDevice">
                                        Tautkan TV
                                    </span>
                                    <span wire:loading wire:target="linkDevice">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Menautkan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
