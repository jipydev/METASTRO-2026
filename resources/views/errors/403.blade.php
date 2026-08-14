<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 text-white font-['Poppins'] min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-3xl bg-rose-500/10 border border-rose-500/20 text-rose-500 flex items-center justify-center">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-4xl font-extrabold mb-2">403</h1>
        <h2 class="text-xl font-semibold text-slate-300 mb-4">Akses Ditolak</h2>
        <p class="text-slate-400 mb-8 text-sm leading-relaxed">
            Anda tidak memiliki izin atau otoritas role yang cukup untuk mengakses halaman ini.
        </p>

        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#fe5a1d] hover:bg-[#e04d13] text-white font-bold rounded-2xl shadow-lg transition text-sm">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
