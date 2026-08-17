<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Portal Login METASTRO 2026 - Spirit of Hiro, Heart of Solder. Silakan masuk untuk mengisi presensi dan mengerjakan tugas.">

    <title>{{ $title ? $title . ' - ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    </noscript>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-poppins antialiased bg-brand-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 min-h-full transition-colors duration-200">
    <main class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>
</body>

</html>
