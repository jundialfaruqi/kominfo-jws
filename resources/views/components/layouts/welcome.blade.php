<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>{{ $title }}</title>
    @include('components.layouts.welcomestyle')
    @livewireStyles
</head>

<body>
    @php
        $getFirstImage = function ($content) {
            if (empty($content)) {
                return asset('nav-brand.png');
            }
            $doc = new DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
            $images = $doc->getElementsByTagName('img');
            if ($images->length > 0) {
                return $images->item(0)->getAttribute('src');
            }
            return asset('nav-brand.png'); // Fallback image
        };
    @endphp
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-decoration-none" href="#">
                <img src="{{ asset('nav-brand.png') }}" width="35" alt="JWS Pekanbaru" class="me-2">
                <div class="d-flex flex-column justify-content-center align-items-start">
                    <span class="fw-bold lh-1" style="font-size: 1.1rem;">JWS Pekanbaru</span>
                    <span class="text-muted lh-1 mt-1"
                        style="font-size: 0.65rem; font-family: 'PlusJakartaSansText', sans-serif; letter-spacing: 0.2px;">Jadwal
                        Waktu Sholat Kota Pekanbaru</span>
                </div>
            </a>
            <div class="ms-auto d-none d-lg-block">
                @auth
                    <a class="nav-ghost-btn" href="{{ route('dashboard.index') }}">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>
                @else
                    <a class="nav-ghost-btn" href="{{ route('login') }}">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        <section class="hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h1 class="lh-1 mb-4 fw-bold">
                            Jadwal Waktu Sholat Pemerintah Kota Pekanbaru
                        </h1>
                        <p class="mb-4">Aplikasi resmi untuk masjid-masjid paripurna di Kota Pekanbaru yang
                            menampilkan jadwal sholat, pengingat adzan dan iqomah, kalender hijriah, serta sarana
                            penyampaian pesan resmi Pemerintah Kota kepada seluruh masyarakat Kota Pekanbaru melalui
                            Masjid
                            Paripurna.</p>
                        <div class="d-flex flex-wrap align-items-center">
                            @if (!empty($showScheduleBtn) && !empty($scheduleUrl))
                                <a class="btn btn-gov-blue btn-lg rounded-4 me-3 mb-2" href="{{ $scheduleUrl }}">Lihat
                                    JWS Saya</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="{{ asset('welcome/assets/img/walikota-wakil-walikota-pekanbaru.webp') }}"
                            class="img-fluid hero-composite" alt="Walikota dan Wakil Walikota Pekanbaru" />
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Jadwal sholat hari ini (card + countdown) --}}
        <section class="py-5">
            <div class="container">
                <div
                    class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end mb-4 border-bottom pb-3">
                    @php
                        $todayLabel = \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y');
                    @endphp
                    <div>
                        <h2 class="header-title mb-2" style="font-size: 2rem;">
                            Jadwal Sholat Hari Ini
                        </h2>
                        <div class="header-date-time d-flex align-items-center gap-2 text-muted">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ $todayLabel }}</span>
                            <span class="mx-2">|</span>
                            <div class="d-flex align-items-center gap-1">
                                <i class="fa-regular fa-clock clock-icon"></i>
                                <span class="font-led clock-text" id="current-time">--:--:--</span>
                            </div>
                        </div>
                    </div>

                    @if (!empty($nextPrayer) && !empty($nextPrayerAtIso))
                        <div id="countdown" class="text-lg-end mt-4 mt-lg-0" data-next-iso="{{ $nextPrayerAtIso }}">
                            <div class="text-muted text-uppercase fw-bold countdown-next-label"
                                style="letter-spacing: 0.05em; font-family: 'PlusJakartaSansDisplay', sans-serif;">
                                <i class="fa-regular fa-bell me-1"></i> Menuju {{ ucfirst($nextPrayer) }}
                            </div>
                            <div class="font-led mt-1 countdown-next-time" id="countdown-text"
                                style="color: #0071e3; line-height: 1;">--:--:--</div>
                        </div>
                    @endif
                </div>

                @php
                    $icons = [
                        'imsak' => 'fa-solid fa-clock',
                        'subuh' => 'fa-solid fa-moon',
                        'terbit' => 'fa-solid fa-sun',
                        'dhuha' => 'fa-solid fa-sun',
                        'dzuhur' => 'fa-solid fa-sun',
                        'ashar' => 'fa-solid fa-cloud-sun',
                        'maghrib' => 'fa-solid fa-moon',
                        'isya' => 'fa-solid fa-moon',
                    ];
                    $order = ['imsak', 'subuh', 'terbit', 'dhuha', 'dzuhur', 'ashar', 'maghrib', 'isya'];
                @endphp

                <div class="row g-3">
                    @foreach ($order as $key)
                        @php
                            $item = $todayTimes[$key] ?? null;
                            $isActive = !empty($activePrayer) && $activePrayer === $key;
                        @endphp
                        <div class="col-6 col-md-3 col-lg-3">
                            <div class="card prayer-card rounded-4 {{ $isActive ? 'active' : '' }}">
                                <div class="card-body d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="prayer-label">{{ $item['label'] ?? ucfirst($key) }}</div>
                                        <div class="prayer-time {{ $isActive ? 'active' : '' }}">
                                            {{ $item['time'] ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="prayer-icon">
                                        <i class="{{ $icons[$key] }}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>

        <section class="py-5">
            <div class="container">
                @php
                    $minDate = '';
                    $maxDate = '';
                    if (!empty($jadwalSholat)) {
                        $minDate = collect($jadwalSholat)->first()['date'] ?? '';
                        $maxDate = collect($jadwalSholat)->last()['date'] ?? '';
                    }
                @endphp
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3">
                    <div>
                        <h2 class="mb-1 text-gov-dark fw-bold">Jadwal Sholat Kota Pekanbaru - {{ $monthName ?? '' }}
                            {{ $yearNumber ?? '' }}</h2>
                    </div>
                    <div class="mt-3 mt-md-0 d-flex gap-2 align-items-center">
                        <label for="filterDate" class="text-muted small fw-semibold mb-0"
                            style="white-space: nowrap;">Cari Tanggal:</label>
                        <input type="date" id="filterDate" class="form-control form-control-sm rounded-pill px-3"
                            style="width: auto; border-color: #d2d2d7; font-family: 'PlusJakartaSansText', sans-serif;"
                            min="{{ $minDate }}" max="{{ $maxDate }}">
                        <button type="button" id="resetFilterBtn" class="btn btn-sm btn-outline-secondary rounded-pill"
                            style="display: none;">Reset</button>
                    </div>
                </div>

                @if (!empty($jadwalSholat))
                    <div class="schedule-table">
                        <div class="table-responsive">
                            <table
                                class="table table-hover table-sm rounded-4 overflow-hidden align-middle schedule-month-table">
                                <thead>
                                    <tr>
                                        <th class="text-gov-dark">Hari</th>
                                        <th class="text-gov-dark">Tanggal</th>
                                        <th class="text-center">Imsak</th>
                                        <th class="text-center">Subuh</th>
                                        <th class="text-center">Terbit</th>
                                        <th class="text-center">Dhuha</th>
                                        <th class="text-center">Dzuhur</th>
                                        <th class="text-center">Ashar</th>
                                        <th class="text-center">Maghrib</th>
                                        <th class="text-center">Isya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwalSholat as $row)
                                        @php $isToday = !empty($todayIsoDate) && ($row['date'] ?? '') === $todayIsoDate; @endphp
                                        @php
                                            $iso = $row['date'] ?? null;
                                            if ($iso) {
                                                $hari = \Carbon\Carbon::parse($iso, 'Asia/Jakarta')
                                                    ->locale('id')
                                                    ->translatedFormat('l');
                                                $tgl = \Carbon\Carbon::parse($iso, 'Asia/Jakarta')->format('d/m/Y');
                                            } else {
                                                $parts = explode(',', $row['tanggal'] ?? ',');
                                                $hari = trim($parts[0] ?? '');
                                                $tgl = trim($parts[1] ?? '');
                                            }
                                        @endphp
                                        <tr class="{{ $isToday ? 'today expanded' : '' }}"
                                            data-date="{{ $iso }}">
                                            <td data-label="Hari" class="fw-bold text-gov-dark">{{ $hari }}
                                            </td>
                                            <td data-label="Tanggal" class="text-gov-dark">
                                                {{ $tgl }}
                                                <i class="fa-solid fa-chevron-down mobile-expand-icon d-md-none"></i>
                                            </td>
                                            <td data-label="Imsak" class="text-center time-cell">
                                                {{ $row['imsak'] ?? '' }}
                                            </td>
                                            <td data-label="Subuh" class="text-center time-cell fardhu">
                                                {{ $row['subuh'] ?? '' }}</td>
                                            <td data-label="Terbit" class="text-center time-cell">
                                                {{ $row['terbit'] ?? '' }}</td>
                                            <td data-label="Dhuha" class="text-center time-cell">
                                                {{ $row['dhuha'] ?? '' }}
                                            </td>
                                            <td data-label="Dzuhur" class="text-center time-cell fardhu">
                                                {{ $row['dzuhur'] ?? '' }}</td>
                                            <td data-label="Ashar" class="text-center time-cell fardhu">
                                                {{ $row['ashar'] ?? '' }}</td>
                                            <td data-label="Maghrib" class="text-center time-cell fardhu">
                                                {{ $row['maghrib'] ?? '' }}</td>
                                            <td data-label="Isya" class="text-center time-cell fardhu">
                                                {{ $row['isya'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Controls -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 px-1"
                            id="paginationWrapper" style="display: none;">
                            <div class="d-flex align-items-center mb-3 mb-md-0">
                                <span class="text-muted me-2"
                                    style="font-size: 0.9rem; font-family: 'PlusJakartaSansText', sans-serif;">Tampilkan:</span>
                                <select id="pageSizeSelect"
                                    class="form-select form-select-sm rounded-pill apple-select" style="width: auto;">
                                    <option value="10" selected>10</option>
                                    <option value="all">Semua</option>
                                </select>
                                <span class="text-muted ms-3" id="pageInfo"
                                    style="font-size: 0.9rem; font-family: 'PlusJakartaSansText', sans-serif;">Menampilkan
                                    0 data</span>
                            </div>
                            <div id="paginationControls"></div>
                        </div>
                    </div>

                    <!-- Data Source Info -->
                    <div class="mt-3 text-center text-md-center px-2">
                        <small class="text-muted" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-circle-info me-1 opacity-75"></i> Data jadwal waktu sholat bersumber
                            dari ketetapan resmi <strong>Kementerian Agama RI</strong> (via API MyQuran).
                        </small>
                    </div>
                @else
                    <div class="text-muted">Data jadwal belum tersedia.</div>
                @endif
            </div>
        </section>

        <section class="content-section pt-5 pb-0">
            <div class="container text-center mb-4">
                <h2 class="text-gov-dark fw-bold mb-2">Tampilan Layar Aplikasi JWS</h2>
                <p class="text-muted">Desain antarmuka eksklusif untuk Masjid Paripurna Kota Pekanbaru.</p>
            </div>
            <div class="container-fluid px-0 position-relative">
                <div id="demoCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                    data-bs-interval="4000">
                    <div class="carousel-indicators mb-2">
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="3"
                            aria-label="Slide 4"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="4"
                            aria-label="Slide 5"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="5"
                            aria-label="Slide 6"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="6"
                            aria-label="Slide 7"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="7"
                            aria-label="Slide 8"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="8"
                            aria-label="Slide 9"></button>
                        <button type="button" data-bs-target="#demoCarousel" data-bs-slide-to="9"
                            aria-label="Slide 10"></button>
                    </div>

                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{ asset('welcome/assets/img/aplikasi-jws.webp') }}" class="d-block w-100"
                                style="object-fit: cover; height: auto;" alt="Tampilan Layar Aplikasi">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Tampilan Layar
                                    Utama</h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-2-layar-adzan.webp') }}" class="d-block w-100"
                                style="object-fit: cover; height: auto;" alt="Layar Adzan">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Layar Peringatan
                                    Adzan</h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-3-layar-iqomah.webp') }}"
                                class="d-block w-100" style="object-fit: cover; height: auto;" alt="Layar Iqomah">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Layar Hitung Mundur
                                    Iqomah</h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-4-layar-sholat.webp') }}"
                                class="d-block w-100" style="object-fit: cover; height: auto;"
                                alt="Layar Saat Sholat">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Layar Gelap Saat
                                    Sholat</h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-5-layar-jumat.webp') }}" class="d-block w-100"
                                style="object-fit: cover; height: auto;" alt="Layar Khutbah Jumat">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Layar Khutbah Jumat
                                </h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-6-tema-1.webp') }}" class="d-block w-100"
                                style="object-fit: cover; height: auto;" alt="Tema Klasik">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Pilihan Tema Klasik
                                </h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-7-tema-2.webp') }}" class="d-block w-100"
                                style="object-fit: cover; height: auto;" alt="Tema Modern">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Pilihan Tema Modern
                                </h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-8-tema-3.webp') }}" class="d-block w-100"
                                style="object-fit: cover; height: auto;" alt="Tema Minimalis">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Pilihan Tema
                                    Minimalis</h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-9-tema-4.webp') }}" class="d-block w-100"
                                style="object-fit: cover; height: auto;" alt="Tema Elegan">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Pilihan Tema Elegan
                                </h6>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('section-features/slide-10-layar-jumbotron.webp') }}"
                                class="d-block w-100" style="object-fit: cover; height: auto;"
                                alt="Layar Standby Jumbotron">
                            <div class="carousel-caption d-block"
                                style="background: rgba(0,0,0,0.6); border-radius: 12px; padding: 8px 16px; bottom: 30px; backdrop-filter: blur(4px); width: max-content; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1);">
                                <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">Layar Mode Standby
                                    (Jumbotron)</h6>
                            </div>
                        </div>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#demoCarousel"
                        data-bs-slide="prev" style="width: auto; left: 15px; opacity: 1;">
                        <div class="d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); color: #1d1d1f;">
                            <i class="fa-solid fa-chevron-left" style="font-size: 1.1rem; margin-right: 2px;"></i>
                        </div>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#demoCarousel"
                        data-bs-slide="next" style="width: auto; right: 15px; opacity: 1;">
                        <div class="d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); color: #1d1d1f;">
                            <i class="fa-solid fa-chevron-right" style="font-size: 1.1rem; margin-left: 2px;"></i>
                        </div>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </section>

        <style>
            .feature-wide-lg {
                flex: 0 0 100%;
                max-width: 100%;
            }
            @media (min-width: 992px) {
                .feature-wide-lg {
                    flex: 0 0 40%;
                    max-width: 40%;
                }
            }
            .feature-card-new {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .feature-card-new:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
            }
            .icon-wrapper-new {
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        </style>
        <!-- Features Grid Section -->
        <section class="features-section pb-5 pt-3">
            <div class="container py-2">
                

                <!-- Section Header (Text Left, Image Right) -->
                <div class="row align-items-center mb-5">
                    <div class="col-lg-6 mb-4 mb-lg-0 text-center text-lg-start">
                        <h2 class="features-main-title fw-bold mb-0">
                            Fitur-fitur Aplikasi<br>Jadwal Waktu Sholat Pekanbaru
                        </h2>
                    </div>
                    <div class="col-lg-6 text-center text-lg-end">
                        <img src="{{ asset('section-features/features-jws-kota-pekanbaru.webp') }}" alt="Fitur JWS Pekanbaru" class="img-fluid w-100" style="max-height: 450px; object-fit: contain;">
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-3">
                    
                    <!-- Row 1 -->
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-wifi fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Tanpa Internet</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Tetap berjalan tanpa koneksi internet</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-regular fa-clock fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Jadwal Sholat Lengkap</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">5 waktu sholat + Imsak, Syuruq & Dhuha</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-clock fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Jam Digital</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Tampilan jam digital yang akurat</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-regular fa-image fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Slider Gambar</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Tampilkan gambar menarik secara dinamis</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-regular fa-newspaper fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Jumbotron</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Informasi penting dalam sorotan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-regular fa-bell fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Alarm Waktu Sholat</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Pengingat otomatis setiap waktu sholat</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-volume-high fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Layar Adzan & Iqomah</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Tampilan khusus untuk setiap momen ibadah</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-chart-column fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Laporan Keuangan</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Transparansi keuangan masjid</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-regular fa-calendar-days fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Agenda</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Jadwal kegiatan masjid</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-music fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Kostum Audio Adzan</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Pilih audio adzan sesuai selera</p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-headphones fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Smart Audio Murottal</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Background murottal otomatis & menenangkan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-font fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Teks Berjalan</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Informasi berjalan secara real-time</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-desktop fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Sinkron Antar Device</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Konten tersinkron antar perangkat</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-mosque fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Kostum Logo Masjid</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Gunakan logo masjid sesuai identitas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-moon fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Tanggal Hijriah</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Tampilkan tanggal Hijriah otomatis</p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4 -->
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-palette fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Tema</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Pilih tema warna sesuai kebutuhan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-tv fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Multi Platform</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Support Android TV, Google TV, dll.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="feature-card-new d-flex align-items-center text-start p-3 shadow-sm rounded-4 border bg-white h-100">
                            <div class="icon-wrapper-new text-gov-blue flex-shrink-0 me-3">
                                <i class="fa-solid fa-arrows-rotate fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-gov-dark" style="font-size: 0.95rem;">Realtime Update</h3>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">Update konten kapan saja secara real-time</p>
                            </div>
                        </div>
                    </div>
                    <div class="col feature-wide-lg">
                        <div class="feature-card-new d-flex flex-column justify-content-center p-3 shadow-sm rounded-4 border bg-white h-100">
                            <h3 class="mb-2 fw-bold text-gov-dark text-center" style="font-size: 0.95rem;">Didukung di berbagai perangkat TV</h3>
                            <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap mt-2">
                                <div class="text-center">
                                    <i class="fa-brands fa-android fs-2 text-success mb-1"></i><br>
                                    <span style="font-size: 0.7rem;" class="text-muted fw-semibold">Android TV</span>
                                </div>
                                <div class="text-center">
                                    <i class="fa-brands fa-google fs-2 text-danger mb-1"></i><br>
                                    <span style="font-size: 0.7rem;" class="text-muted fw-semibold">Google TV</span>
                                </div>
                                <div class="text-center">
                                    <span class="fs-4 fw-bold text-dark lh-1" style="font-family: Arial, sans-serif;">SAMSUNG</span><br>
                                    <span style="font-size: 0.7rem;" class="text-muted fw-semibold">Samsung Tizen OS</span>
                                </div>
                                <div class="text-center">
                                    <span class="fs-4 fw-bold text-danger lh-1" style="font-family: Arial, sans-serif; letter-spacing: -1px;">LG</span><br>
                                    <span style="font-size: 0.7rem;" class="text-muted fw-semibold">WebOS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (!empty($latestArticles) && $latestArticles->count() > 0)
            <section class="py-5">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <div>
                            <h2 class="mb-1 text-gov-dark fw-bold">Berita Terbaru</h2>
                            <p class="text-muted mb-0">Informasi seputar kegiatan dan perkembangan JWS Kota Pekanbaru.
                            </p>
                        </div>
                    </div>

                    <div class="row g-4">
                        @foreach ($latestArticles as $article)
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden article-card">
                                    <div class="ratio ratio-16x9">
                                        <img src="{{ $getFirstImage($article->content) }}"
                                            class="card-img-top object-fit-cover" style="object-fit: cover;"
                                            alt="{{ $article->title }}"
                                            onerror="this.src='{{ asset('nav-brand.png') }}'">
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-muted small fw-semibold" style="letter-spacing: 0.5px;">
                                                {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
                                            </span>
                                            <span class="badge bg-blue-lt px-2 py-1">
                                                {{ $article->category->name ?? 'Berita' }}
                                            </span>
                                        </div>
                                        <h3 class="article-title mb-3 line-clamp-2">
                                            {{ $article->title }}
                                        </h3>
                                        <p class="text-muted small mb-4 line-clamp-3">
                                            {{ $article->description }}
                                        </p>
                                        <div class="mt-auto">
                                            @php
                                                $date = $article->published_at
                                                    ? $article->published_at->format('d-m-Y')
                                                    : $article->created_at->format('d-m-Y');
                                            @endphp
                                            <a href="{{ route('articles.show', ['date' => $date, 'slug' => $article->slug]) }}"
                                                class="btn btn-link p-0 text-gov-blue fw-semibold d-flex align-items-center gap-1">
                                                Baca Selengkapnya
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="py-5">
            <div class="container">
                <h2 class="mb-3 text-gov-dark fw-bold">Galeri Sosialisasi JWS</h2>
                <p class="text-muted mb-5">Sosialisasi Aplikasi Jadwal Waktu Sholat (JWS) Berbasis Web di Masjid
                    Paripurna
                    Agung Ar-Rahman Pekanbaru Sabtu, 18 Oktober 2025.</p>

                <div id="galleryCarousel" class="carousel slide gallery-carousel" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="3"
                            aria-label="Slide 4"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="4"
                            aria-label="Slide 5"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="5"
                            aria-label="Slide 6"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="6"
                            aria-label="Slide 7"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="7"
                            aria-label="Slide 8"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="8"
                            aria-label="Slide 9"></button>
                        <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="9"
                            aria-label="Slide 10"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-10.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 1">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-2.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 2">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-3.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 3">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-4.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 4">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-5.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 5">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-6.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 6">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-6.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 7">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-8.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 8">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-9.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 9">
                        </div>
                        <div class="carousel-item">
                            <img src="{{ asset('welcome/assets/img/sosialisasi-jws-gambar-1.webp') }}"
                                class="d-block w-100 img-fluid" alt="Sosialisasi JWS 10">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sebelumnya</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Berikutnya</span>
                    </button>
                </div>
            </div>
        </section>

    </main>

    <footer class="footer py-5 bg-gov-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <img src="{{ asset('theme/static/logo-pemko-kominfo.webp') }}" alt="Logo Pemko dan Kominfo"
                        class="img-fluid mb-3" style="height: 45px; object-fit: contain; filter: grayscale(100%);">
                    <div class="fw-semibold">
                        Jadwal Waktu Sholat Pemerintah Kota Pekanbaru
                    </div>
                    <div class="text-white-50 small mt-1">© {{ date('Y') }} Diskominfo Pekanbaru</div>
                </div>
                <div
                    class="col-md-6 text-md-end mt-4 mt-md-0 d-flex justify-content-md-end justify-content-start align-items-center">
                    <a class="footer-icon" href="https://www.youtube.com/c/InfoPemkoPekanbaru" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a class="footer-icon" href="https://www.pekanbaru.go.id/" aria-label="Website Pemko">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <a class="footer-icon" href="https://www.instagram.com/diskominfopku/" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a class="footer-icon" href="https://www.instagram.com/diskominfopku/" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a class="footer-icon" href="{{ asset('download/JWS Web V-1.0.apk') }}" download
                        aria-label="Download APK">
                        <i class="fab fa-android"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @include('components.layouts.welcomescript')
    @livewireScripts
</body>

</html>
