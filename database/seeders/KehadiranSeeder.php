<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Kehadiran;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Database\Seeder;

class KehadiranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Menghasilkan data kehadiran dummy untuk setiap karyawan selama
     * beberapa tanggal. Koordinat GPS (lat/lng masuk & keluar) di-generate
     * secara acak namun tetap berada di dalam radius lokasi yang terdaftar.
     *
     * Tipe kehadiran:
     *   1 = hadir     → ada waktu + koordinat dalam radius
     *   2 = sakit     → tidak hadir, tanpa koordinat
     *   3 = mankir    → tidak hadir, tanpa koordinat
     *   4 = cuti      → tidak hadir, tanpa koordinat
     *   5 = izin      → tidak hadir, tanpa koordinat
     *   6 = terlambat → ada waktu (lewat toleransi) + koordinat dalam radius
     */
    public function run(): void
    {
        // Ambil semua lokasi yang tersedia beserta radius-nya
        $lokasiList = Lokasi::all();

        if ($lokasiList->isEmpty()) {
            $this->command->warn('⚠  Tidak ada data Lokasi. Jalankan LokasiSeeder terlebih dahulu.');
            return;
        }

        // Ambil semua karyawan, lewati 5 pertama (biarkan tanpa kehadiran)
        $karyawans = User::query()
            ->where('role', UserRole::Karyawan->value)
            ->get()
            ->skip(5);

        if ($karyawans->isEmpty()) {
            $this->command->warn('⚠  Tidak ada karyawan yang tersedia.');
            return;
        }

        // Tanggal-tanggal yang akan di-seed (bisa ditambah sesuai kebutuhan)
        $dates = [
            '2026-04-10',
            '2026-04-11',
            '2026-04-14',
            '2026-04-15',
            '2026-04-16',
            '2026-05-05',
            '2026-05-06',
            '2026-05-07',
            '2026-05-08',
            '2026-05-09',
            '2026-06-02',
            '2026-06-03',
            '2026-06-04',
        ];

        // Urutan tipe kehadiran yang akan bergilir per karyawan+tanggal
        $tipeKehadiranList = [1, 1, 1, 6, 2, 1, 1, 1, 5, 1, 1, 3, 4];

        foreach ($karyawans as $karyawanIndex => $karyawan) {
            // Ambil jadwal yang di-assign ke karyawan ini
            $jadwalIds = $karyawan->jadwal()->pluck('id_jadwal')->toArray();
            if (empty($jadwalIds)) {
                $jadwalIds = [1]; // fallback ke jadwal pertama jika belum ada
            }

            // Pilih lokasi secara round-robin berdasarkan index karyawan
            $lokasi = $lokasiList[$karyawanIndex % $lokasiList->count()];

            foreach ($dates as $dateIndex => $date) {
                // Tentukan tipe kehadiran secara bergilir agar variatif
                $tipeKehadiranId = $tipeKehadiranList[($karyawanIndex + $dateIndex) % count($tipeKehadiranList)];

                // Pilih jadwal_id secara round-robin
                $jadwalId = $jadwalIds[($karyawanIndex + $dateIndex) % count($jadwalIds)];

                // Pilih rekapan_kehadiran_id (1–4, sesuai jumlah rekap yang di-seed)
                $rekapanKehadiranId = ($karyawanIndex % 4) + 1;

                // Buat kehadiran menggunakan factory dengan state sesuai tipe
                $stateData = $this->resolveState($tipeKehadiranId, $date, $lokasi);

                Kehadiran::factory()
                    ->state($stateData)
                    ->create([
                        'jadwal_id'           => $jadwalId,
                        'tipe_kehadiran_id'   => $tipeKehadiranId,
                        'rekapan_kehadiran_id' => $rekapanKehadiranId,
                        'karyawan_id'         => $karyawan->id_user,
                    ]);
            }
        }

        $this->command->info('✅ KehadiranSeeder selesai — koordinat GPS dalam radius lokasi.');
    }

    /**
     * Kembalikan array state (override attributes factory)
     * sesuai tipe kehadiran & tanggal yang diberikan.
     *
     * @param  int    $tipeKehadiranId  ID tipe kehadiran (1–6)
     * @param  string $date             Tanggal format Y-m-d
     * @param  \App\Models\Lokasi $lokasi  Lokasi pusat absensi
     * @return array<string, mixed>
     */
    private function resolveState(int $tipeKehadiranId, string $date, Lokasi $lokasi): array
    {
        return match ($tipeKehadiranId) {
            // ── Hadir: masuk tepat waktu, keluar setelah jam kerja ───────────
            1 => [
                'tanggal'          => $date,
                'waktu_masuk'      => $date . ' 07:00:00',
                'waktu_keluar'     => $date . ' 15:00:00',
                'latitude_masuk'   => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['latitude'],
                'longitude_masuk'  => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['longitude'],
                'latitude_keluar'  => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['latitude'],
                'longitude_keluar' => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['longitude'],
            ],

            // ── Sakit: tidak hadir, tanpa koordinat ───────────────────────────
            2 => [
                'tanggal'          => $date,
                'waktu_masuk'      => null,
                'waktu_keluar'     => null,
                'latitude_masuk'   => null,
                'longitude_masuk'  => null,
                'latitude_keluar'  => null,
                'longitude_keluar' => null,
            ],

            // ── Mankir: absen tanpa keterangan, tanpa koordinat ───────────────
            3 => [
                'tanggal'          => $date,
                'waktu_masuk'      => null,
                'waktu_keluar'     => null,
                'latitude_masuk'   => null,
                'longitude_masuk'  => null,
                'latitude_keluar'  => null,
                'longitude_keluar' => null,
            ],

            // ── Cuti: disetujui, tanpa koordinat ─────────────────────────────
            4 => [
                'tanggal'          => $date,
                'waktu_masuk'      => null,
                'waktu_keluar'     => null,
                'latitude_masuk'   => null,
                'longitude_masuk'  => null,
                'latitude_keluar'  => null,
                'longitude_keluar' => null,
            ],

            // ── Izin: ada keterangan, tanpa koordinat ────────────────────────
            5 => [
                'tanggal'          => $date,
                'waktu_masuk'      => null,
                'waktu_keluar'     => null,
                'latitude_masuk'   => null,
                'longitude_masuk'  => null,
                'latitude_keluar'  => null,
                'longitude_keluar' => null,
            ],

            // ── Terlambat: waktu masuk lewat toleransi, koordinat dalam radius
            6 => [
                'tanggal'          => $date,
                'waktu_masuk'      => $date . ' ' . $this->jamTerlambat(),
                'waktu_keluar'     => $date . ' 15:00:00',
                'latitude_masuk'   => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['latitude'],
                'longitude_masuk'  => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['longitude'],
                'latitude_keluar'  => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['latitude'],
                'longitude_keluar' => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['longitude'],
            ],

            // ── Default: sama seperti hadir ──────────────────────────────────
            default => [
                'tanggal'          => $date,
                'waktu_masuk'      => $date . ' 07:00:00',
                'waktu_keluar'     => $date . ' 15:00:00',
                'latitude_masuk'   => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['latitude'],
                'longitude_masuk'  => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['longitude'],
                'latitude_keluar'  => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['latitude'],
                'longitude_keluar' => $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius)['longitude'],
            ],
        };
    }

    /**
     * Hasilkan waktu masuk yang terlambat (16–60 menit setelah jam masuk).
     */
    private function jamTerlambat(): string
    {
        $menitTelat = rand(16, 60);
        return date('H:i:s', strtotime("07:00:00 +{$menitTelat} minutes"));
    }

    /**
     * Hasilkan koordinat GPS acak di dalam radius (meter) dari titik pusat.
     * Menggunakan rumus distribusi seragam dalam lingkaran (√U trick).
     *
     * @return array{latitude: float, longitude: float}
     */
    private function koordinatDalamRadius(float $centerLat, float $centerLng, int $radiusMeters): array
    {
        $radiusDalamDerajat = $radiusMeters / 111320;

        $sudut  = lcg_value() * 2 * M_PI;
        $jarak  = sqrt(lcg_value()) * $radiusDalamDerajat;

        $deltaLat = $jarak * cos($sudut);
        $deltaLng = $jarak * sin($sudut) / cos(deg2rad($centerLat));

        return [
            'latitude'  => round($centerLat + $deltaLat, 8),
            'longitude' => round($centerLng + $deltaLng, 8),
        ];
    }
}
