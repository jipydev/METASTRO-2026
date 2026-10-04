<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List Penugasan - METASTRO 2026</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-oswald-header { font-family: 'Oswald', sans-serif; letter-spacing: 0.12em; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="font-poppins antialiased bg-[#FFFDF9] text-slate-900 min-h-dvh">

    <div class="max-w-md mx-auto sm:max-w-xl md:max-w-2xl px-4 py-5 min-h-screen flex flex-col">
        <!-- HEADER -->
        <header class="sticky top-0 z-40 bg-[#FFFDF9] flex items-center justify-between py-4 px-4 mb-4 -mx-4 -mt-5">
            <a href="{{ route('peserta.dashboard') }}" class="inline-block">
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

        <!-- CONTENT -->
        <div>
            <h2 class="font-bold text-lg text-slate-900 mb-4">List Penugasan:</h2>

            <div class="space-y-4">
                @php
                    $days = [
                        ['day' => 1, 'progress' => 3],
                        ['day' => 2, 'progress' => 2],
                        ['day' => 3, 'progress' => 3],
                        ['day' => 4, 'progress' => 4],
                    ];
                @endphp

                @foreach($days as $item)
                <a href="{{ route('peserta.tugas-list') }}" class="block bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:border-orange-200 transition">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-bold text-lg text-slate-900">DAY {{ $item['day'] }}</h3>
                        <svg class="w-5 h-5 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <p class="text-xs text-slate-500 mb-2">Total tugas:</p>
                    
                    <div class="relative w-full">
                        <div class="h-2.5 w-full bg-slate-200 rounded-full flex overflow-hidden">
                            <div class="h-full bg-[#FF3B3B]" style="width: {{ ($item['progress'] / 5) * 100 }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1 px-1 text-[10px] text-slate-500 font-medium">
                            <span>1</span>
                            <span>2</span>
                            <span>3</span>
                            <span>4</span>
                            <span>5</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
