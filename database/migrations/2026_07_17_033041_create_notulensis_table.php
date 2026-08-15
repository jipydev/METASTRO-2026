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
        Schema::create('notulensis', function (Blueprint $table) {
            $table->id();

            // Relasi ke Kegiatan (Opsional/Nullable)
            $table->foreignId('kegiatan_id')
                ->nullable()
                ->constrained('kegiatans')
                ->nullOnDelete();

            // Notulis / Pembuat Notulensi
            $table->foreignId('pembuat_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Konten Notulensi
            $table->string('judul', 150);
            $table->longText('isi')->nullable();
            $table->string('lampiran')->nullable(); // Path file PDF / dokumen

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulensis');
    }
};
