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
        Schema::create('vendor', function (Blueprint $table) {
            $table->bigInteger('id_vendor')->autoIncrement();
            $table->string('nama_vendor', 255);
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->string('nomor_tlp', 13);
            $table->string('email', 255);
            $table->string('alamat', 255);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};
