<div class="page-body">
    <div class="container-xl"
        x-data="statistikMasjidApp(@js($initialDataset), @js($initialDate))"
        x-init="init()">

        {{-- 1. Card Horizontal Statistik Utama (3 Kolom, Tanpa Icon, Tanpa Warna Berlebihan, Tanpa Shadow) --}}
        <div class="card border shadow-none rounded-3 mb-3">
            <div class="row g-0">
                <div class="col-12 col-md-4 p-4 border-bottom border-md-bottom-0 border-md-end">
                    <div class="text-secondary small text-uppercase fw-medium mb-1">
                        Total Masjid &amp; OPD
                    </div>
                    <div class="h1 mb-0 fw-bold">
                        {{ number_format($totalMasjidDanOpd, 0, ',', '.') }}
                    </div>
                </div>
                <div class="col-12 col-md-4 p-4 border-bottom border-md-bottom-0 border-md-end">
                    <div class="text-secondary small text-uppercase fw-medium mb-1">
                        Total Masjid
                    </div>
                    <div class="h1 mb-0 fw-bold">
                        {{ number_format($totalMasjid, 0, ',', '.') }}
                    </div>
                </div>
                <div class="col-12 col-md-4 p-4">
                    <div class="text-secondary small text-uppercase fw-medium mb-1">
                        Total OPD
                    </div>
                    <div class="h1 mb-0 fw-bold">
                        {{ number_format($totalOpd, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Card Horizontal Chart Aktivitas Masjid Bulanan (ApexCharts) --}}
        <div class="card border shadow-none rounded-3 mb-3">
            <div class="card-header border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h3 class="card-title mb-0 fw-bold">
                        Aktivitas Masjid Bulanan
                    </h3>
                    <div class="text-secondary small mt-1">
                        Periode <span class="fw-medium" x-text="monthLabel"></span>
                        &mdash; Total <span class="fw-medium" x-text="totalMonthlyActivities"></span> aktivitas
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span x-show="isLoadingMonth" class="text-secondary small" style="display: none;">
                        Memuat data...
                    </span>
                    <button type="button"
                        x-show="filterBySpecificDate"
                        @click="clearDateFilter()"
                        class="btn btn-sm shadow-none border"
                        style="display: none;">
                        Tampilkan 1 Bulan Penuh
                    </button>
                    <input type="date"
                        class="form-control form-control-sm shadow-none w-auto"
                        x-model="selectedDate"
                        @change="onCalendarDateChange()"
                        aria-label="Filter tanggal kalender">
                </div>
            </div>
            <div class="card-body pt-3 pb-2">
                <div wire:ignore>
                    <div x-ref="activityChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        {{-- 3. Card Tabel Daftar Aktivitas Masjid (Interaktivitas Instan di Client Browser) --}}
        <div class="card border shadow-none rounded-3">
            <div class="card-header border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h3 class="card-title mb-0 fw-bold">
                        Riwayat Aktivitas Masjid
                    </h3>
                    <div class="text-secondary small mt-1">
                        <template x-if="filterBySpecificDate">
                            <span>
                                Menampilkan aktivitas pada tanggal <strong x-text="formatDateIndo(selectedDate)"></strong>
                                (<span x-text="filteredLogs.length"></span> aktivitas)
                            </span>
                        </template>
                        <template x-if="!filterBySpecificDate">
                            <span>
                                Menampilkan seluruh aktivitas bulan <strong x-text="monthLabel"></strong>
                                (<span x-text="filteredLogs.length"></span> aktivitas)
                            </span>
                        </template>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
                    <select class="form-select form-select-sm shadow-none w-auto"
                        x-model.number="perPage"
                        @change="currentPage = 1"
                        aria-label="Jumlah baris per halaman">
                        <option value="10">10 baris</option>
                        <option value="25">25 baris</option>
                        <option value="50">50 baris</option>
                        <option value="100">100 baris</option>
                    </select>

                    <input type="search"
                        name="search_aktivitas_masjid"
                        class="form-control form-control-sm shadow-none"
                        style="min-width: 240px;"
                        placeholder="Cari masjid / aktivitas (min. 3 huruf)..."
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false"
                        x-model.debounce.400ms="searchQuery"
                        @input.debounce.400ms="currentPage = 1"
                        @search="searchQuery = $event.target.value; currentPage = 1">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 56px;">No</th>
                            <th style="width: 28%;">Nama Masjid</th>
                            <th>Keterangan Aktivitas</th>
                            <th style="width: 210px;">Waktu Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, idx) in paginatedLogs" :key="item.id">
                            <tr>
                                <td class="text-secondary" x-text="startIndex + idx + 1"></td>
                                <td class="fw-medium" x-text="item.masjid_name"></td>
                                <td x-text="item.description"></td>
                                <td class="text-secondary whitespace-nowrap">
                                    <div x-text="item.time_formatted"></div>
                                    <div class="small text-secondary" x-text="item.time_human"></div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filteredLogs.length === 0">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-secondary">
                                    Tidak ada aktivitas masjid yang tercatat pada periode atau pencarian ini.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-top d-flex flex-wrap align-items-center justify-content-between gap-2 py-2"
                x-show="filteredLogs.length > 0">
                <div class="text-secondary small">
                    Menampilkan <span class="fw-medium" x-text="startIndex + 1"></span>
                    sampai <span class="fw-medium" x-text="endIndex"></span>
                    dari <span class="fw-medium" x-text="filteredLogs.length"></span> aktivitas
                </div>

                <div class="d-flex align-items-center gap-1" x-show="totalPages > 1">
                    <button type="button"
                        class="btn btn-sm shadow-none border"
                        :disabled="currentPage <= 1"
                        @click="goToPage(currentPage - 1)">
                        Sebelumnya
                    </button>

                    <template x-for="page in visiblePages" :key="page.key">
                        <div>
                            <template x-if="page.type === 'ellipsis'">
                                <span class="px-2 text-secondary small">...</span>
                            </template>
                            <template x-if="page.type === 'page'">
                                <button type="button"
                                    class="btn btn-sm shadow-none border"
                                    :class="currentPage === page.number ? 'fw-bold bg-secondary-lt' : ''"
                                    @click="goToPage(page.number)"
                                    x-text="page.number">
                                </button>
                            </template>
                        </div>
                    </template>

                    <button type="button"
                        class="btn btn-sm shadow-none border"
                        :disabled="currentPage >= totalPages"
                        @click="goToPage(currentPage + 1)">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 768px) {
            .border-md-bottom-0 {
                border-bottom: 0 !important;
            }
            .border-md-end {
                border-right: var(--tblr-border-width, 1px) solid var(--tblr-border-color, #e6e7e9) !important;
            }
        }
    </style>

    <script>
        function statistikMasjidApp(initialDataset, defaultDate) {
            return {
                selectedDate: defaultDate || '',
                currentMonthKey: initialDataset.monthKey || '',
                monthLabel: initialDataset.monthLabel || '',
                categories: initialDataset.categories || [],
                fullDates: initialDataset.fullDates || [],
                dateLabels: initialDataset.dateLabels || [],
                seriesData: initialDataset.seriesData || [],
                totalMonthlyActivities: initialDataset.totalMonthlyActivities || 0,
                logs: initialDataset.logs || [],

                filterBySpecificDate: false,
                searchQuery: '',
                perPage: 10,
                currentPage: 1,
                isLoadingMonth: false,

                chartInstance: null,
                monthCache: {},

                init() {
                    if (this.currentMonthKey) {
                        this.monthCache[this.currentMonthKey] = initialDataset;
                    }

                    this.ensureApexCharts().then(() => {
                        this.renderChart();
                    });

                    // Bersihkan instance chart saat navigasi SPA Livewire
                    document.addEventListener('livewire:navigating', () => {
                        if (this.chartInstance) {
                            try {
                                this.chartInstance.destroy();
                            } catch (e) {}
                            this.chartInstance = null;
                        }
                    }, { once: true });
                },

                ensureApexCharts() {
                    if (typeof window.ApexCharts !== 'undefined') {
                        return Promise.resolve();
                    }

                    return new Promise((resolve) => {
                        const existingScript = document.querySelector('script[data-apexcharts-loader]');
                        if (existingScript) {
                            existingScript.addEventListener('load', () => resolve(), { once: true });
                            return;
                        }

                        const script = document.createElement('script');
                        script.src = "{{ asset('theme/dist/libs/apexcharts/dist/apexcharts.min.js') }}";
                        script.setAttribute('data-apexcharts-loader', '1');
                        script.onload = () => resolve();
                        document.head.appendChild(script);
                    });
                },

                get filteredLogs() {
                    const rawQ = (this.searchQuery || '').trim().toLowerCase();
                    const q = rawQ.length >= 3 ? rawQ : '';
                    const dateFilter = this.filterBySpecificDate ? this.selectedDate : null;

                    return this.logs.filter((item) => {
                        if (dateFilter && item.date_key !== dateFilter) {
                            return false;
                        }
                        if (!q) {
                            return true;
                        }
                        const nameMatch = (item.masjid_name || '').toLowerCase().includes(q);
                        const descMatch = (item.description || '').toLowerCase().includes(q);
                        const timeMatch = (item.time_formatted || '').toLowerCase().includes(q);
                        return nameMatch || descMatch || timeMatch;
                    });
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredLogs.length / this.perPage));
                },

                get startIndex() {
                    return (this.currentPage - 1) * this.perPage;
                },

                get endIndex() {
                    return Math.min(this.startIndex + this.perPage, this.filteredLogs.length);
                },

                get paginatedLogs() {
                    if (this.currentPage > this.totalPages) {
                        this.currentPage = 1;
                    }
                    return this.filteredLogs.slice(this.startIndex, this.startIndex + this.perPage);
                },

                get visiblePages() {
                    const total = this.totalPages;
                    const current = this.currentPage;
                    const pages = [];

                    if (total <= 7) {
                        for (let i = 1; i <= total; i++) {
                            pages.push({ type: 'page', number: i, key: 'p-' + i });
                        }
                        return pages;
                    }

                    pages.push({ type: 'page', number: 1, key: 'p-1' });

                    if (current > 3) {
                        pages.push({ type: 'ellipsis', key: 'el-start' });
                    }

                    const start = Math.max(2, current - 1);
                    const end = Math.min(total - 1, current + 1);

                    for (let i = start; i <= end; i++) {
                        pages.push({ type: 'page', number: i, key: 'p-' + i });
                    }

                    if (current < total - 2) {
                        pages.push({ type: 'ellipsis', key: 'el-end' });
                    }

                    pages.push({ type: 'page', number: total, key: 'p-' + total });
                    return pages;
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.totalPages) {
                        this.currentPage = page;
                    }
                },

                async onCalendarDateChange() {
                    if (!this.selectedDate) {
                        this.filterBySpecificDate = false;
                        this.currentPage = 1;
                        return;
                    }

                    const targetMonth = this.selectedDate.substring(0, 7);
                    this.filterBySpecificDate = true;
                    this.currentPage = 1;

                    if (targetMonth && targetMonth !== this.currentMonthKey) {
                        await this.loadMonthDataset(targetMonth);
                    }
                },

                clearDateFilter() {
                    this.filterBySpecificDate = false;
                    this.currentPage = 1;
                },

                async loadMonthDataset(yearMonth) {
                    if (this.monthCache[yearMonth]) {
                        this.applyDataset(this.monthCache[yearMonth]);
                        return;
                    }

                    this.isLoadingMonth = true;
                    try {
                        const data = await this.$wire.fetchMonthData(yearMonth);
                        if (data && data.monthKey) {
                            this.monthCache[data.monthKey] = data;
                            this.applyDataset(data);
                        }
                    } catch (e) {
                        console.error('Gagal memuat data bulan:', e);
                    } finally {
                        this.isLoadingMonth = false;
                    }
                },

                applyDataset(data) {
                    this.currentMonthKey = data.monthKey;
                    this.monthLabel = data.monthLabel;
                    this.categories = data.categories || [];
                    this.fullDates = data.fullDates || [];
                    this.dateLabels = data.dateLabels || [];
                    this.seriesData = data.seriesData || [];
                    this.totalMonthlyActivities = data.totalMonthlyActivities || 0;
                    this.logs = data.logs || [];
                    this.currentPage = 1;

                    if (this.chartInstance) {
                        const maxVal = Math.max(...this.seriesData, 0);
                        this.chartInstance.updateOptions({
                            xaxis: {
                                categories: this.categories
                            },
                            yaxis: {
                                min: 0,
                                max: maxVal < 5 ? 5 : undefined,
                                tickAmount: 5,
                                decimalsInFloat: 0,
                                labels: {
                                    formatter: (val) => Math.round(val)
                                }
                            }
                        }, false, true);

                        this.chartInstance.updateSeries([
                            {
                                name: 'Jumlah Aktivitas',
                                data: this.seriesData
                            }
                        ], true);
                    }
                },

                formatDateIndo(ymd) {
                    if (!ymd) return '-';
                    const idx = this.fullDates.indexOf(ymd);
                    if (idx !== -1 && this.dateLabels[idx]) {
                        return this.dateLabels[idx];
                    }
                    try {
                        const parts = ymd.split('-');
                        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                        return new Intl.DateTimeFormat('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        }).format(d);
                    } catch (e) {
                        return ymd;
                    }
                },

                renderChart() {
                    const el = this.$refs.activityChart;
                    if (!el || typeof window.ApexCharts === 'undefined') return;

                    if (this.chartInstance) {
                        try {
                            this.chartInstance.destroy();
                        } catch (e) {}
                    }

                    const self = this;
                    const maxVal = Math.max(...this.seriesData, 0);

                    const options = {
                        chart: {
                            type: 'bar',
                            height: 300,
                            fontFamily: 'inherit',
                            parentHeightOffset: 0,
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 250
                            },
                            events: {
                                dataPointSelection: function(event, chartContext, config) {
                                    const index = config.dataPointIndex;
                                    if (index >= 0 && self.fullDates[index]) {
                                        const clickedDate = self.fullDates[index];
                                        if (self.filterBySpecificDate && self.selectedDate === clickedDate) {
                                            self.clearDateFilter();
                                        } else {
                                            self.selectedDate = clickedDate;
                                            self.filterBySpecificDate = true;
                                            self.currentPage = 1;
                                        }
                                    }
                                }
                            }
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '55%',
                                borderRadius: 2
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#374151'],
                        series: [
                            {
                                name: 'Jumlah Aktivitas',
                                data: this.seriesData
                            }
                        ],
                        xaxis: {
                            categories: this.categories,
                            title: {
                                text: 'Tanggal',
                                style: {
                                    fontWeight: 500,
                                    fontSize: '12px'
                                }
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        },
                        yaxis: {
                            min: 0,
                            max: maxVal < 5 ? 5 : undefined,
                            tickAmount: 5,
                            decimalsInFloat: 0,
                            title: {
                                text: 'Jumlah Aktivitas',
                                style: {
                                    fontWeight: 500,
                                    fontSize: '12px'
                                }
                            },
                            labels: {
                                formatter: function(val) {
                                    return Math.round(val);
                                }
                            }
                        },
                        grid: {
                            strokeDashArray: 4,
                            padding: {
                                top: -10,
                                right: 10,
                                left: 10,
                                bottom: 0
                            }
                        },
                        tooltip: {
                            theme: 'light',
                            x: {
                                formatter: function(val, opts) {
                                    const idx = opts.dataPointIndex;
                                    return self.dateLabels[idx] || ('Tanggal ' + val);
                                }
                            },
                            y: {
                                formatter: function(val) {
                                    return val + ' aktivitas';
                                }
                            }
                        },
                        states: {
                            active: {
                                filter: {
                                    type: 'none'
                                }
                            }
                        }
                    };

                    this.chartInstance = new window.ApexCharts(el, options);
                    this.chartInstance.render();
                }
            };
        }
    </script>
</div>

