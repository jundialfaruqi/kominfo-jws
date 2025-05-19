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

<body class=" d-flex flex-column bg-white">
    {{ $slot }}

    @include('components.layouts.script')
    @livewireScripts
</body>

</html>
