<?php

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('jabatans') || ! Schema::hasTable('users')) {
            return;
        }

        (new \Database\Seeders\JabatanSeeder)->run();

        $stakeholderDivisiId = Divisi::query()->where('nama', 'Stakeholder')->value('id');

        if (! $stakeholderDivisiId) {
            return;
        }

        $map = [
            'Ketua' => 'Ketua Pelaksana',
            'Wakil' => 'Wakil Ketua Pelaksana',
            'Anggota' => 'Steering Committee',
            'Pengawas' => 'Wakil Ketua Pengawas',
        ];

        foreach ($map as $from => $to) {
            $fromId = Jabatan::query()->where('nama', $from)->value('id');
            $toId = Jabatan::query()->where('nama', $to)->value('id');

            if (! $fromId || ! $toId) {
                continue;
            }

            User::query()
                ->where('divisi_id', $stakeholderDivisiId)
                ->where('jabatan_id', $fromId)
                ->update(['jabatan_id' => $toId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data remap is not safely reversible.
    }
};
