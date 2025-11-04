<div class="bg-tranparent rounded-3" x-data="serverClock({{ $serverTimestamp ?? 'null' }})" x-init="init()">
    <template x-if="timestamp">
        <div class="flex flex-col items-start">
            {{-- hijriah --}}
            <p class="text-sm text-white font-semibold mb-1">
                <span x-text="formattedHijri + ' /'"></span>
            </p>
            <p x-text="formattedTime + ' WIB'" class="text-3xl font-medium text-gray-800 tracking-wide mb-0"></p>
            <i class="text-sm text-white font-semibold">
                <?php if ($apiSource === 'server'): ?>
                Sumber: <span class="font-medium text-gray-200">Server Lokal</span>
                <?php elseif ($apiSource === 'pekanbaru'): ?>
                Sumber: <span class="font-medium text-gray-200">Api Pekanbaru Super App</span>
                <?php elseif ($apiSource === 'timeapi'): ?>
                Sumber: <span class="font-medium text-gray-200">TimeAPI.io</span>
                <?php elseif ($apiSource === 'google-script'): ?>
                Sumber: <span class="font-medium text-gray-200">Google Script API</span>
                <?php else: ?>
                Gagal menampilkan waktu, mengganti dengan waktu lokal
                <a href="javascript:void(0)" @click="window.location.reload()"
                    class="text-blue-400 hover:text-blue-300 font-medium">coba lagi</a>
                <?php endif; ?>
            </i>
        </div>
    </template>
</div>

<script>
    function serverClock(timestamp) {
        return {
            timestamp: timestamp,
            serverTime: null,
            formattedTime: '',
            formattedHijri: '',
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

                // Tanggal Hijriah menggunakan kalender Islam (umalqura jika tersedia)
                const hijriOptions = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    timeZone: 'Asia/Jakarta'
                };
                // Coba umalqura terlebih dahulu, fallback ke islamic standar
                let hijriLocale = 'id-ID-u-ca-islamic-umalqura';
                try {
                    this.formattedHijri = new Intl.DateTimeFormat(hijriLocale, hijriOptions).format(this.serverTime);
                } catch (e) {
                    hijriLocale = 'id-ID-u-ca-islamic';
                    this.formattedHijri = new Intl.DateTimeFormat(hijriLocale, hijriOptions).format(this.serverTime);
                }
            }
        }
    }
</script>
