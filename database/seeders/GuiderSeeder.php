<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Guider;
use App\Models\Jabatan;
use App\Models\Tim;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class GuiderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan role panitia & guider tersedia
        Role::firstOrCreate(['name' => 'panitia', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'guider', 'guard_name' => 'web']);

        // 2. Pastikan Divisi Guider tersedia
        $divisi = Divisi::firstOrCreate(
            ['nama' => 'Guider'],
            [
                'deskripsi' => 'Divisi Guider atau Mentor kelompok bertugas membimbing, mengarahkan, dan mendampingi anggota baru atau peserta agar bisa beradaptasi dan berkembang dengan baik.',
            ]
        );

        // 3. Pastikan Jabatan Ketua tersedia
        $jabatan = Jabatan::firstOrCreate(
            ['nama' => 'Ketua'],
            ['deskripsi' => 'Ketua divisi bertugas memimpin divisi dan mereview izin anggota divisinya.']
        );

        // 4. Data Akun Guider
        $email = 'guider@metastro.id';
        $nim = '0000004';
        $password = 'password';

        /** @var User $user */
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'nim' => $nim,
                'nama' => 'Guider Metastro',
                'password' => Hash::make($password),
                'divisi_id' => $divisi->id,
                'jabatan_id' => $jabatan->id,
                'status' => true,
                'must_change_password' => false,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'kode_unik' => 'MTR-0004',
                'qr_token' => Str::uuid()->toString(),
                'qr_updated_at' => now(),
            ]
        );

        // 5. Berikan role 'panitia' & 'guider'
        $user->syncRoles(['panitia', 'guider']);

        // 6. Hubungkan sebagai pembimbing ke Tim pertama jika ada
        $tim = Tim::first();
        if ($tim && ! Guider::where('pembimbing_id', $user->id)->where('tim_id', $tim->id)->exists()) {
            Guider::create([
                'pembimbing_id' => $user->id,
                'tim_id' => $tim->id,
            ]);
        }

        // 7. Generate QR Code SVG
        app(QrCodeService::class)->generateForUser($user);

        if (isset($this->command)) {
            $this->command->info('Akun Guider berhasil dibuat/diperbarui:');
            $this->command->line("  - Nama        : {$user->nama}");
            $this->command->line("  - Email       : {$email}");
            $this->command->line("  - NIM         : {$nim}");
            $this->command->line("  - Password    : {$password}");
            $this->command->line("  - Kode Unik   : {$user->kode_unik}");
            $this->command->line("  - Divisi      : {$divisi->nama}");
            $this->command->line('  - Tim Binaan  : '.($tim?->nama ?? 'Belum ada tim'));
        }
    }
}
