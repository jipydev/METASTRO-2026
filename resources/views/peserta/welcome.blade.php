<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selamat Datang - METASTRO 2026</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Schoolbell&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-plus-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-schoolbell { font-family: 'Schoolbell', cursive; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-inter bg-[#FFFDF9] dark:bg-slate-900 text-[#334155] dark:text-slate-100 min-h-screen pb-24">

    <div class="max-w-4xl mx-auto px-4 py-8">
        
        <!-- Welcome Banner -->
        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-700 rounded-2xl p-6 text-center shadow-sm mb-8">
            <h1 class="font-schoolbell text-3xl md:text-4xl text-[#111827] dark:text-amber-400 font-bold mb-2">🎉 Selamat datang, {{ $user->nama }}!</h1>
            <p class="text-slate-600 dark:text-slate-300 font-medium">Cari nama & tim kamu di bawah ini!</p>
        </div>

        <!-- Team Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
            @foreach($tims as $tim)
                @php $isUserTim = $tim->id === $userTimId; @endphp
                <button type="button" 
                    onclick="openModal({{ $tim->id }})"
                    class="bg-white dark:bg-slate-800 rounded-xl p-4 md:p-5 text-left border shadow-sm transition-all hover:shadow-md hover:border-[#FE9100] focus:outline-none focus:ring-2 focus:ring-[#FE9100] {{ $isUserTim ? 'border-[#FE9100] ring-1 ring-[#FE9100]' : 'border-slate-200 dark:border-slate-700' }}">
                    <h2 class="font-plus-jakarta font-bold text-[#111827] dark:text-white uppercase mb-2 line-clamp-1" title="{{ $tim->nama }}">{{ $tim->nama }}</h2>
                    <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed mb-3">
                        @foreach($tim->members as $member)
                            {{ $member->nama }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>
                    @if($isUserTim)
                        <span class="inline-block bg-[#FE9100] text-white text-[10px] font-bold px-2 py-1 rounded-full uppercase">Tim Kamu</span>
                    @else
                        <span class="inline-block bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-[10px] font-bold px-2 py-1 rounded-full uppercase">Detail Tim</span>
                    @endif
                </button>

                <!-- Modal for this Team -->
                <dialog id="modal-{{ $tim->id }}" class="bg-transparent m-0 p-0 w-full h-full max-w-none max-h-none backdrop:bg-black/50 backdrop:backdrop-blur-sm fixed inset-0 z-50 flex items-center justify-center hidden">
                    <!-- Overlay click to close -->
                    <div class="absolute inset-0 z-0" onclick="closeModal({{ $tim->id }})"></div>
                    
                    <!-- Modal Content -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-[90%] max-w-md max-h-[85vh] overflow-y-auto relative z-10 mx-auto mt-10 md:mt-0 flex flex-col font-inter border border-slate-100 dark:border-slate-700">
                        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center sticky top-0 bg-white dark:bg-slate-800 z-20">
                            <h3 class="font-plus-jakarta font-bold text-lg text-[#111827] dark:text-white uppercase truncate pr-4">{{ $tim->nama }}</h3>
                            <button onclick="closeModal({{ $tim->id }})" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1" title="Tutup">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="p-5 space-y-6">
                            <!-- Guiders -->
                            <div>
                                <h4 class="font-bold text-[#111827] dark:text-white mb-3">👥 Guider:</h4>
                                <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                                    @foreach($tim->guiders as $guider)
                                        <li class="flex items-start">
                                            <span class="mr-2 mt-0.5">•</span>
                                            <span>
                                                {{ $guider->pembimbing->nama }} 
                                                @if($guider->pembimbing->nomor_hp)
                                                    <span class="text-blue-600 dark:text-blue-400 font-medium whitespace-nowrap">({{ $guider->pembimbing->nomor_hp }})</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                    @if($tim->guiders->isEmpty())
                                        <li class="text-slate-500 italic ml-4">Belum ada guider.</li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Members -->
                            <div>
                                <h4 class="font-bold text-[#111827] dark:text-white mb-3">👥 Anggota:</h4>
                                <ol class="list-decimal list-inside space-y-1.5 text-sm text-slate-700 dark:text-slate-300">
                                    @foreach($tim->members as $member)
                                        <li class="{{ $member->id === $user->id ? 'font-bold text-[#FE9100] dark:text-amber-400' : '' }}">
                                            {{ $member->nama }}
                                            @if($member->id === $user->id)
                                                <span class="text-xs bg-[#FE9100]/10 text-[#FE9100] ml-1 px-1.5 py-0.5 rounded font-bold uppercase">(Kamu)</span>
                                            @endif
                                        </li>
                                    @endforeach
                                    @if($tim->members->isEmpty())
                                        <li class="text-slate-500 italic list-none">Belum ada anggota.</li>
                                    @endif
                                </ol>
                            </div>

                            <!-- Note Callout -->
                            <div class="bg-[#FEFCE8] dark:bg-yellow-900/20 border border-[#F59E0B] rounded-xl p-4 text-center mt-6">
                                <h4 class="font-bold text-[#111827] dark:text-amber-400 mb-2">Catatan 📝</h4>
                                <p class="text-sm text-slate-700 dark:text-slate-300">Setelah mengetahui tim kamu, jangan lupa hubungi kontak guider masing-masing untuk mendapatkan informasi lebih lanjut. Semangat dengan tim barunya!</p>
                            </div>
                        </div>
                    </div>
                </dialog>
            @endforeach
        </div>

    </div>

    <!-- Sticky Bottom Action Button -->
    <div class="fixed bottom-0 left-0 right-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 p-4 z-40 flex justify-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <div class="w-full max-w-4xl px-4 flex justify-center">
            <a href="{{ route('peserta.dashboard') }}" class="w-full md:w-auto min-w-[280px] flex items-center justify-center gap-2 px-6 py-3.5 bg-[#15803D] hover:bg-[#166534] transition-colors text-white font-bold rounded-xl shadow-lg hover:shadow-xl font-plus-jakarta text-center">
                Lanjut ke Dashboard
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <!-- Modal Logic -->
    <script>
        function openModal(id) {
            const dialog = document.getElementById('modal-' + id);
            if(dialog) {
                dialog.showModal();
                dialog.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }
        function closeModal(id) {
            const dialog = document.getElementById('modal-' + id);
            if(dialog) {
                dialog.close();
                dialog.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    </script>
</body>
</html>