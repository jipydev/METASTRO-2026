<?php

namespace Database\Seeders;

use App\Models\Presensi;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timelines = Timeline::query()->get();
        $users = User::query()
            ->whereNotNull('nim')
            ->where('nim', '!=', '')
            ->get();

        if ($timelines->isEmpty() || $users->isEmpty()) {
            $this->command->info('Tidak ada timeline atau user yang tersedia untuk membuat presensi.');

            return;
        }

        $statuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];

        foreach ($timelines as $timeline) {
            $baseTime = $timeline->tanggal_mulai ? Carbon::parse($timeline->tanggal_mulai) : now();

            foreach ($users as $user) {
                $status = $statuses[array_rand($statuses)];
                $waktuPresensi = (clone $baseTime)->addMinutes(rand(0, 90));
                $payload = [
                    'nim_user' => $user->nim,
                    'timeline_id' => $timeline->id,
                    'waktu_presensi' => $waktuPresensi,
                    'status' => $status,
                ];

                if ($status === 'Izin' || $status === 'Sakit') {
                    $payload['surat_izin'] = 'storage/app/public/dummy/izin.pdf';
                    $payload['jenis_izin'] = $status === 'Izin' ? 'Izin Keterangan' : 'Sakit';
                    $payload['bukti_foto'] = 'storage/app/public/dummy/foto.jpg';
                }

                if ($status === 'Hadir') {
                    $payload['scanned_by_user_nim'] = $users->random()->nim;
                }

                $presensi = new Presensi($payload);
                $presensi->status = $presensi->resolveStatus($timeline);

                Presensi::updateOrCreate(
                    [
                        'nim_user' => $user->nim,
                        'timeline_id' => $timeline->id,
                    ],
                    [
                        'nim_user' => $presensi->nim_user,
                        'timeline_id' => $presensi->timeline_id,
                        'waktu_presensi' => $presensi->waktu_presensi,
                        'status' => $presensi->status,
                        'bukti_foto' => $presensi->bukti_foto ?? null,
                        'surat_izin' => $presensi->surat_izin ?? null,
                        'scanned_by_user_nim' => $presensi->scanned_by_user_nim ?? null,
                        'jenis_izin' => $presensi->jenis_izin ?? null,
                    ]
                );
            }
        }
    }
}
