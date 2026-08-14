<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 text-white font-['Poppins'] min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-3xl bg-rose-500/10 border border-rose-500/20 text-rose-500 flex items-center justify-center">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-4xl font-extrabold mb-2">500</h1>
        <h2 class="text-xl font-semibold text-slate-300 mb-4">Kesalahan Server</h2>
        <p class="text-slate-400 mb-8 text-sm leading-relaxed">
            Terjadi kesalahan internal pada server. Silakan coba beberapa saat lagi atau hubungi administrator.
        </p>

        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-[#fe5a1d] hover:bg-[#e04d13] text-white font-bold rounded-2xl shadow-lg transition text-sm">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
