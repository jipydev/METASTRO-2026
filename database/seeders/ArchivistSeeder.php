<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ArchivistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan role panitia & archivist tersedia
        Role::firstOrCreate(['name' => 'panitia', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'archivist', 'guard_name' => 'web']);

        // 2. Pastikan Divisi Archivist tersedia
        $divisi = Divisi::firstOrCreate(
            ['nama' => 'Archivist'],
            [
                'deskripsi' => 'Divisi Archivist atau Sekretaris bertugas mengelola administrasi, persuratan, dan presensi.',
            ]
        );

        // 3. Pastikan Jabatan Ketua tersedia
        $jabatan = Jabatan::firstOrCreate(
            ['nama' => 'Ketua'],
            ['deskripsi' => 'Ketua divisi bertugas memimpin divisi dan mereview izin anggota divisinya.']
        );

        // 4. Data Akun Archivist
        $email = 'archivist@metastro.id';
        $nim = '0000002';
        $password = 'password';

        /** @var User $user */
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'nim' => $nim,
                'nama' => 'Archivist Metastro',
                'password' => Hash::make($password),
                'divisi_id' => $divisi->id,
                'jabatan_id' => $jabatan->id,
                'status' => true,
                'must_change_password' => false,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'kode_unik' => 'MTR-0002',
                'qr_token' => Str::uuid()->toString(),
                'qr_updated_at' => now(),
            ]
        );

        // 5. Berikan role 'panitia' & 'archivist'
        $user->syncRoles(['panitia', 'archivist']);

        // 6. Generate QR Code SVG
        app(QrCodeService::class)->generateForUser($user);

        if (isset($this->command)) {
            $this->command->info('Akun Archivist berhasil dibuat/diperbarui:');
            $this->command->line("  - Nama     : {$user->nama}");
            $this->command->line("  - Email    : {$email}");
            $this->command->line("  - NIM      : {$nim}");
            $this->command->line("  - Password : {$password}");
            $this->command->line("  - Kode Unik: {$user->kode_unik}");
            $this->command->line("  - Divisi   : {$divisi->nama}");
        }
    }
}
