<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrKodeUnikTest extends TestCase
{
    use RefreshDatabase;

    private Divisi $archivistDivisi;

    private Divisi $chefDivisi;

    private Jabatan $anggota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();

        $this->archivistDivisi = Divisi::firstOrCreate(['nama' => 'Archivist']);
        $this->chefDivisi = Divisi::firstOrCreate(['nama' => 'Chef']);
        $this->anggota = Jabatan::firstOrCreate(['nama' => 'Anggota']);
    }

    public function test_user_automatically_gets_unique_code_on_creation(): void
    {
        $user = User::factory()->create([
            'divisi_id' => $this->chefDivisi->id,
            'jabatan_id' => $this->anggota->id,
        ]);

        $this->assertNotNull($user->kode_unik);
        $this->assertStringStartsWith('MTR-', $user->kode_unik);
        $this->assertNotNull($user->qr_token);
    }

    public function test_qr_service_lookup_by_kode_unik_supports_flexible_formats(): void
    {
        $user = User::factory()->create([
            'nim' => '2508394',
            'kode_unik' => 'MTR-ABCD',
            'status' => true,
            'divisi_id' => $this->chefDivisi->id,
            'jabatan_id' => $this->anggota->id,
        ]);

        $service = app(QrCodeService::class);

        // 1. Exact match with prefix
        $found = $service->lookupByKodeUnik('MTR-ABCD');
        $this->assertNotNull($found);
        $this->assertSame($user->id, $found->id);

        // 2. Lowercase match
        $foundLower = $service->lookupByKodeUnik('mtr-abcd');
        $this->assertNotNull($foundLower);
        $this->assertSame($user->id, $foundLower->id);

        // 3. Suffix only (without MTR- prefix)
        $foundSuffix = $service->lookupByKodeUnik('ABCD');
        $this->assertNotNull($foundSuffix);
        $this->assertSame($user->id, $foundSuffix->id);

        // 4. Without hyphen (MTRABCD)
        $foundNoHyphen = $service->lookupByKodeUnik('MTRABCD');
        $this->assertNotNull($foundNoHyphen);
        $this->assertSame($user->id, $foundNoHyphen->id);

        // 5. By NIM
        $foundNim = $service->lookupByKodeUnik('2508394');
        $this->assertNotNull($foundNim);
        $this->assertSame($user->id, $foundNim->id);
    }

    public function test_scan_api_lookup_by_kode_unik_returns_user_data(): void
    {
        $archivist = User::factory()->create([
            'divisi_id' => $this->archivistDivisi->id,
            'jabatan_id' => $this->anggota->id,
            'status' => true,
        ]);

        $peserta = User::factory()->create([
            'nim' => '2508395',
            'nama' => 'Peserta Unik',
            'kode_unik' => 'MTR-XYZ9',
            'status' => true,
            'divisi_id' => $this->chefDivisi->id,
            'jabatan_id' => $this->anggota->id,
        ]);

        $response = $this->actingAs($archivist)
            ->postJson(route('dashboard.api.scan.lookup'), [
                'kode_unik' => 'XYZ9',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $peserta->id,
                    'nama' => 'Peserta Unik',
                    'nim' => '2508395',
                    'kode_unik' => 'MTR-XYZ9',
                ],
            ]);
    }

    public function test_scan_api_store_with_manual_source_records_manual_keterangan(): void
    {
        $archivist = User::factory()->create([
            'divisi_id' => $this->archivistDivisi->id,
            'jabatan_id' => $this->anggota->id,
            'status' => true,
        ]);

        $peserta = User::factory()->create([
            'status' => true,
            'divisi_id' => $this->chefDivisi->id,
            'jabatan_id' => $this->anggota->id,
        ]);

        $kegiatan = Kegiatan::create([
            'nama' => 'Acara Evaluasi',
            'tempat' => 'Aula Utama',
            'tanggal' => Carbon::today()->toDateString(),
            'tanggal_mulai' => Carbon::today()->toDateString(),
            'tanggal_selesai' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '12:00:00',
            'presensi_mulai' => Carbon::now()->subHour(),
            'presensi_selesai' => Carbon::now()->addHour(),
        ]);

        $response = $this->actingAs($archivist)
            ->postJson(route('dashboard.api.scan.store'), [
                'user_id' => $peserta->id,
                'kegiatan_id' => $kegiatan->id,
                'sumber' => 'manual',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('presensis', [
            'user_id' => $peserta->id,
            'kegiatan_id' => $kegiatan->id,
            'scanned_by' => $archivist->id,
            'status' => 'hadir',
        ]);

        $presensi = Presensi::where('user_id', $peserta->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        $this->assertStringContainsString('Input manual oleh', $presensi->keterangan);
    }
}
