@section('styles')
    <link href="{{ $themeCss }}" rel="stylesheet">
@endsection
<div>
    {{-- @dd($newSlider->count()) --}}
    <div class="container">
        <div class="prayer-times">
            @foreach ($prayerTimes as $prayer)
                <div
                    class="prayer-time {{ $loop->index === $activeIndex ? 'active' : '' }} {{ $loop->index === $nextPrayerIndex ? 'next-prayer' : '' }}">
                    <div class="prayer-icon">
                        @if ($prayer['icon'] === 'sun')
                            <svg class="lucide lucide-sun"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4" />
                                <path d="M12 2v2" />
                                <path d="M12 20v2" />
                                <path d="m4.93 4.93 1.41 1.41" />
                                <path d="m17.66 17.66 1.41 1.41" />
                                <path d="M2 12h2" />
                                <path d="M20 12h2" />
                                <path d="m6.34 17.66-1.41 1.41" />
                                <path d="m19.07 4.93-1.41 1.41" />
                            </svg>
                        @elseif ($prayer['icon'] === 'sunrise')
                            <svg class="icon icon-tabler icons-tabler-outline icon-tabler-sunrise"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"
                                    fill="none" />
                                <path
                                    d="M3 17h1m16 0h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7m-9.7 5.7a4 4 0 0 1 8 0" />
                                <path d="M3 21l18 0" />
                                <path d="M12 9v-6l3 3m-6 0l3 -3" />
                            </svg>
                        @elseif ($prayer['icon'] === 'hazemoon')
                            <svg class="icon icon-tabler icons-tabler-outline icon-tabler-haze-moon"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"
                                    fill="none" />
                                <path d="M3 16h18" />
                                <path d="M3 20h18" />
                                <path
                                    d="M8.296 16c-2.268 -1.4 -3.598 -4.087 -3.237 -6.916c.443 -3.48 3.308 -6.083 6.698 -6.084v.006h.296c-1.991 1.916 -2.377 5.03 -.918 7.405c1.459 2.374 4.346 3.33 6.865 2.275a6.888 6.888 0 0 1 -2.777 3.314" />
                            </svg>
                        @elseif ($prayer['icon'] === 'sunset')
                            <svg class="icon icon-tabler icons-tabler-outline icon-tabler-haze"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"
                                    fill="none" />
                                <path d="M3 12h1" />
                                <path d="M12 3v1" />
                                <path d="M20 12h1" />
                                <path d="M5.6 5.6l.7 .7" />
                                <path d="M18.4 5.6l-.7 .7" />
                                <path d="M8 12a4 4 0 1 1 8 0" />
                                <path d="M3 16h18" />
                                <path d="M3 20h18" />
                            </svg>
                        @elseif ($prayer['icon'] === 'sunwind')
                            <svg class="icon icon-tabler icons-tabler-outline icon-tabler-sun-wind"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"
                                    fill="none" />
                                <path d="M14.468 10a4 4 0 1 0 -5.466 5.46" />
                                <path d="M2 12h1" />
                                <path d="M11 3v1" />
                                <path d="M11 20v1" />
                                <path d="M4.6 5.6l.7 .7" />
                                <path d="M17.4 5.6l-.7 .7" />
                                <path d="M5.3 17.7l-.7 .7" />
                                <path d="M15 13h5a2 2 0 1 0 0 -4" />
                                <path
                                    d="M12 16h5.714l.253 0a2 2 0 0 1 2.033 2a2 2 0 0 1 -2 2h-.286" />
                            </svg>
                        @elseif ($prayer['icon'] === 'moon')
                            <svg class="icon icon-tabler icons-tabler-outline icon-tabler-moon-stars"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"
                                    fill="none" />
                                <path
                                    d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                                <path
                                    d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2" />
                                <path d="M19 11h2m-1 -1v2" />
                            </svg>
                        @endif
                    </div>
                    <div class="prayer-info">
                        <div class="prayer-name">{{ $prayer['name'] }} </div>
                        <div class="prayer-time-value">{{ $prayer['time'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Floating Clock --}}
        <div class="floating-clock">
            <div class="clock-container">
                <canvas id="analogClock" width="300" height="300"></canvas>
                <div class="clock-text">Loading...</div>
            </div>
        </div>

        {{-- Right Content - sekarang full --}}
        <div class="right-content">
            @livewire('firdaus.mosque-info', ['slug' => request()->route('slug')])

            <div class="date-info">
                <div class="next-adzan">
                    <div class="countdown-icon">
                        <svg class="icon icon-tabler icons-tabler-outline icon-tabler-alarm"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"
                                fill="none" />
                            <path
                                d="M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M12 10l0 3l2 0" />
                            <path d="M7 4l-2.75 2" />
                            <path d="M17 4l2.75 2" />
                        </svg>
                    </div>
                    <div class="countdown-text">
                        <span id="next-prayer-label">Maghrib</span>
                        <span>&nbsp;-</span>
                        <span id="countdown-value">-01:04:00</span>
                    </div>
                </div>
                <span class="date-item"></span>
            </div>

            <!-- Finance Overlay (floating, judul tetap, konten scroll) -->
            {{-- <div id="financeOverlay" class="finance-overlay" style="display: none;">
                <div class="finance-title" id="financePeriodTitle">Memuat data keuangan…</div>
                <div class="finance-scroll-container" id="financeScrollContainerAll">
                    <div class="finance-scroll-content" id="financeScrollContentAll">
                        <div class="finance-totals">
                            <div class="total-pill-container">
                                <div class="total-pill masuk">
                                    <span class="label">Total Uang Masuk</span>
                                    <span class="value" id="financeTotalMasukValue">-</span>
                                </div>
                                <div class="total-pill keluar">
                                    <span class="label">Total Uang Keluar</span>
                                    <span class="value" id="financeTotalKeluarValue">-</span>
                                </div>
                                <div class="total-pill saldo">
                                    <span class="label">Total Saldo</span>
                                    <span class="value" id="financeEndingBalanceValue">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="finance-scroll-content" id="financeTopCategoriesList"></div>
                    </div>
                </div>
            </div> --}}

            <div class="mosque-image">
                {{-- Mosque images with object-fit stretch --}}
                @if ($newSlider && $newSlider->count() > 0)
                    {{-- <img id="slide1" src="{{ $slides->slide1 ?? asset('images/other/slide-jws-default.jpg') }}"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;" alt="Slide 1">
                    <img id="slide2" src="{{ $slides->slide2 ?? asset('images/other/slide-jws-default.jpg') }}"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;" alt="Slide 2">
                    <img id="slide3" src="{{ $slides->slide3 ?? asset('images/other/slide-jws-default.jpg') }}"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;" alt="Slide 3">
                    <img id="slide4" src="{{ $slides->slide4 ?? asset('images/other/slide-jws-default.jpg') }}"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;" alt="Slide 4">
                    <img id="slide5" src="{{ $slides->slide5 ?? asset('images/other/slide-jws-default.jpg') }}"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;" alt="Slide 5">
                    <img id="slide6" src="{{ $slides->slide6 ?? asset('images/other/slide-jws-default.jpg') }}"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;" alt="Slide 6"> --}}

                    @foreach ($newSlider as $index => $slide)
                        <img id="slide{{ $index + 1 }}"
                            src="{{ $slide->path ? asset($slide->path) : asset('images/other/slide-jws-default.jpg') }}"
                            alt="Slide {{ $index + 1 }}"
                            style="object-fit: stretch; width: 100%; height: 100%; display: none;">
                    @endforeach
                @else
                    <img id="slide1"
                        src="{{ asset('images/other/slide-jws-default.jpg') }}"
                        alt="Slide 1"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;">
                    <img id="slide2"
                        src="{{ asset('images/other/slide-jws-default.jpg') }}"
                        alt="Slide 2"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;">
                    <img id="slide3"
                        src="{{ asset('images/other/slide-jws-default.jpg') }}"
                        alt="Slide 3"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;">
                    <img id="slide4"
                        src="{{ asset('images/other/slide-jws-default.jpg') }}"
                        alt="Slide 4"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;">
                    <img id="slide5"
                        src="{{ asset('images/other/slide-jws-default.jpg') }}"
                        alt="Slide 5"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;">
                    <img id="slide6"
                        src="{{ asset('images/other/slide-jws-default.jpg') }}"
                        alt="Slide 6"
                        style="object-fit: stretch; width: 100%; height: 100%; display: none;">
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer-message">
            <div class="scrolling-text">
                @if ($marquee)
                    <input id="marquee1" type="hidden"
                        value="{{ $marquee->marquee1 }}">
                    <input id="marquee2" type="hidden"
                        value="{{ $marquee->marquee2 }}">
                    <input id="marquee3" type="hidden"
                        value="{{ $marquee->marquee3 }}">
                    <input id="marquee4" type="hidden"
                        value="{{ $marquee->marquee4 }}">
                    <input id="marquee5" type="hidden"
                        value="{{ $marquee->marquee5 }}">
                    <input id="marquee6" type="hidden"
                        value="{{ $marquee->marquee6 }}">
                @endif
                <p>
                    Matikan ponsel atau gunakan mode senyap saat berada di dalam
                    masjid
                    <span class="separator">•</span>
                    Mohon menjaga kebersihan di dalam masjid
                    <span class="separator">•</span>
                    Berdoalah untuk umat Islam
                    <span class="separator">•</span>
                    Shalatlah berjamaah untuk mendapatkan lebih banyak pahala
                    <span class="separator">•</span>
                    Ingatlah untuk berdonasi demi pemeliharaan masjid
                    <span class="separator">•</span>
                    Harap gunakan rak sepatu yang telah disediakan
                </p>
            </div>
        </div>
    </div>

    {{-- Hidden inputs for prayer times and other data --}}
    <input id="server-timestamp" type="hidden"
        value="{{ $serverTimestamp }}">
    <input id="prayer-times" type="hidden"
        value='@json($jadwalSholat)'>
    <input id="current-month" type="hidden" value="{{ $currentMonth }}">
    <input id="current-year" type="hidden" value="{{ $currentYear }}">
    <input id="active-prayer-status" type="hidden"
        value='{{ $activePrayerStatus ? json_encode($activePrayerStatus) : '' }}'>
    <input id="durasi-data" type="hidden"
        value='{{ $durasi ? json_encode($durasi->toArray()) : '' }}'>
    <input id="current-theme-id" type="hidden"
        value="{{ $theme ? $theme->id : '' }}">
    <input id="current-theme-updated-at" type="hidden"
        value="{{ $theme ? $theme->updated_at->timestamp : 0 }}">
    <input id="current-theme-css" type="hidden"
        value="{{ $theme ? asset($theme->css_file) : asset('css/style.css') }}">

    {{-- Hidden inputs for adzan data --}}
    @foreach ($adzanData as $key => $value)
        <input id="{{ $key }}" type="hidden"
            value="{{ $value }}">
    @endforeach

    {{-- Hidden inputs for petugas data --}}
    @foreach ($petugasData as $key => $value)
        <input id="{{ $key }}" type="hidden"
            value="{{ $value }}">
    @endforeach

    {{-- Hidden inputs for jumbotron data --}}
    @if ($jumbotron)
        <input id="jumbo1" type="hidden"
            value="{{ $jumbotron->jumbo1 ?? '' }}">
        <input id="jumbo2" type="hidden"
            value="{{ $jumbotron->jumbo2 ?? '' }}">
        <input id="jumbo3" type="hidden"
            value="{{ $jumbotron->jumbo3 ?? '' }}">
        <input id="jumbo4" type="hidden"
            value="{{ $jumbotron->jumbo4 ?? '' }}">
        <input id="jumbo5" type="hidden"
            value="{{ $jumbotron->jumbo5 ?? '' }}">
        <input id="jumbo6" type="hidden"
            value="{{ $jumbotron->jumbo6 ?? '' }}">
        <input id="jumbo_is_active" type="hidden"
            value="{{ $jumbotron->is_active ? 'true' : 'false' }}">
    @else
        <input id="jumbo1" type="hidden" value="">
        <input id="jumbo2" type="hidden" value="">
        <input id="jumbo3" type="hidden" value="">
        <input id="jumbo4" type="hidden" value="">
        <input id="jumbo5" type="hidden" value="">
        <input id="jumbo6" type="hidden" value="">
        <input id="jumbo_is_active" type="hidden" value="false">
    @endif

    {{-- Hidden inputs for audio data --}}
    @if ($audio)
        <input id="audio1" type="hidden"
            value="{{ $audio->audio1 ?? '' }}">
        <input id="audio2" type="hidden"
            value="{{ $audio->audio2 ?? '' }}">
        <input id="audio3" type="hidden"
            value="{{ $audio->audio3 ?? '' }}">
        <input id="audio_status" type="hidden"
            value="{{ $audio->status ? 'true' : 'false' }}">
    @else
        <input id="audio1" type="hidden" value="">
        <input id="audio2" type="hidden" value="">
        <input id="audio3" type="hidden" value="">
        <input id="audio_status" type="hidden" value="false">
    @endif

    {{-- Hidden inputs for adzan-audio data --}}
    @if ($adzanaudio)
        <input id="adzan_audio" type="hidden"
            value="{{ $adzanaudio->audioadzan ? asset($adzanaudio->audioadzan) : '' }}">
        <input id="adzan_shubuh" type="hidden"
            value="{{ $adzanaudio->adzanshubuh ? asset($adzanaudio->adzanshubuh) : '' }}">
        <input id="adzan_status" type="hidden"
            value="{{ $adzanaudio->status ? 'true' : 'false' }}">
    @else
        <input id="adzan_audio" type="hidden" value="">
        <input id="adzan_shubuh" type="hidden" value="">
        <input id="adzan_status" type="hidden" value="false">
    @endif

    {{-- Jumbotron Banner --}}
    <div class="jumbotron-image" id="jumbotronImage" style="display: none;">
        <img class="jumbotron-logo"
            src="{{ asset('theme/static/logo.webp') }}" alt="Logo">
        <div class="jumbotron-countdown">
            <span id="jumbotron-next-prayer-label"></span>
            <span> - </span>
            <span id="jumbotron-countdown-value"></span>
        </div>
        <div class="jumbotron-digital-clock">
            <span>JAM</span>
            <span id="jumbotron-clock-time"></span>
        </div>
        <div class="jumbotron-progress-container">
            <div class="jumbotron-progress-bar"></div>
        </div>
    </div>

    {{-- Adzan Popup --}}
    <div class="adzan-popup" id="adzanPopup" style="display: none;">
        <div class="adzan-popup-content">
            <div class="adzan" id="adzanLabel">Waktunya Adzan</div>
            <h2 class="adzan-title" id="adzanTitle"></h2>
            <div class="progress-container">
                <div class="progress-bar" id="adzanProgress"></div>
            </div>
            <div class="countdown" id="adzanCountdown"></div>
            <div class="logo-popup">
                <img src="{{ asset('theme/static/logo.webp') }}"
                    alt="Logo">
            </div>
        </div>
    </div>

    {{-- Iqomah Popup --}}
    <div class="iqomah-popup" id="iqomahPopup" style="display: none;">
        <div class="iqomah-popup-content">
            <h2 class="iqomah-title">Iqomah</h2>
            <div class="iqomah-progress-container">
                <div class="iqomah-progress-bar" id="iqomahProgress"></div>
            </div>
            <div class="iqomah-countdown" id="iqomahCountdown"></div>
            <div class="logo-popup">
                <img src="{{ asset('theme/static/logo.webp') }}"
                    alt="Logo">
            </div>
        </div>
        <div class="iqomah-image">
            <div class="iqomah-image" id="currentIqomahImage"></div>
        </div>
    </div>

    {{-- Friday Info Popup --}}
    {{-- DEBUG: Paksa Friday Info Popup tampil untuk testing UI --}}
    {{-- CARA: Ubah style ke display:flex agar terlihat. Kembalikan ke display:none setelah selesai debug. --}}
    <div class="friday-info-popup" id="fridayInfoPopup"
        style="display: none;">
        <div class="friday-info-content">
            <div class="friday-date" id="fridayDate"></div>
            <div class="friday-officials" id="fridayOfficials"></div>
            <div class="digital-clock">
                <span style="text-align: top">JAM</span>
                <span class="clock-time">00:00:00</span>
            </div>
            <input id="khatib" type="hidden"
                value="{{ $petugas->khatib ?? '' }}">
            <input id="imam" type="hidden"
                value="{{ $petugas->imam ?? '' }}">
            <input id="muadzin" type="hidden"
                value="{{ $petugas->muadzin ?? '' }}">
            <div class="logo-popup">
                <img src="{{ asset('theme/static/logo.webp') }}"
                    alt="Logo">
            </div>
        </div>
        <div class="friday-image">
            <div class="currentFridayImage" id="currentFridayImage"></div>
        </div>
    </div>

    {{-- Adzan Image Display --}}
    <div class="adzan-image-display" id="adzanImageDisplay"
        style="display: none;">
        <img id="currentAdzanImage"
            src="{{ asset('images/other/lurus-rapat-shaf-default.webp') }}"
            alt="Adzan Image">
    </div>
</div>
