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

class PathfinderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan role panitia & pathfinder tersedia
        Role::firstOrCreate(['name' => 'panitia', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'pathfinder', 'guard_name' => 'web']);

        // 2. Pastikan Divisi Pathfinder tersedia
        $divisi = Divisi::firstOrCreate(
            ['nama' => 'Pathfinder'],
            [
                'deskripsi' => 'Divisi Pathfinder atau Acara bertugas merancang konsep, susunan acara, dan alur kegiatan.',
            ]
        );

        // 3. Pastikan Jabatan Ketua tersedia
        $jabatan = Jabatan::firstOrCreate(
            ['nama' => 'Ketua'],
            ['deskripsi' => 'Ketua divisi bertugas memimpin divisi dan mereview izin anggota divisinya.']
        );

        // 4. Data Akun Pathfinder
        $email = 'pathfinder@metastro.id';
        $nim = '0000003';
        $password = 'password';

        /** @var User $user */
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'nim' => $nim,
                'nama' => 'Pathfinder Metastro',
                'password' => Hash::make($password),
                'divisi_id' => $divisi->id,
                'jabatan_id' => $jabatan->id,
                'status' => true,
                'must_change_password' => false,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'kode_unik' => 'MTR-0003',
                'qr_token' => Str::uuid()->toString(),
                'qr_updated_at' => now(),
            ]
        );

        // 5. Berikan role 'panitia' & 'pathfinder'
        $user->syncRoles(['panitia', 'pathfinder']);

        // 6. Generate QR Code SVG
        app(QrCodeService::class)->generateForUser($user);

        if (isset($this->command)) {
            $this->command->info('Akun Pathfinder berhasil dibuat/diperbarui:');
            $this->command->line("  - Nama     : {$user->nama}");
            $this->command->line("  - Email    : {$email}");
            $this->command->line("  - NIM      : {$nim}");
            $this->command->line("  - Password : {$password}");
            $this->command->line("  - Kode Unik: {$user->kode_unik}");
            $this->command->line("  - Divisi   : {$divisi->nama}");
        }
    }
}
