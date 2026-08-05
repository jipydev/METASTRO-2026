<div x-show="openEditTimeline" 
     style="display: none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
     
    <div x-show="openEditTimeline"
         @click.outside="openEditTimeline = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-[2rem] p-6 w-full max-w-sm relative shadow-2xl font-poppins">
        
        <!-- Form Timeline -->
        <form x-bind:action="selectedTimeline && selectedTimeline.id ? '/timeline/' + selectedTimeline.id : '{{ route('timeline.store') }}'"
              method="POST">
            @csrf

            <template x-if="selectedTimeline && selectedTimeline.id">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <!-- Tombol Close (X) -->
            <button type="button" @click="openEditTimeline = false" class="absolute top-5 right-6 text-gray-400 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white font-bold text-lg transition cursor-pointer">
                &times;
            </button>

            <h3 class="text-primary-600 dark:text-primary-400 font-bold text-lg mb-4" x-text="selectedTimeline && selectedTimeline.id ? 'Edit timeline' : 'Tambah timeline'">Edit timeline</h3>
            
            <!-- Form Inputs -->
            <div class="space-y-3">
                <div>
                    <label class="block text-primary-600 dark:text-primary-400 font-bold text-sm mb-1">Judul:</label>
                    <input type="text" 
                           name="judul" 
                           x-model="selectedTimeline.judul" 
                           placeholder="ketik disini" 
                           class="w-full bg-primary-50/50 dark:bg-slate-700 border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 font-poppins"
                           required>
                </div>
                
                <div>
                    <label class="block text-primary-600 dark:text-primary-400 font-bold text-sm mb-1">Hari, tanggal</label>
                    <input type="date" 
                           name="tanggal" 
                           x-model="selectedTimeline.tanggal" 
                           class="w-full bg-primary-50/50 dark:bg-slate-700 border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 font-poppins"
                           required>
                </div>
                
                <div>
                    <label class="block text-primary-600 dark:text-primary-400 font-bold text-sm mb-1">Jam</label>
                    <input type="time" 
                           name="jam" 
                           x-model="selectedTimeline.jam" 
                           class="w-full bg-primary-50/50 dark:bg-slate-700 border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 font-poppins"
                           required>
                </div>
                
                <div>
                    <label class="block text-primary-600 dark:text-primary-400 font-bold text-sm mb-1">Tempat</label>
                    <input type="text" 
                           name="tempat" 
                           x-model="selectedTimeline.tempat" 
                           placeholder="ketik disini" 
                           class="w-full bg-primary-50/50 dark:bg-slate-700 border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 font-poppins"
                           required>
                </div>
            </div>
            
            <!-- Footer Action Buttons -->
            <div class="flex justify-center gap-3 mt-8">
                <template x-if="selectedTimeline && selectedTimeline.id">
                    <button type="button" 
                            @click.prevent="
                                if(confirm('Hapus timeline ini?')){
                                    let f = document.createElement('form');
                                    f.method = 'POST';
                                    f.action = '/timeline/' + selectedTimeline.id;
                                    f.innerHTML = `<input type='hidden' name='_token' value='{{ csrf_token() }}'><input type='hidden' name='_method' value='DELETE'>`;
                                    document.body.appendChild(f);
                                    f.submit();
                                }
                            " 
                            class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-2.5 px-5 rounded-lg transition cursor-pointer">
                        HAPUS
                    </button>
                </template>

                <button type="button" 
                        @click="openEditTimeline = false"
                        class="bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 text-xs font-bold py-2.5 px-5 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition cursor-pointer">
                    BATAL
                </button>
                
                <button type="submit" 
                        class="bg-primary-500 text-white text-xs font-bold py-2.5 px-6 rounded-lg hover:bg-primary-600 transition cursor-pointer shadow-sm">
                    SELESAI
                </button>
            </div>
        </form>
    </div>
</div>