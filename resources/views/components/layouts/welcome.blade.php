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
    <video class="bg-video" playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
        <source src="{{ asset('welcome/assets/mp4/bg.mp4') }}" type="video/mp4" />
    </video>

    <div class="masthead">
        <div class="masthead-content text-white">
            <div class="container-fluid px-4 px-lg-0">
                <h1 class="fst-italic lh-1 mb-4">Selamat Datang di Website Resmi Jadwal Sholat Pekanbaru</h1>
                <p class="mb-5">Aplikasi yang menayangkan jadwal sholat untuk Kota Pekanbaru dengan pengingat adzan,
                    iqomah serta dilengkapi dengan kalender hijriah. Aplikasi ini di kembangkan dan dikelola oleh
                    Diskominfo Pekanbaru</p>

                <form>
                    <div class="row input-group-newsletter">
                        <div class="col-auto">
                            <button class="btn btn-primary rounded-4" type="button"
                                onclick="window.location.href='https://jadwalsholat.pekanbaru.go.id/firdaus'">
                                Lihat Jadwal Sholat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="social-icons">
        <div class="d-flex flex-row flex-lg-column justify-content-center align-items-center h-100 mt-3 mt-lg-0">
            <a class="btn btn-dark m-3" href="https://www.youtube.com/c/InfoPemkoPekanbaru"><i
                    class="fab fa-youtube"></i></a>
            <a class="btn btn-dark m-3" href="https://www.pekanbaru.go.id/"><i
                    class="fa-solid fa-arrow-up-right-from-square"></i></a>
            <a class="btn btn-dark m-3" href="https://www.instagram.com/diskominfopku/"><i
                    class="fab fa-instagram"></i></a>
        </div>
    </div>

    @include('components.layouts.welcomescript')
    @livewireScripts
</body>

</html>
