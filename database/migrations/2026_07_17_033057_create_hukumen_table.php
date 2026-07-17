<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hukuman', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('pemberi_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('kategori', [
                'Ringan',
                'Sedang',
                'Berat'
            ]);

            $table->string('pelanggaran');

            $table->text('konsekuensi');

            $table->date('tanggal_hukuman');

            $table->enum('status', [
                'Belum Dijalankan',
                'Diproses',
                'Selesai'
            ])->default('Belum Dijalankan');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hukuman');
    }
};