<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title }}</title>
    @include('components.layouts.style')
    @livewireStyles

</head>

<body class="d-flex flex-column">
    <div class="row align-items-stretch min-vh-100 g-0">
        <div class="col-lg-7 d-none d-lg-block h-100">
            <div id="carousel-captions" class="carousel slide h-100 shadow-sm border-0" data-bs-ride="carousel">
                <div class="carousel-inner h-100">
                    <div class="carousel-item active h-100">
                        <img class="d-block w-100 h-100" alt=""
                            src="{{ asset('theme/static/illustrations/jws.webp') }}" style="object-fit: cover;" />
                        <div class="carousel-caption-background d-none d-md-block"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Jadwal Sholat</h3>
                            <p>Dengan tampilan yang responsif dan real-time.</p>
                        </div>
                    </div>
                    <div class="carousel-item h-100">
                        <img class="d-block w-100 h-100" alt=""
                            src="{{ asset('theme/static/illustrations/adzan.webp') }}" style="object-fit: cover;" />
                        <div class="carousel-caption-background d-none d-md-block"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Adzan</h3>
                            <p>Adzan yang terintegrasi dengan jadwal sholat.</p>
                        </div>
                    </div>
                    <div class="carousel-item h-100">
                        <img class="d-block w-100 h-100" alt=""
                            src="{{ asset('theme/static/illustrations/iqomah.webp') }}" style="object-fit: cover;" />
                        <div class="carousel-caption-background d-none d-md-block"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Iqomah</h3>
                            <p>Fitur Iqomah yang terintegrasi dengan jadwal sholat.</p>
                        </div>
                    </div>
                    <div class="carousel-item h-100">
                        <img class="d-block w-100 h-100" alt=""
                            src="{{ asset('theme/static/illustrations/after-iqomah.webp') }}"
                            style="object-fit: cover;" />
                        <div class="carousel-caption-background d-none d-md-block"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Informatif</h3>
                            <p>Menampilkan informasi yang penting dan informatif.</p>
                        </div>
                    </div>
                    <div class="carousel-item h-100">
                        <img class="d-block w-100 h-100" alt=""
                            src="{{ asset('theme/static/illustrations/jumat.webp') }}" style="object-fit: cover;" />
                        <div class="carousel-caption-background d-none d-md-block"></div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3>Juma'at</h3>
                            <p>Dengan penanganan khusus untuk waktu sholat juma'at.</p>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" data-bs-target="#carousel-captions" role="button"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next" data-bs-target="#carousel-captions" role="button"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </a>
            </div>
        </div>
        <div class="col-lg-5 align-self-center">
            <div class="container-tight">
                <div class="text-center mb-4">
                    <a href="." class="navbar-brand navbar-brand-autodark"><img
                            src="{{ asset('theme/static/logo-pemko-kominfo.webp') }}" height="36" alt="">
                    </a>
                </div>
                {{ $slot }}
                <div class="small text-center text-secondary mt-3">
                    Versi Aplikasi v1.0.0 • Dilindungi Hak Cipta
                </div>
            </div>
        </div>
    </div>

    @include('components.layouts.script')
    @livewireScripts
</body>

</html>
