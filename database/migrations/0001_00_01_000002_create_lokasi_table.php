<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Status;

return new class extends Migration
{
     /* Tabel lokasi untuk menyimpan data lokasi dengan koordinat GPS */
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lokasi')->autoIncrement();
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 10, 8);
            $table->string('nama_lokasi', 255);
            $table->integer('radius');
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi');
    }
};
