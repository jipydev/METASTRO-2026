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
        Schema::dropIfExists('list_panitias');

        Schema::create('list_panitias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rapat_id')->constrained('rapats')->cascadeOnDelete();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->time('jam_tap');
            $table->string('status');
            $table->timestamps();

            // Unique constraint agar 1 user hanya bisa absen 1 kali per rapat
            $table->unique(['user_id', 'rapat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_panitias');
        
        // Recreate the old structure
        Schema::create('list_panitias', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('divisi');
            $table->string('jam_tap');
            $table->string('tanggal');
            $table->string('status');
            $table->timestamps();
        });
    }
};
