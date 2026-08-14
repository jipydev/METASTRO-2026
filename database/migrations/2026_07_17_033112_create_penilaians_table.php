<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('penilai_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('divisi_id')
                ->nullable()
                ->constrained('divisi')
                ->nullOnDelete();

            $table->enum('kategori', [
                'Panitia',
                'Peserta',
                'Divisi',
            ]);

            $table->unsignedTinyInteger('disiplin');
            $table->unsignedTinyInteger('kehadiran');
            $table->unsignedTinyInteger('kerjasama');
            $table->unsignedTinyInteger('tanggung_jawab');
            $table->unsignedTinyInteger('inisiatif');

            $table->decimal('nilai_akhir', 5, 2)->default(0);

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique([
                'user_id',
                'penilai_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
