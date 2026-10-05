<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selamat Datang - METASTRO 2026</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Schoolbell&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
<body class="font-plus-jakarta bg-[#FFFDF9] dark:bg-slate-900 text-[#334155] dark:text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-3xl w-full bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <!-- Header -->
        <div class="bg-[#FE9100] p-8 text-center relative overflow-hidden">
            <h1 class="font-schoolbell text-4xl md:text-5xl text-white mb-2 relative z-10">Selamat Datang, {{ $user->nama }}!</h1>
            <p class="font-inter text-white/90 relative z-10">Persiapkan dirimu untuk petualangan METASTRO 2026</p>
        </div>

        <!-- Body -->
        <div class="p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Informasi Tim Kamu</h2>
            
            @if($tim)
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-6 mb-8 border border-slate-100 dark:border-slate-600">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-16 h-16 bg-[#10B981]/10 rounded-full flex items-center justify-center border-2 border-[#10B981]">
                            <svg class="w-8 h-8 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-center mb-8 text-[#FE9100]">{{ $tim->nama }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Guiders -->
                        <div>
                            <h4 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <span class="text-[#10B981]">●</span> Team Guiders
                            </h4>
                            <div class="space-y-3">
                                @foreach($tim->guiders as $guider)
                                    <div class="flex items-center gap-3 bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-[#10B981]/20 flex items-center justify-center text-[#10B981] font-bold">
                                            {{ \Illuminate\Support\Str::substr($guider->pembimbing->nama, 0, 1) }}
                                        </div>
                                        <div class="font-medium font-inter">{{ $guider->pembimbing->nama }}</div>
                                    </div>
                                @endforeach
                                @if($tim->guiders->isEmpty())
                                    <p class="text-sm text-slate-500 italic">Belum ada Guider</p>
                                @endif
                            </div>
                        </div>

                        <!-- Members -->
                        <div>
                            <h4 class="font-bold text-lg mb-4 flex items-center gap-2">
                                <span class="text-[#FE9100]">●</span> Anggota Tim
                            </h4>
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                                @foreach($tim->members as $member)
                                    <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm {{ $member->id === $user->id ? 'ring-2 ring-[#FE9100]' : '' }}">
                                        <div class="font-medium font-inter truncate pr-2">{{ $member->nama }}</div>
                                        @if($member->id === $user->id)
                                            <span class="text-xs bg-[#FE9100] text-white px-2 py-1 rounded-full font-bold">Kamu</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center p-8 bg-slate-50 dark:bg-slate-700/50 rounded-2xl border border-slate-100 dark:border-slate-600 mb-8">
                    <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <p class="text-slate-500 font-inter">Kamu belum memiliki pembagian tim.</p>
                </div>
            @endif

            <div class="text-center">
                <a href="{{ route('peserta.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-[#10B981] hover:bg-[#10B981]/90 transition-colors text-white font-bold rounded-xl shadow-lg hover:shadow-xl font-inter group">
                    Lanjut ke Dashboard
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
    </div>

</body>
</html>