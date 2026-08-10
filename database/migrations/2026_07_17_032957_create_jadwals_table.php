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
        Schema::create('jadwal', function (Blueprint $table) {

            $table->id();

            $table->string('nama_kegiatan');
            $table->enum('jenis_kegiatan', [
                'Rapat',
                'Briefing',
                'Pelatihan',
                'Lainnya',
            ]);

            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();

            $table->string('lokasi');
            $table->text('deskripsi')->nullable();

            $table->enum('status', [
                'Terjadwal',
                'Berlangsung',
                'Selesai',
                'Dibatalkan',
            ])->default('Terjadwal');

            $table->foreignId('dibuat_oleh')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
