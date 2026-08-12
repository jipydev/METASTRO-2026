<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 text-white font-['Poppins'] min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-3xl bg-amber-500/10 border border-amber-500/20 text-amber-500 flex items-center justify-center">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-4xl font-extrabold mb-2">404</h1>
        <h2 class="text-xl font-semibold text-slate-300 mb-4">Halaman Tidak Ditemukan</h2>
        <p class="text-slate-400 mb-8 text-sm leading-relaxed">
            Halaman atau data yang Anda cari tidak ditemukan atau telah dipindahkan.
        </p>

        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#fe5a1d] hover:bg-[#e04d13] text-white font-bold rounded-2xl shadow-lg transition text-sm">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
