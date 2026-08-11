<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal_izin');
            $table->enum('jenis_izin', ['Sakit', 'Izin']);
            $table->text('alasan');
            $table->string('surat_izin')->nullable();
            $table->string('bukti')->nullable();

            // Status Approval Koordinator
            $table->enum('status_koordinator', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('reviewed_by_koordinator')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at_koordinator')->nullable();
            $table->text('catatan_koordinator')->nullable();

            // Status Approval Ranger
            $table->enum('status_ranger', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->foreignId('reviewed_by_ranger')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at_ranger')->nullable();
            $table->text('catatan_ranger')->nullable();

            // Status Final ('Pending', 'Diproses', 'Approved', 'Rejected')
            $table->enum('status', ['Pending', 'Diproses', 'Approved', 'Rejected'])->default('Pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
