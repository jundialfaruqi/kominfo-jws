<div x-data="serverClock({{ $serverTimestamp ?? 'null' }})" x-init="init()" class="text-lg font-mono">
    <h2 class="font-bold mb-2">Hari ini</h2>

    <template x-if="timestamp">
        <p x-text="formattedTime + ' WIB'"></p>
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
            interval: null,

            init() {
                if (!this.timestamp) return;

                this.serverTime = new Date(this.timestamp);
                this.update();

                this.interval = setInterval(() => {
                    this.serverTime = new Date(this.serverTime.getTime() + 1000);
                    this.update();
                }, 1000);
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
