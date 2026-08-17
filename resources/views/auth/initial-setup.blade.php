<x-guest-layout :$title>

    <!-- Cropper.js CDN Assets -->
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
                    alert('Ukuran file maksimal 10MB. Silakan pilih foto yang lebih kecil.');
                    event.target.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.cropImageSrc = e.target.result;
                    this.showCropModal = true;
                    
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
                
                const maxSize = 1024 * 1024; // Maks 1MB
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

        <div class="bg-white dark:bg-slate-800 shadow-xl rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-slate-700">
            
            {{-- Header --}}
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 rounded-2xl mb-2.5 text-2xl">
                    👤
                </div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Lengkapi Profil Anda</h1>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                    Selamat datang! Karena ini login pertama Anda, harap unggah foto profil, periksa nama & email, serta buat password baru.
                </p>
            </div>

            {{-- Form Onboarding --}}
            <form method="POST" action="{{ route('initial-setup.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf

                {{-- Photo Avatar Upload & Crop --}}
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-brand-500 shadow-md">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-700 flex flex-col items-center justify-center border-4 border-dashed border-slate-300 dark:border-slate-600 text-slate-400">
                                <span class="text-2xl mb-0.5">📷</span>
                                <span class="text-[10px] font-semibold">Foto</span>
                            </div>
                        </template>
                        <label for="foto" class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 transition cursor-pointer text-xs font-bold">
                            Ubah
                        </label>
                    </div>
                    <input type="file" id="foto" name="foto" accept="image/*" required class="hidden" data-skip-compress="true" @change="previewPhoto($event)">
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 mt-2 cursor-pointer" onclick="document.getElementById('foto').click()">
                        + Unggah Foto Profil (Wajib)
                    </span>
                    <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                </div>

                {{-- NIM (Readonly) --}}
                <div>
                    <label for="nim" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">NIM</label>
                    <input id="nim" type="text" value="{{ $user->nim }}" readonly
                           class="w-full bg-slate-100 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs font-mono text-gray-500 dark:text-slate-400 cursor-not-allowed outline-none">
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label for="nama" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                    <input id="nama" type="text" name="nama" value="{{ old('nama', $user->nama) }}" required maxlength="255" autocomplete="name" placeholder="Nama Lengkap Anda"
                           class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                    <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">Alamat Email *</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email" placeholder="contoh@email.com"
                           class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                {{-- Password Baru --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label for="password" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">Password Baru *</label>
                        <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Min. 8 karakter"
                               class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider mb-1">Ulangi Password *</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Ulangi password"
                               class="w-full bg-slate-50 dark:bg-slate-700/60 border border-gray-300 dark:border-slate-600 rounded-xl py-2.5 px-3.5 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-3">
                    <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer text-xs">
                        Simpan & Masuk ke Dashboard
                    </button>
                </div>

            </form>
        </div>

        <!-- Crop Modal Box -->
        <div x-show="showCropModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div @click.away="closeCropModal()" class="bg-white dark:bg-slate-800 rounded-3xl p-6 w-full max-w-lg shadow-2xl flex flex-col relative z-50 overflow-hidden border border-gray-100 dark:border-slate-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Sesuaikan Potongan Foto</h3>
                
                <div class="w-full bg-slate-900 rounded-xl overflow-hidden" style="max-height: 380px; height: 380px;">
                    <img id="cropImage" :src="cropImageSrc" class="max-w-full block" alt="Crop Area">
                </div>
                
                <div class="flex justify-end gap-2.5 mt-5">
                    <button type="button" @click="closeCropModal()"
                            class="px-4 py-2 text-xs rounded-xl font-semibold text-gray-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 transition">
                        Batal
                    </button>
                    <button type="button" @click="saveCrop()"
                            class="px-5 py-2 text-xs rounded-xl font-semibold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm">
                        Terapkan Foto
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-guest-layout>