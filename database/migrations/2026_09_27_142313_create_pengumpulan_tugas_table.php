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
        Schema::create('pengumpulan_tugases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tim_id')->nullable()->constrained('tims')->onDelete('set null');
            $table->string('file_path');
            $table->string('catatan_peserta');
            $table->string('catatan_pemeriksa')->nullable();
            $table->foreignId('pemeriksa_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'reviewed', 'rejected'])->default('pending');
            $table->datetime('tanggal_pengumpulan')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
