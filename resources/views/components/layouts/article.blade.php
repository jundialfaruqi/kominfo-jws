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
                <span class="fw-bold">Jadwal Waktu Sholat</span>
            </a>
            <div class="ms-auto">
                <a class="btn btn-gov-blue rounded-4 btn-sm py-2 px-3" href="{{ route('welcome.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home me-1">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                    </svg>
                    Beranda
                </a>
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
                    <a class="btn btn-outline-light rounded-circle m-1"
                        href="https://www.instagram.com/diskominfopku/">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a class="btn btn-outline-light rounded-circle m-1"
                        href="https://www.instagram.com/diskominfopku/">
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
