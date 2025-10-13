<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="px-7 py-7">
                        <div class="d-flex align-items-center mb-4">
                            <h1 class="mb-0 flex-grow-1 text-center">Balance API Documentation</h1>
                        </div>

                        <div class="mb-4">
                            <p class="text-secondary">
                                Halaman ini menjelaskan dokumentasi lengkap untuk API Balance Summary & Details
                                yang tersedia pada endpoint berikut:
                            </p>
                            <ul>
                                <li><code>GET /api/balance-summary/{slug}</code></li>
                                <li><code>GET /api/balance-details/{slug}</code></li>
                            </ul>
                            <p class="text-secondary">Keduanya menggunakan controller dan logika yang sama.</p>
                        </div>

                        <div class="mb-5">
                            <h2 class="mb-3">Parameter Query</h2>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Tipe</th>
                                            <th>Default</th>
                                            <th>Opsi</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>details</code></td>
                                            <td>string</td>
                                            <td><code>summary</code></td>
                                            <td><code>none</code>, <code>summary</code>, <code>full</code></td>
                                            <td>
                                                Mengontrol tingkat detail pada respons:
                                                <ul>
                                                    <li><b>none</b>: hanya mengembalikan <code>grandTotals</code>.</li>
                                                    <li><b>summary</b>: mengembalikan <code>categories</code> dan <code>grandTotals</code> (ringkas).</li>
                                                    <li><b>full</b>: mengembalikan <code>categoriesWithItems</code> terpaginasikan per kategori, plus <code>categories</code> dan <code>grandTotals</code>.</li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><code>page</code></td>
                                            <td>integer</td>
                                            <td>1</td>
                                            <td>&ge; 1</td>
                                            <td>Nomor halaman untuk pagination rincian transaksi per kategori saat <code>details=full</code>.</td>
                                        </tr>
                                        <tr>
                                            <td><code>per_page</code></td>
                                            <td>integer</td>
                                            <td>50</td>
                                            <td>1-500</td>
                                            <td>Jumlah item per halaman untuk rincian transaksi per kategori saat <code>details=full</code>.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h2 class="mb-3">Format Respons</h2>
                            <p>Respons berupa JSON dengan struktur berikut (contoh):</p>
                            <pre class="bg-dark text-white p-3 rounded-3"><code>{
  "grandTotals": {
    "sumMasuk": 12500000,
    "sumKeluar": 5000000,
    "sumSaldo": 7500000,
    "sumMasukDisplay": "12.500.000",
    "sumKeluarDisplay": "5.000.000",
    "sumSaldoDisplay": "7.500.000"
  },
  "categories": [
    {
      "kategori": "Infaq",
      "sumMasuk": 10000000,
      "sumKeluar": 2000000,
      "sumSaldo": 8000000,
      "sumMasukDisplay": "10.000.000",
      "sumKeluarDisplay": "2.000.000",
      "sumSaldoDisplay": "8.000.000"
    }
  ],
  "categoriesWithItems": [
    {
      "kategori": "Infaq",
      "items": [
        { "tanggal": "2024-10-12", "keterangan": "Donasi A", "jenis": "Masuk", "masuk": 2000000, "keluar": 0, "running_balance": 8000000, "masukDisplay": "2.000.000", "keluarDisplay": "0", "runningBalanceDisplay": "8.000.000" }
      ],
      "pagination": { "page": 1, "per_page": 50, "total": 124, "last_page": 3 }
    }
  ]
}</code></pre>
                            <p class="text-secondary">Catatan: <code>running_balance</code> diambil langsung dari kolom <code>laporans.running_balance</code> untuk efisiensi; nilai display diformat tanpa tanda plus/minus dan tanpa desimal.</p>
                        </div>

                        <div class="mb-5">
                            <h2 class="mb-3">Contoh Penggunaan</h2>
                            <ul>
                                <li>
                                    Ringkas (default):
                                    <pre><code>curl "{{ url('/api/balance-summary/1') }}"</code></pre>
                                </li>
                                <li>
                                    Ringkas eksplisit:
                                    <pre><code>curl "{{ url('/api/balance-summary/1?details=summary') }}"</code></pre>
                                </li>
                                <li>
                                    Full dengan pagination (halaman 2, 100 item per halaman):
                                    <pre><code>curl "{{ url('/api/balance-details/1?details=full&page=2&per_page=100') }}"</code></pre>
                                </li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2 class="mb-3">Aturan Pagination</h2>
                            <ul>
                                <li><code>per_page</code> default 50, maksimum 500.</li>
                                <li>Pagination berlaku pada <code>categoriesWithItems</code> saat <code>details=full</code>.</li>
                                <li>Metadata pagination tersedia: <code>page</code>, <code>per_page</code>, <code>total</code>, <code>last_page</code>.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2 class="mb-3">Tips & Catatan</h2>
                            <ul>
                                <li>Contoh slug: <code>1</code>. Jika slug mengandung karakter <code>/</code>, lakukan URL encoding (contoh: <code>/foo/bar</code> menjadi <code>%2Ffoo%2Fbar</code>).</li>
                                <li>Jika Anda menginginkan respons sangat ringan, gunakan <code>details=none</code> untuk hanya mengambil <code>grandTotals</code>.</li>
                                <li>Format angka display menggunakan pemisah ribuan (tanpa tanda plus/minus).</li>
                                <li>Respons kesalahan akan ditampilkan dalam format HTML jika endpoint tidak ditemukan atau terjadi error pada server.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2 class="mb-3">Cakupan Keamanan</h2>
                            <ul>
                                <li>Endpoint ini read-only; tidak ada perubahan data.</li>
                                <li>Tidak ada kredensial yang dikirimkan pada query.</li>
                                <li>Pastikan penggunaan sesuai dengan akses yang diizinkan oleh aplikasi.</li>
                            </ul>
                        </div>

                        <div class="mb-5">
                            <h2 class="mb-3">Perubahan Terakhir</h2>
                            <ul>
                                <li>Penambahan parameter <code>details</code>, <code>page</code>, dan <code>per_page</code>.</li>
                                <li>Penggunaan langsung kolom <code>laporans.running_balance</code> untuk efisiensi.</li>
                                <li>Pagination pada <code>categoriesWithItems</code> saat <code>details=full</code>.</li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>