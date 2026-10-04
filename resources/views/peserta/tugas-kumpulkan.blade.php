<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kumpulkan Tugas - METASTRO 2026</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="font-poppins antialiased bg-[#FFFDF9] text-slate-900 min-h-dvh">

    <div class="max-w-md mx-auto sm:max-w-xl md:max-w-2xl px-4 py-5 min-h-screen flex flex-col">
        <!-- HEADER -->
        <header class="flex items-center gap-4 py-2 mb-2">
            <a href="{{ route('peserta.tugas-list') }}" class="font-bold text-xl text-slate-900 hover:text-slate-600 transition">&lt;</a>
            <h1 class="font-bold text-lg text-slate-900 flex-1 text-center pr-6">Kumpulkan Tugas</h1>
        </header>

        <!-- CONTENT -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-50 flex-1">
            <p class="text-right text-xs italic text-slate-600 mb-4">Deadline tugas: 21 Oktober pukul 23.59</p>

            <div class="flex items-center gap-2 mb-4">
                <span class="bg-[#FF1A1A] text-white text-[10px] font-bold px-2 py-0.5 rounded">Individu</span>
                <h2 class="font-bold text-sm text-slate-800">Membuat esai</h2>
            </div>

            <div class="text-xs text-slate-700 leading-relaxed mb-6 space-y-3">
                <p>Deksripsi:<br>
                Setelah mendengarkan pemateri di hari pertama, peserta menulis esai terkait seluruh materi dengan ketentuan:</p>
                
                <ol class="list-decimal pl-4 space-y-0.5">
                    <li>maksimal 600 kata</li>
                    <li>tidak boleh AI generated</li>
                    <li>Font Times New Roman dan</li>
                    <li>spacing sebanyak 1,5</li>
                </ol>

                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi erat lacus, rutrum sed aliquet hendrerit, rutrum quis elit.</p>
                
                <p>Proin vitae nisi vitae libero commodo elementum in eget elit. Aenean scelerisque eros at leo venenatis, vitae ornare tortor luctus. In hac habitasse platea dictumst. Suspendisse mollis augue elit, at volutpat diam mollis at. Fusce orci enim, mattis sit amet elit in, condimentum semper quam. Vestibulum mattis massa tortor, sed scelerisque odio auctor ac.</p>
            </div>

            <!-- ACTION AREA -->
            <div class="mt-8 flex flex-col items-center">
                <label class="flex items-start gap-2 cursor-pointer mb-4 max-w-[280px]">
                    <input type="checkbox" class="mt-0.5 w-3.5 h-3.5 rounded-sm border-red-500 text-red-500 focus:ring-red-500">
                    <span class="text-[9px] text-red-500 text-center leading-tight">
                        Saya telah membaca deskripsi tugas dengan baik dan mengerjakan tugas sesuai dengan ketentuan.
                    </span>
                </label>

                <button class="bg-[#708090] hover:bg-[#5C6B7A] text-white text-xs font-medium px-6 py-2.5 rounded-lg transition w-full sm:w-auto">
                    Kumpulkan tugas disini
                </button>
                <p class="text-[10px] italic text-slate-400 mt-2">upload file didukung: pdf maks 5 mb.</p>
            </div>
        </div>
    </div>
</body>
</html>
