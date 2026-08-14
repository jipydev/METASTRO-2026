<x-guest-layout>
    <!-- Include Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <div x-data="{
        photoPreview: null,
        cropper: null,
        showCropModal: false,
        cropImageSrc: null,
        
        previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file maksimal adalah 10MB. Silakan pilih foto dengan ukuran lebih kecil.');
                    event.target.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.cropImageSrc = e.target.result;
                    this.showCropModal = true;
                    // Render Image then init cropper
                    this.$nextTick(() => {
                        if (this.cropper) {
                            this.cropper.destroy();
                        }
                        const imageElement = document.getElementById('cropImage');
                        this.cropper = new Cropper(imageElement, {
                            aspectRatio: 1,
                            viewMode: 1,
                            autoCropArea: 1,
                        });
                    });
                };
                reader.readAsDataURL(file);
            }
        },
        
        saveCrop() {
            if (this.cropper) {
                const canvas = this.cropper.getCroppedCanvas({
                    maxWidth: 1024,
                    maxHeight: 1024
                });
                
                const maxSize = 1024 * 1024; // 1MB
                let quality = 0.9;
                
                const compressAndSave = (q) => {
                    canvas.toBlob((blob) => {
                        if (blob.size > maxSize && q > 0.1) {
                            compressAndSave(q - 0.1);
                        } else {
                            this.photoPreview = canvas.toDataURL('image/jpeg', q);
                            
                            const file = new File([blob], 'profile.jpg', { type: 'image/jpeg', lastModified: new Date().getTime() });
                            const container = new DataTransfer();
                            container.items.add(file);
                            document.getElementById('foto').files = container.files;
                            this.closeCropModal();
                        }
                    }, 'image/jpeg', q);
                };
                
                compressAndSave(quality);
            }
        },
        
        closeCropModal() {
            this.showCropModal = false;
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            if (!this.photoPreview) {
                document.getElementById('foto').value = '';
            }
        }
    }" class="w-full max-w-md mx-auto font-poppins">

        <div class="bg-white dark:bg-slate-800 shadow-2xl rounded-3xl p-8 border border-gray-100 dark:border-slate-700">
            
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-primary-500/10 text-primary-500 rounded-2xl mb-3">
                    <span class="icon-[akar-icons--user-check] text-3xl"></span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Lengkapi Profil Anda</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Selamat datang! Karena ini adalah login pertama Anda, harap lengkapi foto profil, nama, email, dan ganti password baru.
                </p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('initial-setup.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Photo Avatar Upload --}}
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-primary-500 shadow-md">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center border-4 border-dashed border-slate-300 dark:border-slate-600 text-slate-400">
                                <span class="icon-[akar-icons--camera] text-3xl"></span>
                            </div>
                        </template>
                        <label for="foto" class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-full opacity-0 group-hover:opacity-100 transition cursor-pointer text-xs font-bold">
                            Ubah
                        </label>
                    </div>
                    <input type="file" id="foto" name="foto" accept="image/*" required class="hidden" @change="previewPhoto($event)">
                    <span class="text-xs font-semibold text-primary-500 mt-2 cursor-pointer" onclick="document.getElementById('foto').click()">
                        + Unggah Foto Profil (Wajib, Maks. 10MB)
                    </span>
                    <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                </div>

                {{-- NIM Readonly --}}
                <div>
                    <x-input-label for="nim" :value="__('NIM')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="nim" class="block mt-1 w-full bg-slate-100 dark:bg-slate-700/50 cursor-not-allowed" type="text" :value="$user->nim" readonly />
                </div>

                {{-- Name --}}
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" placeholder="Nama Lengkap Anda" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" :value="__('Alamat Email')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="email" placeholder="contoh@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- Password --}}
                <div>
                    <x-input-label for="password" :value="__('Password Baru')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang password baru" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-primary-500 hover:bg-primary-600 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/20 transition cursor-pointer text-sm">
                        Simpan & Masuk Aplikasi
                    </button>
                </div>

            </form>
        </div>

        <!-- Crop Modal -->
        <div x-show="showCropModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div @click.away="closeCropModal()" class="bg-white dark:bg-slate-800 rounded-3xl p-6 w-full max-w-lg shadow-2xl flex flex-col relative z-50 overflow-hidden">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Sesuaikan Foto</h3>
                
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-xl overflow-hidden" style="max-height: 400px; height: 400px;">
                    <img id="cropImage" :src="cropImageSrc" class="max-w-full block" alt="Crop Area">
                </div>
                
                <div class="flex justify-end gap-3 mt-6 relative z-50">
                    <button type="button" @click="closeCropModal()" class="px-5 py-2.5 rounded-xl font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="saveCrop()" class="px-5 py-2.5 rounded-xl font-semibold text-white bg-primary-500 hover:bg-primary-600 shadow-lg shadow-primary-500/20 transition cursor-pointer">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
