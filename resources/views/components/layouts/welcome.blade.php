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
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('nav-brand.png') }}" width="30" alt="JWS Diskominfo"
                    class="navbar-brand-image me-1">
                <span class="fw-bold">Jadwal Waktu Sholat</span>
            </a>
            <div class="ms-auto d-none d-lg-block">
                <a class="btn btn-gov-blue rounded-4" href="https://www.pekanbaru.go.id/">Website
                    Pemko</a>
            </div>
        </div>
    </nav>


    <section class="hero hero-gradient text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="lh-1 mb-4">
                        Jadwal Waktu Sholat Pemerintah Kota Pekanbaru
                    </h1>
                    <p class="mb-4">Aplikasi resmi untuk masjid-masjid paripurna di Kota Pekanbaru yang
                        menampilkan jadwal sholat, pengingat adzan dan iqomah, kalender hijriah, serta sarana
                        penyampaian pesan resmi Pemerintah Kota kepada seluruh masyarakat Kota Pekanbaru melalui Masjid
                        Paripurna.</p>
                    <div class="d-flex flex-wrap align-items-center">
                        @if (!empty($showScheduleBtn) && !empty($scheduleUrl))
                            <a class="btn btn-gov-blue btn-lg rounded-4 me-3 mb-2" href="{{ $scheduleUrl }}">Lihat
                                JWS</a>
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
    <section class="py-4 bg-white">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-stretch gap-2 mb-3">
                @php
                    $todayLabel = \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y');
                @endphp
                <div id="current-time-banner" class="countdown-banner">
                    <div class="countdown-icon"><i class="fa-regular fa-clock"></i></div>
                    <div class="flex-grow-1">
                        <div class="countdown-label">{{ $todayLabel }}</div>
                        <div class="countdown-time" id="current-time">--:--:--</div>
                    </div>
                </div>
                <div class="countdown-banner header-title-banner">
                    <div class="flex-grow-1 text-center">
                        <div class="header-title">
                            <i class="fa-solid fa-mosque"></i>
                            Jadwal Sholat Hari Ini
                        </div>
                    </div>
                </div>
                @if (!empty($nextPrayer) && !empty($nextPrayerAtIso))
                    <div id="countdown" class="countdown-banner" data-next-iso="{{ $nextPrayerAtIso }}">
                        <div class="flex-grow-1 countdown-content">
                            <div class="countdown-label">Menuju {{ ucfirst($nextPrayer) }}</div>
                            <div class="countdown-time" id="countdown-text">--:--:--</div>
                        </div>
                        <div class="countdown-icon"><i class="fa-regular fa-bell"></i></div>
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

    <section class="py-4 bg-white">
        <div class="container">
            <h2 class="mb-3 text-gov-dark">Jadwal Sholat Kota Pekanbaru - {{ $monthName ?? '' }}
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
                                    <tr class="{{ $isToday ? 'today' : '' }}">
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
                                        <td class="fw-bold text-gov-dark">{{ $hari }}</td>
                                        <td class="text-gov-dark">{{ $tgl }}</td>
                                        <td class="text-center time-cell">{{ $row['imsak'] ?? '' }}</td>
                                        <td class="text-center time-cell fardhu">{{ $row['subuh'] ?? '' }}</td>
                                        <td class="text-center time-cell">{{ $row['terbit'] ?? '' }}</td>
                                        <td class="text-center time-cell">{{ $row['dhuha'] ?? '' }}</td>
                                        <td class="text-center time-cell fardhu">{{ $row['dzuhur'] ?? '' }}</td>
                                        <td class="text-center time-cell fardhu">{{ $row['ashar'] ?? '' }}</td>
                                        <td class="text-center time-cell fardhu">{{ $row['maghrib'] ?? '' }}</td>
                                        <td class="text-center time-cell fardhu">{{ $row['isya'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-muted">Data jadwal belum tersedia.</div>
            @endif
        </div>
    </section>

    <section class="content-section py-5 bg-soft">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <img src="{{ asset('welcome/assets/img/aplikasi-jws.webp') }}" class="img-fluid rounded-4 shadow"
                        alt="Kantor Walikota Pekanbaru" />
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-3 text-gov-dark">
                        Aplikasi Jadwal Waktu Sholat Pemerintah Kota Pekanbaru
                    </h2>
                    <li class="d-flex align-items-center mb-2"><i class="fa-solid fa-mosque text-primary me-2">
                        </i>
                        <span>
                            Jadwal sholat harian Pekanbaru
                        </span>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa-regular fa-bell text-primary me-2"></i>
                        <span>
                            Pengingat adzan dan iqomah
                        </span>
                    </li>
                    <li class="d-flex align-items-center mb-2"><i
                            class="fa-solid fa-calendar-days text-primary me-2"></i><span>Kalender
                            hijriah</span>
                    </li>
                    <li class="d-flex align-items-center"><i
                            class="fa-solid fa-bullhorn text-primary me-2"></i><span>Pesan resmi
                            Pemerintah
                            Kota</span></li>
                    </ul>
                    <a class="btn btn-gov-blue rounded-4 my-4"
                        href="https://jadwalsholat.pekanbaru.go.id/firdaus">Buka
                        Jadwal Sholat</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="mb-3 text-gov-dark">Galeri Sosialisasi JWS</h2>
            <p class="text-muted mb-4">Sosialisasi Aplikasi Jadwal Waktu Sholat (JWS) Berbasis Web di Masjid Paripurna
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
                <div class="col-md-6 text-md-end">
                    <a class="btn btn-outline-light m-1" href="https://www.youtube.com/c/InfoPemkoPekanbaru"><i
                            class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light m-1" href="https://www.pekanbaru.go.id/"><i
                            class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    <a class="btn btn-outline-light m-1" href="https://www.instagram.com/diskominfopku/"><i
                            class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    @include('components.layouts.welcomescript')
    @livewireScripts
</body>

</html>
