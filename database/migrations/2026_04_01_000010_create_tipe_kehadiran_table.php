<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Enums\TipeKehadiran;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipe_kehadiran', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tipe_kehadiran')->autoIncrement();
            $table->enum('status_kehadiran', [
                TipeKehadiran::Hadir->value, 
                TipeKehadiran::Sakit->value, 
                TipeKehadiran::Izin->value, 
                TipeKehadiran::Mankir->value, 
                TipeKehadiran::Cuti->value, 
                TipeKehadiran::Terlambat->value]);

            $table->string('bukti',255)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipe_kehadiran');
    }
};
