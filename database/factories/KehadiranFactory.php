<?php

namespace Database\Factories;

use App\Models\Kehadiran;
use App\Models\Lokasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk data kehadiran dengan koordinat GPS dummy
 * yang dihasilkan di dalam radius lokasi yang terdaftar.
 */
class KehadiranFactory extends Factory
{
    protected $model = Kehadiran::class;

    /**
     * Default state: kehadiran tipe "hadir" dengan koordinat di dalam radius lokasi.
     */
    public function definition(): array
    {
        $lokasi = Lokasi::inRandomOrder()->first();
        $date   = $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d');

        $koordinatMasuk  = $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius);
        $koordinatKeluar = $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius);

        return [
            'tanggal'          => $date,
            'waktu_masuk'      => $date . ' 07:00:00',
            'waktu_keluar'     => $date . ' 15:00:00',
            'latitude_masuk'   => $koordinatMasuk['latitude'],
            'longitude_masuk'  => $koordinatMasuk['longitude'],
            'latitude_keluar'  => $koordinatKeluar['latitude'],
            'longitude_keluar' => $koordinatKeluar['longitude'],
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // State Methods (satu per tipe kehadiran)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Tipe 1: Hadir — masuk & keluar tepat waktu, koordinat di dalam radius.
     */
    public function hadir(string $date, Lokasi $lokasi): static
    {
        $koordinatMasuk  = $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius);
        $koordinatKeluar = $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius);

        return $this->state(fn(array $attributes) => [
            'tanggal'          => $date,
            'waktu_masuk'      => $date . ' 07:00:00',
            'waktu_keluar'     => $date . ' 15:00:00',
            'latitude_masuk'   => $koordinatMasuk['latitude'],
            'longitude_masuk'  => $koordinatMasuk['longitude'],
            'latitude_keluar'  => $koordinatKeluar['latitude'],
            'longitude_keluar' => $koordinatKeluar['longitude'],
        ]);
    }

    /**
     * Tipe 6: Terlambat — waktu masuk lewat toleransi, koordinat tetap di dalam radius.
     */
    public function terlambat(string $date, Lokasi $lokasi): static
    {
        $menitTelat = $this->faker->numberBetween(16, 60); // lewat batas toleransi 15 menit
        $jamMasuk   = date('H:i:s', strtotime("07:00:00 +{$menitTelat} minutes"));

        $koordinatMasuk  = $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius);
        $koordinatKeluar = $this->koordinatDalamRadius($lokasi->latitude, $lokasi->longitude, $lokasi->radius);

        return $this->state(fn(array $attributes) => [
            'tanggal'          => $date,
            'waktu_masuk'      => $date . ' ' . $jamMasuk,
            'waktu_keluar'     => $date . ' 15:00:00',
            'latitude_masuk'   => $koordinatMasuk['latitude'],
            'longitude_masuk'  => $koordinatMasuk['longitude'],
            'latitude_keluar'  => $koordinatKeluar['latitude'],
            'longitude_keluar' => $koordinatKeluar['longitude'],
        ]);
    }

    /**
     * Tipe 2: Sakit — tidak hadir, tidak ada koordinat maupun waktu.
     */
    public function sakit(string $date): static
    {
        return $this->state(fn(array $attributes) => [
            'tanggal'          => $date,
            'waktu_masuk'      => null,
            'waktu_keluar'     => null,
            'latitude_masuk'   => null,
            'longitude_masuk'  => null,
            'latitude_keluar'  => null,
            'longitude_keluar' => null,
        ]);
    }

    /**
     * Tipe 3: Mankir — absen tanpa keterangan, tidak ada data lokasi/waktu.
     */
    public function mankir(string $date): static
    {
        return $this->state(fn(array $attributes) => [
            'tanggal'          => $date,
            'waktu_masuk'      => null,
            'waktu_keluar'     => null,
            'latitude_masuk'   => null,
            'longitude_masuk'  => null,
            'latitude_keluar'  => null,
            'longitude_keluar' => null,
        ]);
    }

    /**
     * Tipe 4: Cuti — sudah disetujui, tidak ada data lokasi/waktu.
     */
    public function cuti(string $date): static
    {
        return $this->state(fn(array $attributes) => [
            'tanggal'          => $date,
            'waktu_masuk'      => null,
            'waktu_keluar'     => null,
            'latitude_masuk'   => null,
            'longitude_masuk'  => null,
            'latitude_keluar'  => null,
            'longitude_keluar' => null,
        ]);
    }

    /**
     * Tipe 5: Izin — ada keterangan resmi, tidak ada data lokasi/waktu.
     */
    public function izin(string $date): static
    {
        return $this->state(fn(array $attributes) => [
            'tanggal'          => $date,
            'waktu_masuk'      => null,
            'waktu_keluar'     => null,
            'latitude_masuk'   => null,
            'longitude_masuk'  => null,
            'latitude_keluar'  => null,
            'longitude_keluar' => null,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helper: Hasilkan koordinat acak di dalam radius (meter) dari titik pusat
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Menghasilkan titik GPS acak yang berada di dalam lingkaran
     * berpusat pada ($centerLat, $centerLng) dengan jari-jari $radiusMeters.
     *
     * Rumus:
     *  - 1 derajat lintang  ≈ 111.320 meter
     *  - 1 derajat bujur    ≈ 111.320 × cos(lintang) meter
     *  - Menggunakan √U untuk distribusi yang seragam dalam lingkaran.
     *
     * @param float $centerLat     Lintang pusat (derajat)
     * @param float $centerLng     Bujur pusat (derajat)
     * @param int   $radiusMeters  Jari-jari dalam meter
     * @return array{latitude: float, longitude: float}
     */
    private function koordinatDalamRadius(float $centerLat, float $centerLng, int $radiusMeters): array
    {
        // Konversi radius meter → derajat lintang
        $radiusDalam1Derajat = $radiusMeters / 111320;

        // Sudut acak (0 – 2π) dan jarak acak dengan distribusi seragam dalam lingkaran
        $sudut    = $this->faker->randomFloat(8, 0, 2 * M_PI);
        $jarak    = sqrt($this->faker->randomFloat(8, 0, 1)) * $radiusDalam1Derajat;

        // Offset lintang & bujur
        $deltaLat = $jarak * cos($sudut);
        $deltaLng = $jarak * sin($sudut) / cos(deg2rad($centerLat));

        return [
            'latitude'  => round($centerLat + $deltaLat, 8),
            'longitude' => round($centerLng + $deltaLng, 8),
        ];
    }
}
