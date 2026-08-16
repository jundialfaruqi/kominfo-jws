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
<script>
    (function() {
        const tableBody = document.querySelector('.schedule-month-table tbody');
        if (!tableBody) return;
        
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        const totalRows = rows.length;
        if (totalRows === 0) return;

        let pageSize = 10;
        let currentPage = 1;
        
        const todayIndex = rows.findIndex(row => row.classList.contains('today'));
        if (todayIndex !== -1) {
            currentPage = Math.floor(todayIndex / pageSize) + 1;
        }

        const pageSizeSelect = document.getElementById('pageSizeSelect');
        const pageInfo = document.getElementById('pageInfo');
        const paginationControls = document.getElementById('paginationControls');
        const paginationWrapper = document.getElementById('paginationWrapper');
        
        if (!pageSizeSelect || !pageInfo || !paginationControls || !paginationWrapper) return;
        
        paginationWrapper.style.display = 'flex';

        function renderTable() {
            const isAll = pageSizeSelect.value === 'all';
            const currentSize = isAll ? totalRows : pageSize;
            const totalPages = isAll ? 1 : Math.ceil(totalRows / currentSize);
            
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIdx = (currentPage - 1) * currentSize;
            const endIdx = startIdx + currentSize;

            rows.forEach((row, index) => {
                if (isAll || (index >= startIdx && index < endIdx)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            const displayStart = startIdx + 1;
            const displayEnd = isAll ? totalRows : Math.min(endIdx, totalRows);
            pageInfo.textContent = `Menampilkan ${displayStart}-${displayEnd} dari ${totalRows} data`;

            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            paginationControls.innerHTML = '';
            if (totalPages <= 1) return;

            const container = document.createElement('div');
            container.className = 'apple-pagination';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'apple-page-btn';
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; renderTable(); } };
            container.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `apple-page-btn ${i === currentPage ? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.onclick = () => { currentPage = i; renderTable(); };
                container.appendChild(pageBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'apple-page-btn';
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; renderTable(); } };
            container.appendChild(nextBtn);

            paginationControls.appendChild(container);
        }

        pageSizeSelect.addEventListener('change', function() {
            if (this.value === 'all') {
                currentPage = 1;
            } else {
                pageSize = parseInt(this.value);
                if (todayIndex !== -1) {
                    currentPage = Math.floor(todayIndex / pageSize) + 1;
                } else {
                    currentPage = 1;
                }
            }
            renderTable();
        });

        renderTable();
    })();
</script>
