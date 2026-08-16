<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>{{ $title ?? 'Berita - JWS Kota Pekanbaru' }}</title>
    @include('components.layouts.welcomestyle')
    @livewireStyles
</head>

<body class="bg-white">
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center text-decoration-none" href="{{ route('welcome.index') }}">
                <img src="{{ asset('nav-brand.png') }}" width="35" alt="JWS Pekanbaru" class="me-2">
                <div class="d-flex flex-column justify-content-center align-items-start">
                    <span class="fw-bold lh-1" style="font-size: 1.1rem;">JWS Pekanbaru</span>
                    <span class="text-muted lh-1 mt-1" style="font-size: 0.65rem; font-family: 'PlusJakartaSansText', sans-serif; letter-spacing: 0.2px;">Jadwal Waktu Sholat Kota Pekanbaru</span>
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
        {{ $slot }}
    </main>

    <footer class="footer py-5 bg-gov-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <img src="{{ asset('theme/static/logo-pemko-kominfo.webp') }}" alt="Logo Pemko dan Kominfo" class="img-fluid mb-3" style="height: 45px; object-fit: contain; filter: grayscale(100%);">
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
