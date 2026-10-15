<style>
    .policy-content {
        color: #27272a;
        font-size: 1.025rem;
        line-height: 1.8;
    }

    .policy-content h2 {
        color: #0f172a;
        font-weight: 700;
        font-size: 1.45rem;
        margin-top: 2.25rem;
        margin-bottom: 0.85rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .policy-content h3 {
        color: #1e293b;
        font-weight: 600;
        font-size: 1.15rem;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .policy-content p,
    .policy-content ul {
        margin-bottom: 1.25rem;
    }

    .policy-content ul {
        padding-left: 1.25rem;
    }

    .policy-content li {
        margin-bottom: 0.5rem;
    }

    .policy-table-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        margin: 1.5rem 0;
    }

    .policy-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: 0.95rem;
    }

    .policy-table th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 600;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .policy-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }

    .policy-table tr:last-child td {
        border-bottom: none;
    }

    .policy-info-box {
        background-color: #f8fafc;
        border-left: 3px solid #0284c7;
        padding: 1rem 1.25rem;
        border-radius: 0 6px 6px 0;
        margin: 1.5rem 0;
        font-size: 0.95rem;
        color: #334155;
    }
</style>

<div class="py-4 bg-white" style="margin-top: 70px; min-height: 80vh;">
    <div class="container py-lg-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                {{-- Breadcrumb (Clean, No Badge) --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb mb-0" style="font-size: 0.9rem;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('welcome.index') }}" class="text-decoration-none text-muted">Beranda</a>
                        </li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">
                            Kebijakan Privasi & Kepatuhan
                        </li>
                    </ol>
                </nav>

                {{-- Header Artikel --}}
                <div class="mb-4 pb-3 border-bottom">
                    <h1 class="display-6 fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">
                        Kebijakan Privasi & Formulir Kepatuhan Layanan
                    </h1>
                    <div class="text-muted" style="font-size: 0.925rem;">
                        Aplikasi JWS Kominfo (Android TV, Google TV & Web Portal) • Terakhir diperbarui: 16 Oktober 2026
                    </div>
                </div>

                {{-- Isi Konten Artikel --}}
                <article class="policy-content">
                    <p>
                        Dinas Komunikasi, Informatika, Statistik dan Persandian (DISKOMINFOTIKSAN) Pemerintah Kota
                        Pekanbaru berkomitmen penuh untuk melindungi privasi pengguna, pengurus masjid, jamaah, dan
                        seluruh pihak yang memanfaatkan sistem aplikasi <strong>JWS Kominfo (Jadwal Waktu
                            Sholat)</strong>,
                        baik melalui perangkat TV Masjid (Smart TV / Android TV) maupun portal manajemen web resmi.
                    </p>

                    <p>
                        Halaman ini menjelaskan secara transparan bagaimana data diproses, batasan izin sistem,
                        pernyataan kepatuhan resmi terhadap kebijakan <em>Google Play Developer Policy</em>, serta
                        jaminan perlindungan data publik.
                    </p>

                    <h2>1. Identitas Resmi & Entitas Pemerintah</h2>
                    <p>
                        Aplikasi <strong>JWS Kominfo</strong> dikembangkan, dikelola, dan diterbitkan secara resmi oleh:
                    </p>
                    <ul>
                        <li><strong>Instansi Pengembang:</strong> Dinas Komunikasi, Informatika, Statistik dan
                            Persandian (DISKOMINFOTIKSAN) Kota Pekanbaru</li>
                        <li><strong>Pemerintahan:</strong> Pemerintah Kota Pekanbaru, Provinsi Riau, Republik Indonesia
                        </li>
                        <li><strong>Domain Resmi:</strong> <a href="https://jadwalsholat.pekanbaru.go.id"
                                target="_blank" rel="noopener noreferrer"
                                class="text-primary text-decoration-none">jadwalsholat.pekanbaru.go.id</a></li>
                        <li><strong>Package Name Android:</strong> <code>id.go.pekanbaru.jws</code></li>
                        <li><strong>Peruntukan:</strong> Layanan publik non-komersial penyedia jadwal sholat digital,
                            kalender ibadah, peringatan adzan-iqomah, serta media informasi resmi masjid dan mushalla.
                        </li>
                    </ul>

                    <h2>2. Pernyataan Aplikasi Pemerintah (Government App Declaration)</h2>
                    <p>
                        Aplikasi ini merupakan bagian dari sarana pelayanan informasi publik Pemerintah Kota Pekanbaru:
                    </p>
                    <ul>
                        <li>Data jadwal waktu sholat disinkronisasikan dan dihitung berdasarkan otoritas resmi hisab
                            rukyat Kementerian Agama Republik Indonesia melalui integrasi basis data resmi.</li>
                        <li>Aplikasi ini tidak memungut biaya apapun dari pengurus masjid atau masyarakat (100% nirlaba
                            dan gratis).</li>
                        <li>Aplikasi ini tidak berafiliasi dengan entitas politik, periklanan swasta, atau lembaga
                            komersial manapun.</li>
                    </ul>

                    <h2>3. Data Pengguna & Keamanan (Data Safety)</h2>
                    <p>
                        Kami memegang prinsip minimalisasi data (<em>data minimization</em>). Kami hanya memproses data
                        yang benar-benar esensial agar sistem Smart TV dapat menampilkan informasi jadwal sholat masjid
                        terkait secara akurat.
                    </p>

                    <h3>A. Data yang Diproses</h3>
                    <ul>
                        <li>
                            <strong>Pengenal Perangkat (Device Identifier):</strong> ID unik perangkat televisi dibuat
                            secara acak saat aplikasi diinstal untuk keperluan pengaitan (<em>pairing</em>) TV masjid ke
                            akun masjid di dashboard admin. ID ini tidak terikat pada identitas personal pengguna.
                        </li>
                        <li>
                            <strong>Log Konektivitas Teknis (IP Address & Waktu Sinkronisasi):</strong> Dicatat
                            sementara pada server semata-mata untuk keperluan diagnosis stabilitas jaringan, pembaruan
                            konten, dan pencegahan penyalahgunaan server.
                        </li>
                    </ul>

                    <h3>B. Data yang TIDAK Pernah Dikumpulkan</h3>
                    <ul>
                        <li><strong>Tidak mengumpulkan data identitas personal:</strong> Tidak mengambil nama asli
                            pengguna TV, alamat rumah pribadi, atau nomor telepon jamaah.</li>
                        <li><strong>Tidak mengumpulkan lokasi presisi (GPS):</strong> Penentuan koordinat waktu sholat
                            merujuk pada titik koordinat masjid yang didaftarkan oleh pengurus melalui dashboard web,
                            bukan pelacakan GPS fisik perangkat TV.</li>
                        <li><strong>Tidak mengakses sensor audio & kamera:</strong> Aplikasi tidak pernah merekam suara
                            atau video di lingkungan masjid.</li>
                        <li><strong>Tidak mengakses kontak, penyimpanan berkas pribadi, atau SMS.</strong></li>
                        <li><strong>Tidak mengumpulkan data keuangan atau kartu kredit.</strong></li>
                        <li><strong>Tidak menargetkan pengumpulan data anak-anak di bawah umur.</strong></li>
                    </ul>

                    <h2>4. Izin Sistem Android (Android Device Permissions)</h2>
                    <p>
                        Aplikasi Android TV <code>id.go.pekanbaru.jws</code> hanya meminta izin sistem dasar yang
                        esensial:
                    </p>
                    <div class="policy-table-wrapper">
                        <table class="policy-table">
                            <thead>
                                <tr>
                                    <th>Nama Izin (Permission)</th>
                                    <th>Klasifikasi</th>
                                    <th>Alasan & Tujuan Penggunaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>android.permission.INTERNET</code></td>
                                    <td>Normal</td>
                                    <td>Mengunduh jadwal sholat, pengumuman agenda masjid, dan menyinkronkan waktu
                                        server secara berkala.</td>
                                </tr>
                                <tr>
                                    <td><code>android.permission.RECEIVE_BOOT_COMPLETED</code></td>
                                    <td>Normal</td>
                                    <td>Memungkinkan aplikasi otomatis berjalan di latar depan saat televisi dinyalakan
                                        (berguna untuk TV masjid yang menyala saat jam operasional).</td>
                                </tr>
                                <tr>
                                    <td><code>QUICKBOOT_POWERON</code></td>
                                    <td>Normal</td>
                                    <td>Menjaga aplikasi tetap aktif secara otomatis saat televisi terbangun dari mode
                                        siaga (<em>standby</em>).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted" style="font-size: 0.9rem;">
                        <em>Catatan: Aplikasi tidak menggunakan izin berbahaya (Dangerous Permissions) atau izin jendela
                            mengambang (SYSTEM_ALERT_WINDOW).</em>
                    </p>

                    <h2>5. Kebijakan Iklan & Monetisasi</h2>
                    <ul>
                        <li>Aplikasi JWS Kominfo sepenuhnya <strong>Bebas Iklan (No Ads)</strong>.</li>
                        <li>Tidak terdapat SDK jaringan periklanan pihak ketiga (seperti Google AdMob, Unity, Facebook
                            Audience Network, dll).</li>
                        <li>Tidak ada pembelian dalam aplikasi (<em>In-App Purchase</em>) maupun langganan berbayar.
                        </li>
                    </ul>

                    <h2>6. Retensi, Kontrol & Penghapusan Data</h2>
                    <p>
                        Pengurus masjid memiliki kendali penuh atas data masjid dan keterikatan perangkat:
                    </p>
                    <ul>
                        <li>
                            <strong>Pemutusan Keterikatan TV (Unlink Device):</strong> Pengurus masjid dapat memutuskan
                            tautan kode TV kapan saja melalui menu <em>TV Pairing</em> pada dashboard web JWS Kominfo.
                        </li>
                        <li>
                            <strong>Penghapusan Profil Masjid:</strong> Pengurus masjid dapat mengajukan permohonan
                            penonaktifan atau penghapusan data masjid kepada administrator DISKOMINFOTIKSAN Kota
                            Pekanbaru melalui kanal komunikasi resmi.
                        </li>
                        <li>
                            <strong>Penyimpanan Offline:</strong> Data jadwal sholat yang telah diunduh tersimpan secara
                            lokal di memori internal TV agar jadwal sholat tetap tampil presisi meskipun jaringan
                            internet masjid sedang terputus.
                        </li>
                    </ul>

                    <h2>7. Ringkasan Kepatuhan Google Play Console</h2>
                    <div class="policy-table-wrapper">
                        <table class="policy-table">
                            <thead>
                                <tr>
                                    <th>Kategori Formulir Play Console</th>
                                    <th>Status Pernyataan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Aplikasi Pemerintah (Government App)</strong></td>
                                    <td>Ya, aplikasi resmi Dinas Komunikasi, Informatika, Statistik dan Persandian Kota
                                        Pekanbaru.</td>
                                </tr>
                                <tr>
                                    <td><strong>Keberadaan Iklan (Ads Declaration)</strong></td>
                                    <td>Tidak mengandung iklan sama sekali.</td>
                                </tr>
                                <tr>
                                    <td><strong>Rating Konten (IARC Content Rating)</strong></td>
                                    <td>Rating 3+ (Everyone / Semua Umur) - Bebas konten negatif.</td>
                                </tr>
                                <tr>
                                    <td><strong>Keamanan Data (Data Safety)</strong></td>
                                    <td>Hanya memproses Device ID untuk fungsionalitas aplikasi; seluruh data
                                        ditransmisikan terenkripsi (HTTPS).</td>
                                </tr>
                                <tr>
                                    <td><strong>Navigasi Layar (Android TV Standard)</strong></td>
                                    <td>100% mendukung D-Pad Remote TV; tidak memerlukan layar sentuh (touchscreen).
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h2>8. Kontak & Dukungan Pengembang</h2>
                    <p>
                        Apabila terdapat pertanyaan, masukan teknis, atau permintaan terkait pengelolaan privasi data,
                        silakan menghubungi tim teknis resmi melalui saluran berikut:
                    </p>
                    <div class="policy-info-box">
                        <strong>Dinas Komunikasi, Informatika, Statistik dan Persandian Kota Pekanbaru</strong><br>
                        Kompleks Perkantoran Pemerintah Kota Pekanbaru<br>
                        Jl. Abdul Rahman Hamid No. 1, Kecamatan Tenayan Raya, Kota Pekanbaru, Riau 28285<br>
                        Email Layanan: <a href="mailto:kominfopkb@gmail.com"
                            class="text-decoration-none">kominfopkb@gmail.com</a><br>
                        Portal Resmi: <a href="https://pekanbaru.go.id" target="_blank" rel="noopener noreferrer"
                            class="text-decoration-none">https://pekanbaru.go.id</a>
                    </div>
                </article>

            </div>
        </div>
    </div>
</div>
