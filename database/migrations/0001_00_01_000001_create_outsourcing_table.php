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
        Schema::create('outsourcing', function (Blueprint $table) {
            $table->unsignedBigInteger('id_outsourcing')->autoIncrement();
            $table->string('nama_outsourcing', 255);
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->string('nomor_tlp', 13);
            $table->string('email', 255)->unique();
            $table->string('alamat', 255);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('outsourcing');
    }
};
