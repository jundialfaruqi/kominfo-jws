{{-- table --}}
@if ($showTable)
    <div class="card-body">
        {{-- <div class="row g-2 align-items-center mb-3">
            <div class="col-auto">
                <div class="input-icon">
                    <input wire:model.live="search" type="text" class="form-control rounded-3"
                        placeholder="Cari laporan atau nama profil masjid...">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                    </span>
                </div>
            </div>
        </div> --}}

        {{-- Tabel per kategori --}}
        @php
            $isAdmin = Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']);
        @endphp
        @if ($isAdmin)
            {{-- Admin: pilih profil masjid terlebih dahulu sebelum menampilkan data --}}
            <div class="card mb-3 rounded-3">
                <div class="card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label">Pilih Profil Masjid</label>
                            <select wire:model.live="idMasjid" class="form-select rounded-3">
                                <option value="">-- Pilih Masjid --</option>
                                @foreach ($profils as $profil)
                                    <option value="{{ $profil->id }}">{{ $profil->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-8">
                            <label class="form-label">Filter Tanggal:</label>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <select wire:model.live="filterDateMode" class="form-select rounded-3"
                                    style="width: auto;">
                                    <option value="semua">Semua</option>
                                    <option value="hari">Hari</option>
                                    <option value="bulan">Bulan</option>
                                    <option value="tahun">Tahun</option>
                                    <option value="rentang">Rentang</option>
                                    <option value="7hari">7 Hari (terakhir)</option>
                                </select>
                                @if ($filterDateMode === 'hari')
                                    <input type="date" wire:model.live="filterDay" class="form-control rounded-3"
                                        style="width: auto;">
                                @elseif ($filterDateMode === 'bulan')
                                    {{-- Ubah ke dropdown bulan & tahun agar tidak perlu ketik --}}
                                    <select wire:model.live="filterMonthSelect" class="form-select rounded-3"
                                        style="width: auto;">
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                    <select wire:model.live="filterMonthYearSelect" class="form-select rounded-3"
                                        style="width: auto;">
                                        @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                @elseif ($filterDateMode === 'tahun')
                                    <input type="number" wire:model.live="filterYear" min="2000"
                                        max="{{ now()->year + 5 }}" class="form-control rounded-3" style="width: 8rem;"
                                        placeholder="YYYY">
                                @elseif ($filterDateMode === 'rentang')
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <input type="date" wire:model.live="filterStartDate"
                                            class="form-control rounded-3" style="width: auto;"
                                            placeholder="Tanggal awal">
                                        <span class="mx-1">s.d.</span>
                                        <input type="date" wire:model.live="filterEndDate"
                                            class="form-control rounded-3" style="width: auto;"
                                            placeholder="Tanggal akhir">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (!$idMasjid)
                <div class="alert alert-info rounded-3">Silakan pilih Profil Masjid untuk menampilkan data laporan
                    keuangan.</div>
            @else
                {{-- Admin: kelompok per masjid (terpilih) lalu per kategori, tiap kategori tabel sendiri dengan card penuh --}}
                @php
                    // computedGroups berisi tepat satu masjid yang dipilih
                @endphp
                @forelse ($computedGroups as $group)
                    <div class="mb-3">
                        <div class="fw-bold bg-primary-subtle px-3 py-2 rounded-3 text-center fs-3">Ringkasan
                            Laporan Keuangan
                            {{ $group['masjidName'] ?? 'Tidak diketahui' }}
                        </div>
                    </div>
                    {{-- per kategori dan total keseluruhan per profil (id_masjid) --}}
                    <div class="card mb-3 rounded-3 shadow-sm">
                        <div class="card-body">
                            @php
                                $filterLabel = 'Semua';
                                try {
                                    if ($filterDateMode === 'hari' && !empty($filterDay)) {
                                        $filterLabel = \Carbon\Carbon::createFromFormat(
                                            'Y-m-d',
                                            $filterDay,
                                        )->translatedFormat('d F Y');
                                    } elseif ($filterDateMode === 'bulan' && !empty($filterMonth)) {
                                        $filterLabel = \Carbon\Carbon::createFromFormat(
                                            'Y-m',
                                            $filterMonth,
                                        )->translatedFormat('F Y');
                                    } elseif ($filterDateMode === 'tahun' && !empty($filterYear)) {
                                        $filterLabel = $filterYear;
                                    } elseif (
                                        $filterDateMode === 'rentang' &&
                                        !empty($filterStartDate) &&
                                        !empty($filterEndDate)
                                    ) {
                                        $filterLabel =
                                            \Carbon\Carbon::createFromFormat(
                                                'Y-m-d',
                                                $filterStartDate,
                                            )->translatedFormat('d F Y') .
                                            ' s.d. ' .
                                            \Carbon\Carbon::createFromFormat('Y-m-d', $filterEndDate)->translatedFormat(
                                                'd F Y',
                                            );
                                    } elseif ($filterDateMode === '7hari') {
                                        $filterLabel =
                                            \Carbon\Carbon::today('Asia/Jakarta')
                                                ->subDays(6)
                                                ->translatedFormat('d F Y') .
                                            ' s.d. ' .
                                            \Carbon\Carbon::today('Asia/Jakarta')->translatedFormat('d F Y');
                                    } elseif ($filterDateMode === 'semua') {
                                        $filterLabel = 'Semua';
                                    }
                                } catch (\Exception $e) {
                                    // fallback ke raw value jika format gagal
                                    if ($filterDateMode === 'hari') {
                                        $filterLabel = $filterDay ?? 'Semua';
                                    } elseif ($filterDateMode === 'bulan') {
                                        $filterLabel = $filterMonth ?? 'Semua';
                                    } elseif ($filterDateMode === 'tahun') {
                                        $filterLabel = $filterYear ?? 'Semua';
                                    } elseif ($filterDateMode === 'rentang') {
                                        $filterLabel = ($filterStartDate ?? '-') . ' s.d. ' . ($filterEndDate ?? '-');
                                    } elseif ($filterDateMode === '7hari') {
                                        $filterLabel = '7 hari terakhir';
                                    }
                                }
                            @endphp
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div class="fw-semibold">
                                    Ringkasan per Kategori
                                    <span class="badge badge-small bg-primary text-white">{{ $filterLabel }}</span>
                                </div>
                                <div class="text-muted small">Profil: {{ $group['masjidName'] ?? '-' }}</div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama Kategori</th>
                                            <th class="text-end">Total Masuk</th>
                                            <th class="text-end">Total Keluar</th>
                                            <th class="text-end">Sisa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (
                                            $filterDateMode === 'bulan' ||
                                                (($previousTotalsAdmin['sumMasuk'] ?? 0) !== 0 ||
                                                    ($previousTotalsAdmin['sumKeluar'] ?? 0) !== 0 ||
                                                    ($previousTotalsAdmin['ending'] ?? 0) !== 0))
                                            <tr class="table-secondary">
                                                <td><b>Saldo Sebelumnya</b> <span class="text-muted small">(s.d. akhir
                                                        bulan sebelumnya)</span></td>
                                                <td class="text-end">
                                                    <b>Rp
                                                        {{ number_format($previousTotalsAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}</b>
                                                </td>
                                                <td class="text-end">
                                                    <b>Rp
                                                        {{ number_format($previousTotalsAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}</b>
                                                </td>
                                                <td class="text-end">
                                                    <b>Rp
                                                        {{ number_format($previousTotalsAdmin['ending'] ?? 0, 0, ',', '.') }}</b>
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach ($summaryCategoriesAdmin ?? [] as $sum)
                                            <tr>
                                                <td>{{ $sum['categoryName'] }}</td>
                                                <td class="text-end">
                                                    Rp
                                                    {{ number_format($sum['sumMasuk'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-end">
                                                    Rp
                                                    {{ number_format($sum['sumKeluar'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-end">
                                                    Rp
                                                    {{ number_format($sum['ending'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-end text-uppercase">Sisa saldo akhir saat ini</th>
                                            <th class="text-end" colspan="3">
                                                Rp {{ number_format($grandTotalsAdmin['ending'] ?? 0, 0, ',', '.') }}
                                            </th>
                                        </tr>
                                        <tr class="bg-primary-lt">
                                            <th class="text-end text-uppercase">
                                                Saldo akhir
                                                {{ $filterLabel }}
                                                <span class="text-muted small">(pemasukan - pengeluaran)</span>
                                            </th>
                                            <th class="text-end">
                                                Rp {{ number_format($grandTotalsAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}
                                            </th>
                                            <th class="text-end">
                                                Rp
                                                {{ number_format($grandTotalsAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}
                                            </th>
                                            <th class="text-end">
                                                Rp
                                                {{ number_format(($grandTotalsAdmin['sumMasuk'] ?? 0) - ($grandTotalsAdmin['sumKeluar'] ?? 0), 0, ',', '.') }}
                                            </th>
                                        </tr>
                                        @if ($filterDateMode === 'bulan')
                                            <tr class="bg-secondary text-white">
                                                <th class="text-end text-uppercase">Total pemasukan, pengeluaran dan
                                                    saldo akhir saat ini</th>
                                                <th class="text-end">
                                                    Rp
                                                    {{ number_format(($grandTotalsAdmin['sumMasuk'] ?? 0) + ($previousTotalsAdmin['sumMasuk'] ?? 0), 0, ',', '.') }}
                                                </th>
                                                <th class="text-end">
                                                    Rp
                                                    {{ number_format(($grandTotalsAdmin['sumKeluar'] ?? 0) + ($previousTotalsAdmin['sumKeluar'] ?? 0), 0, ',', '.') }}
                                                </th>
                                                <th class="text-end">
                                                    Rp
                                                    {{ number_format($grandTotalsAdmin['ending'] ?? 0, 0, ',', '.') }}
                                                </th>
                                            </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @foreach ($group['categories'] as $category)
                        <div class="card mb-3 rounded-3 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="fw-bold">Kategori:
                                    {{ $category['categoryName'] }}</span>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <label class="small text-muted">Tampil</label>
                                    <select
                                        wire:model.live="paginatePerCategory.{{ $group['masjidId'] }}.{{ $category['categoryId'] }}"
                                        class="form-select form-select-sm rounded-3" style="width: auto;">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="small text-muted">/ halaman</span>
                                    <div class="ms-0 ms-md-2 mt-2 mt-md-0 w-100 w-md-auto">
                                        <div class="input-icon">
                                            <input
                                                wire:model.live="searchPerCategory.{{ $group['masjidId'] }}.{{ $category['categoryId'] }}"
                                                type="text"
                                                class="form-control form-control-sm rounded-3 py-2 w-100"
                                                placeholder="Cari uraian di kategori ini...">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20"
                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                    <path d="M21 21l-6 -6" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                {{-- Toolbar per kategori (opsional) dapat ditambahkan di sini jika diinginkan --}}
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive overflow-auto" style="max-height: 70vh;">
                                    <table class="table table-vcenter table-nowrap card-table mb-0">
                                        <thead class="sticky-top bg-body shadow-sm" style="z-index: 1;">
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Uraian</th>
                                                <th>Group Category</th>
                                                <th class="text-end">Masuk</th>
                                                <th class="text-end">Keluar</th>
                                                <th class="text-end">Sisa</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $currentSection = null; @endphp
                                            @foreach ($category['items'] as $row)
                                                @if ($row['jenis'] !== $currentSection)
                                                    @php $currentSection = $row['jenis']; @endphp
                                                    <tr class="table-info">
                                                        <td colspan="8" class="fw-bold">
                                                            {{ $currentSection === 'masuk' ? 'Transaksi Masuk' : 'Transaksi Keluar' }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td>{{ $row['no'] }}</td>
                                                    <td>{{ $row['tanggal'] }}</td>
                                                    <td>
                                                        @if ($row['is_opening'])
                                                            <span class="badge bg-primary-subtle text-primary">Saldo
                                                                Awal</span>
                                                        @endif
                                                        {{ $row['uraian'] }}
                                                    </td>
                                                    <td>{{ $row['groupCategoryName'] }}</td>
                                                    <td class="text-end">{{ $row['masukDisplay'] }}</td>
                                                    <td class="text-end">{{ $row['keluarDisplay'] }}</td>
                                                    <td class="text-end">{{ $row['runningBalanceDisplay'] }}</td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <button wire:click="edit('{{ $row['id'] }}')"
                                                                class="btn btn-outline-primary btn-sm rounded-3"
                                                                title="Ubah">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path
                                                                        d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                                    <path d="M13.5 6.5l4 4" />
                                                                </svg>
                                                            </button>
                                                            <button wire:click="delete('{{ $row['id'] }}')"
                                                                class="btn btn-outline-danger btn-sm rounded-3"
                                                                title="Hapus" data-bs-toggle="modal"
                                                                data-bs-target="#deleteModal">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M4 7h16" />
                                                                    <path d="M10 11v6" />
                                                                    <path d="M14 11v6" />
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path d="M9 7v-3h6v3" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-3 border-top-0">
                                    <div>
                                        <div class="fw-semibold">
                                            Total kategori {{ $category['categoryName'] }}
                                        </div>
                                        <div class="text-muted">Masuk:
                                            Rp {{ $category['totals']['totalMasukDisplay'] }} | Keluar:
                                            Rp {{ $category['totals']['totalKeluarDisplay'] }} | Sisa:
                                            Rp {{ $category['totals']['endingBalanceDisplay'] }}
                                        </div>
                                        <div class="text-danger small">*Tidak mengikuti filter tanggal</div>
                                    </div>
                                    <div class="ms-auto">
                                        {{ $category['paginator']->links(data: ['scrollTo' => false]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="text-center text-muted">Tidak ada data laporan keuangan.</div>
                @endforelse
            @endif
        @else
            {{-- Filter tanggal (Non-admin) --}}
            <div class="card mb-3 rounded-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="fw-semibold me-2">Filter Tanggal:</div>
                        <select wire:model.live="filterDateMode" class="form-select rounded-3" style="width: auto;">
                            <option value="semua">Semua</option>
                            <option value="hari">Hari</option>
                            <option value="bulan">Bulan</option>
                            <option value="tahun">Tahun</option>
                            <option value="rentang">Rentang</option>
                            <option value="7hari">7 Hari (terakhir)</option>
                        </select>
                        @if ($filterDateMode === 'hari')
                            <input type="date" wire:model.live="filterDay" class="form-control rounded-3"
                                style="width: auto;">
                        @elseif ($filterDateMode === 'bulan')
                            <input type="month" wire:model.live="filterMonth" class="form-control rounded-3"
                                style="width: auto;">
                        @elseif ($filterDateMode === 'tahun')
                            <input type="number" wire:model.live="filterYear" min="2000"
                                max="{{ now()->year + 5 }}" class="form-control rounded-3" style="width: 8rem;"
                                placeholder="YYYY">
                        @elseif ($filterDateMode === 'rentang')
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <input type="date" wire:model.live="filterStartDate"
                                    class="form-control rounded-3" style="width: auto;" placeholder="Tanggal awal">
                                <span class="mx-1">s.d.</span>
                                <input type="date" wire:model.live="filterEndDate" class="form-control rounded-3"
                                    style="width: auto;" placeholder="Tanggal akhir">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Ringkasan per kategori (Non-admin) --}}
            <div class="card mb-3 rounded-3 shadow-sm">
                <div class="card-body">
                    @php
                        $filterLabel = 'Semua';
                        try {
                            if ($filterDateMode === 'hari' && !empty($filterDay)) {
                                $filterLabel = \Carbon\Carbon::createFromFormat('Y-m-d', $filterDay)->translatedFormat(
                                    'd F Y',
                                );
                            } elseif ($filterDateMode === 'bulan' && !empty($filterMonth)) {
                                $filterLabel = \Carbon\Carbon::createFromFormat('Y-m', $filterMonth)->translatedFormat(
                                    'F Y',
                                );
                            } elseif ($filterDateMode === 'tahun' && !empty($filterYear)) {
                                $filterLabel = $filterYear;
                            } elseif (
                                $filterDateMode === 'rentang' &&
                                !empty($filterStartDate) &&
                                !empty($filterEndDate)
                            ) {
                                $filterLabel =
                                    \Carbon\Carbon::createFromFormat('Y-m-d', $filterStartDate)->translatedFormat(
                                        'd F Y',
                                    ) .
                                    ' s.d. ' .
                                    \Carbon\Carbon::createFromFormat('Y-m-d', $filterEndDate)->translatedFormat(
                                        'd F Y',
                                    );
                            } elseif ($filterDateMode === '7hari') {
                                $filterLabel =
                                    \Carbon\Carbon::today('Asia/Jakarta')->subDays(6)->translatedFormat('d F Y') .
                                    ' s.d. ' .
                                    \Carbon\Carbon::today('Asia/Jakarta')->translatedFormat('d F Y');
                            } elseif ($filterDateMode === 'semua') {
                                $filterLabel = 'Semua';
                            }
                        } catch (\Exception $e) {
                            // fallback ke raw value jika format gagal
                            if ($filterDateMode === 'hari') {
                                $filterLabel = $filterDay ?? 'Semua';
                            } elseif ($filterDateMode === 'bulan') {
                                $filterLabel = $filterMonth ?? 'Semua';
                            } elseif ($filterDateMode === 'tahun') {
                                $filterLabel = $filterYear ?? 'Semua';
                            } elseif ($filterDateMode === 'rentang') {
                                $filterLabel = ($filterStartDate ?? '-') . ' s.d. ' . ($filterEndDate ?? '-');
                            } elseif ($filterDateMode === '7hari') {
                                $filterLabel = '7 hari terakhir';
                            }
                        }
                    @endphp
                    <div class="fw-semibold mb-2">
                        Ringkasan per Kategori
                        <span class="badge badge-small bg-primary text-white">{{ $filterLabel }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Kategori</th>
                                    <th class="text-end">Total Masuk</th>
                                    <th class="text-end">Total Keluar</th>
                                    <th class="text-end">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (
                                    $filterDateMode === 'bulan' ||
                                        (($previousTotalsNonAdmin['sumMasuk'] ?? 0) !== 0 ||
                                            ($previousTotalsNonAdmin['sumKeluar'] ?? 0) !== 0 ||
                                            ($previousTotalsNonAdmin['ending'] ?? 0) !== 0))
                                    <tr class="table-secondary">
                                        <td><b>Saldo Sebelumnya</b> <span class="text-muted small">(s.d. akhir bulan
                                                sebelumnya)</span></td>
                                        <td class="text-end">
                                            <b>
                                                Rp
                                                {{ number_format($previousTotalsNonAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}
                                            </b>
                                        </td>
                                        <td class="text-end">
                                            <b>
                                                Rp
                                                {{ number_format($previousTotalsNonAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}
                                            </b>
                                        </td>
                                        <td class="text-end">
                                            <b>
                                                Rp
                                                {{ number_format($previousTotalsNonAdmin['ending'] ?? 0, 0, ',', '.') }}
                                            </b>
                                        </td>
                                    </tr>
                                @endif
                                @foreach ($summaryCategoriesNonAdmin ?? [] as $sum)
                                    <tr>
                                        <td>{{ $sum['categoryName'] }}</td>
                                        <td class="text-end">
                                            Rp {{ number_format($sum['sumMasuk'], 0, ',', '.') }}
                                        </td>

                                        <td class="text-end">
                                            Rp {{ number_format($sum['sumKeluar'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($sum['ending'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end text-uppercase">Sisa saldo akhir saat ini
                                    </th>
                                    <th colspan="3" class="text-end">
                                        Rp {{ number_format($grandTotalsNonAdmin['ending'] ?? 0, 0, ',', '.') }}
                                    </th>
                                </tr>
                                <tr class="bg-primary-lt text-white">
                                    <th class="text-end text-uppercase">Saldo Akhir {{ $filterLabel }}
                                        <span class="text-muted small">(Pemasukan - Pengeluaran)</span>
                                    </th>
                                    <th class="text-end">
                                        Rp {{ number_format($grandTotalsNonAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}
                                    </th>
                                    <th class="text-end">
                                        Rp {{ number_format($grandTotalsNonAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}
                                    </th>
                                    <th class="text-end">
                                        Rp
                                        {{ number_format(($grandTotalsNonAdmin['sumMasuk'] ?? 0) - ($grandTotalsNonAdmin['sumKeluar'] ?? 0), 0, ',', '.') }}
                                    </th>
                                </tr>
                                @if ($filterDateMode === 'bulan')
                                    <tr class="bg-secondary text-white">
                                        <th class="text-end text-uppercase">Total pemasukan, pengeluaran dan saldo
                                            akhir saat ini</th>
                                        <th class="text-end">
                                            Rp
                                            {{ number_format(($grandTotalsNonAdmin['sumMasuk'] ?? 0) + ($previousTotalsNonAdmin['sumMasuk'] ?? 0), 0, ',', '.') }}
                                        </th>
                                        <th class="text-end">
                                            Rp
                                            {{ number_format(($grandTotalsNonAdmin['sumKeluar'] ?? 0) + ($previousTotalsNonAdmin['sumKeluar'] ?? 0), 0, ',', '.') }}
                                        </th>
                                        <th class="text-end">
                                            Rp {{ number_format($grandTotalsNonAdmin['ending'] ?? 0, 0, ',', '.') }}
                                        </th>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            {{-- Non-admin: per kategori card --}}
            @forelse ($computedCategoryGroupsNonAdmin as $cat)
                <div class="card mb-3 rounded-3 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="fw-bold">Kategori: {{ $cat['categoryName'] }}</span>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <label class="small text-muted">Tampil</label>
                            <select wire:model.live="paginatePerCategoryNonAdmin.{{ $cat['categoryId'] }}"
                                class="form-select form-select-sm rounded-3 py-1" style="width: auto;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="small text-muted">/ halaman</span>
                            <div class="ms-0 ms-md-2 mt-2 mt-md-0 w-100 w-md-auto">
                                <div class="input-icon">
                                    <input wire:model.live="searchPerCategoryNonAdmin.{{ $cat['categoryId'] }}"
                                        type="text" class="form-control form-control-sm rounded-3 py-2"
                                        placeholder="Cari uraian di kategori ini...">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20"
                                            height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                            <path d="M21 21l-6 -6" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive overflow-auto" style="max-height: 70vh;">
                            <table class="table table-vcenter table-nowrap card-table mb-0">
                                <thead class="sticky-top bg-body shadow-sm" style="z-index: 1;">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Uraian</th>
                                        <th>Group Category</th>
                                        <th class="text-end">Masuk</th>
                                        <th class="text-end">Keluar</th>
                                        <th class="text-end">Sisa</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $currentSection = null; @endphp
                                    @foreach ($cat['items'] as $row)
                                        @if ($row['jenis'] !== $currentSection)
                                            @php $currentSection = $row['jenis']; @endphp
                                            <tr class="table-info">
                                                <td colspan="8" class="fw-bold">
                                                    {{ $currentSection === 'masuk' ? 'Transaksi Masuk' : 'Transaksi Keluar' }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>{{ $row['no'] }}</td>
                                            <td>{{ $row['tanggal'] }}</td>
                                            <td>
                                                @if ($row['is_opening'])
                                                    <span class="badge bg-primary-subtle text-primary">Saldo
                                                        Awal</span>
                                                @endif
                                                {{ $row['uraian'] }}
                                            </td>
                                            <td>{{ $row['groupCategoryName'] }}</td>
                                            <td class="text-end">{{ $row['masukDisplay'] }}</td>
                                            <td class="text-end">{{ $row['keluarDisplay'] }}</td>
                                            <td class="text-end">{{ $row['runningBalanceDisplay'] }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button wire:click="edit('{{ $row['id'] }}')"
                                                        class="btn btn-outline-primary btn-sm rounded-3"
                                                        title="Ubah">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                            height="20" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                            <path d="M13.5 6.5l4 4" />
                                                        </svg>
                                                    </button>
                                                    <button wire:click="delete('{{ $row['id'] }}')"
                                                        class="btn btn-outline-danger btn-sm rounded-3" title="Hapus"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                            height="20" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M4 7h16" />
                                                            <path d="M10 11v6" />
                                                            <path d="M14 11v6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7v-3h6v3" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 border-top-0">
                            <div>
                                <div class="fw-semibold">
                                    Total kategori {{ $cat['categoryName'] }}
                                </div>
                                <div class="text-muted">Masuk:
                                    Rp {{ $cat['totals']['totalMasukDisplay'] }} | Keluar:
                                    Rp {{ $cat['totals']['totalKeluarDisplay'] }} | Sisa:
                                    Rp {{ $cat['totals']['endingBalanceDisplay'] }}
                                </div>
                                <div class="text-danger small">*Tidak mengikuti filter tanggal</div>
                            </div>
                            <div class="ms-auto">
                                {{ $cat['paginator']->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted">Tidak ada data laporan keuangan.</div>
            @endforelse
        @endif
    </div>
@endif
