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
        Schema::table('tugases', function (Blueprint $table) {
            $table->string('nomor', 10)->nullable()->unique()->after('id');
        });

        // Backfill existing tasks with 3-digit numbers
        $tugases = \App\Models\Tugas::orderBy('id')->get();
        $counter = 1;
        foreach ($tugases as $tugas) {
            $number = str_pad((string) $counter, 3, '0', STR_PAD_LEFT);
            $tugas->update(['nomor' => $number]);
            $counter++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugases', function (Blueprint $table) {
            $table->dropColumn('nomor');
        });
    }
};
