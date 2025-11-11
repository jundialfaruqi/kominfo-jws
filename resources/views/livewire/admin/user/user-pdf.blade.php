<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Daftar User - Role: {{ $roleName }}</title>
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

        .small {
            font-size: 10px;
            color: #555;
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

        .text-center {
            text-align: center;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-1 {
            margin-bottom: 8px;
        }

        .mt-1 {
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <div class="letterhead mb-1">
        <h1 class="text-center">Daftar Pengguna Aplikasi JWS-Diskominfo</h1>
        <div class="small">Tanggal Cetak: {{ $exportDateLabel }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">No.</th>
                <th>Nama Masjid</th>
                <th>Admin</th>
                <th>Kontak</th>
                <th>Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index => $u)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $u->profil->name ?? '-' }}</td>
                    <td>{{ $u->name }}</td>
                    <td>
                        {{ $u->email }}<br>
                        {{ $u->phone }}
                    </td>
                    <td>
                        @php
                            $last = $u->last_activity_at;
                            $now = \Carbon\Carbon::now();
                        @endphp
                        @if (empty($last))
                            Tidak ada aktivitas
                        @else
                            @php $diffDays = \Carbon\Carbon::parse($last)->diffInDays($now); @endphp
                            @if ($diffDays < 30)
                                Aktif {{ \Carbon\Carbon::parse($last)->diffForHumans() }}
                            @elseif ($diffDays < 90)
                                Kurang (aktivitas {{ \Carbon\Carbon::parse($last)->diffForHumans() }})
                            @else
                                Tidak aktif lebih dari 3 bulan (terakhir
                                {{ \Carbon\Carbon::parse($last)->diffForHumans() }})
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="small mt-1">* Aktifitas : </div>
    <div class="small"> - Aktif ≤ 30 Hari</div>
    <div class="small"> - Kurang (31-90 Hari)</div>
    <div class="small"> - Tidak Aktif > 3 Bulan</div>
    <div class="small"> - Tidak Ada Aktifitas</div>
</body>

</html>
