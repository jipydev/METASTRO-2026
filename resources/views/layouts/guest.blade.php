<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title . ' - ' . config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="flex justify-center">
        <div class="min-h-screen bg-zinc-50 px-4 pt-8 flex flex-col md:justify-center md:pt-0 gap-4 max-w-sm sm:max-w-md">
            <h1 class="font-bold text-xl lg:3xl text-primary-500">METASTRO 2026</h1>

            <h2 class="text-3xl lg:text-4xl font-semibold">Selamat datang, <span class="text-primary-500 font-bold">HIROES.</span>
            </h2>

            <p>Silakan masuk untuk mengisi presensi dan mengerjakan tugas.</p>

            <div class="w-full sm:max-w-md mt-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
