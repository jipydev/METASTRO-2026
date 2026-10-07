<x-guest-layout :$title>

    <main class="w-full bg-white dark:bg-slate-900 min-h-screen shadow-2xl relative overflow-x-hidden">
        <header
            class="w-full bg-white/50 dark:bg-slate-900/50 backdrop-blur-xs px-4 py-2.5 flex items-center justify-between border-b border-orange-100/80 dark:border-slate-800 sticky top-0 z-40">
            <div class="container mx-auto flex justify-between items-center gap-2 sm:gap-4">
                <a href="{{ route('landing-page') }}" class="flex items-center tracking-tight select-none font-oswald">
                    <span class="font-black text-lg text-[#FF7300]">METASTRO 2026</span>
                </a>
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
                    <img src="{{ $sponsorImg }}" alt="Sponsor Logos" class="h-5 md:h-6 object-contain w-auto"
                        onerror="this.src='{{ asset('images/logo.webp') }}'">
                </div>
            </div>
        </header>

        <div class="container mx-auto">
            <section class="relative pt-10 pb-8 text-center overflow-visible">
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/peserta/bintang2.png') }}" alt="Bintang"
                        class="w-10 h-10 object-contain">
                </div>
                <div class="container mx-auto">
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#FF7300] mb-2">
                        Daftar Tim METASTRO 2026
                    </h1>
                    <p class="text-slate-700 dark:text-slate-300 text-xs sm:text-[13px] leading-relaxed">
                        Temukan nama dan tim kamu di bawah ini.
                    </p>
                </div>
            </section>

            <section class=" py-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-5">
                    @foreach ($tims as $tim)
                        <button type="button" onclick="openModal({{ $tim->id }})"
                            class="group bg-white dark:bg-slate-800 rounded-xl p-5 text-left border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg hover:border-[#FF7300] focus:outline-none focus:ring-2 focus:ring-[#FF7300]">
                            <h2 class="font-oswald font-bold text-slate-900 dark:text-white uppercase tracking-wide mb-3 line-clamp-1 group-hover:text-[#FF7300] transition-colors"
                                title="{{ $tim->nama }}">{{ $tim->nama }}</h2>
                            <span
                                class="inline-block bg-orange-50 dark:bg-orange-950/40 text-[#FF7300] text-[10px] font-bold px-2.5 py-1 rounded-full uppercase border border-orange-200 dark:border-orange-800">Detail
                                Tim</span>
                        </button>

                        <!-- Modal for this Team -->
                        <dialog id="modal-{{ $tim->id }}"
                            class="bg-transparent m-0 p-0 w-full h-full max-w-none max-h-none backdrop:bg-black/50 backdrop:backdrop-blur-sm fixed inset-0 z-50 flex items-center justify-center hidden">
                            <!-- Overlay click to close -->
                            <div class="absolute inset-0 z-0" onclick="closeModal({{ $tim->id }})"></div>

                            <!-- Modal Content -->
                            <div
                                class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-[90%] max-w-xl max-h-[85vh] overflow-y-auto relative z-10 mx-auto mt-10 md:mt-0 flex flex-col font-poppins border border-slate-100 dark:border-slate-700">
                                <div
                                    class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-slate-800 z-20">
                                    <h3
                                        class="font-oswald font-bold text-lg text-slate-900 dark:text-white uppercase tracking-wide truncate pr-4">
                                        {{ $tim->nama }}</h3>
                                    <button onclick="closeModal({{ $tim->id }})"
                                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1"
                                        title="Tutup">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="p-5 space-y-6">
                                    <!-- Guiders -->
                                    <div>
                                        <h4 class="font-bold text-[#111827] dark:text-white mb-3">👥 Guider:</h4>
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            @foreach ($tim->guiders as $guider)
                                                @php($pembimbing = $guider->pembimbing)
                                                <article
                                                    class="flex min-w-0 items-center gap-3 rounded-2xl border border-orange-100 bg-orange-50/60 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-700/40">
                                                    @if ($pembimbing?->foto && file_exists(public_path("foto_profil/$pembimbing->foto")))
                                                        <img src="{{ asset("foto_profil/$pembimbing->foto") }}"
                                                            alt="{{ $pembimbing->nama }}"
                                                            class="h-14 w-14 shrink-0 rounded-2xl object-cover ring-2 ring-white dark:ring-slate-600">
                                                    @else
                                                        <div
                                                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#FF7300] text-base font-bold text-white shadow-sm">
                                                            {{ $pembimbing?->initials() ?? '?' }}
                                                        </div>
                                                    @endif
                                                    <div class="min-w-0">
                                                        <p
                                                            class="mb-1 text-[10px] font-bold uppercase tracking-wider text-[#FF7300]">
                                                            Guider
                                                        </p>
                                                        <span class="group relative block max-w-full" tabindex="0"
                                                            title="{{ $pembimbing?->nama ?? 'Guider belum tersedia' }}">
                                                            <p
                                                                class="truncate font-semibold text-slate-900 dark:text-white">
                                                                {{ $pembimbing?->nama ?? 'Guider belum tersedia' }}
                                                            </p>
                                                            <span role="tooltip"
                                                                class="pointer-events-none invisible absolute bottom-full left-0 z-30 mb-2 w-max max-w-[18rem] rounded-lg bg-slate-900 px-3 py-2 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:visible group-hover:opacity-100 group-focus:visible group-focus:opacity-100 dark:bg-white dark:text-slate-900">
                                                                {{ $pembimbing?->nama ?? 'Guider belum tersedia' }}
                                                            </span>
                                                        </span>
                                                        @if ($pembimbing?->nomor_hp)
                                                            <a href="tel:{{ $pembimbing->nomor_hp }}"
                                                                class="mt-1 block truncate text-xs text-slate-500 transition-colors hover:text-[#FF7300] dark:text-slate-300"
                                                                title="Hubungi guider">
                                                                {{ $pembimbing->nomor_hp }}
                                                            </a>
                                                        @else
                                                            <p class="mt-1 text-xs italic text-slate-400">Nomor belum
                                                                tersedia</p>
                                                        @endif
                                                    </div>
                                                </article>
                                            @endforeach
                                            @if ($tim->guiders->isEmpty())
                                                <p class="text-slate-500 italic ml-4">Belum ada guider.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Members -->
                                    <div>
                                        <h4 class="font-bold text-[#111827] dark:text-white mb-3">👥 Anggota:</h4>
                                        <ol
                                            class="list-decimal list-inside space-y-1.5 text-sm text-slate-700 dark:text-slate-300">
                                            @foreach ($tim->members as $member)
                                                <li>
                                                    {{ $member->nama }}

                                                </li>
                                            @endforeach
                                            @if ($tim->members->isEmpty())
                                                <li class="text-slate-500 italic list-none">Belum ada anggota.</li>
                                            @endif
                                        </ol>
                                    </div>

                                    <!-- Note Callout -->
                                    <div
                                        class="bg-[#FEFCE8] dark:bg-yellow-900/20 border border-[#F59E0B] rounded-xl p-4 text-center mt-6">
                                        <h4 class="font-bold text-[#111827] dark:text-amber-400 mb-2">Catatan 📝</h4>
                                        <p class="text-sm text-slate-700 dark:text-slate-300">Setelah mengetahui tim
                                            kamu,
                                            jangan lupa hubungi kontak guider masing-masing untuk mendapatkan informasi
                                            lebih
                                            lanjut. Semangat dengan tim barunya!</p>
                                    </div>
                                </div>
                            </div>
                        </dialog>
                    @endforeach
                </div>
            </section>
        </div>

        <footer
            class="w-full py-5 text-center text-[11px] text-slate-400 border-t border-slate-100 dark:border-slate-800">
            &copy; 2026 METASTRO &bull; All Rights Reserved.
        </footer>
    </main>

    <!-- Modal Logic -->
    <script>
        function openModal(id) {
            const dialog = document.getElementById('modal-' + id);
            if (dialog) {
                dialog.showModal();
                dialog.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const dialog = document.getElementById('modal-' + id);
            if (dialog) {
                if (dialog.open) {
                    dialog.close();
                }
                dialog.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.querySelectorAll('dialog[id^="modal-"]').forEach((dialog) => {
            dialog.addEventListener('cancel', (event) => {
                event.preventDefault();
                closeModal(dialog.id.replace('modal-', ''));
            });

            dialog.addEventListener('close', () => {
                dialog.classList.add('hidden');
                document.body.style.overflow = '';
            });
        });
    </script>
</x-guest-layout>
