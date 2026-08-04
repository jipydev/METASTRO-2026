@props(['show', 'url'])

<div x-show="{{ $show }}" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-sm" style="display: none;" x-transition>
    <div @click.outside="{{ $show }} = false" class="relative max-w-2xl w-full flex justify-center flex-col items-center">
        <!-- Tombol Close -->
        <button type="button" @click="{{ $show }} = false" class="absolute -top-12 right-0 md:-right-8 text-white hover:text-red-400 font-bold text-4xl transition-colors">
            &times;
        </button>
        <!-- Konten Gambar -->
        <img src="{{ $url }}" alt="Bukti Dokumentasi" class="w-full max-h-[85vh] object-contain rounded-xl shadow-2xl bg-black/50">
    </div>
</div>