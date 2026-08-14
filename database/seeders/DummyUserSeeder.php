<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisis = Divisi::all();
        $jabatans = Jabatan::all();

        // Check if there are divisi and jabatan available
        if ($divisis->isEmpty() || $jabatans->isEmpty()) {
            $this->command->info('Divisi or Jabatan is empty. Please run DivisiSeeder and JabatanSeeder first.');
            return;
        }

        $counter = 1;
        $password = Hash::make('metastro2026');

        foreach ($divisis as $divisi) {
            foreach ($jabatans as $jabatan) {
                // Generate a unique NIM for the dummy user (e.g. 99 + padded counter)
                $nim = '99' . str_pad($counter, 4, '0', STR_PAD_LEFT);

                $user = User::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'name' => "Dummy {$divisi->nama_divisi} - {$jabatan->nama_jabatan}",
                        'password' => $password,
                        'divisi_id' => $divisi->id,
                        'jabatan_id' => $jabatan->id,
                        'role_id' => null, // Leave it null unless specified
                        'status_aktif' => true,
                        'email_verified_at' => now(),
                        'is_initial_setup_completed' => true,
                    ]
                );

                // Default role 'panitia', change to 'admin' or 'peserta' if needed
                $roleName = 'panitia';

                // Give 'admin' role if Jabatan is Ketua Pengawas as an example, but panitia is safer
                if (strtolower($jabatan->nama_jabatan) === 'pengawas' || strtolower($jabatan->nama_jabatan) === 'ketua pengawas') {
                    $roleName = 'panitia';
                }

                try {
                    if (!$user->hasRole($roleName)) {
                        $user->assignRole($roleName);
                    }
                } catch (\Throwable $e) {
                    // Ignore if Spatie roles are not configured properly yet
                }

                $counter++;
            }
        }

        $this->command->info('Dummy users for all combinations of Divisi and Jabatan created successfully.');
    }
}
