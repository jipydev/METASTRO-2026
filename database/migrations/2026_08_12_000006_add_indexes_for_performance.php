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
        Schema::table('users', function (Blueprint $table) {
            $table->index('status_aktif', 'idx_users_status_aktif');
            $table->index('divisi_id', 'idx_users_divisi_id');
            $table->index('jabatan_id', 'idx_users_jabatan_id');
        });

        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->index('status', 'idx_pengajuan_status');
            $table->index('status_koordinator', 'idx_pengajuan_status_koordinator');
            $table->index('status_ranger', 'idx_pengajuan_status_ranger');
            $table->index('tanggal_izin', 'idx_pengajuan_tanggal_izin');
        });

        Schema::table('rapats', function (Blueprint $table) {
            $table->index('tanggal', 'idx_rapats_tanggal');
            $table->index('status_absen', 'idx_rapats_status_absen');
        });

        Schema::table('pengumuman', function (Blueprint $table) {
            $table->index('status', 'idx_pengumuman_status');
            $table->index('tanggal_publish', 'idx_pengumuman_tanggal_publish');
        });

        Schema::table('notulensi', function (Blueprint $table) {
            $table->index('created_at', 'idx_notulensi_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_status_aktif');
            $table->dropIndex('idx_users_divisi_id');
            $table->dropIndex('idx_users_jabatan_id');
        });

        Schema::table('pengajuan_izin', function (Blueprint $table) {
            $table->dropIndex('idx_pengajuan_status');
            $table->dropIndex('idx_pengajuan_status_koordinator');
            $table->dropIndex('idx_pengajuan_status_ranger');
            $table->dropIndex('idx_pengajuan_tanggal_izin');
        });

        Schema::table('rapats', function (Blueprint $table) {
            $table->dropIndex('idx_rapats_tanggal');
            $table->dropIndex('idx_rapats_status_absen');
        });

        Schema::table('pengumuman', function (Blueprint $table) {
            $table->dropIndex('idx_pengumuman_status');
            $table->dropIndex('idx_pengumuman_tanggal_publish');
        });

        Schema::table('notulensi', function (Blueprint $table) {
            $table->dropIndex('idx_notulensi_created_at');
        });
    }
};
