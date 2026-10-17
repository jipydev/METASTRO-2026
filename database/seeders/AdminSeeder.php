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

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan role sistem Spatie tersedia
        foreach (['admin', 'panitia', 'peserta'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Pastikan Divisi Chiper (divisi penentu hak akses Admin) tersedia
        $chiperDivisi = Divisi::firstOrCreate(
            ['nama' => 'Chiper'],
            [
                'deskripsi' => 'Divisi Chiper atau Pengelolaan Web bertugas merancang, memelihara, memperbarui, dan mengoptimalkan situs web resmi agar berfungsi secara optimal.',
            ]
        );

        // 3. Pastikan Jabatan tersedia
        $jabatan = Jabatan::firstOrCreate(
            ['nama' => 'Ketua'],
            ['deskripsi' => 'Ketua divisi bertugas memimpin divisi dan mereview izin anggota divisinya.']
        );

        // 4. Data Akun Admin
        $email = 'admin@metastro.id';
        $nim = '0000001';
        $password = 'password';

        /** @var User $user */
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'nim' => $nim,
                'nama' => 'Administrator Metastro',
                'password' => Hash::make($password),
                'divisi_id' => $chiperDivisi->id,
                'jabatan_id' => $jabatan->id,
                'status' => true,
                'must_change_password' => false,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'kode_unik' => 'MTR-0001',
                'qr_token' => Str::uuid()->toString(),
                'qr_updated_at' => now(),
            ]
        );

        // 5. Berikan role 'admin'
        $user->syncRoles(['admin']);

        // 6. Generate QR Code SVG
        app(QrCodeService::class)->generateForUser($user);

        if (isset($this->command)) {
            $this->command->info('Akun Admin berhasil dibuat/diperbarui:');
            $this->command->line("  - Nama     : {$user->nama}");
            $this->command->line("  - Email    : {$email}");
            $this->command->line("  - NIM      : {$nim}");
            $this->command->line("  - Password : {$password}");
            $this->command->line("  - Kode Unik: {$user->kode_unik}");
            $this->command->line("  - Divisi   : {$chiperDivisi->nama}");
        }
    }
}
