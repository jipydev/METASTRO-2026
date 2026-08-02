<x-app-layout>
    <!-- Alpine.js x-data mengontrol kedua modal secara bersamaan -->
    <div x-data="{ openEditPengumuman: false, openEditTimeline: false }" class="max-w-md mx-auto min-h-screen bg-gray-50 pb-20 relative">
        
        <div class="px-4 py-6 space-y-4">
            
            <!-- SECTION 1: Pengumuman -->
            <div class="bg-cyan-50 rounded-2xl p-4 shadow-sm border border-cyan-100 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-teal-800 text-lg mb-1">Pengumuman<span class="text-red-500">*</span></h3>
                    <p class="text-teal-700 text-sm font-medium">RABES 2: 2 Agustus 2026 pukul 08.00</p>
                </div>
                <!-- Trigger Modal Pengumuman -->
                <button @click="openEditPengumuman = true" class="text-teal-800 hover:text-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
            </div>

            <!-- SECTION 2: Timeline -->
            <div class="bg-cyan-50 rounded-2xl p-4 shadow-sm border border-cyan-100 relative flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-teal-800 text-lg mb-2">Timeline</h3>
                    <div class="text-teal-800">
                        <p class="font-semibold text-sm">RABES 1</p>
                        <p class="text-sm">Selasa, 20 Juli 2026</p>
                        <p class="text-sm">pukul 08.00</p>
                        <p class="text-sm">Ruang PGSD 4</p>
                    </div>
                </div>
                <!-- Trigger Modal Timeline -->
                <button @click="openEditTimeline = true" class="text-teal-800 hover:text-teal-600 z-10">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <div class="absolute bottom-4 right-4 text-teal-600 font-bold pointer-events-none">
                    &gt;&gt;
                </div>
            </div>

        </div>

        <!-- Memanggil Komponen Modal -->
        <x-modal-edit-pengumuman />
        <x-modal-edit-timeline />

    </div>
</x-app-layout>