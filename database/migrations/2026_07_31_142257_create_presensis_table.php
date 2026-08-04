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
            $table->string('nim_user', 7);
            $table->foreign('nim_user')->references('nim')->on('users')->onDelete('cascade');
            $table->foreignId('timeline_id')->constrained('timelines')->onDelete('cascade');
            $table->datetime('waktu_presensi')->nullable();
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->nullable();
            $table->string('bukti_foto', 255)->nullable();
            $table->string('surat_izin', 255)->nullable();
            $table->string('jenis_izin', 255)->nullable();
            $table->string('scanned_by_user_nim', 7)->nullable();
            $table->foreign('scanned_by_user_nim')->references('nim')->on('users')->onDelete('cascade');
            $table->timestamps();
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
