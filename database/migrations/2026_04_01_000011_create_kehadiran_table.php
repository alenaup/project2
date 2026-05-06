<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->bigInteger('id_kehadiran')->autoIncrement();
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamp('waktu_keluar')->nullable();
            $table->time('waktu_telat')->nullable();
            $table->date('tanggal');
            $table->string('lokasi_masuk', 255);
            $table->string('lokasi_keluar', 255)->nullable();
            $table->enum('status_rekapan', ['valid', 'tidak valid'])->default('valid');

            /* bukti menjelaskan apakah kehadiranya diterima pada rekapan */
            $table->string('bukti', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->bigInteger('jadwal_id');
            $table->foreign('jadwal_id', 'jadwal_mencatatt_kehadiran')
                ->references('id_jadwal')->on('jadwal')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('tipe_kehadiran_id');
            $table->foreign('tipe_kehadiran_id', 'tipe_dari_kehadiran')
                ->references('id_tipe_kehadiran')->on('tipe_kehadiran')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('rekapan_kehadiran_id');
            $table->foreign('rekapan_kehadiran_id', 'rekapan_mencatat_kehadiran')
                ->references('id_rekapan')->on('rekap_kehadiran')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};




