<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            // 1. Tambah kolom is_published jika belum ada
            if (!Schema::hasColumn('kegiatans', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('jenis');
            }
        });

        // 2. Ubah kolom jenis sementara menjadi VARCHAR agar data lama tidak error/terpotong
        DB::statement("ALTER TABLE kegiatans MODIFY COLUMN jenis VARCHAR(50) NOT NULL");

        // 3. (Opsional/Penting) Petakan data lama jika ada yang tidak sesuai ke 'peserta' atau 'panitia'
        // Sesuaikan dengan kondisi data lama Anda di database production
        DB::statement("UPDATE kegiatans SET jenis = 'peserta' WHERE jenis NOT IN ('panitia', 'peserta')");

        // 4. Ubah kembali kolom jenis ke ENUM('panitia', 'peserta') yang diinginkan
        DB::statement("ALTER TABLE kegiatans MODIFY COLUMN jenis ENUM('panitia', 'peserta') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            if (Schema::hasColumn('kegiatans', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });

        DB::statement("ALTER TABLE kegiatans MODIFY COLUMN jenis ENUM('rapat', 'pelaksanaan') NOT NULL");
    }
};
