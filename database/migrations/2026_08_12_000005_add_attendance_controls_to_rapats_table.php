<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapats', function (Blueprint $table) {
            $table->enum('status_absen', ['Tutup', 'Buka', 'Dijadwalkan'])->default('Tutup')->after('total');
            $table->time('waktu_buka')->nullable()->after('status_absen');
            $table->time('waktu_telat')->nullable()->after('waktu_buka');
            $table->time('waktu_tutup')->nullable()->after('waktu_telat');
        });
    }

    public function down(): void
    {
        Schema::table('rapats', function (Blueprint $table) {
            $table->dropColumn(['status_absen', 'waktu_buka', 'waktu_telat', 'waktu_tutup']);
        });
    }
};
