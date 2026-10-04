<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Arsip Tugas - METASTRO 2026</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .font-oswald-header { font-family: 'Oswald', sans-serif; letter-spacing: 0.12em; }
    </style>
</head>
<body class="font-poppins antialiased bg-[#FFFDF9] text-slate-900 min-h-dvh">

    <div class="max-w-md mx-auto sm:max-w-xl md:max-w-2xl px-4 py-5 min-h-screen flex flex-col">
        <!-- TOP HEADER APP BAR -->
        <header class="flex items-center justify-between py-2 mb-4">
            <a href="{{ route('peserta.dashboard') }}" class="inline-block group">
                <h1 class="font-oswald-header text-xl sm:text-2xl font-bold uppercase tracking-[0.14em] text-slate-900">
                    METASTRO 2026
                </h1>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" class="w-9 h-9 rounded-full flex items-center justify-center border border-slate-200 bg-white text-slate-700 relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </button>
                <button type="button" class="w-9 h-9 rounded-full flex items-center justify-center border border-slate-200 bg-white text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- PAGE HEADER -->
        <header class="flex items-center gap-4 py-2 mb-4">
            <a href="{{ route('peserta.dashboard') }}" class="font-bold text-xl text-slate-900 hover:text-slate-600 transition">&lt;</a>
            <h2 class="font-bold text-lg text-slate-900">Arsip tugas</h2>
        </header>

        <!-- CONTENT -->
        <div>
            <h3 class="font-bold text-base text-slate-900 mb-4">
                Day 1 telah terkumpul: <span class="text-[#00A82D]">25%</span>
            </h3>

            <div class="bg-[#FFFDF9] border border-orange-50 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2 py-0.5 rounded">Individu</span>
                    <h4 class="font-bold text-sm text-slate-800">Membuat esai</h4>
                </div>
                
                <div class="text-xs text-slate-600 space-y-1 mb-4">
                    <p>Deadline tugas: 20 Oktober 2026 23:59</p>
                    <p>Dikumpulkan: 20 Oktober 2026 18:35</p>
                </div>

                <div class="flex justify-center">
                    <button class="bg-[#FFF2CC] hover:bg-[#FFE599] text-slate-800 text-xs font-medium px-8 py-1.5 rounded-lg transition">Lihat file</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
