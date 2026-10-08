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
    <label for="foto" class="block font-bold text-gray-700 dark:text-slate-300 mb-1 uppercase tracking-wider">
        Foto Profil
    </label>
    <div class="flex items-center gap-4">
        <div class="shrink-0">
            @if (isset($user) && $user->foto)
                <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nama }}"
                    class="w-16 h-16 rounded-2xl object-cover border border-gray-300 dark:border-slate-600">
            @else
                <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-lg font-bold">
                    {{ isset($user) ? mb_strtoupper(mb_substr($user->nama, 0, 1)) : '?' }}
                </div>
            @endif
        </div>
        <div class="flex-1">
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                class="block w-full text-xs text-gray-600 dark:text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-600 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-brand-700">
            <p class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
        </div>
    </div>
    <x-input-error :messages="$errors->get('foto')" class="mt-1" />

    <div x-show="showCropModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        @keydown.escape.window="closeCropModal()">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl dark:bg-slate-800" @click.outside="closeCropModal()">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Potong Foto Profil</h2>
            <div class="mt-4 max-h-[60vh] overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-900">
                <img id="adminUserCropImage" :src="cropImageSrc" alt="Pratinjau foto yang akan dipotong"
                    class="block max-h-[60vh] max-w-full">
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" @click="closeCropModal()"
                    class="rounded-xl bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
                    Batal
                </button>
                <button type="button" @click="saveCrop()"
                    class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-700">
                    Gunakan Foto
                </button>
            </div>
        </div>
    </div>
</div>
