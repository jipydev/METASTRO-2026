<x-guest-layout title="Akses Ditolak">

    <!-- Container Mobile View (Tengah di Desktop, Full-width di Mobile) -->
    <main
        class="w-full bg-white dark:bg-slate-900 min-h-screen shadow-2xl relative flex flex-col justify-between overflow-x-hidden border-x border-slate-200/60 dark:border-slate-800">

        <!-- 1. TOP NAVBAR -->
        <header
            class="w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-xs px-4 py-2.5 flex items-center justify-between border-b border-orange-100/80 dark:border-slate-800 fixed left-0 right-0 top-0 z-40">
            <div class="container mx-auto flex justify-between items-center gap-2 sm:gap-4">
                <!-- Brand Logo Metastro 2026 -->
                <div class="flex items-center tracking-tight select-none font-oswald">
                    <span class="font-black text-lg text-[#FF7300]">METASTRO 2026</span>
                </div>

                <!-- Sponsor / Organization Logos -->
                <div class="flex items-center gap-2">
                    @php
                        $sponsorImg = file_exists(public_path('images/peserta/sponsor.webp'))
                            ? asset('images/peserta/sponsor.webp')
                            : (file_exists(public_path('images/peserta/sponsor.png'))
                                ? asset('images/peserta/sponsor.png')
                                : (file_exists(public_path('images/peserta/sponsor.jpg'))
                                    ? asset('images/peserta/sponsor.jpg')
                                    : asset('images/logo.webp')));
                    @endphp
                    <div class="shrink-0">
                        <img src="{{ $sponsorImg }}" alt="Sponsor Logos" class="h-5 md:h-6 object-contain w-auto"
                            onerror="this.src='{{ asset('images/logo.webp') }}'">
                    </div>
                </div>
            </div>
        </header>

        <!-- 2. CONTENT SECTION -->
        <section
            class="relative w-full flex-1 flex flex-col justify-center items-center text-center px-6 py-16 mt-12 overflow-hidden">
            
            <!-- Hiasan Bintang Kecil -->
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/peserta/bintang2.png') }}" alt="Bintang" class="w-10 h-10 object-contain animate-pulse">
            </div>

            <!-- Konten Teks Simple -->
            <div class="flex flex-col items-center max-w-md mx-auto">
                <span class="font-oswald text-xs sm:text-sm font-bold text-[#FF7300] tracking-widest uppercase mb-1.5 px-3 py-1 bg-orange-50 dark:bg-orange-950/40 rounded-full border border-orange-200/60 dark:border-orange-900/50">
                    403 - AKSES DITOLAK
                </span>

                <!-- Pesan Penjelasan Singkat -->
                <p class="font-poppins text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-4 leading-relaxed">
                    Anda tidak memiliki izin atau otoritas role yang cukup untuk mengakses halaman <strong class="text-[#FF7300]">METASTRO 2026</strong> ini.
                </p>
            </div>

            <!-- Tombol Beranda -->
            <div class="mt-8 w-full flex flex-col items-center">
                <div class="flex items-center justify-center gap-4">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg bg-[#FF7300] hover:bg-[#e06500] text-white font-poppins text-xs sm:text-sm font-semibold tracking-wider uppercase shadow-[0_0_15px_rgba(255,115,0,0.35)] transition-all duration-200 transform hover:scale-105 active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

            <!-- Hiasan Sudut -->
            <img src="{{ asset('images/peserta/gradient.png') }}" alt=""
                class="absolute -bottom-6 -right-4 w-32 sm:w-36 h-auto object-contain pointer-events-none opacity-40 dark:opacity-20 z-0">
        </section>

        <!-- 3. BOTTOM FOOTER -->
        <footer
            class="w-full py-4 text-center text-[11px] text-slate-400 border-t border-slate-100 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-xs relative z-10">
            &copy; 2026 METASTRO &bull; All Rights Reserved.
        </footer>
    </main>

</x-guest-layout>