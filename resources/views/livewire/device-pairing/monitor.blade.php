<div>
    <div class="page-body">
        <div class="container-xl">


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
                                <th>Status</th>
                                <th>Device ID</th>
                                <th>Kode Aktivasi</th>
                                <th>Perangkat</th>
                                <th>Diaktifkan Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($devices->groupBy(fn($d) => $d->profil->name ?? 'Tanpa Profil Masjid') as $masjidName => $groupedDevices)
                                <tr class="table-light">
                                    <td colspan="6" class="fw-bold text-primary text-uppercase" style="background-color: #f4f6fa;">
                                        <i class="fas fa-mosque me-2"></i> Masjid: {{ $masjidName }}
                                    </td>
                                </tr>
                                @foreach($groupedDevices as $device)
                                    <tr>
                                        <td>
                                            <span id="status-badge-{{ $device->device_id }}"
                                                class="badge bg-secondary text-secondary-fg">
                                                <span class="status-dot me-1">●</span> Memeriksa...
                                            </span>
                                        </td>
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
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">
                                        Tidak ada TV yang sedang terhubung.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($devices->hasPages())
                    <div class="card-footer d-flex align-items-center border-top-0">
                        {{ $devices->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            </div>

        </div>
    </div>

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

            // ── Pusher Presence Channel: status online/offline real-time ──
            const PUSHER_KEY    = '{{ config("broadcasting.connections.reverb.key") }}';
            const PUSHER_HOST   = window.location.hostname;
            const PUSHER_PORT   = window.location.protocol === 'https:' ? 443 : {{ config("broadcasting.connections.reverb.options.port", 8080) }};
            const PUSHER_SCHEME = window.location.protocol === 'https:' ? 'https' : 'http';

            // Collect all device IDs on the page
            const deviceIds = @json($devices->pluck('device_id')->toArray());

            if (typeof Pusher !== 'undefined' && deviceIds.length > 0) {
                const pusher = new Pusher(PUSHER_KEY, {
                    wsHost: PUSHER_HOST,
                    wsPort: PUSHER_PORT,
                    wssPort: PUSHER_PORT,
                    forceTLS: PUSHER_SCHEME === 'https',
                    enabledTransports: ['ws', 'wss'],
                    cluster: 'mt1',
                    channelAuthorization: {
                        customHandler: function (params, callback) {
                            fetch('/broadcasting/auth/custom', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                body: 'socket_id=' + encodeURIComponent(params.socketId) + '&channel_name=' + encodeURIComponent(params.channelName),
                                credentials: 'include'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('HTTP error ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                callback(false, data);
                            })
                            .catch(error => {
                                callback(true, error);
                            });
                        }
                    }
                });

                function setBadge(deviceId, isOnline) {
                    const badge = document.getElementById(`status-badge-${deviceId}`);
                    if (!badge) return;
                    if (isOnline) {
                        badge.className = 'badge bg-success text-success-fg';
                        badge.innerHTML = '<span class="status-dot me-1">●</span> Online';
                    } else {
                        badge.className = 'badge bg-danger text-danger-fg';
                        badge.innerHTML = '<span class="status-dot me-1">●</span> Offline';
                    }
                }

                deviceIds.forEach(deviceId => {
                    const channel = pusher.subscribe(`presence-device-${deviceId}`);

                    channel.bind('pusher:subscription_succeeded', members => {
                        // Jika ada member lain (TV) selain admin browser ini
                        const count = members.count;
                        setBadge(deviceId, count > 1); // admin sendiri = 1
                    });

                    channel.bind('pusher:member_added', member => {
                        setBadge(deviceId, true);
                    });

                    channel.bind('pusher:member_removed', member => {
                        // Cek apakah masih ada member lain
                        const count = channel.members.count;
                        setBadge(deviceId, count > 1);
                    });
                });
            } else {
                // Pusher JS belum dimuat, set semua offline
                deviceIds.forEach(deviceId => setBadge(deviceId, false));
            }
        </script>
    @endscript
</div>
