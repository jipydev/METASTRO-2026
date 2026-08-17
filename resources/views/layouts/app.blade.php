<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh overflow-x-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Portal Resmi METASTRO 2026 - Spirit of Hiro, Heart of Solder. Platform Manajemen & Absensi Panitia.">

    <title>{{ $title ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>

    <!-- Anti-FOUC Theme Script -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    </noscript>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-poppins antialiased bg-brand-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 min-h-dvh overflow-x-hidden transition-colors duration-200"
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false,
        darkMode: document.documentElement.classList.contains('dark'),
        toggleSidebar() {
            if (window.innerWidth < 1024) {
                this.sidebarOpen = !this.sidebarOpen;
                return;
            }
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        },
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        }
    }">
    <div class="min-h-dvh">
        <div x-show="sidebarOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="sidebarOpen = false"></div>

        @include('layouts.navigation')

        <div class="flex min-h-dvh flex-col min-w-0 transition-[margin] duration-200 lg:ml-64"
            :class="sidebarCollapsed && !sidebarOpen ? 'lg:!ml-[4.75rem]' : ''">
            <header class="sticky top-0 z-30 h-14 flex items-center gap-3 px-4 lg:px-6 bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700/80">
                <button type="button" @click="toggleSidebar()"
                    class="lg:hidden p-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="min-w-0 truncate font-oswald text-base font-semibold uppercase tracking-tight text-slate-900 dark:text-white">
                    {{ $title ?: 'METASTRO 2026' }}
                </span>
                <div class="ml-auto">
                    @include('components.notification-dropdown')
                </div>
            </header>

            @isset($header)
                <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700/80 shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700/80 mt-auto transition-colors duration-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm font-poppins text-gray-500 dark:text-slate-400">
                        &copy; 2026 Chiper Metastro.
                    </p>
                </div>
            </footer>
        </div>
    </div>
</body>

</html>
