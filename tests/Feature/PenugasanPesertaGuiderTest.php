<?php

namespace Tests\Feature;

use App\Models\AnggotaTim;
use App\Models\Divisi;
use App\Models\Guider;
use App\Models\Jabatan;
use App\Models\PengumpulanTugas;
use App\Models\Tim;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PenugasanPesertaGuiderTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['sqlite'];

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => 'c:\\laragon\\www\\METASTRO-2026\\metastro_2026',
        ]);
        \Illuminate\Support\Facades\DB::purge('sqlite');
        \Illuminate\Support\Facades\DB::reconnect('sqlite');
        $this->withoutVite();
        Storage::fake('public');

        // Setup role & divisi dasar
        Role::firstOrCreate(['name' => 'Peserta', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Panitia', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        $divisiPathfinder = Divisi::firstOrCreate(['nama' => 'Pathfinder']);
        $divisiGuider = Divisi::firstOrCreate(['nama' => 'Guider']);
        $jabatanAnggota = Jabatan::firstOrCreate(['nama' => 'Anggota']);
    }

    public function test_peserta_can_view_assigned_tasks_and_upload_pdf_individual_task()
    {
        $this->withoutExceptionHandling();
        $pathfinderDivisi = Divisi::where('nama', 'Pathfinder')->first();
        $pathfinder = User::factory()->create([
            'divisi_id' => $pathfinderDivisi->id,
            'status' => true,
        ]);

        $peserta = User::factory()->create(['status' => true]);
        $peserta->assignRole('Peserta');

        // Pathfinder buat tugas individu
        $tugas = Tugas::create([
            'judul' => 'Esai Refleksi Diri',
            'deskripsi' => 'Buat esai maksimal 500 kata format PDF.',
            'jenis' => 'individu',
            'day' => 'Day 1',
            'tenggat_waktu' => Carbon::now()->addDays(2),
            'pembuat_id' => $pathfinder->id,
        ]);

        // Peserta lihat list penugasan
        $response = $this->actingAs($peserta)->get(route('peserta.tugas-list'));
        $response->assertStatus(200);
        $response->assertSee('Esai Refleksi Diri');
        $response->assertSee('Belum Dikerjakan');

        // Peserta mengumpulkan file PDF
        $file = UploadedFile::fake()->create('jawaban_esai.pdf', 500, 'application/pdf');

        $submitResponse = $this->actingAs($peserta)->post(route('peserta.tugas-kumpulkan.store', $tugas->id), [
            'file_tugas' => $file,
            'catatan_peserta' => 'Tugas sudah selesai dikerjakan.',
        ]);

        $submitResponse->assertRedirect(route('peserta.tugas-kumpulkan', $tugas->id));
        $submitResponse->assertSessionHas('success');

        $this->assertDatabaseHas('pengumpulan_tugases', [
            'tugas_id' => $tugas->id,
            'user_id' => $peserta->id,
            'catatan_peserta' => 'Tugas sudah selesai dikerjakan.',
            'status' => 'pending',
        ]);

        // Cek halaman list tugas menampilkan badge pending
        $listView = $this->actingAs($peserta)->get(route('peserta.tugas-list'));
        $listView->assertSee('Menunggu Review');
    }

    public function test_team_task_is_submitted_once_by_team_representative()
    {
        $pathfinder = User::factory()->create([
            'divisi_id' => Divisi::where('nama', 'Pathfinder')->first()->id,
            'status' => true,
        ]);

        $slugA = 'tim-' . \Illuminate\Support\Str::random(8);
        $timA = Tim::create(['nama' => 'Tim ' . $slugA, 'slug' => $slugA]);

        $peserta1 = User::factory()->create(['status' => true]);
        $peserta2 = User::factory()->create(['status' => true]);
        $peserta1->assignRole('Peserta');
        $peserta2->assignRole('Peserta');

        AnggotaTim::create(['tim_id' => $timA->id, 'anggota_id' => $peserta1->id]);
        AnggotaTim::create(['tim_id' => $timA->id, 'anggota_id' => $peserta2->id]);

        $tugasTim = Tugas::create([
            'judul' => 'Video Yel-Yel Tim',
            'deskripsi' => 'Kumpulkan laporan PDF video tim.',
            'jenis' => 'tim',
            'day' => 'Day 1',
            'tenggat_waktu' => Carbon::now()->addDays(2),
            'pembuat_id' => $pathfinder->id,
        ]);

        // Peserta 1 mengumpulkan tugas tim
        $file = UploadedFile::fake()->create('laporan_tim.pdf', 1000, 'application/pdf');
        $this->actingAs($peserta1)->post(route('peserta.tugas-kumpulkan.store', $tugasTim->id), [
            'file_tugas' => $file,
            'catatan_peserta' => 'Mewakili Tim Alpha.',
        ]);

        $this->assertDatabaseHas('pengumpulan_tugases', [
            'tugas_id' => $tugasTim->id,
            'tim_id' => $timA->id,
            'status' => 'pending',
        ]);

        // Peserta 2 dalam tim yang sama melihat status bahwa tugas sudah dikumpulkan
        $response = $this->actingAs($peserta2)->get(route('peserta.tugas-list'));
        $response->assertSee('Menunggu Review');

        $kumpulkanView = $this->actingAs($peserta2)->get(route('peserta.tugas-kumpulkan', $tugasTim->id));
        $kumpulkanView->assertSee('Mewakili Tim Alpha.');
    }

    public function test_peserta_can_delete_submission_before_deadline_but_cannot_after_deadline()
    {
        $this->withoutExceptionHandling();
        $pathfinder = User::factory()->create([
            'divisi_id' => Divisi::where('nama', 'Pathfinder')->first()->id,
            'status' => true,
        ]);

        $peserta = User::factory()->create(['status' => true]);
        $peserta->assignRole('Peserta');

        $tugas = Tugas::create([
            'judul' => 'Tugas Deadline',
            'deskripsi' => 'Uji deadline.',
            'jenis' => 'individu',
            'tenggat_waktu' => Carbon::now()->addDays(1),
            'pembuat_id' => $pathfinder->id,
        ]);

        $file = UploadedFile::fake()->create('doc.pdf', 200, 'application/pdf');
        $uploadResp = $this->actingAs($peserta)->post(route('peserta.tugas-kumpulkan.store', $tugas->id), [
            'file_tugas' => $file,
        ]);

        // Hapus sebelum deadline -> berhasil
        $delResponse = $this->actingAs($peserta)->delete(route('peserta.tugas-kumpulkan.destroy', $tugas->id));
        $delResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('pengumpulan_tugases', [
            'tugas_id' => $tugas->id,
            'user_id' => $peserta->id,
        ]);

        // Ubah tugas menjadi sudah lewat deadline
        $tugas->update(['tenggat_waktu' => Carbon::now()->subDays(1)]);

        // Coba upload setelah deadline -> ditolak
        $lateUpload = $this->actingAs($peserta)->post(route('peserta.tugas-kumpulkan.store', $tugas->id), [
            'file_tugas' => $file,
        ]);
        $lateUpload->assertSessionHasErrors('file_tugas');
        $this->assertDatabaseMissing('pengumpulan_tugases', [
            'tugas_id' => $tugas->id,
            'user_id' => $peserta->id,
        ]);
    }

    public function test_guider_only_reviews_their_own_team_members()
    {
        $divisiGuider = Divisi::where('nama', 'Guider')->first();
        $guiderA = User::factory()->create([
            'divisi_id' => $divisiGuider->id,
            'status' => true,
        ]);
        $guiderB = User::factory()->create([
            'divisi_id' => $divisiGuider->id,
            'status' => true,
        ]);

        $slugA = 'tim-a-' . \Illuminate\Support\Str::random(8);
        $slugB = 'tim-b-' . \Illuminate\Support\Str::random(8);
        $timA = Tim::create(['nama' => 'Tim A ' . $slugA, 'slug' => $slugA]);
        $timB = Tim::create(['nama' => 'Tim B ' . $slugB, 'slug' => $slugB]);

        // Guider A membimbing Tim A, Guider B membimbing Tim B
        Guider::create(['tim_id' => $timA->id, 'pembimbing_id' => $guiderA->id]);
        Guider::create(['tim_id' => $timB->id, 'pembimbing_id' => $guiderB->id]);

        $pesertaA = User::factory()->create(['status' => true]);
        $pesertaB = User::factory()->create(['status' => true]);
        AnggotaTim::create(['tim_id' => $timA->id, 'anggota_id' => $pesertaA->id]);
        AnggotaTim::create(['tim_id' => $timB->id, 'anggota_id' => $pesertaB->id]);

        $tugas = Tugas::create([
            'judul' => 'Tugas Review',
            'deskripsi' => 'Review guider test.',
            'jenis' => 'individu',
            'tenggat_waktu' => Carbon::now()->addDays(1),
            'pembuat_id' => $guiderA->id,
        ]);

        // Peserta A kumpul tugas
        $fileA = UploadedFile::fake()->create('jawaban_a.pdf', 300, 'application/pdf');
        $this->actingAs($pesertaA)->post(route('peserta.tugas-kumpulkan.store', $tugas->id), [
            'file_tugas' => $fileA,
            'catatan_peserta' => 'Catatan Anggota Tim A',
        ]);

        // Peserta B kumpul tugas
        $fileB = UploadedFile::fake()->create('jawaban_b.pdf', 300, 'application/pdf');
        $this->actingAs($pesertaB)->post(route('peserta.tugas-kumpulkan.store', $tugas->id), [
            'file_tugas' => $fileB,
            'catatan_peserta' => 'Catatan Anggota Tim B',
        ]);

        // Guider A membuka halaman review
        $reviewViewGuiderA = $this->actingAs($guiderA)->get(route('dashboard.pengumpulan-tugas.index'));
        $reviewViewGuiderA->assertStatus(200);
        $reviewViewGuiderA->assertSee('Catatan Anggota Tim A');
        $reviewViewGuiderA->assertDontSee('Catatan Anggota Tim B');

        // Guider B membuka halaman review
        $reviewViewGuiderB = $this->actingAs($guiderB)->get(route('dashboard.pengumpulan-tugas.index'));
        $reviewViewGuiderB->assertStatus(200);
        $reviewViewGuiderB->assertSee('Catatan Anggota Tim B');
        $reviewViewGuiderB->assertDontSee('Catatan Anggota Tim A');

        // Guider A mereview submission dari peserta A
        $submisiA = PengumpulanTugas::where('user_id', $pesertaA->id)->first();
        $reviewResponse = $this->actingAs($guiderA)->put(route('dashboard.pengumpulan-tugas.update', $submisiA->id), [
            'status' => 'reviewed',
            'catatan_pemeriksa' => 'Pekerjaan sangat bagus!',
        ]);

        $reviewResponse->assertSessionHas('success');
        $this->assertDatabaseHas('pengumpulan_tugases', [
            'id' => $submisiA->id,
            'status' => 'reviewed',
            'catatan_pemeriksa' => 'Pekerjaan sangat bagus!',
            'pemeriksa_id' => $guiderA->id,
        ]);

        // Guider A mencoba mereview submission Peserta B (harus 403 Forbidden)
        $submisiB = PengumpulanTugas::where('user_id', $pesertaB->id)->first();
        $unauthorizedReview = $this->actingAs($guiderA)->put(route('dashboard.pengumpulan-tugas.update', $submisiB->id), [
            'status' => 'reviewed',
            'catatan_pemeriksa' => 'Coba review tim lain',
        ]);
        $unauthorizedReview->assertStatus(403);
    }
}
