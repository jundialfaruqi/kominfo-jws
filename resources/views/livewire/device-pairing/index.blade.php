<div>
    <div class="page-body">
        <div class="container-xl">

            <!-- Form Card -->
            <div class="card rounded-4 shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="mb-4">Kode Aktivasi TV</h2>
                    <p class="text-secondary mb-4">
                        Masukkan 6 digit kode yang tampil di layar TV untuk menyambungkan TV dengan profil masjid Anda
                        secara instan.
                    </p>


                    <form wire:submit.prevent="linkDevice">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <div class="form-label">Masukkan Kode 6 Digit</div>
                                <input type="text" wire:model="pairingCode" placeholder="Contoh: X7B9KL"
                                    class="form-control rounded-3 text-uppercase" maxlength="6" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary rounded-3 w-100"
                                    wire:loading.attr="disabled">
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
        </script>
    @endscript
</div>
