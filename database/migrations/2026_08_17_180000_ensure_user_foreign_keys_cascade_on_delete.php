<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pastikan FK hukumans ke users memakai cascade delete di hosting legacy.
     */
    public function up(): void
    {
        if (! Schema::hasTable('hukumans')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::table('hukumans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['issued_by']);
        });

        Schema::table('hukumans', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('issued_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak di-reverse agar perilaku delete tetap aman di production.
    }
};
