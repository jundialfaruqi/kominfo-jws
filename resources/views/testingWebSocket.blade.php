<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>{{ $title ?? config('app.name') }}</title>
</head>

<body>
    @vite('resources/js/app.js')
</body>
<script>
    setTimeout(() => {
        window.Echo.channel('masjid-1')
            .listen('ContentUpdatedEvent', (e) => {
                console.log(e)
            })
    }, 500)
</script>


</html>