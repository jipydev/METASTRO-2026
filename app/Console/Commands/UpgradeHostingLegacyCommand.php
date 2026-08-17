<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpgradeHostingLegacyCommand extends Command
{
    protected $signature = 'hosting:upgrade-legacy
                            {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Ubah skema database hosting lama ke skema app sekarang, tanpa menghapus data user';

    public function handle(): int
    {
        if (! Schema::hasTable('divisi') && Schema::hasTable('divisis')) {
            $this->warn('Database ini sudah memakai skema baru (tabel divisis ada). Tidak ada yang diubah.');

            return self::SUCCESS;
        }

        if (! Schema::hasTable('divisi') || ! Schema::hasTable('users') || ! Schema::hasTable('rapats')) {
            $this->error('Ini bukan dump hosting lama. Diperlukan tabel divisi, users, dan rapats.');

            return self::FAILURE;
        }

        $this->warn('Script ini mengubah struktur database. Backup dulu sebelum lanjut.');

        if (! $this->option('force') && ! $this->confirm('Lanjut upgrade skema sekarang?', false)) {
            return self::SUCCESS;
        }

        $sql = file_get_contents(database_path('scripts/upgrade_hosting_legacy.sql'));

        if ($sql === false) {
            $this->error('File database/scripts/upgrade_hosting_legacy.sql tidak ditemukan.');

            return self::FAILURE;
        }

        DB::unprepared($sql);

        $this->info('Upgrade selesai.');
        $this->line('Users: '.$this->countSafe('users'));
        $this->line('Kegiatan: '.$this->countSafe('kegiatans'));
        $this->line('Presensi: '.$this->countSafe('presensis'));
        $this->line('Izin: '.$this->countSafe('pengajuan_izins'));
        $this->line('Pengumuman: '.$this->countSafe('pengumumans'));

        return self::SUCCESS;
    }

    private function countSafe(string $table): int
    {
        return Schema::hasTable($table) ? (int) DB::table($table)->count() : 0;
    }
}
