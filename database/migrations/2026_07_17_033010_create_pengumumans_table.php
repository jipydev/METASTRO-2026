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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();

            // Konten Pengumuman
            $table->string('judul', 200);
            $table->longText('isi');
            $table->string('lampiran')->nullable(); // File PDF / Gambar banner mading

            // Target Sasaran (Sangat berguna untuk membedakan info Maba vs Panitia)
            $table->enum('target', ['semua', 'panitia', 'peserta'])->default('semua')->index();

            // Publikasi & Status
            $table->dateTime('tanggal_publish')->nullable()->index();
            $table->enum('status', ['draft', 'published'])->default('draft')->index();

            // Pembuat (Informer / Sekretaris / Admin)
            $table->foreignId('pembuat_id')
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
        Schema::dropIfExists('pengumumans');
    }
};
