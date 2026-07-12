<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Trigger dinonaktifkan — logika perizinan sakit ditangani di aplikasi layer.
     */
    public function up(): void
    {
        // Trigger dihapus dari database, logika dipindah ke aplikasi
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_perizinan_disetujui');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
