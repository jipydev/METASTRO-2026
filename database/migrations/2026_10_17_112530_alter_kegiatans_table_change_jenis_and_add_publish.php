<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            // 1. Tambah kolom baru tentang publish (boolean: true/false)
            // Default diatur ke false (tidak publish) agar data lama aman
            $table->boolean('is_published')->default(false)->after('jenis');

            // 2. Mengubah tipe/opsi kolom 'jenis' menggunakan DB::statement 
            // Karena Laravel Blueprint->change() terkadang bermasalah pada beberapa versi DB untuk Enum
            DB::statement("ALTER TABLE kegiatans MODIFY COLUMN jenis ENUM('rapat', 'pelaksanaan') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            // 1. Hapus kolom publish
            $table->dropColumn('is_published');

            // 2. Kembalikan kolom 'jenis' ke kondisi sebelumnya
            DB::statement("ALTER TABLE kegiatans MODIFY COLUMN jenis ENUM('rapat', 'acara', 'pembinaan', 'gladi') NOT NULL");
        });
    }
};
