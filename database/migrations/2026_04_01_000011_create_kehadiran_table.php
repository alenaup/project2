<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kehadiran')->autoIncrement();
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamp('waktu_keluar')->nullable();
            $table->date('tanggal');
            $table->string('latitude_masuk', 255)->nullable();
            $table->string('longitude_masuk', 255)->nullable();
            $table->string('latitude_keluar', 255)->nullable();
            $table->string('longitude_keluar', 255)->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('jadwal_id');
            $table->foreign('jadwal_id', 'jadwal_mencatat_kehadiran')
                ->references('id_jadwal')->on('jadwal')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('tipe_kehadiran_id');
            $table->foreign('tipe_kehadiran_id', 'tipe_dari_kehadiran')
                ->references('id_tipe_kehadiran')->on('tipe_kehadiran')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('rekapan_kehadiran_id');
            $table->foreign('rekapan_kehadiran_id', 'rekapan_mencatat_kehadiran')
                ->references('id_rekapan')->on('rekap_kehadiran')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('karyawan_id');
            $table->foreign('karyawan_id', 'karyawan_mencatat_kehadiran')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};




