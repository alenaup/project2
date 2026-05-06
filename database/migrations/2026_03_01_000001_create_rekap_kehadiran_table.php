<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\enums\Status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /* Table Rekap Kehadiran Karyawan */
    public function up(): void
    {
        Schema::create('rekap_kehadiran', function (Blueprint $table) {
            $table->bigInteger('id_rekapan')->autoIncrement();
            $table->integer('total_lembur')->default(0);
            $table->integer('total_jam_kerja')->default(0);
            $table->integer('total_terlambat')->default(0);
            $table->date('tanggal_validasi')->nullable();
            $table->enum('status_validasi', ['Valid', 'Tidak_Valid']);
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->bigInteger('pemvalidasi_id');
            $table->date('tanggal')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_kehadiran');
    }
};
