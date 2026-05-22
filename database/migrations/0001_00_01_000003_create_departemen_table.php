<?php
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /* Tabel Departement */
    /* Table departement berelasi dengan user role karyawan dan user role kepala departemt */
    /* pada Tabel Departement terhubung relasi dengan tabel lokasi */
    public function up(): void
    {
        Schema::create('departemen', function (Blueprint $table) {
            $table->unsignedBigInteger('id_departemen')->autoIncrement();
            $table->string('nama_departemen', 255);
            $table->enum('status', [Status::Active->value, Status::Inactive->value])->default(Status::Active->value);
            $table->timestamps();

            $table->unsignedBigInteger('lokasi_id')->nullable();
            $table->foreign('lokasi_id', 'lokasi_absensi_departemen')
                ->references('id_lokasi')->on('lokasi')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('departemen');
    }
};
