@php
    $navItem = function (bool $active): string {
        return 'flex items-center rounded-xl text-xs font-semibold transition ' . ($active
            ? 'bg-brand-50 text-brand-600 dark:bg-brand-950/50 dark:text-brand-300'
            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-brand-600 dark:hover:text-brand-400');
    };
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 flex h-dvh w-64 flex-col overflow-hidden bg-white dark:bg-slate-800 border-r border-gray-100 dark:border-slate-700/80
        transition-[width,transform] duration-200
        max-lg:w-[min(16rem,calc(100vw-3.5rem))] max-lg:-translate-x-full max-lg:shadow-xl"
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

    @php
        $navGroup = 'px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500';
    @endphp

    <nav class="sidebar-scroll min-h-0 flex-1 overflow-y-auto overscroll-contain px-2.5 py-4 space-y-0.5">
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

        {{-- Group: Presensi --}}
        <p class="{{ $navGroup }}" x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Presensi</p>
        <hr class="border-slate-100 dark:border-slate-700/60 !my-1" x-show="sidebarCollapsed && !sidebarOpen" x-cloak>

        <a href="{{ route('presensi.index') }}" title="QR Saya"
            class="{{ $navItem(request()->routeIs('presensi.index')) }}"
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
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>QR Saya</span>
        </a>

        <a href="{{ route('presensi.history') }}" title="Riwayat"
            class="{{ $navItem(request()->routeIs('presensi.history')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Riwayat</span>
        </a>

        @if (auth()->user()->canScanPresensi())
            <a href="{{ route('presensi.scan') }}" title="Scan QR"
                class="{{ $navItem(request()->routeIs('presensi.scan')) }}"
                :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Scan QR</span>
            </a>
        @endif

        @if (auth()->user()->canViewPanitiaList())
            <a href="{{ route('presensi.monitoring') }}" title="Monitoring"
                class="{{ $navItem(request()->routeIs('presensi.monitoring')) }}"
                :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Monitoring</span>
            </a>
        @endif

        {{-- Group: Perizinan --}}
        <p class="{{ $navGroup }}" x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Perizinan</p>
        <hr class="border-slate-100 dark:border-slate-700/60 !my-1" x-show="sidebarCollapsed && !sidebarOpen" x-cloak>

        <a href="{{ route('pengajuan-izin.index') }}" title="Pengajuan Izin"
            class="{{ $navItem(request()->routeIs('pengajuan-izin.index') || request()->routeIs('pengajuan-izin.create')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Pengajuan Izin</span>
        </a>

        @if (auth()->user()->canReviewIzin())
            <a href="{{ route('pengajuan-izin.review') }}" title="Review Izin"
                class="{{ $navItem(request()->routeIs('pengajuan-izin.review')) }}"
                :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Review Izin</span>
            </a>
        @endif

        {{-- Group: Hukuman --}}
        <p class="{{ $navGroup }}" x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Hukuman</p>
        <hr class="border-slate-100 dark:border-slate-700/60 !my-1" x-show="sidebarCollapsed && !sidebarOpen" x-cloak>

        <a href="{{ route('hukuman.index') }}" title="Hukuman Saya"
            class="{{ $navItem(request()->routeIs('hukuman.index') || request()->routeIs('hukuman.show')) }}"
            :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Hukuman Saya</span>
        </a>

        @if (auth()->user()->canIssueHukumanRanger())
            <a href="{{ route('hukuman.kelola', 'ranger') }}" title="Kelola Hukuman"
                class="{{ $navItem(request()->routeIs('hukuman.kelola') && request()->route('mode') !== 'pengawas' || request()->routeIs('hukuman.create') && request()->route('mode') !== 'pengawas') }}"
                :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Kelola Hukuman</span>
            </a>
        @endif

        @if (auth()->user()->canIssueHukumanPengawas())
            <a href="{{ route('hukuman.kelola', 'pengawas') }}" title="Hukuman Pengawas"
                class="{{ $navItem(request()->routeIs('hukuman.kelola') && request()->route('mode') === 'pengawas' || request()->routeIs('hukuman.create') && request()->route('mode') === 'pengawas') }}"
                :class="sidebarCollapsed && !sidebarOpen ? 'justify-center p-2.5' : 'gap-2.5 px-3 py-2'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                <span x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Hukuman Pengawas</span>
            </a>
        @endif

        @if (auth()->user()->isAdmin())
            {{-- Group: Admin --}}
            <p class="{{ $navGroup }}" x-show="!sidebarCollapsed || sidebarOpen" x-cloak>Admin</p>
            <hr class="border-slate-100 dark:border-slate-700/60 !my-1" x-show="sidebarCollapsed && !sidebarOpen" x-cloak>

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
