<div>
    <div class="container">
        <div class="prayer-times">
            @foreach ($prayerTimes as $prayer)
                <div
                    class="prayer-time {{ $loop->index === $activeIndex ? 'active' : '' }} {{ $loop->index === $nextPrayerIndex ? 'next-prayer' : '' }}">
                    <div class="prayer-icon">
                        @if ($prayer['icon'] === 'sun')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-sun">
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-sunrise">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 17h1m16 0h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7m-9.7 5.7a4 4 0 0 1 8 0" />
                                <path d="M3 21l18 0" />
                                <path d="M12 9v-6l3 3m-6 0l3 -3" />
                            </svg>
                        @elseif ($prayer['icon'] === 'hazemoon')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-haze-moon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 16h18" />
                                <path d="M3 20h18" />
                                <path
                                    d="M8.296 16c-2.268 -1.4 -3.598 -4.087 -3.237 -6.916c.443 -3.48 3.308 -6.083 6.698 -6.084v.006h.296c-1.991 1.916 -2.377 5.03 -.918 7.405c1.459 2.374 4.346 3.33 6.865 2.275a6.888 6.888 0 0 1 -2.777 3.314" />
                            </svg>
                        @elseif ($prayer['icon'] === 'sunset')
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-haze">
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
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

        <!-- Floating Clock -->
        <div class="floating-clock">
            <div class="clock-container">
                <canvas id="analogClock" width="324" height="324"></canvas>
                <div class="clock-text">Loading...</div>
            </div>
        </div>

        <!-- Right Content - sekarang full -->
        <div class="right-content">
            @livewire('firdaus.mosque-info', ['slug' => request()->route('slug')])

            <div class="date-info">
                <ul>
                    <li class="date-item"></li>
                </ul>
            </div>


            <div class="mosque-image">
                <!-- Hidden inputs to store slide URLs -->
                @if ($slides)
                    <input type="hidden" id="slide1" value="{{ $slides->slide1 }}">
                    <input type="hidden" id="slide2" value="{{ $slides->slide2 }}">
                    <input type="hidden" id="slide3" value="{{ $slides->slide3 }}">
                    <input type="hidden" id="slide4" value="{{ $slides->slide4 }}">
                    <input type="hidden" id="slide5" value="{{ $slides->slide5 }}">
                    <input type="hidden" id="slide6" value="{{ $slides->slide6 }}">
                @endif
                <div class="countdown-timer">
                    <div class="countdown-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-clock">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <div class="countdown-text">
                        <span id="next-prayer-label">Maghrib</span>
                        <span>&nbsp; - </span>
                        <span id="countdown-value">-01:04:00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
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
                <p>-</p>
            </div>
        </div>
    </div>
    <input type="hidden" id="server-timestamp" value="{{ $serverTimestamp }}">
    <input type="hidden" id="prayer-times" value='@json($jadwalSholat)'>
    <input type="hidden" id="current-month" value="{{ $currentMonth }}">
    <input type="hidden" id="current-year" value="{{ $currentYear }}">
    <input type="hidden" id="active-prayer-status"
        value='{{ $activePrayerStatus ? json_encode($activePrayerStatus) : '' }}'>

    <!-- Hidden inputs for adzan data -->
    @foreach ($adzanData as $key => $value)
        <input type="hidden" id="{{ $key }}" value="{{ $value }}">
    @endforeach

    <!-- Hidden inputs for petugas data -->
    @foreach ($petugasData as $key => $value)
        <input type="hidden" id="{{ $key }}" value="{{ $value }}">
    @endforeach

    <!-- Adzan Popup -->
    <div id="adzanPopup" class="adzan-popup" style="display: none;">
        <div class="adzan-popup-content">
            <div class="adzan">Waktunya Azan</div>
            <h2 id="adzanTitle" class="adzan-title"></h2>
            <div class="progress-container">
                <div id="adzanProgress" class="progress-bar"></div>
            </div>
            <div id="adzanCountdown" class="countdown"></div>
        </div>
    </div>

    <!-- Iqomah Popup -->
    <div id="iqomahPopup" class="iqomah-popup" style="display: none;">
        <div class="iqomah-popup-content">
            <h2 class="iqomah-title">Iqomah</h2>
            <div class="iqomah-progress-container">
                <div id="iqomahProgress" class="iqomah-progress-bar"></div>
            </div>
            <div id="iqomahCountdown" class="iqomah-countdown"></div>
        </div>
        <div class="iqomah-image">
            <img id="currentIqomahImage" src="{{ asset('images/other/matikan-hp.jpg') }}" alt="Iqomah Image">
        </div>
    </div>

    <!-- Friday Info Popup -->
    <div id="fridayInfoPopup" class="friday-info-popup" style="display: none;">
        <div class="friday-info-content">
            <div id="fridayDate" class="friday-date"></div>
            <div id="fridayOfficials" class="friday-officials"></div>
        </div>
        <div class="friday-image">
            <img id="currentFridayImage" src="{{ asset('images/other/matikan-hp.jpg') }}" alt="Friday Image">
        </div>
    </div>

    <!-- Adzan Image Display -->
    <div id="adzanImageDisplay" class="adzan-image-display" style="display: none;">
        <img id="currentAdzanImage" src="" alt="Adzan Image">
    </div>
</div>
