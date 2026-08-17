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
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();

            // Informasi Utama Kegiatan
            $table->string('nama', 150); // Contoh: "RABES 1", "Day 1 Ospek Jurusan"
            $table->text('deskripsi')->nullable();
            $table->string('tipe', 50)->default('rapat'); // rapat, acara, pembinaan, gladi
            $table->string('tempat', 150);

            // Waktu Pelaksanaan Kegiatan
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable()->index();

            // Kontrol & Penjadwalan Sesi Presensi
            $table->enum('status_presensi', ['tutup', 'buka', 'dijadwalkan'])->default('tutup')->index();
            $table->time('presensi_mulai')->nullable();   // Jam mulai boleh scan
            $table->time('presensi_selesai')->nullable(); // Batas akhir scan ditutup

            // Penanggung Jawab / Pembuat Kegiatan
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
