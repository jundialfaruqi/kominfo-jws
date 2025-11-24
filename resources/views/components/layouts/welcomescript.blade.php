<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        const el = document.getElementById('countdown');
        if (!el) return;
        const targetIso = el.getAttribute('data-next-iso');
        const target = new Date(targetIso).getTime();
        const textEl = document.getElementById('countdown-text');

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function tick() {
            const now = Date.now();
            let diff = Math.max(0, Math.floor((target - now) / 1000));
            const h = Math.floor(diff / 3600);
            diff %= 3600;
            const m = Math.floor(diff / 60);
            const s = diff % 60;
            textEl.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
        }
        tick();
        const timer = setInterval(function() {
            tick();
            if (Date.now() >= target) {
                clearInterval(timer);
            }
        }, 1000);
    })();
</script>
<script>
    (function() {
        const t = document.getElementById('current-time');
        if (!t) return;
        const fmt = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta'
        });

        function render() {
            const parts = fmt.formatToParts(new Date());
            const hh = parts.find(p => p.type === 'hour')?.value || '00';
            const mm = parts.find(p => p.type === 'minute')?.value || '00';
            const ss = parts.find(p => p.type === 'second')?.value || '00';
            t.textContent = hh + ':' + mm + ':' + ss;
        }
        render();
        setInterval(render, 1000);
    })();
</script>
