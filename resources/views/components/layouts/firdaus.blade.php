<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link href="{{ asset('css/adzan.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    @yield('styles')
</head>

<body>
    {{ $slot }}

    @include('components.layouts.firdausscript')
    @livewireScripts
</body>

</html>
