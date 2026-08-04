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
        Schema::table('notulensi', function (Blueprint $table) {
            $table->dropForeign(['jadwal_id']);
            $table->dropColumn('jadwal_id');
            $table->longText('isi_notulensi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notulensi', function (Blueprint $table) {
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal')->cascadeOnDelete();
            $table->longText('isi_notulensi')->nullable(false)->change();
        });
    }
};
