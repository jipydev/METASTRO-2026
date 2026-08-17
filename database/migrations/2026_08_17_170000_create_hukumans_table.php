<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hukumans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->enum('kategori', ['ringan', 'sedang', 'berat', 'khusus']);
            $table->enum('issuer_mode', ['ranger', 'pengawas']);
            $table->text('alasan');
            $table->text('pembelaan')->nullable();
            $table->timestamp('pembelaan_at')->nullable();
            $table->string('tugas_link', 2048)->nullable();
            $table->timestamp('tugas_submitted_at')->nullable();
            $table->timestamp('deadline_at');
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'selesai_at']);
            $table->index(['issued_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hukumans');
    }
};
