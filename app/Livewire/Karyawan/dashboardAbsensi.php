<?php

namespace App\Livewire\Karyawan;

use App\Services\JadwalService;
use App\Services\KehadiranService;
use App\Services\RekapService;
use Livewire\Component;

class dashboardAbsensi extends Component
{
    // ── Input dari frontend ────────────────────────────────────────────────
    public string $jenisAbsensi = 'masuk';
    public ?string $waktu = null;
    public ?string $latitude = null;
    public ?string $longitude = null;
    public ?float $jarak = null;

    // ── Data jadwal aktif (diisi saat mount) ──────────────────────────────
    public ?int $jadwalId = null;
    public ?string $jamMasuk = null;   // HH:MM:SS dari shift
    public ?string $jamKeluar = null;   // HH:MM:SS dari shift
    public ?string $toleransiTelat = null;   // HH:MM:SS
    public bool $sudahAbsenMasuk = false;
    public bool $sudahAbsenKeluar = false;
    public bool $adaJadwal = false;
    public ?string $pesanJadwal = null;
    public ?int $rekapId = null;

    // ──────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        // Cari jadwal yang aktif hari ini untuk karyawan ini
        $jadwal = (new JadwalService)->kirimWaktuJadwal();

        if (! $jadwal) {
            $this->adaJadwal = false;
            $this->pesanJadwal = 'Tidak ada jadwal kerja hari ini.';

            return;
        }

        $this->adaJadwal = true;
        $this->jadwalId = $jadwal->id_jadwal;
        $this->toleransiTelat = $jadwal->toleransi_telat;

        if ($jadwal->shift) {
            $this->jamMasuk = $jadwal->shift->jam_masuk;
            $this->jamKeluar = $jadwal->shift->jam_keluar;
        }

        // Cek apakah sudah absen masuk hari ini
        $kehadiranHariIni = (new KehadiranService)->cekKehadiran();

        if ($kehadiranHariIni) {
            $this->sudahAbsenMasuk = ! is_null($kehadiranHariIni->waktu_masuk);
            $this->sudahAbsenKeluar = ! is_null($kehadiranHariIni->waktu_keluar);
            // Default ke keluar jika sudah masuk tapi belum keluar
            if ($this->sudahAbsenMasuk && ! $this->sudahAbsenKeluar) {
                $this->jenisAbsensi = 'keluar';
            }
        }

        // mengecek statuus kehadiran pada rekap kehadiran
        $rekap = (new RekapService)->ambilRekapDetail();

        if ($rekap) {
            $this->rekapId = $rekap->id_rekapan;
        }
    }

    // ──────────────────────────────────────────────────────────────────────

    public function simpanAbsensi(KehadiranService $service)
    {
        $data = [
            'waktu' => $this->waktu,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'jadwalId' => $this->jadwalId,
            'jamMasuk' => $this->jamMasuk,
            'toleransi' => $this->toleransiTelat,
            'rekapId' => $this->rekapId,
        ];

        if ($this->jenisAbsensi === 'masuk') {
            $hasil = $service->absenMasuk($data);
        } else {
            $hasil = $service->absenKeluar($data);
        }

        if (! $hasil['success']) {
            $this->dispatch(
                'flash-error',
                message: $hasil['message']
            );
            return;
        }

        session()->flash(
            'success',
            $hasil['message']
        );

        $this->redirect(route('dashboard'));
    }

    // ──────────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.Karyawan.dashboardAbsensi', [
            'jamMasuk' => $this->jamMasuk,
            'jamKeluar' => $this->jamKeluar,
        ]);
    }
}
