            <!-- ================= PENGATURAN ================= -->
            <section class="screen screen--light" id="scrPengaturan" hidden>
                <div class="set-wrap">
                    <h1>Pengaturan</h1>
                    <p class="sub">Lokasi, sensor, dan status perangkat</p>

                    <div class="card">
                        <h3>Lokasi</h3>
                        <select id="provSelect" aria-label="Provinsi"></select>
                        <select id="citySelect" aria-label="Kabupaten atau Kota" disabled></select>
                        <button class="btn primary" id="btnGps">📍 Lokasi Saya</button>
                        <p class="hint" id="gpsHint">Pilih provinsi lalu kabupaten/kota secara manual, atau tekan
                            <b>Lokasi Saya</b> untuk memakai GPS perangkat.
                        </p>
                    </div>

                    <div class="card">
                        <h3>Kompas</h3>
                        <button class="btn" id="btnSensor2">🧭 Aktifkan Sensor Kompas</button>
                        <p class="hint">iPhone/iPad akan meminta izin gerak. Kalibrasi dengan menggerakkan ponsel
                            membentuk angka 8.</p>
                    </div>

                    <div class="card">
                        <h3>Status</h3>
                        <div class="kv"><span>Lokasi</span><span id="kvCity">—</span></div>
                        <div class="kv"><span>Koordinat</span><span id="kvCoord">—</span></div>
                        <div class="kv"><span>Jarak ke Makkah</span><span id="kvDist">—</span></div>
                        <div class="kv"><span>Arah kiblat</span><span id="kvQibla">—</span></div>
                        <div class="kv"><span>Akurasi sensor</span><span id="kvAcc">—</span></div>
                    </div>
                </div>

                <p class="about">
                    Arah kiblat dihitung dengan rumus <i>great-circle bearing</i> dari koordinat Anda ke Ka'bah
                    (21,4225° LU / 39,8262° BT). Jadwal sholat bersumber dari API Kemenag melalui api.myquran.com
                    dan disimpan di perangkat agar tetap terbaca saat offline. Arah kompas mengikuti utara magnetik;
                    deklinasi di Indonesia kecil (&lt; 2°).
                </p>
                <p class="about" style="padding-top:6px">
                    Foto Masjidil Haram: <b>Saudipics.com</b> — CC BY-SA 4.0, via Wikimedia Commons.
                </p>
            </section>
