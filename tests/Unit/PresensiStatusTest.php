<?php

namespace Tests\Unit;

use App\Models\Presensi;
use App\Models\Timeline;
use Tests\TestCase;

class PresensiStatusTest extends TestCase
{
    public function test_status_check_is_case_insensitive_and_trimmed(): void
    {
        $presensi = new Presensi([
            'status' => ' Izin ',
        ]);

        $this->assertTrue($presensi->isStatus('izin'));
        $this->assertFalse($presensi->isStatus('sakit'));
    }

    public function test_status_is_determined_from_timeline_start_time(): void
    {
        $timeline = new Timeline([
            'tanggal_mulai' => '2026-08-05 08:00:00',
        ]);

        $presensiOnTime = new Presensi([
            'waktu_presensi' => '2026-08-05 07:45:00',
            'timeline_id' => 1,
        ]);

        $presensiNearStart = new Presensi([
            'waktu_presensi' => '2026-08-05 07:46:00',
            'timeline_id' => 1,
        ]);

        $presensiLate = new Presensi([
            'waktu_presensi' => '2026-08-05 08:16:00',
            'timeline_id' => 1,
        ]);

        $this->assertSame('Hadir', $presensiOnTime->resolveStatus($timeline));
        $this->assertSame('Alpha', $presensiNearStart->resolveStatus($timeline));
        $this->assertSame('Alpha', $presensiLate->resolveStatus($timeline));
    }
}
