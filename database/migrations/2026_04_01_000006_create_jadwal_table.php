<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Status;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->unsignedBigInteger('id_jadwal')->autoIncrement();
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->time('toleransi_telat')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->string('nama_periode', 255);
            $table->timestamps();

            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id', 'jadwa_diatur_shift')
                ->references('id_shift')->on('shift')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('dibuat_oleh');
            $table->foreign('dibuat_oleh', 'jadwal dibuat oleh kepala departemen')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
