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
            $table->bigInteger('id_jadwal')->autoIncrement();
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->date('tanggal');
            $table->bigInteger('dibuat_oleh');
            $table->timestamps();

            $table->bigInteger('shift_id');
            $table->foreign('shift_id', 'jadwa_diatur_shift')
                ->references('id_shift')->on('shift')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
