@section('styles')
    <link href="{{ $themeCss }}" rel="stylesheet">
@endsection
<div>
    <div class="container">
        <div class="prayer-times">
            @foreach ($prayerTimes as $prayer)
                <div
                    class="prayer-time {{ $loop->index === $activeIndex ? 'active' : '' }} {{ $loop->index === $nextPrayerIndex ? 'next-prayer' : '' }}">
                    <div class="prayer-icon">
                        @if ($prayer['icon'] === 'sun')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-sun">
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
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-sunrise">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 17h1m16 0h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7m-9.7 5.7a4 4 0 0 1 8 0" />
                                <path d="M3 21l18 0" />
                                <path d="M12 9v-6l3 3m-6 0l3 -3" />
                            </svg>
                        @elseif ($prayer['icon'] === 'hazemoon')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-haze-moon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 16h18" />
                                <path d="M3 20h18" />
                                <path
                                    d="M8.296 16c-2.268 -1.4 -3.598 -4.087 -3.237 -6.916c.443 -3.48 3.308 -6.083 6.698 -6.084v.006h.296c-1.991 1.916 -2.377 5.03 -.918 7.405c1.459 2.374 4.346 3.33 6.865 2.275a6.888 6.888 0 0 1 -2.777 3.314" />
                            </svg>
                        @elseif ($prayer['icon'] === 'sunset')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-haze">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
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
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-sun-wind">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14.468 10a4 4 0 1 0 -5.466 5.46" />
                                <path d="M2 12h1" />
                                <path d="M11 3v1" />
                                <path d="M11 20v1" />
                                <path d="M4.6 5.6l.7 .7" />
                                <path d="M17.4 5.6l-.7 .7" />
                                <path d="M5.3 17.7l-.7 .7" />
                                <path d="M15 13h5a2 2 0 1 0 0 -4" />
                                <path d="M12 16h5.714l.253 0a2 2 0 0 1 2.033 2a2 2 0 0 1 -2 2h-.286" />
                            </svg>
                        @elseif ($prayer['icon'] === 'moon')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-moon-stars">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                                <path d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2" />
                                <path d="M19 11h2m-1 -1v2" />
                            </svg>
                        @endif
                    </div>
                    <div class="prayer-info">
                        <div class="prayer-name">{{ $prayer['name'] }} </div>
                        <div class="prayer-time-value">{{ $prayer['time'] }}</div>
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-alarm">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
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

            <div class="mosque-image">
                {{-- Hidden inputs to store slide URLs with default fallback --}}
                @if ($slides)
                    <input type="hidden" id="slide1"
                        value="{{ $slides->slide1 ?? asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide2"
                        value="{{ $slides->slide2 ?? asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide3"
                        value="{{ $slides->slide3 ?? asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide4"
                        value="{{ $slides->slide4 ?? asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide5"
                        value="{{ $slides->slide5 ?? asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide6"
                        value="{{ $slides->slide6 ?? asset('images/other/slide-jws-default.jpg') }}">
                @else
                    <input type="hidden" id="slide1" value="{{ asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide2" value="{{ asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide3" value="{{ asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide4" value="{{ asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide5" value="{{ asset('images/other/slide-jws-default.jpg') }}">
                    <input type="hidden" id="slide6" value="{{ asset('images/other/slide-jws-default.jpg') }}">
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer-message">
            <div class="scrolling-text">
                @if ($marquee)
                    <input type="hidden" id="marquee1" value="{{ $marquee->marquee1 }}">
                    <input type="hidden" id="marquee2" value="{{ $marquee->marquee2 }}">
                    <input type="hidden" id="marquee3" value="{{ $marquee->marquee3 }}">
                    <input type="hidden" id="marquee4" value="{{ $marquee->marquee4 }}">
                    <input type="hidden" id="marquee5" value="{{ $marquee->marquee5 }}">
                    <input type="hidden" id="marquee6" value="{{ $marquee->marquee6 }}">
                @endif
                <p>
                    Matikan ponsel atau gunakan mode senyap saat berada di dalam masjid
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
    <input type="hidden" id="server-timestamp" value="{{ $serverTimestamp }}">
    <input type="hidden" id="prayer-times" value='@json($jadwalSholat)'>
    <input type="hidden" id="current-month" value="{{ $currentMonth }}">
    <input type="hidden" id="current-year" value="{{ $currentYear }}">
    <input type="hidden" id="active-prayer-status"
        value='{{ $activePrayerStatus ? json_encode($activePrayerStatus) : '' }}'>
    <input type="hidden" id="durasi-data" value='{{ $durasi ? json_encode($durasi->toArray()) : '' }}'>
    <input type="hidden" id="current-theme-id" value="{{ $theme ? $theme->id : '' }}">
    <input type="hidden" id="current-theme-updated-at" value="{{ $theme ? $theme->updated_at->timestamp : 0 }}">
    <input type="hidden" id="current-theme-css"
        value="{{ $theme ? asset($theme->css_file) : asset('css/style.css') }}">

    {{-- Hidden inputs for adzan data --}}
    @foreach ($adzanData as $key => $value)
        <input type="hidden" id="{{ $key }}" value="{{ $value }}">
    @endforeach

    {{-- Hidden inputs for petugas data --}}
    @foreach ($petugasData as $key => $value)
        <input type="hidden" id="{{ $key }}" value="{{ $value }}">
    @endforeach

    {{-- Hidden inputs for jumbotron data --}}
    @if ($jumbotron)
        <input type="hidden" id="jumbo1" value="{{ $jumbotron->jumbo1 ?? '' }}">
        <input type="hidden" id="jumbo2" value="{{ $jumbotron->jumbo2 ?? '' }}">
        <input type="hidden" id="jumbo3" value="{{ $jumbotron->jumbo3 ?? '' }}">
        <input type="hidden" id="jumbo4" value="{{ $jumbotron->jumbo4 ?? '' }}">
        <input type="hidden" id="jumbo5" value="{{ $jumbotron->jumbo5 ?? '' }}">
        <input type="hidden" id="jumbo6" value="{{ $jumbotron->jumbo6 ?? '' }}">
        <input type="hidden" id="jumbo_is_active" value="{{ $jumbotron->is_active ? 'true' : 'false' }}">
    @else
        <input type="hidden" id="jumbo1" value="">
        <input type="hidden" id="jumbo2" value="">
        <input type="hidden" id="jumbo3" value="">
        <input type="hidden" id="jumbo4" value="">
        <input type="hidden" id="jumbo5" value="">
        <input type="hidden" id="jumbo6" value="">
        <input type="hidden" id="jumbo_is_active" value="false">
    @endif

    {{-- Hidden inputs for audio data --}}
    @if ($audio)
        <input type="hidden" id="audio1" value="{{ $audio->audio1 ?? '' }}">
        <input type="hidden" id="audio2" value="{{ $audio->audio2 ?? '' }}">
        <input type="hidden" id="audio3" value="{{ $audio->audio3 ?? '' }}">
        <input type="hidden" id="audio_status" value="{{ $audio->status ? 'true' : 'false' }}">
    @else
        <input type="hidden" id="audio1" value="">
        <input type="hidden" id="audio2" value="">
        <input type="hidden" id="audio3" value="">
        <input type="hidden" id="audio_status" value="false">
    @endif

    {{-- Hidden inputs for adzan-audio data --}}
    @if ($adzanaudio)
        <input type="hidden" id="adzan_audio"
            value="{{ $adzanaudio->audioadzan ? asset($adzanaudio->audioadzan) : '' }}">
        <input type="hidden" id="adzan_shubuh"
            value="{{ $adzanaudio->adzanshubuh ? asset($adzanaudio->adzanshubuh) : '' }}">
        <input type="hidden" id="adzan_status" value="{{ $adzanaudio->status ? 'true' : 'false' }}">
    @else
        <input type="hidden" id="adzan_audio" value="">
        <input type="hidden" id="adzan_shubuh" value="">
        <input type="hidden" id="adzan_status" value="false">
    @endif

    {{-- Jumbotron Banner --}}
    <div id="jumbotronImage" class="jumbotron-image" style="display: none;">
        <img src="{{ asset('theme/static/logo.webp') }}" alt="Logo" class="jumbotron-logo">
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
    <div id="adzanPopup" class="adzan-popup" style="display: none;">
        <div class="adzan-popup-content">
            <div class="adzan">Waktunya Azan</div>
            <h2 id="adzanTitle" class="adzan-title"></h2>
            <div class="progress-container">
                <div id="adzanProgress" class="progress-bar"></div>
            </div>
            <div id="adzanCountdown" class="countdown"></div>
            <div class="logo-popup">
                <img src="{{ asset('theme/static/logo.webp') }}" alt="Logo">
            </div>
        </div>
    </div>

    {{-- Iqomah Popup --}}
    <div id="iqomahPopup" class="iqomah-popup" style="display: none;">
        <div class="iqomah-popup-content">
            <h2 class="iqomah-title">Iqomah</h2>
            <div class="iqomah-progress-container">
                <div id="iqomahProgress" class="iqomah-progress-bar"></div>
            </div>
            <div id="iqomahCountdown" class="iqomah-countdown"></div>
            <div class="logo-popup">
                <img src="{{ asset('theme/static/logo.webp') }}" alt="Logo">
            </div>
        </div>
        <div class="iqomah-image">
            <div id="currentIqomahImage" class="iqomah-image"></div>
        </div>
    </div>

    {{-- Friday Info Popup --}}
    {{-- DEBUG: Popup ini hanya muncul pada hari Jumat saat waktu Zuhur --}}
    {{-- DEBUG: Untuk testing, uncomment baris di bawah untuk memaksa tampil --}}
    {{-- <div id="fridayInfoPopup" class="friday-info-popup" style="display: flex;"> --}}
    <div id="fridayInfoPopup" class="friday-info-popup" style="display: none;">
        <div class="friday-info-content">
            <div id="fridayDate" class="friday-date"></div>
            <div id="fridayOfficials" class="friday-officials"></div>
            <div class="digital-clock">
                <span style="text-align: top">JAM</span>
                <span class="clock-time">00:00:00</span>
            </div>
            @if ($petugas)
                <input type="hidden" id="khatib" value="{{ $petugas->khatib }}">
                <input type="hidden" id="imam" value="{{ $petugas->imam }}">
                <input type="hidden" id="muadzin" value="{{ $petugas->muadzin }}">
            @endif
            <div class="logo-popup">
                <img src="{{ asset('theme/static/logo.webp') }}" alt="Logo">
            </div>
        </div>
        <div class="friday-image">
            <div id="currentFridayImage" class="currentFridayImage"></div>
        </div>
    </div>

    {{-- DEBUG: Button untuk testing Friday Info Popup (hapus setelah selesai debug) --}}
    {{-- <button onclick="testFridayPopup()"
        style="position: fixed; top: 10px; right: 10px; z-index: 9999; background: red; color: white; padding: 10px; border: none; cursor: pointer;">Test
        Friday Popup</button> --}}

    {{-- Adzan Image Display --}}
    <div id="adzanImageDisplay" class="adzan-image-display" style="display: none;">
        <img id="currentAdzanImage" src="" alt="Adzan Image">
    </div>
</div>
