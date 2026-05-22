<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan_jadwal', function (Blueprint $table) {
            $table->unsignedBigInteger('id_relasi')->autoIncrement();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'karyawan_memiliki_jadwal')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('jadwal_id');
            $table->foreign('jadwal_id', 'jadwal_memiliki_karyawan')
                ->references('id_jadwal')->on('jadwal')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan_jadwal');
    }
};
