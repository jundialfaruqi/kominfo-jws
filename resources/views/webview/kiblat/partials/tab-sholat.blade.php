            <!-- ================= BERANDA: SHOLAT ================= -->
            <section class="screen screen--sky" id="scrSholat">
                <div class="hero">
                    <div class="date-block">
                        <div class="greg" id="gregDate">—</div>
                        <div class="hijri" id="hijriDate">—</div>
                    </div>

                    <div class="np">
                        <div class="np-label" data-cd-label>Menunggu jadwal</div>
                        <div class="np-row">
                            <span class="np-time" data-cd-time>--:--</span>
                            <span class="np-remain" data-cd-remain>--:--:--</span>
                        </div>
                    </div>
                </div>

                <figure class="haram" id="haramBanner">
                    <img src="@include('webview.kiblat.partials.haram-image-data')"
                        alt="Masjidil Haram, Makkah" />
                    <div class="haram-status" id="haramStatus" hidden>
                        <div class="hs-top" id="hsTop">WAKTUNYA ADZAN</div>
                        <div class="hs-name" id="hsName">MAGHRIB</div>
                        <div class="hs-sub" id="hsSub" hidden>MENUJU ADZAN SHUBUH</div>
                        <div class="hs-bar-track">
                            <div class="hs-bar-fill" id="hsBarFill"></div>
                        </div>
                        <div class="hs-bottom" id="hsBottom">04:00</div>
                    </div>
                    <figcaption id="haramCaption">Masjidil Haram · Makkah</figcaption>
                </figure>

                <div class="grid-head">
                    <h2>Jadwal Sholat</h2>
                    <button class="city-link" id="cityLink">Pilih lokasi</button>
                </div>

                <div class="pgrid" id="prayerGrid"></div>

                <p class="src" id="jadwalNote">Sumber jadwal: <b>Kemenag</b> (api.myquran.com)</p>
            </section>
