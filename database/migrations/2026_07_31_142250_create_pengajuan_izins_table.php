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
        Schema::create('pengajuan_izins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('kegiatan_id')
                ->nullable()
                ->constrained('kegiatans')
                ->nullOnDelete();

            $table->date('tanggal_izin')->index();
            $table->enum('jenis_izin', ['sakit', 'izin'])->default('izin');
            $table->text('alasan');
            $table->string('bukti')->nullable(); // Foto surat dokter/dispensasi

            // Approval Koordinator / Ketua Divisi
            $table->enum('status_koordinator', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('reviewed_by_koordinator')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at_koordinator')->nullable();
            $table->text('catatan_koordinator')->nullable();

            // Approval Ranger (Kedisiplinan)
            $table->enum('status_ranger', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('reviewed_by_ranger')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at_ranger')->nullable();
            $table->text('catatan_ranger')->nullable();

            // Status Final
            $table->enum('status', ['pending', 'diproses', 'approved', 'rejected'])->default('pending')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izins');
    }
};
