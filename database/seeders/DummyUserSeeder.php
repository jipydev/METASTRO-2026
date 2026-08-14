<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisis = Divisi::whereIn('nama_divisi', [
            'Archivist', 'Scribe', 'Gearmaster', 'Chef', 'Chiper',
            'Rescuer', 'Informer', 'Ranger', 'Guider', 'Documenter',
            'Guardian', 'Pathfinder', 'Fundkeeper', 'Stakeholder'
        ])->get();

        $jabatans = Jabatan::whereIn('nama_jabatan', [
            'Ketua', 'Wakil', 'Ketua Pengawas', 'Pengawas', 'Anggota'
        ])->get();

        if ($divisis->isEmpty() || $jabatans->isEmpty()) {
            $this->command->info('Divisi or Jabatan is empty. Please run DivisiSeeder and JabatanSeeder first.');
            return;
        }

        $counter = 1;
        $password = Hash::make('metastro2026');

        foreach ($divisis as $divisi) {
            foreach ($jabatans as $jabatan) {
                // Unique NIM format: 990001, 990002, etc.
                $nim = '99' . str_pad($counter, 4, '0', STR_PAD_LEFT);

                $user = User::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'name' => "Dummy {$divisi->nama_divisi} - {$jabatan->nama_jabatan}",
                        'password' => $password,
                        'divisi_id' => $divisi->id,
                        'jabatan_id' => $jabatan->id,
                        'role_id' => null,
                        'status_aktif' => true,
                        'email_verified_at' => now(),
                        'is_initial_setup_completed' => true,
                    ]
                );

                // Role assignment:
                // Chiper members / Admins get 'admin' role. Everyone else gets 'panitia'.
                $roleName = (strtolower($divisi->nama_divisi) === 'chiper') ? 'admin' : 'panitia';

                try {
                    $user->syncRoles([$roleName]);
                } catch (\Throwable $e) {
                    // Ignore if Spatie roles are not configured
                }

                $counter++;
            }
        }

        $this->command->info("Dummy users created/updated successfully ({$counter} users).");
    }
}
