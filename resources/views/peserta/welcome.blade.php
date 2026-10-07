<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selamat Datang - METASTRO 2026</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Schoolbell&display=swap"
        rel="stylesheet">
    <style>
        .font-plus-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-schoolbell {
            font-family: 'Schoolbell', cursive;
        }

        .font-inter {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f7f3ee;
            background-image: url('{{ asset('images/peserta/welcome-bg.png') }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="font-inter text-[#334155] min-h-screen min-h-dvh pb-28 antialiased selection:bg-[#FE9100]/20 selection:text-[#FE9100]">

    <div class="max-w-md md:max-w-xl mx-auto px-4 py-6 md:py-8">

        <!-- Welcome Banner -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl md:rounded-3xl p-5 md:p-6 text-center shadow-sm border border-white/80 mb-5">
            <h1 class="font-plus-jakarta text-xl md:text-2xl text-[#111827] font-bold mb-1 flex items-center justify-center gap-1.5">
                <span>🎉</span> Selamat datang
            </h1>
            <p class="font-inter text-xs md:text-sm text-[#334155]">Cari nama &amp; tim kamu di bawah ini!</p>
        </div>

        <!-- Team Grid -->
        <div class="grid grid-cols-2 gap-3 md:gap-4">
            @foreach($tims as $tim)
                @php $isUserTim = $tim->id === $userTimId; @endphp
                <button type="button" onclick="openModal({{ $tim->id }})"
                    class="group text-left bg-white/95 backdrop-blur-sm rounded-2xl p-4 md:p-5 flex flex-col justify-between shadow-sm transition-all duration-200 hover:shadow-md focus:outline-none {{ $isUserTim ? 'border-2 border-[#FE9100] ring-2 ring-[#FE9100]/20' : 'border border-white/80 hover:border-[#FE9100]/40' }}">
                    <div>
                        <div class="flex items-start justify-between gap-1 mb-2">
                            <h2 class="font-plus-jakarta font-bold text-xs sm:text-sm md:text-base text-[#111827] uppercase leading-tight truncate w-full"
                                title="{{ $tim->nama }}">
                                {{ $tim->nama }}
                            </h2>
                        </div>

                        <div class="mb-3">
                            <span class="text-[11px] md:text-xs text-slate-400 font-medium block mb-1">Guider:</span>
                            <ul class="space-y-0.5 text-[11px] md:text-xs text-[#334155]">
                                @forelse($tim->guiders as $guider)
                                    <li class="truncate">• {{ $guider->pembimbing->nama }}</li>
                                @empty
                                    <li class="text-slate-400 italic">• Belum ada</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-[11px] md:text-xs font-semibold text-[#10B981] group-hover:text-emerald-600 transition-colors flex items-center gap-1">
                            Lihat anggota &rarr;
                        </span>
                        @if($isUserTim)
                            <span class="bg-[#FE9100] text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider">Kamu</span>
                        @endif
                    </div>
                </button>

                <!-- Modal for this Team -->
                <dialog id="modal-{{ $tim->id }}"
                    class="bg-transparent m-0 p-0 w-full h-full max-w-none max-h-none backdrop:bg-black/40 backdrop:backdrop-blur-sm fixed inset-0 z-50 flex items-center justify-center hidden">
                    <!-- Overlay click to close -->
                    <div class="absolute inset-0 z-0" onclick="closeModal({{ $tim->id }})"></div>

                    <!-- Modal Content -->
                    <div class="bg-white rounded-2xl md:rounded-3xl shadow-2xl w-[92%] max-w-sm max-h-[85vh] overflow-y-auto relative z-10 mx-auto flex flex-col font-inter border border-white">
                        <!-- Modal Header -->
                        <div class="p-4 md:p-5 pb-2 flex justify-between items-center sticky top-0 bg-white z-20">
                            <h3 class="font-plus-jakarta font-bold text-base md:text-lg text-[#111827] uppercase truncate pr-3">
                                {{ $tim->nama }}
                            </h3>
                            <button type="button" onclick="closeModal({{ $tim->id }})"
                                class="text-slate-400 hover:text-slate-700 transition-colors p-1"
                                title="Tutup">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-4 md:px-5 space-y-3 pb-4">
                            <!-- Guiders -->
                            <div>
                                <h4 class="font-bold text-[#111827] text-xs md:text-sm mb-2 flex items-center gap-1.5">
                                    👥 Guider:
                                </h4>
                                <ul class="space-y-1 text-xs text-[#334155]">
                                    @forelse($tim->guiders as $guider)
                                        <li class="flex items-center gap-1.5 flex-wrap">
                                            <span>• {{ $guider->pembimbing->nama }}</span>
                                            @if($guider->pembimbing->nomor_hp)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guider->pembimbing->nomor_hp) }}"
                                                    target="_blank"
                                                    class="text-blue-600 hover:text-blue-700 hover:underline font-medium">
                                                    ({{ $guider->pembimbing->nomor_hp }})
                                                </a>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="text-slate-400 italic ml-2">• Belum ada guider.</li>
                                    @endforelse
                                </ul>
                            </div>

                            <!-- Orange Divider -->
                            <div class="border-b border-[#FE9100]/80 my-2"></div>

                            <!-- Members -->
                            <div>
                                <h4 class="font-bold text-[#111827] text-xs md:text-sm mb-2 flex items-center gap-1.5">
                                    👥 Anggota:
                                </h4>
                                <ol class="space-y-1 text-xs text-[#334155]">
                                    @forelse($tim->members as $index => $member)
                                        <li class="flex items-center gap-1.5 {{ $member->id === $user->id ? 'font-bold text-[#FE9100]' : '' }}">
                                            <span class="w-4 text-slate-400 text-right">{{ $index + 1 }}.</span>
                                            <span>{{ $member->nama }}</span>
                                            @if($member->id === $user->id)
                                                <span class="text-[10px] bg-[#FE9100]/10 text-[#FE9100] px-1.5 py-0.5 rounded font-bold uppercase ml-1">(Kamu)</span>
                                            @endif
                                        </li>
                                    @empty
                                        <li class="text-slate-400 italic ml-2">Belum ada anggota.</li>
                                    @endforelse
                                </ol>
                            </div>

                            <!-- Note Callout -->
                            <div class="bg-[#FEFCE8] border border-[#F59E0B] rounded-xl p-3 text-center mt-3">
                                <h5 class="font-bold text-[#111827] text-xs mb-1">Catatan 📝</h5>
                                <p class="text-[11px] leading-relaxed text-slate-700">
                                    Setelah mengetahui tim kamu, jangan lupa hubungi kontak guider masing-masing untuk mendapatkan informasi lebih lanjut. Semangat dengan tim barunya!
                                </p>
                            </div>
                        </div>
                    </div>
                </dialog>
            @endforeach
        </div>

    </div>

    <!-- Sticky Bottom Action Button -->
    <div class="fixed bottom-0 left-0 right-0 z-40 p-4 pb-6 flex justify-center pointer-events-none">
        <div class="w-full max-w-sm px-2 pointer-events-auto">
            <a href="{{ route('peserta.dashboard') }}"
                class="w-full flex items-center justify-center py-3.5 px-6 bg-white/95 backdrop-blur hover:bg-white transition-all duration-200 border border-[#FE9100] text-[#10B981] hover:text-[#059669] font-bold rounded-2xl shadow-lg hover:shadow-xl font-plus-jakarta text-center text-sm md:text-base">
                Lanjut ke Dashboard
            </a>
        </div>
    </div>

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
                dialog.close();
                dialog.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    </script>
</body>

</html>