<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<div x-data="{
    photoPreview: null,
    cropper: null,
    showCropModal: false,
    cropImageSrc: null,

    previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) {
            return;
        }

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

                this.cropper = new Cropper(document.getElementById('adminUserCropImage'), {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                });
            });
        };
        reader.readAsDataURL(file);
    },

    saveCrop() {
        if (!this.cropper) {
            return;
        }

        const canvas = this.cropper.getCroppedCanvas({
            maxWidth: 1024,
            maxHeight: 1024,
        });
        const maxSize = 1024 * 1024;

        const compressAndSave = (quality) => {
            canvas.toBlob((blob) => {
                if (!blob) {
                    alert('Foto tidak dapat diproses. Silakan coba foto lain.');
                    return;
                }

                if (blob.size > maxSize && quality > 0.1) {
                    compressAndSave(quality - 0.1);
                    return;
                }

                this.photoPreview = canvas.toDataURL('image/jpeg', quality);
                const file = new File([blob], 'profile.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now(),
                });
                const container = new DataTransfer();
                container.items.add(file);
                document.getElementById('foto').files = container.files;
                this.closeCropModal();
            }, 'image/jpeg', quality);
        };

        compressAndSave(0.9);
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
}">
    <div class="flex flex-col items-center">
        <div class="relative group">
            <template x-if="photoPreview">
                <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-brand-500 shadow-md"
                    alt="Pratinjau foto profil">
            </template>
            <template x-if="!photoPreview">
                @if (isset($user) && $user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nama }}"
                        class="w-24 h-24 rounded-full object-cover border-4 border-slate-200 dark:border-slate-700 shadow-md">
                @else
                    <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-700 flex flex-col items-center justify-center border-4 border-dashed border-slate-300 dark:border-slate-600 text-slate-400">
                        <span class="text-2xl mb-0.5">📷</span>
                        <span class="text-[10px] font-semibold">Foto</span>
                    </div>
                @endif
            </template>
            <label for="foto"
                class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 transition cursor-pointer text-xs font-bold">
                Ubah
            </label>
        </div>
        <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
            data-skip-compress="true" @change="previewPhoto($event)">
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 mt-2 cursor-pointer"
            onclick="document.getElementById('foto').click()">
            + Unggah Foto Profil (Opsional)
        </span>
        <p class="text-[11px] text-slate-400 mt-1">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
        <x-input-error :messages="$errors->get('foto')" class="mt-1" />
    </div>

    <div x-show="showCropModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div @click.away="closeCropModal()"
            class="bg-white dark:bg-slate-800 rounded-3xl p-6 w-full max-w-lg shadow-2xl flex flex-col relative z-50 overflow-hidden border border-gray-100 dark:border-slate-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Sesuaikan Potongan Foto</h3>
            <div class="w-full bg-slate-900 rounded-xl overflow-hidden" style="max-height: 380px; height: 380px;">
                <img id="adminUserCropImage" :src="cropImageSrc" class="max-w-full block" alt="Crop Area">
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
