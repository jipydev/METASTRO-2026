<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('requested_role');

            $table->foreignId('requested_divisi_id')
                ->nullable()
                ->constrained('divisi')
                ->nullOnDelete();

            $table->foreignId('requested_jabatan_id')
                ->nullable()
                ->constrained('jabatan')
                ->nullOnDelete();

            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');

            $table->text('admin_note')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_requests');
    }
};
