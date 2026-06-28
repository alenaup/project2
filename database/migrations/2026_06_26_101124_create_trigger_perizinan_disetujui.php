<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus trigger jika sudah ada
        DB::unprepared('DROP TRIGGER IF EXISTS trg_after_perizinan_disetujui');

        // Buat trigger baru
        DB::unprepared("
            CREATE TRIGGER trg_after_perizinan_disetujui
            AFTER UPDATE ON perizinan_sakit
            FOR EACH ROW
            BEGIN
                DECLARE v_jadwal_id BIGINT;
                DECLARE v_rekap_id BIGINT;

                -- Jika status berubah menjadi 'disetujui'
                IF NEW.status = 'disetujui' AND OLD.status != 'disetujui' THEN
                    
                    -- Cari jadwal_id milik karyawan ini (ambil 1 data terbaru/default)
                    SELECT jadwal_id INTO v_jadwal_id 
                    FROM karyawan_jadwal 
                    WHERE user_id = NEW.karyawan_id 
                    LIMIT 1;

                    -- Cari rekapan_kehadiran_id aktif (ambil 1 data terbaru sebagai referensi)
                    SELECT id_rekapan INTO v_rekap_id 
                    FROM rekap_kehadiran 
                    LIMIT 1;

                    -- Pastikan jadwal_id & rekap_id ditemukan agar tidak error foreign key
                    IF v_jadwal_id IS NOT NULL AND v_rekap_id IS NOT NULL THEN
                        INSERT INTO kehadiran (
                            karyawan_id,
                            tanggal,
                            tipe_kehadiran_id, -- Asumsi: 2 adalah ID untuk tipe Sakit/Izin
                            waktu_masuk,
                            waktu_keluar,
                            jadwal_id,
                            rekapan_kehadiran_id,
                            created_at,
                            updated_at
                        ) VALUES (
                            NEW.karyawan_id,
                            NEW.tanggal_mulai,
                            2, 
                            NULL,
                            NULL,
                            v_jadwal_id,
                            v_rekap_id,
                            NOW(),
                            NOW()
                        );
                    END IF;
                    
                END IF;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trigger_perizinan_disetujui');
    }
};
