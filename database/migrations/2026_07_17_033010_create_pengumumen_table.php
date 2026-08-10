<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {

            $table->id();

            $table->string('judul');

            $table->longText('isi');

            $table->string('lampiran')->nullable();

            $table->dateTime('tanggal_publish');

            $table->enum('status', [
                'Draft',
                'Publish',
            ])->default('Draft');

            $table->foreignId('pembuat_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
