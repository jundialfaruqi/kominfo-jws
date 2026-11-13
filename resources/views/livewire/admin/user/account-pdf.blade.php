<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Informasi Akun JWS - {{ $user->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 16mm 12mm 16mm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.38;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-wrapper {
            width: 100%;
            border-bottom: 2.5px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 11px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-logo-cell {
            width: 74px;
            vertical-align: middle;
            text-align: left;
            padding-left: 14px;
            padding-right: 6px;
        }

        .kop-logo {
            width: 66px;
            height: auto;
            display: block;
        }

        .kop-text-cell {
            vertical-align: middle;
            text-align: center;
            padding-right: 10px;
        }

        .kop-pemerintah {
            font-size: 15.5pt;
            font-weight: normal;
            letter-spacing: 1.3px;
            color: #000000;
            line-height: 1.18;
            white-space: nowrap;
        }

        .kop-dinas {
            font-size: 18.5pt;
            font-weight: bold;
            letter-spacing: 1.1px;
            color: #000000;
            line-height: 1.16;
            white-space: nowrap;
        }

        .kop-alamat {
            font-size: 10.2pt;
            font-weight: normal;
            letter-spacing: 1.0px;
            color: #000000;
            margin-top: 3px;
            line-height: 1.26;
        }

        /* Judul Surat */
        .doc-title-block {
            text-align: center;
            margin-bottom: 9px;
        }

        .doc-title {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.3px;
            margin: 0;
        }

        .doc-subtitle {
            font-size: 8.5pt;
            color: #374151;
            margin-top: 2px;
        }

        .intro-text {
            margin: 0 0 8px 0;
            text-align: justify;
        }

        /* Tabel Informasi Akun */
        .section-heading {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin: 8px 0 5px 0;
            padding-left: 7px;
            border-left: 3px solid #0ea5a3;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table td {
            border: 1px solid #cbd5e1;
            padding: 4.5px 8px;
            vertical-align: top;
        }

        .info-label {
            width: 33%;
            background-color: #f8fafc;
            font-weight: bold;
            color: #1e293b;
        }

        .info-value {
            width: 67%;
            color: #0f172a;
            word-break: break-word;
        }

        .password-badge {
            display: inline-block;
            font-family: Courier, monospace;
            font-size: 9.5pt;
            font-weight: bold;
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
            padding: 1px 6px;
            border-radius: 3px;
        }

        .url-text {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Kotak Peringatan Wajib Ganti Password */
        .warning-box {
            background-color: #fffbeb;
            border: 1px solid #f59e0b;
            border-left: 4px solid #d97706;
            padding: 6px 9px;
            margin-bottom: 9px;
            font-size: 8.4pt;
            color: #78350f;
        }

        /* Aturan Penggunaan */
        .rules-box {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 7px 11px;
            margin-bottom: 10px;
        }

        .rules-list {
            margin: 2px 0 0 0;
            padding-left: 16px;
        }

        .rules-list li {
            margin-bottom: 4px;
            text-align: justify;
            font-size: 8.3pt;
            line-height: 1.35;
        }

        .rules-list li:last-child {
            margin-bottom: 0;
        }

        /* Tanda Tangan & Footer */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 8px;
        }

        .signature-left {
            width: 56%;
            vertical-align: top;
            text-align: left;
            padding: 0;
            font-size: 7.8pt;
            color: #64748b;
        }

        .signature-right {
            width: 44%;
            vertical-align: top;
            text-align: right;
            padding: 0;
            font-size: 8.8pt;
        }

        .signature-box-inner {
            display: inline-block;
            text-align: left;
        }

        .sig-space {
            height: 28px;
        }
    </style>
</head>

<body>
    {{-- Kop Surat Resmi --}}
    <div class="kop-wrapper">
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    @if (!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Logo Pemerintah Kota Pekanbaru" class="kop-logo">
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-pemerintah">PEMERINTAH KOTA PEKANBARU</div>
                    <div class="kop-dinas">DINAS KOMUNIKASI INFORMATIKA</div>
                    <div class="kop-dinas">STATISTIK DAN PERSANDIAN</div>
                    <div class="kop-alamat">
                        Jalan Abdul Rahman Hamid Kel. Tuah Negeri Kec. Tenayan Raya<br>
                        Pekanbaru 28285
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Judul Surat --}}
    <div class="doc-title-block">
        <div class="doc-title">Informasi Akun Pengguna Aplikasi JWS</div>
        <div class="doc-subtitle">Layanan Jadwal Waktu Sholat (JWS) Digital Kota Pekanbaru</div>
    </div>

    <p class="intro-text">
        Dengan hormat, bersama dokumen ini kami sampaikan rincian akun pengelola Aplikasi Jadwal Waktu Sholat (JWS)
        Dinas Komunikasi Informatika Statistik dan Persandian Kota Pekanbaru untuk digunakan sebagaimana mestinya:
    </p>

    {{-- Informasi Akun --}}
    <div class="section-heading">Rincian Informasi Akun</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Pengelola</td>
            <td class="info-value">{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Nama Masjid / Instansi</td>
            <td class="info-value">{{ $user->profil->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Email Login</td>
            <td class="info-value">{{ $user->email }}</td>
        </tr>
        <tr>
            <td class="info-label">Password</td>
            <td class="info-value">
                <span class="password-badge">{{ $defaultPassword }}</span>
            </td>
        </tr>
        <tr>
            <td class="info-label">No. Telepon</td>
            <td class="info-value">{{ $user->phone ?: '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Status Akun</td>
            <td class="info-value">{{ $user->status }}</td>
        </tr>
        <tr>
            <td class="info-label">URL Login Aplikasi</td>
            <td class="info-value"><span class="url-text">{{ $loginUrl }}</span></td>
        </tr>
        @if (!empty($displayUrl))
            <tr>
                <td class="info-label">URL Tampilan Display JWS</td>
                <td class="info-value"><span class="url-text">{{ $displayUrl }}</span></td>
            </tr>
        @endif
    </table>

    {{-- Keterangan Wajib Ganti Password --}}
    <div class="warning-box">
        <strong>Perhatian Penting:</strong> Demi keamanan akun dan perangkat display masjid,
        <strong>pengguna wajib segera mengubah password</strong> setelah berhasil login pertama kali melalui menu
        <strong>Pengaturan</strong> di dalam dashboard aplikasi JWS. Jangan membagikan kredensial akun kepada pihak yang tidak berwenang.
    </div>

    {{-- Aturan Penggunaan Aplikasi JWS --}}
    <div class="section-heading">Aturan Penggunaan Aplikasi JWS</div>
    <div class="rules-box">
        <ol class="rules-list">
            <li>
                <strong>Peruntukan Resmi:</strong> Akun ini diberikan khusus kepada pengelola masjid atau instansi terdaftar untuk mengelola jadwal waktu sholat, profil masjid, petugas sholat Jumat, agenda kegiatan, slide informasi, teks berjalan (<em>marquee</em>), audio, serta laporan keuangan pada layar JWS.
            </li>
            <li>
                <strong>Kewajiban Kerahasiaan &amp; Perubahan Password:</strong> Pengelola wajib mengganti password default pada login pertama dan bertanggung jawab penuh atas seluruh aktivitas serta perubahan konten yang dilakukan menggunakan akun ini.
            </li>
            <li>
                <strong>Kepatuhan Konten Publik:</strong> Seluruh materi teks, gambar, video, dan audio yang diunggah wajib sesuai dengan syariat Islam, etika publik, norma kesopanan, serta peraturan perundang-undangan yang berlaku, dan bebas dari unsur SARA, ujaran kebencian, hoaks, maupun promosi komersial tanpa izin.
            </li>
            <li>
                <strong>Akurasi Informasi Masjid:</strong> Pengelola wajib menjaga keakuratan data profil masjid, koordinat/lokasi waktu sholat, agenda, petugas, dan laporan keuangan yang dipublikasikan kepada jamaah.
            </li>
            <li>
                <strong>Larangan Penyalahgunaan Sistem:</strong> Dilarang memindahtangankan akses akun tanpa persetujuan pengelola resmi atau melakukan tindakan yang dapat mengganggu kestabilan dan keamanan server Pemerintah Kota Pekanbaru.
            </li>
            <li>
                <strong>Evaluasi &amp; Penonaktifan Akses:</strong> Dinas Komunikasi Informatika Statistik dan Persandian Kota Pekanbaru berhak melakukan penangguhan atau penonaktifan akun apabila ditemukan pelanggaran terhadap ketentuan penggunaan aplikasi ini.
            </li>
        </ol>
    </div>

    {{-- Penutup & Tanda Tangan --}}
    <table class="signature-table">
        <tr>
            <td class="signature-left">
                Dicetak pada: {{ $exportDateLabel }}<br>
                Dokumen ini diterbitkan secara elektronik oleh Sistem JWS Diskominfotiksan Kota Pekanbaru.
            </td>
            <td class="signature-right">
                <div class="signature-box-inner">
                    Pekanbaru, {{ $exportDateLabel }}<br>
                    <strong>Pengelola Aplikasi JWS</strong><br>
                    Diskominfotiksan Kota Pekanbaru
                    <div class="sig-space"></div>
                    <span><em>Tim Layanan Aplikasi JWS</em></span>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
