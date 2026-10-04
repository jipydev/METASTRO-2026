<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('tanggal');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });

        DB::table('kegiatans')->update([
            'tanggal_mulai' => DB::raw('tanggal'),
            'tanggal_selesai' => DB::raw('tanggal'),
        ]);

        Schema::table('kegiatans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable(false)->change();
            $table->date('tanggal_selesai')->nullable(false)->change();
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropIndex(['tanggal_mulai', 'tanggal_selesai']);
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
