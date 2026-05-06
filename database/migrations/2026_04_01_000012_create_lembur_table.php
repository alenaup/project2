<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembur', function (Blueprint $table) {
            $table->bigInteger('id_lembur')->autoIncrement();
            $table->timestamp('mulai_lembur')->nullable();
            $table->timestamp('selesai_lembur')->nullable();
            $table->timestamp('tanggal_dibuat')->nullable();
            $table->enum('status', ['Lembur', 'Tidak_lembur']);
            $table->string('pemvalidasi', 255);
            $table->string('keterangan', 255);
            $table->timestamps();

            $table->bigInteger('karyawan_id');
            $table->foreign('karyawan_id', 'fk_karyawan')
                ->references('id_user')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembur');
    }
};
