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

                        <tbody>
                            @forelse ($devices->groupBy(fn($d) => $d->profil->name ?? 'Tanpa Profil Masjid') as $masjidName => $groupedDevices)
                                <tr class="table-light">
                                    <td colspan="6" class="text-primary" style="background-color: #f4f6fa;">
                                        <div class="d-flex flex-column">
                                            <div class="fw-bold text-uppercase">
                                                Masjid: {{ $masjidName }}
                                            </div>
                                            @if ($groupedDevices->first()->profil && $groupedDevices->first()->profil->address)
                                                <div class="text-secondary small mt-1" style="font-size: 0.75rem;">
                                                    {{ $groupedDevices->first()->profil->address }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr class="text-muted small" style="background-color: #fcfcfc;">
                                    <th class="fw-bold">Status</th>
                                    <th class="fw-bold">Device ID</th>
                                    <th class="fw-bold">Kode Aktivasi</th>
                                    <th class="fw-bold">Perangkat</th>
                                    <th class="fw-bold">Diaktifkan Pada</th>
                                    <th class="fw-bold">Aksi</th>
                                </tr>
                                @foreach ($groupedDevices as $device)
                                    <tr wire:key="device-row-{{ $device->id }}">
                                        <td>
                                            <span id="status-badge-{{ $device->device_id }}"
                                                data-device-id="{{ $device->device_id }}"
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

            // ── Pusher Presence Channel: status online/offline real-time (mendukung pindah halaman & pencarian) ──
            const PUSHER_KEY = '{{ config('broadcasting.connections.reverb.key') }}';
            const PUSHER_HOST = window.location.hostname;
            const PUSHER_PORT = window.location.protocol === 'https:' ? 443 :
                {{ config('broadcasting.connections.reverb.options.port', 8080) }};
            const PUSHER_SCHEME = window.location.protocol === 'https:' ? 'https' : 'http';

            function setBadge(deviceId, isOnline) {
                const badge = document.getElementById(`status-badge-${deviceId}`);
                if (!badge) return;

                const targetState = isOnline ? 'online' : 'offline';
                if (badge.getAttribute('data-status-state') === targetState) return;

                badge.setAttribute('data-status-state', targetState);
                if (isOnline) {
                    badge.className = 'badge bg-success text-success-fg';
                    badge.innerHTML = '<span class="status-dot me-1">●</span> Online';
                } else {
                    badge.className = 'badge bg-danger text-danger-fg';
                    badge.innerHTML = '<span class="status-dot me-1">●</span> Offline';
                }
            }

            let pusherInstance = null;
            const subscribedChannels = new Map();

            if (typeof Pusher !== 'undefined') {
                pusherInstance = new Pusher(PUSHER_KEY, {
                    wsHost: PUSHER_HOST,
                    wsPort: PUSHER_PORT,
                    wssPort: PUSHER_PORT,
                    forceTLS: PUSHER_SCHEME === 'https',
                    enabledTransports: ['ws', 'wss'],
                    cluster: 'mt1',
                    channelAuthorization: {
                        customHandler: function(params, callback) {
                            fetch('/broadcasting/auth/custom', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            ?.getAttribute('content') || ''
                                    },
                                    body: 'socket_id=' + encodeURIComponent(params.socketId) +
                                        '&channel_name=' + encodeURIComponent(params.channelName),
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
            }

            function syncDevicePresenceSubscriptions() {
                const badges = document.querySelectorAll('[data-device-id]');
                const currentDeviceIds = Array.from(badges)
                    .map(el => el.getAttribute('data-device-id'))
                    .filter(Boolean);

                if (currentDeviceIds.length === 0) return;

                if (!pusherInstance) {
                    currentDeviceIds.forEach(deviceId => setBadge(deviceId, false));
                    return;
                }

                currentDeviceIds.forEach(deviceId => {
                    // Jika channel sudah pernah di-subscribe (mis. kembali ke halaman sebelumnya),
                    // langsung terapkan status terakhir dari daftar member channel
                    if (subscribedChannels.has(deviceId)) {
                        const existingChannel = subscribedChannels.get(deviceId);
                        if (existingChannel && existingChannel.subscribed && existingChannel.members) {
                            setBadge(deviceId, existingChannel.members.count > 1);
                        }
                        return;
                    }

                    // Subscribe ke channel untuk deviceId baru di halaman aktif
                    const channel = pusherInstance.subscribe(`presence-device-${deviceId}`);
                    subscribedChannels.set(deviceId, channel);

                    channel.bind('pusher:subscription_succeeded', members => {
                        const count = members.count;
                        setBadge(deviceId, count > 1); // admin sendiri = 1
                    });

                    channel.bind('pusher:member_added', () => {
                        setBadge(deviceId, true);
                    });

                    channel.bind('pusher:member_removed', () => {
                        const count = channel.members ? channel.members.count : 0;
                        setBadge(deviceId, count > 1);
                    });

                    channel.bind('pusher:subscription_error', () => {
                        setBadge(deviceId, false);
                    });
                });
            }

            // Jalankan saat halaman pertama kali dimuat
            syncDevicePresenceSubscriptions();

            // Jalankan ulang setiap kali DOM tabel berubah karena pindah halaman pagination atau pencarian Livewire
            const tableContainer = document.querySelector('.table-responsive');
            if (tableContainer) {
                const observer = new MutationObserver(() => {
                    syncDevicePresenceSubscriptions();
                });
                observer.observe(tableContainer, {
                    childList: true,
                    subtree: true
                });
            }

            if (typeof Livewire !== 'undefined' && typeof Livewire.hook === 'function') {
                Livewire.hook('morph.updated', () => {
                    syncDevicePresenceSubscriptions();
                });
            }
        </script>
    @endscript
</div>
