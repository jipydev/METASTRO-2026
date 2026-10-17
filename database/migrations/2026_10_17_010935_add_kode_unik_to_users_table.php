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
        Schema::table('users', function (Blueprint $table) {
            $table->string('kode_unik', 20)->nullable()->unique()->after('qr_token');
        });

        $users = \App\Models\User::whereNull('kode_unik')->get();
        if ($users->isNotEmpty()) {
            $qrService = app(\App\Services\QrCodeService::class);
            foreach ($users as $user) {
                $user->update(['kode_unik' => $qrService->generateUniqueCode()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kode_unik');
        });
    }
};
