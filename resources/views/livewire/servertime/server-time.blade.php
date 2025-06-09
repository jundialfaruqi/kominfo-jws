<div x-data="serverClock({{ $serverTimestamp ?? 'null' }})" x-init="init()" class="text-lg font-mono">
    <h2 class="font-bold mb-2">Hari ini</h2>

    <template x-if="timestamp">
        <div>
            <p x-text="formattedTime + ' WIB'"></p>
            <p class="text-sm text-gray-100">
                <?php if ($apiSource === 'pekanbaru'): ?>
                Menggunakan Api Pekanbaru Super App
                <?php elseif ($apiSource === 'timeapi'): ?>
                Menggunakan waktu TimeAPI.io
                <?php else: ?>
                Sumber waktu tidak diketahui
                <?php endif; ?>
            </p>
        </div>
    </template>

    <template x-if="!timestamp">
        <p>Gagal mengambil waktu server.</p>
    </template>
</div>

<script>
    function serverClock(timestamp) {
        return {
            timestamp: timestamp,
            serverTime: null,
            formattedTime: '',
            lastUpdate: null,

            init() {
                if (!this.timestamp) return;

                this.serverTime = new Date(this.timestamp);
                this.lastUpdate = performance.now();
                this.update();

                // Gunakan requestAnimationFrame untuk pembaruan halus
                const tick = (now) => {
                    if (now - this.lastUpdate >= 1000) {
                        this.serverTime = new Date(this.serverTime.getTime() + (now - this.lastUpdate));
                        this.lastUpdate = now;
                        this.update();
                    }
                    requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            },

            update() {
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: 'Asia/Jakarta',
                    hour12: false
                };

                const formatter = new Intl.DateTimeFormat('id-ID', options);
                this.formattedTime = formatter.format(this.serverTime);
            }
        }
    }
</script>
