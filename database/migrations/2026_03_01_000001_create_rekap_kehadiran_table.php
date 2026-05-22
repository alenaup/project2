<?php

use App\enums\Status;
use App\Enums\Validasi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /* Table Rekap Kehadiran Karyawan */
    public function up(): void
    {
        Schema::create('rekap_kehadiran', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rekapan')->autoIncrement();
            $table->integer('total_mankir')->default(0);
            $table->integer('total_cuti')->default(0);
            $table->integer('total_lembur')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_sakit')->default(0);
            $table->integer('total_hadir')->default(0);
            $table->integer('total_terlambat')->default(0);

            $table->integer('total_jam_kerja')->default(0);
            $table->date('tanggal_validasi')->nullable();
            $table->enum('status_validasi', [Validasi::Valid->value, Validasi::Invalid->value, Validasi::Pending->value])->nullable();
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);

            $table->unsignedBigInteger('pengaju');
            $table->foreign('pengaju', 'admin pengaju rekapan')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('pevalidasi')->nullable();
            $table->foreign('pevalidasi', 'hr menyetujui pengajuan rekapan')
                ->references('id_user')->on('user')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_kehadiran');
    }
};
