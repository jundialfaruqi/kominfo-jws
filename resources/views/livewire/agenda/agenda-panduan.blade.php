<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4">
                <div class="card-header">
                    <a wire:navigate href="{{ route('agenda-masjid.index') }}"
                        class="btn btn-outline-secondary rounded-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-left-dashed">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12h6m3 0h1.5m3 0h.5" />
                            <path d="M5 12l4 4" />
                            <path d="M5 12l4 -4" />
                        </svg>
                        Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="px-8">
                        <div class="d-flex align-items-center">
                            <h1 class="mb-5 flex-grow-1 text-center">Tutorial Menggunakan Fitur Agenda di Aplikasi JWS
                            </h1>
                        </div>
                        <div class="card-text">
                            <h2 class="fw-bold text-center">Membuat Agenda Baru</h2>
                            <div class="mb-4">
                                <img src="{{ asset('welcome/assets/img/agenda/langkah-1-membuat-agenda.webp') }}"
                                    alt="Langkah 1 Membuat Agenda">
                            </div>
                            <p>
                                <strong>1. </strong>
                                Dari halaman admin utama, pilih menu <span class="badge fw-bold">Agenda</span>
                                yang ada di sidebar sebelah kiri.
                            </p>
                            <p>
                                <strong>2. </strong>
                                Pada halaman Agenda klik tombol <span class="badge fw-bold">Tambah Agenda Baru
                                </span> yang berwarna biru.
                            </p>

                            <h2 class="fw-bold text-center pt-4">Mengisi Form Agenda</h2>
                            <div class="mb-4">
                                <img src="{{ asset('welcome/assets/img/agenda/langkah-2-mengisi-form-agenda.webp') }}"
                                    alt="Langkah 2 Mengisi Form Agenda">
                            </div>

                            <strong>1. Pilih Tanggal </strong>
                            <p>
                                Silahkan pilih tanggal agenda. Tanggal Agenda akan menjadi penanda yang akan ditampilkan
                                di section Agenda pada halaman utama JWS Masjid.
                            </p>

                            <strong>2. Ketikkan Nama Agenda </strong>
                            <p>
                                Buat nama Agenda yang akan ditampilkan di section Agenda.
                                Buatlah nama agenda
                                sesingkat mungkin.
                            </p>

                            <strong>3. Klik Aktif </strong>
                            <p>
                                Klik aktif untuk membuat Agenda menjadi aktif agar dapat ditayangkan di section
                                Agenda.
                            </p>

                            <strong>4. Simpan </strong>
                            <p>
                                Klik tombol <span class="badge fw-bold">Simpan</span> yang berwarna biru untuk
                                menyimpan Agenda Baru yang ingin ditampilkan.
                            </p>

                            <h2 class="fw-bold text-center pt-4">Agenda Siap Ditampilkan</h2>
                            <div class="mb-4">
                                <img src="{{ asset('welcome/assets/img/agenda/agenda-siap-ditampilkan.webp') }}"
                                    alt="Langkah 2 Mengisi Form Agenda">
                            </div>

                            <p>Salam, Tim Diskominfo Kota Pekanbaru</p>
                            <img class="img-fluid" src="{{ asset('theme/static/logo-pemko-kominfo.webp') }}"
                                alt="Logo Diskominfo Pemerintah Kota Pekanbaru" style="width: 150px; height: auto;">
                        </div>
                    </div>
                </div>
                <div class="card-footer rounded-bottom-4 mt-5">
                    <a wire:navigate href="{{ route('agenda-masjid.index') }}"
                        class="btn btn-outline-secondary rounded-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-left-dashed">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12h6m3 0h1.5m3 0h.5" />
                            <path d="M5 12l4 4" />
                            <path d="M5 12l4 -4" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
