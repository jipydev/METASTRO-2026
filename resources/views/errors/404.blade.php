<x-app-layout>
    <div class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4 font-poppins">
        <div class="p-4 rounded-3xl bg-amber-500/10 border border-amber-500/20 text-amber-500 mb-6">
            <span class="icon-[akar-icons--search] text-6xl"></span>
        </div>
        
        <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-2">404 - Halaman Tidak Ditemukan</h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-md mb-8 text-sm sm:text-base">
            Halaman atau data yang Anda cari tidak ditemukan atau telah dipindahkan.
        </p>

        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-bold rounded-2xl shadow-lg transition flex items-center gap-2 text-sm">
            <span class="icon-[akar-icons--arrow-left]"></span> Kembali ke Dashboard
        </a>
    </div>
</x-app-layout>
