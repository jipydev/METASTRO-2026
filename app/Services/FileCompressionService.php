<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileCompressionService
{
    private const MAX_IMAGE_EDGE = 1920;

    private const JPEG_QUALITY = 85;

    /**
     * Simpan file setelah dikompres (gambar selalu; PDF jika Ghostscript tersedia).
     * File lain atau hasil kompres yang justru lebih besar disimpan apa adanya.
     */
    public function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = (string) $file->getMimeType();

        if ($this->isImage($mime, $extension)) {
            return $this->storeImage($file, $directory, $disk);
        }

        if ($this->isPdf($mime, $extension)) {
            return $this->storePdf($file, $directory, $disk);
        }

        return $file->store($directory, $disk);
    }

    private function isImage(string $mime, string $extension): bool
    {
        return str_starts_with($mime, 'image/')
            || in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    private function isPdf(string $mime, string $extension): bool
    {
        return $mime === 'application/pdf' || $extension === 'pdf';
    }

    private function storeImage(UploadedFile $file, string $directory, string $disk): string
    {
        if (! extension_loaded('gd')) {
            return $file->store($directory, $disk);
        }

        $sourcePath = $file->getRealPath();
        if ($sourcePath === false) {
            return $file->store($directory, $disk);
        }

        $info = @getimagesize($sourcePath);
        if ($info === false) {
            return $file->store($directory, $disk);
        }

        $type = $info[2];
        $image = $this->createImage($sourcePath, $type);
        if (! $image instanceof GdImage) {
            return $file->store($directory, $disk);
        }

        if ($type === IMAGETYPE_JPEG) {
            $image = $this->fixJpegOrientation($image, $sourcePath);
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $maxEdge = max($width, $height);

        if ($maxEdge > self::MAX_IMAGE_EDGE) {
            $ratio = self::MAX_IMAGE_EDGE / $maxEdge;
            $newWidth = max(1, (int) round($width * $ratio));
            $newHeight = max(1, (int) round($height * $ratio));
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $keepPng = $type === IMAGETYPE_PNG && $this->pngHasTransparency($sourcePath);
        $tmp = tempnam(sys_get_temp_dir(), 'cmp');

        if ($keepPng) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, $tmp, 7);
            $extension = 'png';
        } else {
            $canvas = imagecreatetruecolor(imagesx($image), imagesy($image));
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagecopy($canvas, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagejpeg($canvas, $tmp, self::JPEG_QUALITY);
            imagedestroy($canvas);
            $extension = 'jpg';
        }

        imagedestroy($image);

        if (! is_file($tmp) || filesize($tmp) >= $file->getSize()) {
            @unlink($tmp);

            return $file->store($directory, $disk);
        }

        $path = trim($directory, '/').'/'.Str::uuid().'.'.$extension;
        Storage::disk($disk)->put($path, (string) file_get_contents($tmp));
        @unlink($tmp);

        return $path;
    }

    private function storePdf(UploadedFile $file, string $directory, string $disk): string
    {
        $gs = $this->ghostscriptBinary();
        $sourcePath = $file->getRealPath();

        if ($gs === null || $sourcePath === false) {
            return $file->store($directory, $disk);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'pdf').'.pdf';
        $result = Process::timeout(45)->run([
            $gs,
            '-dSAFER',
            '-dBATCH',
            '-dNOPAUSE',
            '-dQUIET',
            '-sDEVICE=pdfwrite',
            '-dCompatibilityLevel=1.4',
            '-dPDFSETTINGS=/printer',
            '-sOutputFile='.$tmp,
            $sourcePath,
        ]);

        if (! $result->successful() || ! is_file($tmp) || filesize($tmp) === 0 || filesize($tmp) >= $file->getSize()) {
            @unlink($tmp);

            return $file->store($directory, $disk);
        }

        $path = trim($directory, '/').'/'.Str::uuid().'.pdf';
        Storage::disk($disk)->put($path, (string) file_get_contents($tmp));
        @unlink($tmp);

        return $path;
    }

    private function createImage(string $path, int $type): ?GdImage
    {
        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        return $image instanceof GdImage ? $image : null;
    }

    private function fixJpegOrientation(GdImage $image, string $path): GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if (! $rotated instanceof GdImage) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }

    private function pngHasTransparency(string $path): bool
    {
        $image = @imagecreatefrompng($path);
        if (! $image instanceof GdImage) {
            return false;
        }

        if (imagecolortransparent($image) >= 0) {
            imagedestroy($image);

            return true;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $stepX = max(1, (int) floor($width / 80));
        $stepY = max(1, (int) floor($height / 80));

        for ($x = 0; $x < $width; $x += $stepX) {
            for ($y = 0; $y < $height; $y += $stepY) {
                $alpha = ((imagecolorat($image, $x, $y) & 0x7F000000) >> 24);
                if ($alpha > 0) {
                    imagedestroy($image);

                    return true;
                }
            }
        }

        imagedestroy($image);

        return false;
    }

    private function ghostscriptBinary(): ?string
    {
        $candidates = PHP_OS_FAMILY === 'Windows'
            ? ['gswin64c', 'gswin32c', 'gs']
            : ['gs'];

        foreach ($candidates as $bin) {
            $lookup = PHP_OS_FAMILY === 'Windows'
                ? Process::run(['where', $bin])
                : Process::run(['which', $bin]);

            if (! $lookup->successful()) {
                continue;
            }

            $path = trim(strtok(str_replace("\r", '', $lookup->output()), "\n") ?: '');
            if ($path !== '') {
                return $path;
            }
        }

        return null;
    }
}
