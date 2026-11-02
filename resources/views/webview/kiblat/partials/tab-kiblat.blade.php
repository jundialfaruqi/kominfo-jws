            <!-- ================= KIBLAT ================= -->
            <section class="screen screen--dark" id="scrKiblat" hidden>
                <div class="qibla-head">
                    <span class="qibla-title">Arah Kiblat</span>
                    <span class="qibla-city" id="cityKiblat">—</span>
                </div>

                <div class="compass-wrap" id="wrap">
                    <div class="fixed-mark"></div>
                    <div class="dial" id="dial"></div>
                    <img class="compass-logo" src="{{ asset('theme/static/diskominfotiksan-logo-small.webp') }}"
                        alt="Diskominfotiksan Kota Pekanbaru" />
                    <div class="needle" id="needle"></div>
                    <div class="hub">🕋</div>
                </div>

                <div class="qibla-read">
                    <b id="bearingVal">—</b>
                    <span id="bearingDir">Pilih lokasi dulu</span>
                </div>

                <div class="verdict" id="verdict">Sentuh kompas untuk mengaktifkan sensor, atau seret untuk
                    mengarahkan manual.</div>
            </section>
