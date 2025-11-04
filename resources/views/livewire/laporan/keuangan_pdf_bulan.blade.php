<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Bulan {{ $filterLabel }} - {{ $group['masjidName'] ?? '-' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 8px;
        }

        h2 {
            font-size: 14px;
            margin: 16px 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 4px;
        }

        th {
            background: #f0f0f0;
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .small {
            font-size: 10px;
            color: #555;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 12px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        .category-header {
            margin-top: 18px;
            font-weight: bold;
        }

        .fw-bold {
            font-weight: bold;
        }

        /* Letterhead styles */
        .letterhead {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .letterhead-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .letterhead-table td {
            border: none;
            vertical-align: middle;
        }

        .letterhead-logo {
            width: 80px;
        }

        .letterhead-logo img {
            max-height: 70px;
            max-width: 80px;
            display: block;
            margin: 0 auto;
        }

        .letterhead-text {
            text-align: center;
        }

        .letterhead-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .letterhead-address {
            font-size: 12px;
            color: #333;
        }
    </style>
</head>

<body>
    {{-- Kop surat: logo masjid (kiri), nama masjid (tengah), alamat di bawah nama, logo pemerintah (kanan). Tanpa fallback logo default. --}}
    <div class="letterhead">
        <table class="letterhead-table">
            <tr>
                <td class="letterhead-logo">
                    @if (!empty($masjid->logo_masjid))
                        <img src="{{ public_path($masjid->logo_masjid) }}" alt="Logo Masjid">
                    @endif
                </td>
                <td class="letterhead-text">
                    <div class="letterhead-name">{{ $masjid->name ?? ($group['masjidName'] ?? '-') }}</div>
                    <div class="letterhead-address">{{ $masjid->address ?? '-' }}</div>
                </td>
                <td class="letterhead-logo">
                    @if (!empty($masjid->logo_pemerintah))
                        <img src="{{ public_path($masjid->logo_pemerintah) }}" alt="Logo Pemerintah">
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <h2>Laporan Keuangan Bulan {{ $filterLabel }}</h2>
    <div class="small mb-2">Profil Masjid: <strong>{{ $group['masjidName'] ?? '-' }}</strong></div>

    <h2>Ringkasan per Kategori <span class="small">(Filter: {{ $filterLabel }})</span></h2>
    <table>
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th class="text-end">Total Masuk</th>
                <th class="text-end">Total Keluar</th>
                <th class="text-end">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @php
                $showPrevRow =
                    ($previousTotalsAdmin['sumMasuk'] ?? 0) !== 0 ||
                    ($previousTotalsAdmin['sumKeluar'] ?? 0) !== 0 ||
                    ($previousTotalsAdmin['ending'] ?? 0) !== 0;
            @endphp
            @if ($showPrevRow)
                <tr>
                    <td><strong>Saldo Sebelumnya</strong></td>
                    <td class="text-end">Rp {{ number_format($previousTotalsAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($previousTotalsAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-end">Rp {{ number_format($previousTotalsAdmin['ending'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endif
            @foreach ($summaryCategoriesAdmin as $sum)
                <tr>
                    <td>{{ $sum['categoryName'] }}</td>
                    <td class="text-end">Rp {{ number_format($sum['sumMasuk'], 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($sum['sumKeluar'], 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($sum['ending'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Pemasukan</th>
                <th class="text-end">Rp {{ number_format($grandTotalsAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-end">Pengeluaran</th>
                <th class="text-end">Rp {{ number_format($grandTotalsAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-end">Saldo</th>
                <th class="text-end">Rp
                    {{ number_format(($grandTotalsAdmin['sumMasuk'] ?? 0) - ($grandTotalsAdmin['sumKeluar'] ?? 0), 0, ',', '.') }}
                </th>
            </tr>
            <tr>
                <th colspan="3" class="text-end">Total Saldo</th>
                <th class="text-end">Rp {{ number_format($grandTotalsAdmin['ending'] ?? 0, 0, ',', '.') }}</th>
            </tr>
            {{-- <tr>
                <th class="text-end">Saldo akhir {{ $filterLabel }} (pemasukan - pengeluaran)</th>
                <th class="text-end">Rp {{ number_format($grandTotalsAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}</th>
                <th class="text-end">Rp {{ number_format($grandTotalsAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}</th>
                <th class="text-end">Rp
                    {{ number_format(($grandTotalsAdmin['sumMasuk'] ?? 0) - ($grandTotalsAdmin['sumKeluar'] ?? 0), 0, ',', '.') }}
                </th>
            </tr> --}}
            {{-- <tr>
                <th class="text-end">Total Pemasukan - Total Pengeluaran = Total Saldo</th>
                <th class="text-end">Rp
                    {{ number_format(($previousTotalsAdmin['sumMasuk'] ?? 0) + ($grandTotalsAdmin['sumMasuk'] ?? 0), 0, ',', '.') }}
                </th>
                <th class="text-end">Rp
                    {{ number_format(($previousTotalsAdmin['sumKeluar'] ?? 0) + ($grandTotalsAdmin['sumKeluar'] ?? 0), 0, ',', '.') }}
                </th>
                <th class="text-end">Rp {{ number_format($grandTotalsAdmin['ending'] ?? 0, 0, ',', '.') }}</th>
            </tr> --}}
        </tfoot>
    </table>

    <h2>Total Saldo Akhir Pada Saat ini <span class="small">(Total Pemasukan - Total Pengeluaran = Total Saldo)</span>
    </h2>
    <table>
        <thead>
            <tr>
                <th class="text-end">Total Pemasukan</th>
                <th class="text-end">Total Pengeluaran</th>
                <th class="text-end">Total Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-end">Rp {{ number_format($grandTotalsAdmin['sumMasuk'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($grandTotalsAdmin['sumKeluar'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($grandTotalsAdmin['ending'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @foreach ($group['categories'] as $category)
        <div class="category-header">Kategori: {{ $category['categoryName'] }}</div>
        <table class="mt-2">
            <thead>
                <tr>
                    <th style="width: 3rem;">No</th>
                    <th style="width: 8rem;">Tanggal</th>
                    <th>Uraian</th>
                    <th style="width: 6rem;">Jenis</th>
                    <th class="text-end" style="width: 10rem;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="fw-bold">Transaksi Masuk</td>
                </tr>
                @php $hasMasuk = false; @endphp
                @foreach ($category['rows'] as $row)
                    @if ($row['jenis'] === 'masuk')
                        @php $hasMasuk = true; @endphp
                        <tr>
                            <td>{{ $row['no'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                            <td>{{ $row['uraian'] }}</td>
                            <td>Masuk</td>
                            <td class="text-end">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
                @if (!$hasMasuk)
                    <tr>
                        <td colspan="5" class="small">Tidak ada transaksi masuk pada bulan ini untuk kategori ini.
                        </td>
                    </tr>
                @endif

                <tr>
                    <td colspan="5" class="fw-bold">Transaksi Keluar</td>
                </tr>
                @php $hasKeluar = false; @endphp
                @foreach ($category['rows'] as $row)
                    @if ($row['jenis'] === 'keluar')
                        @php $hasKeluar = true; @endphp
                        <tr>
                            <td>{{ $row['no'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                            <td>{{ $row['uraian'] }}</td>
                            <td>Keluar</td>
                            <td class="text-end">Rp {{ number_format($row['saldo'], 0, ',', '.') }}</td>
                        </tr>
                    @endif
                @endforeach
                @if (!$hasKeluar)
                    <tr>
                        <td colspan="5" class="small">Tidak ada transaksi keluar pada bulan ini untuk kategori ini.
                        </td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Saldo Sebelumnya</th>
                    <th class="text-end">Rp {{ number_format($category['previousEnding'] ?? 0, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">Total Masuk</th>
                    <th class="text-end">Rp {{ number_format($category['sumMasuk'] ?? 0, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">Total Keluar</th>
                    <th class="text-end">Rp {{ number_format($category['sumKeluar'] ?? 0, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">
                        Saldo Akhir {{ $category['categoryName'] }}
                        <span class="small">({{ $filterLabel }})</span>
                    </th>
                    <th class="text-end">Rp
                        {{ number_format(($category['sumMasuk'] ?? 0) - ($category['sumKeluar'] ?? 0), 0, ',', '.') }}
                    </th>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">Total saldo akhir {{ $category['categoryName'] }} saat ini</th>
                    <th class="text-end">Rp {{ number_format($category['ending'] ?? 0, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @endforeach
</body>

</html>
