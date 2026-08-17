<?php

namespace Tests\Unit;

use App\Services\FileCompressionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileCompressionServiceTest extends TestCase
{
    public function test_it_resizes_and_compresses_large_jpeg(): void
    {
        Storage::fake('public');

        $source = sys_get_temp_dir().'/compress-src.jpg';
        $image = imagecreatetruecolor(2200, 1600);
        for ($i = 0; $i < 250; $i++) {
            $color = imagecolorallocate($image, random_int(0, 255), random_int(0, 255), random_int(0, 255));
            imagefilledellipse($image, random_int(0, 2199), random_int(0, 1599), 90, 90, $color);
        }
        imagejpeg($image, $source, 100);
        imagedestroy($image);

        $originalSize = filesize($source);
        $upload = new UploadedFile($source, 'bukti.jpg', 'image/jpeg', null, true);
        $path = (new FileCompressionService)->store($upload, 'izin/bukti');

        Storage::disk('public')->assertExists($path);
        $this->assertLessThan($originalSize, Storage::disk('public')->size($path));

        $info = getimagesize(Storage::disk('public')->path($path));
        $this->assertNotFalse($info);
        $this->assertLessThanOrEqual(1920, max($info[0], $info[1]));

        @unlink($source);
    }

    public function test_it_stores_pdf_without_ghostscript(): void
    {
        Storage::fake('public');

        $upload = UploadedFile::fake()->create('surat.pdf', 120, 'application/pdf');
        $path = (new FileCompressionService)->store($upload, 'izin/surat');

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.pdf', $path);
    }
}
