<!-- TOP HEADER APP BAR (NAVBAR SELALU ADA) -->
<header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex items-center justify-between py-3.5 px-4 mb-4 -mx-4 -mt-5 shadow-sm border-b border-slate-100 dark:border-slate-800 rounded-b-2xl transition-colors duration-200">
    <!-- Brand Title -->
    <a href="{{ route('peserta.dashboard') }}" class="inline-block group">
        <h1 class="font-oswald-header text-xl sm:text-2xl font-bold uppercase tracking-[0.14em] text-[#1E293B] dark:text-white transition group-hover:text-[#FF5B00]">
            METASTRO 2026
        </h1>
    </a>

    <!-- Action Icons: Notifikasi & Hamburger Menu -->
    <div class="flex items-center gap-2">
        <!-- Notification Bell -->
        <button type="button" @click="notifOpen = !notifOpen"
            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 relative transition cursor-pointer"
            title="Notifikasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <!-- Red Dot Notification Badge -->
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
        </button>

        <!-- Hamburger Menu Button (Garis 3) -->
        <button type="button" @click="mobileMenu = true"
            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 text-[#1E293B] dark:text-slate-200 transition cursor-pointer"
            title="Buka Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            </svg>
        </button>
    </div>
</header>

<!-- NOTIFIKASI MODAL / POPOVER -->
<div x-show="notifOpen" x-cloak class="fixed inset-0 z-50 flex items-start justify-end p-4 bg-black/30 backdrop-blur-xs"
    @click.self="notifOpen = false"
    @keydown.escape.window="notifOpen = false">
    <div class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-xs mt-12 p-4 shadow-xl border border-slate-100 dark:border-slate-700">
        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 dark:border-slate-700">
            <span class="font-bold text-xs text-[#1E293B] dark:text-white">Pemberitahuan</span>
            <button type="button" @click="notifOpen = false" class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">Tutup</button>
        </div>
        <div class="space-y-2.5 text-xs">
            <div class="p-2.5 rounded-xl bg-orange-50 dark:bg-orange-950/40 border border-orange-100 dark:border-orange-900/40">
                <p class="font-semibold text-[#FF5B00]">Deadline Terdekat!</p>
                <p class="text-slate-600 dark:text-slate-300 mt-0.5 text-[11px]">Video perkenalan singkat berakhir dalam waktu 12 menit lagi.</p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                <p class="font-semibold text-[#1E293B] dark:text-slate-200">Pengingat Perlengkapan</p>
                <p class="text-slate-500 dark:text-slate-400 mt-0.5 text-[11px]">Jangan lupa periksa buku angkatan dan nametag Anda untuk Day 1.</p>
            </div>
        </div>
    </div>
</div>

<!-- SLIDE-OVER NAVIGATION DRAWER (SIDEBAR) -->
<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 flex justify-end"
    @keydown.escape.window="mobileMenu = false">
    <!-- Backdrop -->
    <div x-show="mobileMenu" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="mobileMenu = false"></div>

    <!-- Drawer Content -->
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="relative w-72 max-w-[80vw] bg-white dark:bg-slate-800 h-full p-5 shadow-2xl flex flex-col justify-between z-10 overflow-y-auto">
        
        <div>
            <!-- Header Drawer -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                <div>
                    <h4 class="font-oswald-header text-base font-bold uppercase tracking-wider text-[#1E293B] dark:text-white">
                        METASTRO 2026
                    </h4>
                    <p class="text-[11px] text-slate-400">Portal Peserta</p>
                </div>
                <button type="button" @click="mobileMenu = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- User Info Card -->
            @php
                $user = auth()->user();
                $timAnggota = $user ? \App\Models\AnggotaTim::where('anggota_id', $user->id)->with('tim')->first() : null;
                $reguName = $timAnggota?->tim?->nama ?? 'Regu Peserta';
            @endphp
            <div class="flex gap-3 mb-6 mt-4 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-700">
                <div class="text-[#FF5B00] mt-0.5 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="min-w-0 flex-1 text-xs">
                    <p class="font-semibold text-[#1E293B] dark:text-white truncate">{{ $user?->nama ?? $user?->name ?? 'Peserta' }}</p>
                    <p class="text-slate-500 dark:text-slate-400 mt-0.5">NIM: {{ $user?->nim ?? '-' }}</p>
                    <p class="text-slate-500 dark:text-slate-400">Regu: {{ $reguName }}</p>
                </div>
            </div>

            <!-- Menu Links -->
            <nav class="space-y-4">
                <div>
                    <h5 class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mb-2 px-3">NAVIGASI UTAMA</h5>
                    <div class="space-y-1 text-sm">
                        <!-- Beranda -->
                        <a href="{{ route('peserta.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2 font-medium transition border-b-2 {{ request()->routeIs('peserta.dashboard') ? 'border-[#FF5B00] text-[#1E293B] dark:text-white font-semibold' : 'border-transparent text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white hover:border-slate-200 dark:hover:border-slate-700' }}">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('peserta.dashboard') ? 'text-[#FF5B00]' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Beranda</span>
                        </a>

                        <!-- List Tim -->
                        <a href="{{ route('peserta.welcome') }}"
                            class="flex items-center gap-3 px-3 py-2 font-medium transition border-b-2 {{ request()->routeIs('peserta.welcome') ? 'border-[#FF5B00] text-[#1E293B] dark:text-white font-semibold' : 'border-transparent text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white hover:border-slate-200 dark:hover:border-slate-700' }}">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('peserta.welcome') ? 'text-[#FF5B00]' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>List Tim</span>
                        </a>

                        <!-- List Penugasan -->
                        <a href="{{ route('peserta.tugas-list') }}"
                            class="flex items-center gap-3 px-3 py-2 font-medium transition border-b-2 {{ request()->routeIs('peserta.tugas-list') || request()->routeIs('peserta.tugas-hari') || request()->routeIs('peserta.tugas-kumpulkan') ? 'border-[#FF5B00] text-[#1E293B] dark:text-white font-semibold' : 'border-transparent text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white hover:border-slate-200 dark:hover:border-slate-700' }}">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('peserta.tugas-list') || request()->routeIs('peserta.tugas-hari') || request()->routeIs('peserta.tugas-kumpulkan') ? 'text-[#FF5B00]' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span>List Penugasan</span>
                        </a>

                        <!-- Arsip Tugas -->
                        <a href="{{ route('peserta.arsip') }}"
                            class="flex items-center gap-3 px-3 py-2 font-medium transition border-b-2 {{ request()->routeIs('peserta.arsip') ? 'border-[#FF5B00] text-[#1E293B] dark:text-white font-semibold' : 'border-transparent text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white hover:border-slate-200 dark:hover:border-slate-700' }}">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('peserta.arsip') ? 'text-[#FF5B00]' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            <span>Arsip Tugas</span>
                        </a>

                        <!-- Izin -->
                        <a href="{{ route('dashboard.pengajuan-izin.index') }}"
                            class="flex items-center gap-3 px-3 py-2 font-medium transition border-b-2 {{ request()->routeIs('dashboard.pengajuan-izin.*') ? 'border-[#FF5B00] text-[#1E293B] dark:text-white font-semibold' : 'border-transparent text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white hover:border-slate-200 dark:hover:border-slate-700' }}">
                            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard.pengajuan-izin.*') ? 'text-[#FF5B00]' : 'text-slate-500 dark:text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Pengajuan Izin</span>
                        </a>
                    </div>
                </div>

                <div>
                    <h5 class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mb-2 px-3 mt-4">INFORMASI & MEDIA SOSIAL</h5>
                    <div class="space-y-1 text-sm">
                        <!-- Booklet -->
                        <a href="#" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-3 px-3 py-2 font-medium text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white border-b-2 border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition">
                            <svg class="w-5 h-5 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Booklet</span>
                        </a>

                        <!-- Instagram -->
                        <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-3 px-3 py-2 font-medium text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white border-b-2 border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition">
                            <svg class="w-5 h-5 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8a5 5 0 015-5h8a5 5 0 015 5v8a5 5 0 01-5 5H8a5 5 0 01-5-5V8zm5-3a3 3 0 00-3 3v8a3 3 0 003 3h8a3 3 0 003-3V8a3 3 0 00-3-3H8zm7.5 9.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zm-2.5-4a4 4 0 100 8 4 4 0 000-8zm4.5-1.5a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                            <span>Instagram</span>
                        </a>

                        <!-- Dark Mode Toggle -->
                        <button type="button" @click="toggleTheme()"
                            class="w-full flex items-center gap-3 px-3 py-2 font-medium text-slate-700 dark:text-slate-300 hover:text-[#1E293B] dark:hover:text-white border-b-2 border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition cursor-pointer">
                            <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="!darkMode" x-cloak class="w-5 h-5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <span x-text="darkMode ? 'Mode Terang' : 'Mode Gelap'"></span>
                        </button>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Bottom of Drawer: Logout -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-700 mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
