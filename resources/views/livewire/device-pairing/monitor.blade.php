<div>
    <div class="page-body">
        <div class="container-xl">

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible rounded-3" role="alert">
                    <div class="d-flex">
                        <div>
                            <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
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

            <div class="card rounded-4 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar TV Terhubung</h3>
                    <div class="w-25">
                        <input type="text" class="form-control rounded-3" placeholder="Cari kode/masjid..."
                            wire:model.live="search">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Device ID</th>
                                <th>Kode Aktivasi</th>
                                <th>Perangkat</th>
                                <th>Profil Masjid</th>
                                <th>Tanggal Taut</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($devices as $device)
                                <tr>
                                    <td>
                                        <span class="text-secondary">{{ Str::limit($device->device_id, 15) }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-blue text-blue-fg fw-bold">{{ $device->pairing_code }}</span>
                                    </td>
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
                                        @if ($device->profil)
                                            <div class="fw-bold">{{ $device->profil->name }}</div>
                                            <div class="text-secondary small">{{ $device->profil->address }}</div>
                                        @else
                                            <span class="text-danger">Profil Tidak Ditemukan</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $device->updated_at->format('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger rounded-3"
                                            wire:click="unlinkDevice({{ $device->id }})"
                                            wire:confirm="Apakah Anda yakin ingin memutus TV ini dari masjid tersebut?">
                                            Putuskan TV
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-secondary">
                                        Tidak ada TV yang sedang terhubung.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($devices->hasPages())
                    <div class="card-footer d-flex align-items-center border-top-0">
                        {{ $devices->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
