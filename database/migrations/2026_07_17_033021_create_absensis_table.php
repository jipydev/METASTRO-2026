<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {

            $table->id();

            $table->foreignId('jadwal_id')
                ->constrained('jadwal')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status',[
                'Hadir',
                'Izin',
                'Sakit',
                'Alpha'
            ]);

            $table->text('keterangan')->nullable();

            $table->string('bukti')->nullable();

            $table->timestamp('waktu_absen')->nullable();

            $table->decimal('persentase_kehadiran',5,2)
                ->default(0);

            $table->timestamps();

            $table->unique([
                'jadwal_id',
                'user_id'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};