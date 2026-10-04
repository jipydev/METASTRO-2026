<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->enum('jenis', ['rapat', 'pelaksanaan'])
                ->default('rapat')
                ->after('deskripsi')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropIndex(['jenis']);
            $table->dropColumn('jenis');
        });
    }
};
