<?php

namespace Database\Seeders;

use App\Models\Tim;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan role peserta tersedia
        Role::firstOrCreate(['name' => 'peserta', 'guard_name' => 'web']);

        // 2. Data Akun Peserta
        $email = 'peserta@metastro.id';
        $nim = '2500001';
        $password = 'password';

        /** @var User $user */
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'nim' => $nim,
                'nama' => 'Peserta Metastro',
                'password' => Hash::make($password),
                'divisi_id' => null,
                'jabatan_id' => null,
                'status' => true,
                'must_change_password' => false,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'kode_unik' => 'MTR-1001',
                'qr_token' => Str::uuid()->toString(),
                'qr_updated_at' => now(),
            ]
        );

        // 3. Berikan role 'peserta'
        $user->syncRoles(['peserta']);

        // 4. Hubungkan ke Tim pertama jika ada
        $tim = Tim::first();
        if ($tim && ! $user->tims()->where('tim_id', $tim->id)->exists()) {
            $user->tims()->create(['tim_id' => $tim->id]);
        }

        // 5. Generate QR Code SVG
        app(QrCodeService::class)->generateForUser($user);

        if (isset($this->command)) {
            $this->command->info('Akun Peserta berhasil dibuat/diperbarui:');
            $this->command->line("  - Nama     : {$user->nama}");
            $this->command->line("  - Email    : {$email}");
            $this->command->line("  - NIM      : {$nim}");
            $this->command->line("  - Password : {$password}");
            $this->command->line("  - Kode Unik: {$user->kode_unik}");
            $this->command->line('  - Tim      : '.($tim?->nama ?? 'Belum ada tim'));
        }
    }
}
