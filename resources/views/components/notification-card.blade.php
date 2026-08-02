@props([
    'title' => 'Izin Rabes 2 Kamu diterima oleh Ranger',
    'time' => 'Senin, 20 Juli 2026 18.00'
])

<div x-show="showNotif" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-75"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     style="display: none;" 
     class="absolute -right-12 sm:right-0 top-full mt-3 w-[85vw] sm:w-72 max-w-[320px] bg-gradient-to-br from-[#1c6989] to-[#3ba1c4] text-white text-sm rounded-2xl p-4 shadow-xl z-50">
    
    <!-- Panah Segitiga (Otomatis menyesuaikan posisi menunjuk ke bel pada HP & Desktop) -->
    <div class="absolute -top-2 right-[3.5rem] sm:right-4 w-0 h-0 border-l-[8px] border-l-transparent border-b-[8px] border-b-[#1c6989] border-r-[8px] border-r-transparent"></div>
    
    <p class="font-semibold leading-snug">{{ $title }}</p>
    <p class="text-xs text-white/80 mt-1.5">{{ $time }}</p>
</div>