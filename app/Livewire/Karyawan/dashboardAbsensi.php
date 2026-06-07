<?php

namespace App\Livewire\Karyawan;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Kehadiran;
use App\Models\RekapKehadiran;
use App\Models\TipeKehadiran;

class dashboardAbsensi extends Component
{
    // ── Input dari frontend ────────────────────────────────────────────────
    public string $jenisAbsensi = 'masuk';
    public ?string $waktu       = null;
    public ?string $latitude    = null;
    public ?string $longitude   = null;
    public ?float  $jarak       = null;

    // ── Data jadwal aktif (diisi saat mount) ──────────────────────────────
    public ?int    $jadwalId           = null;
    public ?string $jamMasuk           = null;   // HH:MM:SS dari shift
    public ?string $jamKeluar          = null;   // HH:MM:SS dari shift
    public ?string $toleransiTelat     = null;   // HH:MM:SS
    public bool    $sudahAbsenMasuk    = false;
    public bool    $sudahAbsenKeluar   = false;
    public bool    $adaJadwal          = false;
    public ?string $pesanJadwal        = null;

    // ──────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $user  = Auth::user();
        $today = now()->toDateString();

        // Cari jadwal yang aktif hari ini untuk karyawan ini
        $jadwal = $user->jadwal()
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_akhir', '>=', $today)
            ->with('shift')
            ->first();

        if (! $jadwal) {
            $this->adaJadwal   = false;
            $this->pesanJadwal = 'Tidak ada jadwal kerja hari ini.';
            return;
        }

        $this->adaJadwal   = true;
        $this->jadwalId    = $jadwal->id_jadwal;
        $this->toleransiTelat = $jadwal->toleransi_telat;

        if ($jadwal->shift) {
            $this->jamMasuk  = $jadwal->shift->jam_masuk;
            $this->jamKeluar = $jadwal->shift->jam_keluar;
        }

        // Cek apakah sudah absen masuk hari ini
        $kehadiranHariIni = $user->kehadiran()
            ->where('jadwal_id', $jadwal->id_jadwal)
            ->whereDate('tanggal', $today)
            ->first();

        if ($kehadiranHariIni) {
            $this->sudahAbsenMasuk  = ! is_null($kehadiranHariIni->waktu_masuk);
            $this->sudahAbsenKeluar = ! is_null($kehadiranHariIni->waktu_keluar);
            // Default ke keluar jika sudah masuk tapi belum keluar
            if ($this->sudahAbsenMasuk && ! $this->sudahAbsenKeluar) {
                $this->jenisAbsensi = 'keluar';
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────

    public function simpanAbsensi(): void
    {
        $user  = Auth::user();
        $today = now()->toDateString();

        // 1. Validasi jadwal tersedia
        if (! $this->adaJadwal || ! $this->jadwalId) {
            session()->flash('error', 'Tidak ada jadwal kerja hari ini. Absensi tidak dapat dilakukan.');
            return;
        }

        // 2. Validasi lokasi sudah diambil
        if (is_null($this->latitude) || is_null($this->longitude)) {
            session()->flash('error', 'Anda belum mengambil lokasi GPS.');
            return;
        }

        // 3. Validasi waktu sudah diisi
        if (is_null($this->waktu)) {
            session()->flash('error', 'Waktu absensi belum tersedia.');
            return;
        }

        // 4. Ambil rekap kehadiran yang aktif (milik admin/pengaju yang sama outsourcing-nya)
        $rekap = RekapKehadiran::where('status', 'active')->first()
               ?? RekapKehadiran::first();
        if (! $rekap) {
            session()->flash('error', 'Data rekap kehadiran belum tersedia. Hubungi admin.');
            return;
        }

        // 5. Cari / buat record kehadiran hari ini
        $kehadiran = $user->kehadiran()
            ->where('jadwal_id', $this->jadwalId)
            ->whereDate('tanggal', $today)
            ->first();

        // ── ABSEN MASUK ───────────────────────────────────────────────────
        if ($this->jenisAbsensi === 'masuk') {
            if ($kehadiran && ! is_null($kehadiran->waktu_masuk)) {
                session()->flash('error', 'Anda sudah melakukan absen masuk hari ini.');
                return;
            }

            // Tentukan tipe kehadiran: terlambat atau hadir
            $tipeId = $this->tentukanTipeKehadiran($this->waktu);

            if ($kehadiran) {
                // Update jika record sudah ada tapi waktu_masuk masih null
                $kehadiran->update([
                    'waktu_masuk'      => $this->waktu,
                    'latitude_masuk'   => $this->latitude,
                    'longitude_masuk'  => $this->longitude,
                    'tipe_kehadiran_id' => $tipeId,
                ]);
            } else {
                Kehadiran::create([
                    'tanggal'              => $today,
                    'waktu_masuk'          => $this->waktu,
                    'waktu_keluar'         => null,
                    'latitude_masuk'       => $this->latitude,
                    'longitude_masuk'      => $this->longitude,
                    'latitude_keluar'      => null,
                    'longitude_keluar'     => null,
                    'jadwal_id'            => $this->jadwalId,
                    'tipe_kehadiran_id'    => $tipeId,
                    'rekapan_kehadiran_id' => $rekap->id_rekapan,
                    'karyawan_id'          => $user->id_user,
                ]);
            }

            session()->flash('success', 'Absen masuk berhasil disimpan.');

        // ── ABSEN KELUAR ──────────────────────────────────────────────────
        } elseif ($this->jenisAbsensi === 'keluar') {
            if (! $kehadiran || is_null($kehadiran->waktu_masuk)) {
                session()->flash('error', 'Anda belum melakukan absen masuk hari ini.');
                return;
            }

            if (! is_null($kehadiran->waktu_keluar)) {
                session()->flash('error', 'Anda sudah melakukan absen keluar hari ini.');
                return;
            }

            $kehadiran->update([
                'waktu_keluar'     => $this->waktu,
                'latitude_keluar'  => $this->latitude,
                'longitude_keluar' => $this->longitude,
            ]);

            session()->flash('success', 'Absen keluar berhasil disimpan.');

        } else {
            session()->flash('error', 'Jenis absensi tidak valid.');
            return;
        }

        $this->redirect(route('dashboard'));
    }

    // ──────────────────────────────────────────────────────────────────────

    /**
     * Tentukan tipe kehadiran berdasarkan waktu masuk vs jam masuk shift.
     * Mengembalikan id_tipe_kehadiran:
     *   1 = hadir, 6 = terlambat
     */
    private function tentukanTipeKehadiran(string $waktuMasuk): int
    {
        if (! $this->jamMasuk) {
            return 1; // default hadir jika tidak ada shift
        }

        $today         = now()->toDateString();
        $batasTolerasi = Carbon::parse($today . ' ' . $this->jamMasuk);

        // Tambahkan toleransi jika ada
        if ($this->toleransiTelat) {
            [$h, $m, $s] = explode(':', $this->toleransiTelat);
            $batasTolerasi->addHours((int)$h)->addMinutes((int)$m)->addSeconds((int)$s);
        }

        $waktuAbsen = Carbon::parse($waktuMasuk);

        return $waktuAbsen->greaterThan($batasTolerasi) ? 6 : 1;
    }

    // ──────────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.Karyawan.dashboardAbsensi', [
            'jamMasuk'  => $this->jamMasuk,
            'jamKeluar' => $this->jamKeluar,
        ]);
    }
}
