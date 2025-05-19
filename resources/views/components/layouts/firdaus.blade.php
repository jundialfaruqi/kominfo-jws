<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @include('components.layouts.firdausstyle')
    @livewireStyles
</head>

<body>
    {{ $slot }}

    @include('components.layouts.firdausscript')
    @livewireScripts
</body>

</html>
