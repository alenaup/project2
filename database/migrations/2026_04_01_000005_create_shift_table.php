<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->unsignedBigInteger('id_shift')->autoIncrement();
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->string('nama_shift', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift');
    }
};
