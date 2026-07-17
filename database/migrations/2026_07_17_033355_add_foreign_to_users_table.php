<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreign('divisi_id')
                ->references('id')
                ->on('divisi')
                ->nullOnDelete();

            $table->foreign('jabatan_id')
                ->references('id')
                ->on('jabatan')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['divisi_id']);
            $table->dropForeign(['jabatan_id']);

        });
    }
};
