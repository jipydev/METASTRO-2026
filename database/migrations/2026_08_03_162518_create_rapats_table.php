<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rapats', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); // Contoh: RABES 1
            $table->date('tanggal');
            $table->time('jam');
            $table->string('tempat');
            $table->integer('hadir')->default(0); // Untuk data presensi
            $table->integer('total')->default(120); // Total peserta
            $table->timestamps();
        });
    }
};
