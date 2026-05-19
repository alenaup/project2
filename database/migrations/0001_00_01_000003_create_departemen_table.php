<?php
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
            $table->bigInteger('id_departemen')->autoIncrement();
            $table->string('nama_departemen', 255);
            $table->timestamps();

            $table->bigInteger('lokasi_id');
            $table->foreign('lokasi_id', 'departemen_dibuat_di_lokasi')
                ->references('id_lokasi')->on('lokasi')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departemen');
    }
};
