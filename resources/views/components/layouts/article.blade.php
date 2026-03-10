<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>{{ $title ?? 'Berita - JWS Kota Pekanbaru' }}</title>
    @include('components.layouts.welcomestyle')
    @livewireStyles
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome.index') }}">
                <img src="{{ asset('nav-brand.png') }}" width="30" alt="JWS Diskominfo"
                    class="navbar-brand-image me-1">
                <span class="fw-bold">JWS Diskominfo</span>
            </a>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

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
                    <a class="btn btn-outline-light rounded-circle m-1"
                        href="https://www.youtube.com/c/InfoPemkoPekanbaru">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a class="btn btn-outline-light rounded-circle m-1" href="https://www.pekanbaru.go.id/">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <a class="btn btn-outline-light rounded-circle m-1" href="https://www.instagram.com/diskominfopku/">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a class="btn btn-outline-light rounded-circle m-1" href="https://www.instagram.com/diskominfopku/">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a class="btn btn-outline-light rounded-circle m-1"
                        href="https://drive.google.com/file/d/1Q1AwnytOJj_5id_6qzq_qtkuCzAsLNCt/view?usp=sharing">
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
