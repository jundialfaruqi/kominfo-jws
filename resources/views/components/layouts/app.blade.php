<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>JWS - {{ $title ?? 'Default Title' }}</title>
    {{-- CSS files --}}
    @include('components.layouts.style')
    @livewireStyles

</head>

<body>
    <div class="page">
        {{-- Sidebar --}}
        @include('components.layouts.sidebar')
        {{-- Navbar --}}
        @include('components.layouts.navbar')
        {{-- Page wrapper --}}
        <div class="page-wrapper">
            {{-- Page header --}}
            @include('components.layouts.header')
            {{-- Page body --}}
            {{ $slot }}
            {{-- Page footer --}}
            @include('components.layouts.footer')
        </div>
    </div>
    {{-- Script files --}}
    @include('components.layouts.script')
    @livewireScripts
</body>

</html>
