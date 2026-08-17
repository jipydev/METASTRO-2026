<?php

namespace Tests\Unit;

use App\Services\AttendanceImportParser;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AttendanceImportParserTest extends TestCase
{
    public function test_it_parses_csv_with_headers_and_semicolon_delimiter(): void
    {
        $path = sys_get_temp_dir().'/presensi-import.csv';
        file_put_contents($path, "nim;nama;status;waktu\n2508394;Budi;hadir;08:15\n");

        $rows = (new AttendanceImportParser)->parse(
            new UploadedFile($path, 'presensi.csv', 'text/csv', null, true)
        );

        $this->assertCount(1, $rows);
        $this->assertSame('2508394', $rows[0]['nim']);
        $this->assertSame('hadir', $rows[0]['status']);
        $this->assertSame('08:15', $rows[0]['waktu']);

        @unlink($path);
    }

    public function test_it_normalizes_numeric_nim_and_blank_status(): void
    {
        $parser = new AttendanceImportParser;

        $this->assertSame('2508394', $parser->normalizeNim(2508394.0));
        $this->assertSame('hadir', $parser->normalizeStatus(null));
        $this->assertSame('izin', $parser->normalizeStatus('Izin'));
    }
}
