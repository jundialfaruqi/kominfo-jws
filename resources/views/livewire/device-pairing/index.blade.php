<div>
    <div class="page-body">
        <div class="container-xl">

            <!-- Form Card -->
            <div class="card rounded-4 bg-transparent border-0 my-md-4">
                <div class="card-body p-0">
                    <div class="row justify-content-center">
                        {{-- <div class="col-md-10 col-lg-4"> --}}
                        <p class="text-right text-secondary mb-4">
                            Masukkan 6 digit kode yang tampil di layar TV untuk menyambungkan TV dengan profil
                            masjid Anda
                            secara instan.
                        </p>

                        <form wire:submit.prevent="linkDevice">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-8">
                                    <div class="form-label">Masukkan Kode 6 Digit</div>
                                    <input type="text" wire:model="pairingCode" placeholder="Contoh: X7B9KL"
                                        class="form-control rounded-3 text-uppercase" maxlength="6" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary rounded-3 w-100"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="linkDevice">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-link" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 15l6 -6" />
                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" />
                                                <path
                                                    d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" />
                                            </svg>
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

                        <div class="hr-text my-4">Atau Scan QR Otomatis</div>

                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary rounded-3 w-100"
                                    data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-qrcode"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path d="M7 17l0 .01" />
                                        <path
                                            d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path d="M7 7l0 .01" />
                                        <path
                                            d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path d="M17 7l0 .01" />
                                        <path d="M14 14l3 0" />
                                        <path d="M17 14l0 3" />
                                        <path d="M14 14l0 3" />
                                        <path d="M14 17l3 0" />
                                        <path d="M17 17l3 0" />
                                        <path d="M20 14l0 3" />
                                    </svg>
                                    Scan QR Code TV
                                </button>
                            </div>
                        </div>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>

            {{-- Tabel TV Terhubung --}}
            <div class="card rounded-4 shadow-sm border-0 mt-4">
                <div class="card-header">
                    <h3 class="card-title">TV yang Terhubung</h3>
                    <div class="card-options text-secondary small">
                        Total: {{ $devices->count() }} TV
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th>Perangkat</th>
                                <th>Kode Aktivasi</th>
                                <th>Tanggal Taut</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($devices as $device)
                                <tr>
                                    <td>
                                        @if ($device->device_brand || $device->device_model)
                                            <div class="fw-bold text-capitalize">{{ $device->device_brand }}
                                                {{ $device->device_model }}</div>
                                            <div class="text-secondary small">{{ $device->os_version ?? '-' }}</div>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-blue text-blue-fg fw-bold">{{ $device->pairing_code }}</span>
                                    </td>
                                    <td>
                                        {{ $device->updated_at->format('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger rounded-3"
                                            wire:click="unlinkDevice({{ $device->id }})"
                                            wire:confirm="Apakah Anda yakin ingin memutus TV ini?">
                                            Putuskan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-secondary">
                                        Belum ada TV yang terhubung ke masjid Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="modal modal-blur fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scan QR Code dari TV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <div id="qr-reader" style="width: 100%;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    @script
        <script>
            $wire.on('success', event => {
                let msg = Array.isArray(event) ? (event[0].message || event[0]) : (event.message || event);
                iziToast.success({
                    title: 'Berhasil',
                    message: msg,
                    position: 'topRight'
                });
            });

            $wire.on('error', event => {
                let msg = Array.isArray(event) ? (event[0].message || event[0]) : (event.message || event);
                iziToast.error({
                    title: 'Gagal',
                    message: msg,
                    position: 'topRight'
                });
            });

            // Logika QR Scanner
            let html5QrcodeScanner = null;
            const modal = document.getElementById('qrScannerModal');

            modal.addEventListener('shown.bs.modal', function() {
                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader", {
                            fps: 10,
                            qrbox: {
                                width: 250,
                                height: 250
                            }
                        }, /* verbose= */ false);
                }
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            });

            modal.addEventListener('hidden.bs.modal', function() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                }
            });

            function onScanSuccess(decodedText, decodedResult) {
                html5QrcodeScanner.clear();

                let modalInstance = bootstrap.Modal.getInstance(modal);
                modalInstance.hide();

                $wire.set('pairingCode', decodedText);
                $wire.linkDevice();
            }

            function onScanFailure(error) {
                // Abaikan error saat proses pemindaian berlangsung
            }
        </script>
    @endscript
</div>
