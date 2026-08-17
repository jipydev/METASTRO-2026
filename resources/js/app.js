import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

window.appBrandColor = function appBrandColor() {
    const value = getComputedStyle(document.documentElement)
        .getPropertyValue('--color-brand-500')
        .trim();

    return value || '#fe5a1d';
};

const COMPRESSIBLE_IMAGE = /^image\/(jpeg|jpg|png|webp)$/i;
const MAX_IMAGE_EDGE = 1920;
const JPEG_QUALITY = 0.85;

async function compressImageFile(file) {
    if (!COMPRESSIBLE_IMAGE.test(file.type)) {
        return file;
    }

    let bitmap;
    try {
        bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });
    } catch {
        return file;
    }

    const scale = Math.min(1, MAX_IMAGE_EDGE / Math.max(bitmap.width, bitmap.height));
    const width = Math.max(1, Math.round(bitmap.width * scale));
    const height = Math.max(1, Math.round(bitmap.height * scale));

    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        bitmap.close();
        return file;
    }

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);
    ctx.drawImage(bitmap, 0, 0, width, height);
    bitmap.close();

    const blob = await new Promise((resolve) => {
        canvas.toBlob(resolve, 'image/jpeg', JPEG_QUALITY);
    });

    if (!blob || blob.size >= file.size) {
        return file;
    }

    const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';

    return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
}

document.addEventListener('change', async (event) => {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || input.type !== 'file') {
        return;
    }

    if (input.dataset.skipCompress === 'true') {
        return;
    }

    if (input.dataset.compressed === '1') {
        delete input.dataset.compressed;
        return;
    }

    const file = input.files?.[0];
    if (!file || !COMPRESSIBLE_IMAGE.test(file.type)) {
        return;
    }

    event.stopImmediatePropagation();

    try {
        const compressed = await compressImageFile(file);
        const transfer = new DataTransfer();
        transfer.items.add(compressed);
        input.files = transfer.files;
    } catch {
        // Biarkan file asli jika kompresi gagal.
    }

    input.dataset.compressed = '1';
    input.dispatchEvent(new Event('change', { bubbles: true }));
}, true);

Alpine.start();
