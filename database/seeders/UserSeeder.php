<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Semua akun seed memakai password: password
     */
    public function run(): void
    {
        foreach (['admin', 'panitia', 'peserta'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->upsertUser([
            'nim' => '0000001',
            'nama' => 'Administrator',
            'email' => 'admin@metastro.id',
            'divisi' => 'Chiper',
            'jabatan' => 'Ketua',
            'role' => 'admin',
        ]);

        $this->upsertUser([
            'nim' => '0000002',
            'nama' => 'Ketua Gearmaster',
            'email' => 'panitia@metastro.id',
            'divisi' => 'Gearmaster',
            'jabatan' => 'Ketua',
            'role' => 'panitia',
        ]);

        $this->upsertUser([
            'nim' => '0000003',
            'nama' => 'Peserta Contoh',
            'email' => 'peserta@metastro.id',
            'divisi' => null,
            'jabatan' => null,
            'role' => 'peserta',
        ]);

        $divisis = Divisi::orderBy('nama')->get();
        $nim = 1000001;

        foreach ($divisis as $divisi) {
            $jabatans = match (strcasecmp($divisi->nama, 'Stakeholder')) {
                0 => [
                    'Person in Charge',
                    'Ketua Pelaksana',
                    'Wakil Ketua Pelaksana',
                    'Ketua Pengawas',
                    'Wakil Ketua Pengawas',
                    'Steering Committee',
                ],
                default => ['Ketua', 'Wakil', 'Anggota'],
            };

            foreach ($jabatans as $jabatanNama) {
                $isChiperKetua = strcasecmp($divisi->nama, 'Chiper') === 0 && $jabatanNama === 'Ketua';
                $isGearmasterKetua = strcasecmp($divisi->nama, 'Gearmaster') === 0 && $jabatanNama === 'Ketua';

                if ($isChiperKetua || $isGearmasterKetua) {
                    continue;
                }

                $slug = Str::slug($divisi->nama);
                $jabatanSlug = Str::slug($jabatanNama);
                $role = strcasecmp($divisi->nama, 'Chiper') === 0 ? 'admin' : 'panitia';

                $this->upsertUser([
                    'nim' => (string) $nim++,
                    'nama' => "{$jabatanNama} {$divisi->nama}",
                    'email' => "{$jabatanSlug}.{$slug}@metastro.id",
                    'divisi' => $divisi->nama,
                    'jabatan' => $jabatanNama,
                    'role' => $role,
                ]);
            }

            $leaderJabatan = strcasecmp($divisi->nama, 'Stakeholder') === 0 ? 'Ketua Pelaksana' : 'Ketua';

            $ketua = User::where('divisi_id', $divisi->id)
                ->whereHas('jabatan', fn ($q) => $q->where('nama', $leaderJabatan))
                ->first();

            if ($ketua) {
                $divisi->update(['koordinator_id' => $ketua->id]);
            }
        }
    }

    /**
     * @param  array{nim: string, nama: string, email: string, divisi: ?string, jabatan: ?string, role: string}  $data
     */
    private function upsertUser(array $data): User
    {
        $divisiId = $data['divisi']
            ? Divisi::where('nama', $data['divisi'])->value('id')
            : null;

        $jabatanId = $data['jabatan']
            ? Jabatan::where('nama', $data['jabatan'])->value('id')
            : null;

        $user = User::firstOrNew(['email' => $data['email']]);

        $user->fill([
            'nama' => $data['nama'],
            'divisi_id' => $divisiId,
            'jabatan_id' => $jabatanId,
            'status' => true,
            'is_initial_setup_completed' => true,
            'email_verified_at' => now(),
        ]);

        if (! $user->exists) {
            $nim = $data['nim'];
            while (User::where('nim', $nim)->exists()) {
                $nim = (string) ((int) $nim + 1);
            }

            $user->nim = $nim;
            $user->password = 'password';
            $user->qr_token = (string) Str::uuid();
            $user->qr_updated_at = now();
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        return $user;
    }
}
