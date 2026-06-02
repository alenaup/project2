<?php

use App\Enums\Status;
use App\Enums\Validasi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembur', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lembur')->autoIncrement();
            $table->timestamp('mulai_lembur')->nullable();
            $table->timestamp('selesai_lembur')->nullable();
            $table->timestamp('tanggal_divalidasi')->nullable();
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->enum('status_validasi', [Validasi::Valid->value, Validasi::Invalid->value, Validasi::Pending->value])->nullable();
            $table->string('keterangan', 255);
            $table->timestamps();

            $table->unsignedBigInteger('karyawan_id');
            $table->foreign('karyawan_id', 'fk_karyawan')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('pemvalidasi_id')->nullable();
            $table->foreign('pemvalidasi_id', 'fk_kepala_departemen')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembur');
    }
};
