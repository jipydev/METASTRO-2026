@php
    $navItem = function (bool $active): string {
        return 'flex items-center rounded-xl text-xs font-semibold transition ' . ($active
            ? 'bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-300'
            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-brand-600 dark:hover:text-brand-400');
    };
@endphp

<aside
    class="flex w-64 min-h-screen shrink-0 flex-col overflow-hidden bg-white dark:bg-slate-800 border-r border-gray-100 dark:border-slate-700/80
        max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-50 max-lg:h-dvh max-lg:min-h-0
        max-lg:w-[min(16rem,calc(100vw-3.5rem))] max-lg:-translate-x-full max-lg:shadow-xl max-lg:transition-transform max-lg:duration-200"
    :class="{
        'max-lg:translate-x-0': sidebarOpen,
        '!w-[4.75rem]': sidebarCollapsed && !sidebarOpen
    }">

    <div class="h-16 flex items-center gap-2 px-3 border-b border-gray-100 dark:border-slate-700/80"
        :class="sidebarCollapsed && !sidebarOpen ? 'justify-center' : 'justify-between'">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0" :class="sidebarCollapsed && !sidebarOpen && 'hidden'">
            <img src="{{ asset('favicon.ico') }}" alt="Logo" class="h-8 w-8 object-contain shrink-0 rounded-lg" />
            <span class="font-oswald text-lg font-semibold uppercase tracking-tight text-slate-900 dark:text-white truncate"
                x-show="!sidebarCollapsed || sidebarOpen" x-cloak>
                METASTRO 2026
            </span>
        </a>
        <button type="button" @click="toggleSidebar()"
            class="hidden lg:inline-flex cursor-pointer p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition"
            :title="sidebarCollapsed ? 'Buka menu' : 'Tutup menu'">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <nav class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-2.5 py-4 space-y-0.5 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
        <a href="{{ route('dashboard') }}" title="Dashboard"
            class="{{ $navItem(request()->routeIs('dashboard')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Dashboard</span>
        </a>

        <a href="{{ route('pengumuman.index') }}" title="Pengumuman"
            class="{{ $navItem(request()->routeIs('pengumuman.*')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Pengumuman</span>
        </a>

        <a href="{{ route('kegiatan.index') }}" title="Kegiatan"
            class="{{ $navItem(request()->routeIs('kegiatan.*')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Kegiatan</span>
        </a>

        <a href="{{ route('notulensi.index') }}" title="Notulensi"
            class="{{ $navItem(request()->routeIs('notulensi.*')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Notulensi</span>
        </a>

        <a href="{{ route('presensi.index') }}" title="Presensi"
            class="{{ $navItem(request()->routeIs('presensi.*')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.2" />
                <rect x="14" y="3" width="7" height="7" rx="1.2" />
                <rect x="3" y="14" width="7" height="7" rx="1.2" />
                <rect x="5.5" y="5.5" width="2" height="2" fill="currentColor" stroke="none" />
                <rect x="16.5" y="5.5" width="2" height="2" fill="currentColor" stroke="none" />
                <rect x="5.5" y="16.5" width="2" height="2" fill="currentColor" stroke="none" />
                <rect x="14" y="14" width="3" height="3" />
                <rect x="18.5" y="14" width="2.5" height="2.5" />
                <rect x="14" y="18.5" width="2.5" height="2.5" />
                <rect x="18" y="18" width="3" height="3" />
            </svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Presensi</span>
        </a>

        <a href="{{ route('pengajuan-izin.index') }}" title="Izin"
            class="{{ $navItem(request()->routeIs('pengajuan-izin.*')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Izin</span>
        </a>

        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.users.index') }}" title="Pengguna"
                class="{{ $navItem(request()->routeIs('admin.*')) }}"
                :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Pengguna</span>
            </a>
        @endif
    </nav>

    <div class="mt-auto shrink-0 border-t border-gray-100 dark:border-slate-700/80 p-2.5 space-y-1">
        <button @click="toggleTheme()" type="button" title="Ubah tema"
            class="w-full flex items-center rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg x-show="darkMode" x-cloak class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg x-show="!darkMode" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
        </button>

        <a href="{{ route('profile.edit') }}" title="{{ Auth::user()->nama }}"
            class="{{ $navItem(request()->routeIs('profile.*')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <span class="truncate" x-show="!sidebarCollapsed || sidebarOpen" x-cloak>{{ Auth::user()->nama }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Keluar"
                class="w-full flex items-center rounded-xl text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition"
                :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Keluar</span>
            </button>
        </form>
    </div>
</aside>
