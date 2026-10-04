<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List Penugasan Detail - METASTRO 2026</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="font-poppins antialiased bg-[#FFFDF9] text-slate-900 min-h-dvh">

    <div class="max-w-md mx-auto sm:max-w-xl md:max-w-2xl px-4 py-5 min-h-screen flex flex-col" x-data="{ tab: 'semua' }">
        <!-- HEADER -->
        <header class="flex items-center gap-4 py-2 mb-4">
            <a href="{{ route('peserta.dashboard') }}" class="font-bold text-xl text-slate-900 hover:text-slate-600 transition">&lt;</a>
            <h1 class="font-bold text-lg text-slate-900">List Penugasan:</h1>
        </header>

        <!-- SORTING & FILTERS -->
        <div class="mb-4">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-xs text-slate-600">Sorting berdasarkan:</span>
                <button class="bg-[#FF7A00] text-white text-xs font-medium px-3 py-1 rounded flex items-center gap-1">
                    deadline
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <!-- TABS -->
            <div class="flex gap-2 text-xs font-medium overflow-x-auto pb-2 scrollbar-hide">
                <button @click="tab = 'semua'" :class="tab == 'semua' ? 'bg-[#006C7F] text-white' : 'text-[#DB9968]'" class="px-4 py-1.5 rounded-lg whitespace-nowrap">Semua</button>
                <button @click="tab = 'individu'" :class="tab == 'individu' ? 'bg-[#006C7F] text-white' : 'text-[#DB9968]'" class="px-4 py-1.5 rounded-lg whitespace-nowrap">Individu</button>
                <button @click="tab = 'regu'" :class="tab == 'regu' ? 'bg-[#006C7F] text-white' : 'text-[#DB9968]'" class="px-4 py-1.5 rounded-lg whitespace-nowrap">Regu</button>
                <button @click="tab = 'angkatan'" :class="tab == 'angkatan' ? 'bg-[#006C7F] text-white' : 'text-[#DB9968]'" class="px-4 py-1.5 rounded-lg whitespace-nowrap">Angkatan</button>
            </div>
        </div>

        <!-- TASK LIST -->
        <div class="space-y-4">
            <!-- Individu -->
            <div x-show="tab == 'semua' || tab == 'individu'" class="bg-[#FFFDF9] border border-orange-50 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2 py-0.5 rounded">Individu</span>
                    <h3 class="font-bold text-sm text-slate-800">Membuat esai</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed mb-3">
                    Setelah mendengarkan pemateri di hari pertama, peserta menulis esai terkait seluruh materi dengan ketentuan: maksimal 600 kata, tidak boleh AI...... &gt;&gt;
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] hover:bg-[#FFE599] text-slate-800 text-xs font-medium px-6 py-1.5 rounded-lg transition inline-block">Kumpulkan</a>
                </div>
            </div>

            <!-- Regu -->
            <div x-show="tab == 'semua' || tab == 'regu'" class="bg-[#FFFDF9] border border-orange-50 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-[#4299E1] text-white text-[10px] font-bold px-2 py-0.5 rounded">Regu</span>
                    <h3 class="font-bold text-sm text-slate-800">Video yel-yel regu</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed mb-3">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi erat lacus, rutrum sed aliquet hendrerit, rutrum quis elit. Proin vitae nisi vitae libero ...... &gt;&gt;
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] hover:bg-[#FFE599] text-slate-800 text-xs font-medium px-6 py-1.5 rounded-lg transition inline-block">Kumpulkan</a>
                </div>
            </div>

            <!-- Regu 2 -->
            <div x-show="tab == 'semua' || tab == 'regu'" class="bg-[#FFFDF9] border border-orange-50 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-[#4299E1] text-white text-[10px] font-bold px-2 py-0.5 rounded">Regu</span>
                    <h3 class="font-bold text-sm text-slate-800">Bendera dengan tiang</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed mb-3">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi erat lacus, rutrum sed aliquet hendrerit, rutrum quis elit. Proin vitae nisi vitae libero ...... &gt;&gt;
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] hover:bg-[#FFE599] text-slate-800 text-xs font-medium px-6 py-1.5 rounded-lg transition inline-block">Kumpulkan</a>
                </div>
            </div>

            <!-- Angkatan -->
            <div x-show="tab == 'semua' || tab == 'angkatan'" class="bg-[#FFFDF9] border border-orange-50 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-[#00FF00] text-slate-900 text-[10px] font-bold px-2 py-0.5 rounded">Angkatan</span>
                    <h3 class="font-bold text-sm text-slate-800">Video jargon MKB</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed mb-3">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi erat lacus, rutrum sed aliquet hendrerit, rutrum quis elit. Proin vitae nisi vitae libero ...... &gt;&gt;
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] hover:bg-[#FFE599] text-slate-800 text-xs font-medium px-6 py-1.5 rounded-lg transition inline-block">Kumpulkan</a>
                </div>
            </div>
            
            <!-- Individu 2 -->
            <div x-show="tab == 'semua' || tab == 'individu'" class="bg-[#FFFDF9] border border-orange-50 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2 py-0.5 rounded">Individu</span>
                    <h3 class="font-bold text-sm text-slate-800">Koneksi LinkedIn 150+</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed mb-3">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi erat lacus, rutrum sed aliquet hendrerit, rutrum quis elit. Proin vitae nisi vitae libero ...... &gt;&gt;
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('peserta.tugas-kumpulkan') }}" class="bg-[#FFF2CC] hover:bg-[#FFE599] text-slate-800 text-xs font-medium px-6 py-1.5 rounded-lg transition inline-block">Kumpulkan</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
