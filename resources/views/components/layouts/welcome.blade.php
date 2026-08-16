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
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('nav-brand.png') }}" width="30" alt="JWS Diskominfo"
                    class="navbar-brand-image me-1">
                <span class="fw-bold">JWS Diskominfo</span>
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
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="lh-1 mb-4 fw-bold">
                        Jadwal Waktu Sholat Pemerintah Kota Pekanbaru
                    </h1>
                    <p class="mb-4">Aplikasi resmi untuk masjid-masjid paripurna di Kota Pekanbaru yang
                        menampilkan jadwal sholat, pengingat adzan dan iqomah, kalender hijriah, serta sarana
                        penyampaian pesan resmi Pemerintah Kota kepada seluruh masyarakat Kota Pekanbaru melalui Masjid
                        Paripurna.</p>
                    <div class="d-flex flex-wrap align-items-center">
                        @if (!empty($showScheduleBtn) && !empty($scheduleUrl))
                            <a class="btn btn-gov-blue btn-lg rounded-4 me-3 mb-2" href="{{ $scheduleUrl }}">Lihat
                                JWS Saya</a>
                        @endif
                        <a class="btn btn-outline-light btn-lg rounded-4 mb-2"
                            href="https://www.youtube.com/c/InfoPemkoPekanbaru"><i class="fab fa-youtube me-2"></i>Info
                            Pemko</a>
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
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-end mb-4 border-bottom pb-3">
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
                        <div class="text-muted text-uppercase fw-bold countdown-next-label" style="letter-spacing: 0.05em; font-family: 'PlusJakartaSansDisplay', sans-serif;">
                            <i class="fa-regular fa-bell me-1"></i> Menuju {{ ucfirst($nextPrayer) }}
                        </div>
                        <div class="font-led mt-1 countdown-next-time" id="countdown-text" style="color: #0071e3; line-height: 1;">--:--:--</div>
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
            <h2 class="mb-3 text-gov-dark fw-bold">Jadwal Sholat Kota Pekanbaru - {{ $monthName ?? '' }}
                {{ $yearNumber ?? '' }}</h2>
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
                                    <tr class="{{ $isToday ? 'today expanded' : '' }}">
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
                                        <td data-label="Hari" class="fw-bold text-gov-dark">{{ $hari }}</td>
                                        <td data-label="Tanggal" class="text-gov-dark">
                                            {{ $tgl }}
                                            <i class="fa-solid fa-chevron-down mobile-expand-icon d-md-none"></i>
                                        </td>
                                        <td data-label="Imsak" class="text-center time-cell">{{ $row['imsak'] ?? '' }}</td>
                                        <td data-label="Subuh" class="text-center time-cell fardhu">{{ $row['subuh'] ?? '' }}</td>
                                        <td data-label="Terbit" class="text-center time-cell">{{ $row['terbit'] ?? '' }}</td>
                                        <td data-label="Dhuha" class="text-center time-cell">{{ $row['dhuha'] ?? '' }}</td>
                                        <td data-label="Dzuhur" class="text-center time-cell fardhu">{{ $row['dzuhur'] ?? '' }}</td>
                                        <td data-label="Ashar" class="text-center time-cell fardhu">{{ $row['ashar'] ?? '' }}</td>
                                        <td data-label="Maghrib" class="text-center time-cell fardhu">{{ $row['maghrib'] ?? '' }}</td>
                                        <td data-label="Isya" class="text-center time-cell fardhu">{{ $row['isya'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 px-1" id="paginationWrapper" style="display: none;">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <span class="text-muted me-2" style="font-size: 0.9rem; font-family: 'PlusJakartaSansText', sans-serif;">Tampilkan:</span>
                            <select id="pageSizeSelect" class="form-select form-select-sm rounded-pill apple-select" style="width: auto;">
                                <option value="10" selected>10</option>
                                <option value="all">Semua</option>
                            </select>
                            <span class="text-muted ms-3" id="pageInfo" style="font-size: 0.9rem; font-family: 'PlusJakartaSansText', sans-serif;">Menampilkan 0 data</span>
                        </div>
                        <div id="paginationControls"></div>
                    </div>
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
        <div class="container-fluid px-0">
            <img src="{{ asset('welcome/assets/img/aplikasi-jws.webp') }}" class="img-fluid w-100" style="object-fit: cover; height: auto;"
                alt="Aplikasi Jadwal Waktu Sholat" />
        </div>
    </section>

    <!-- Features Grid Section -->
    <section class="features-section py-5">
        <div class="container py-4">
            <div class="row g-4">
                <!-- Fitur 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card d-flex align-items-center text-start p-4">
                        <div class="text-gov-blue flex-shrink-0 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-mosque">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 21h7v-2a2 2 0 1 1 4 0v2h7" />
                                <path d="M4 21v-10" />
                                <path d="M20 21v-10" />
                                <path d="M4 16h3v-3h10v3h3" />
                                <path d="M17 13a5 5 0 0 0 -10 0" />
                                <path d="M21 10.5c0 -.329 -.077 -.653 -.224 -.947l-.776 -1.553l-.776 1.553a2.118 2.118 0 0 0 -.224 .947a.5 .5 0 0 0 .5 .5h1a.5 .5 0 0 0 .5 -.5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="feature-title mb-1" style="font-size: 1.1rem;">Jadwal Sholat</h3>
                            <p class="feature-desc mb-0" style="font-size: 0.9rem;">Waktu sholat fardhu akurat harian khusus untuk area Kota Pekanbaru.</p>
                        </div>
                    </div>
                </div>
                <!-- Fitur 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card d-flex align-items-center text-start p-4">
                        <div class="text-gov-blue flex-shrink-0 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="feature-title mb-1" style="font-size: 1.1rem;">Pengingat Adzan</h3>
                            <p class="feature-desc mb-0" style="font-size: 0.9rem;">Notifikasi dan hitung mundur presisi menuju waktu adzan & iqomah.</p>
                        </div>
                    </div>
                </div>
                <!-- Fitur 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card d-flex align-items-center text-start p-4">
                        <div class="text-gov-blue flex-shrink-0 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M8 14v4" />
                                <path d="M12 14v4" />
                                <path d="M16 14v4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="feature-title mb-1" style="font-size: 1.1rem;">Kalender Hijriah</h3>
                            <p class="feature-desc mb-0" style="font-size: 0.9rem;">Konversi dan penanggalan tahun berjalan Hijriah terintegrasi.</p>
                        </div>
                    </div>
                </div>
                <!-- Fitur 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card d-flex align-items-center text-start p-4">
                        <div class="text-gov-blue flex-shrink-0 me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-microphone">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 2m0 3a3 3 0 0 1 3 -3h0a3 3 0 0 1 3 3v5a3 3 0 0 1 -3 3h0a3 3 0 0 1 -3 -3z" />
                                <path d="M5 10a7 7 0 0 0 14 0" />
                                <path d="M8 21l8 0" />
                                <path d="M12 17l0 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="feature-title mb-1" style="font-size: 1.1rem;">Pesan Resmi</h3>
                            <p class="feature-desc mb-0" style="font-size: 0.9rem;">Papan informasi digital penyampaian pesan Pemerintah Kota.</p>
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
                        <p class="text-muted mb-0">Informasi seputar kegiatan dan perkembangan JWS Kota Pekanbaru.</p>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach ($latestArticles as $article)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden article-card">
                                <div class="ratio ratio-16x9">
                                    <img src="{{ $getFirstImage($article->content) }}"
                                        class="card-img-top object-fit-cover" style="object-fit: cover;" alt="{{ $article->title }}"
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
            <p class="text-muted mb-5">Sosialisasi Aplikasi Jadwal Waktu Sholat (JWS) Berbasis Web di Masjid Paripurna
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

    <footer class="footer py-5 bg-gov-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="fw-semibold">
                        Jadwal Waktu Sholat Pemerintah Kota Pekanbaru
                    </div>
                    <div>© {{ date('Y') }} Diskominfo Pekanbaru</div>
                </div>
                <div class="col-md-6 text-md-end mt-4 mt-md-0 d-flex justify-content-md-end justify-content-start align-items-center">
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
                    <a class="footer-icon" href="{{ asset('download/JWS Web V-1.0.apk') }}" download aria-label="Download APK">
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
