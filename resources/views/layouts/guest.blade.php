<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Nikhan & Associates Law Office' }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.webp') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-[#0A0A0B] dark:text-zinc-100">

    @include('landing.partials.navbar', ['variant' => 'landing'])

    <main class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6">
        {{ $slot }}
    </main>
</body>

</html>
