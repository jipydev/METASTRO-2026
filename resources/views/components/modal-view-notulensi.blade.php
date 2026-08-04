<!-- resources/views/components/modal-view-notulensi.blade.php -->
<div x-show="openViewNotulensi" style="display: none;" class="fixed inset-0 z-[999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        
        <!-- Background Overlay -->
        <div x-show="openViewNotulensi"
             x-transition.opacity
             class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
             aria-hidden="true" 
             @click="openViewNotulensi = false"></div>

        <!-- Modal Panel -->
        <div x-show="openViewNotulensi" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative inline-block w-full px-4 pt-5 pb-4 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:max-w-md sm:w-full sm:p-6">
            
            <!-- Header Modal (Judul & Tombol Close) -->
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-bold text-[#105e75]" id="modal-title" x-text="notulensiTitle">
                    Notulensi rabes
                </h3>
                <button @click="openViewNotulensi = false" class="text-black hover:text-gray-600 font-bold text-lg leading-none transition">
                    X
                </button>
            </div>

            <!-- Area Preview PDF (Sesuai Mockup) -->
            <div class="w-full h-80 bg-[#b3ddf7] rounded-xl flex items-center justify-center text-black font-medium text-sm md:text-base border border-blue-200">
                &lt;preview pdf&gt;
            </div>
            
        </div>
    </div>
</div>