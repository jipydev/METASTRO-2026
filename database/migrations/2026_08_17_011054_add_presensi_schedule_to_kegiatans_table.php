<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dateTime('presensi_dibuka_pada')->nullable()->after('status_presensi');
            $table->dateTime('presensi_ditutup_pada')->nullable()->after('presensi_dibuka_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn(['presensi_dibuka_pada', 'presensi_ditutup_pada']);
        });
    }
};
