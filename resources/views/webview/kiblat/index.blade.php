<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="theme-color" content="#04223f" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <title>Salaam — Jadwal Sholat &amp; Kompas Kiblat</title>
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🕌</text></svg>" />
    @include('webview.kiblat.partials.styles')
</head>

<body>
    <div class="app">
        <main>
            @include('webview.kiblat.partials.tab-sholat')
            @include('webview.kiblat.partials.tab-kiblat')
            @include('webview.kiblat.partials.tab-pengaturan')
        </main>

        @include('webview.kiblat.partials.navbar')
    </div>

    @include('webview.kiblat.partials.scripts')
</body>

</html>
