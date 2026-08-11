<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->unique()->after('nim');
            }
            if (! Schema::hasColumn('users', 'is_initial_setup_completed')) {
                $table->boolean('is_initial_setup_completed')->default(false)->after('status_aktif');
            }
        });

        // Set existing users as completed so existing accounts don't get locked out
        DB::table('users')->update(['is_initial_setup_completed' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_initial_setup_completed')) {
                $table->dropColumn('is_initial_setup_completed');
            }
            if (Schema::hasColumn('users', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
