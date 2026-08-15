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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();

            // Relasi Utama
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('kegiatan_id')
                ->constrained('kegiatans')
                ->cascadeOnDelete();

            // Jembatan ke Pengajuan Izin (Jika statusnya izin/sakit)
            $table->foreignId('pengajuan_izin_id')
                ->nullable()
                ->constrained('pengajuan_izins')
                ->nullOnDelete();

            // Petugas Scanner (Archivist/Admin saat scan QR)
            $table->foreignId('scanned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Detail Kehadiran
            $table->timestamp('jam_tap')->nullable();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->string('keterangan')->nullable();

            $table->timestamps();

            // 1 User hanya punya 1 record presensi per kegiatan
            $table->unique(['user_id', 'kegiatan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
