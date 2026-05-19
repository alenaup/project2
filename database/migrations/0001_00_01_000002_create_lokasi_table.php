<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Status;

return new class extends Migration
{
    /* Tabel vendor atau perusahaan outsourcing */
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->bigInteger('id_lokasi')->autoIncrement();
            $table->decimal('longtitude', 10, 8);
            $table->decimal('latitude', 10, 8);
            $table->string('alamat', 255);
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
