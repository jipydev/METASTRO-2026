<div x-show="openEditPengumuman" 
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
     
    <div x-show="openEditPengumuman"
         @click.outside="openEditPengumuman = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-[2rem] p-6 w-full max-w-sm relative shadow-2xl">
        
        <!-- Tombol Close (X) -->
        <button @click="openEditPengumuman = false" class="absolute top-5 right-6 text-black font-bold text-lg hover:text-gray-600">
            x
        </button>

        <h3 class="text-teal-800 font-bold text-lg mb-1">Edit pengumuman</h3>
        <p class="text-gray-400 italic text-xs mb-3">Masukkan deksripsi pengumuman</p>
        
        <!-- Form Textarea -->
        <textarea class="w-full bg-[#f0f8ff] border-none rounded-2xl h-32 p-4 text-sm text-gray-700 focus:ring-0 resize-none" placeholder="Isi pengumuman..."></textarea>
        
        <!-- Footer Action Buttons -->
        <div class="flex justify-center gap-4 mt-6">
            <button class="bg-teal-800 text-white text-xs font-bold py-2.5 px-6 rounded-lg hover:bg-teal-900 transition">HAPUS</button>
            <button class="bg-teal-800 text-white text-xs font-bold py-2.5 px-6 rounded-lg hover:bg-teal-900 transition">SELESAI</button>
        </div>
    </div>
</div>