<?php

namespace App\Livewire\AdminOutsourcing;

use App\Models\Kehadiran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Services\UserService;
use App\Services\KehadiranService;
use Livewire\Component;

// class component livewire
class RekapanKaryawan extends Component
{
    public string $startDate;
    public string $endDate;
    public string $rekapBulan;

    public array $karyawanByOutsourcing = [];
    public array $datas = [];
    public array $koloms = [];

    public int $perPage = 10;
    public int $halamanAktif = 1;
    public int $totalKaryawan = 0;

    public function mount()
    {
        $this->startDate = Carbon::now()->subMonth()->day(26)->format('Y-m-d');
        $this->endDate = Carbon::now()->day(25)->format('Y-m-d');
        $this->rekapBulan = Carbon::now()->format('Y-m');
        $this->loadData();
    }

    public function updatedStartDate()
    {
        $this->halamanAktif = 1;
        $this->loadData();
    }

    public function updatedEndDate()
    {
        $this->halamanAktif = 1;
        $this->loadData();
    }

    public function updatedRekapBulan()
    {
        $this->loadRekapRecord();
    }

    public function gantiHalaman(int $halaman): void
    {
        $this->halamanAktif = $halaman;
        $this->loadData();
    }

    // ──────────────────────────────────────────────────────
    // Computed Properties (data dari database)
    // ──────────────────────────────────────────────────────

    /**
     * Mengambil total karyawan hadir pada tanggal range.
     */
    public function getTotalHadirProperty(): int
    {
        $karyawanIds = $this->karyawanByOutsourcing;
        return (new KehadiranService)
            ->totalHadirByDateRange(
                $karyawanIds,
                $this->startDate,
                $this->endDate
            );
    }

    /**
     * Mengambil total kehadiran bertipe alpha / tidak hadir tanpa keterangan.
     */
    public function getTotalAlphaProperty(): int
    {
        $getKaryawan = $this->karyawanByOutsourcing;
        return (new KehadiranService)->cekKehadiranBanyakKaryawanByDateRange('mankir', $getKaryawan, $this->startDate, $this->endDate);
    }

    /**
     * Mengambil total kehadiran bertipe izin atau sakit.
     */
    public function getTotalIzinSakitProperty(): int
    {
        $getKaryawan = $this->karyawanByOutsourcing;
        $sakit = (new KehadiranService)->cekKehadiranBanyakKaryawanByDateRange('sakit', $getKaryawan, $this->startDate, $this->endDate);
        $izin = (new KehadiranService)->cekKehadiranBanyakKaryawanByDateRange('izin', $getKaryawan, $this->startDate, $this->endDate);
        return $sakit + $izin;
    }

    /**
     * Mengambil total karyawan aktif.
     */
    public function getTotalKaryawanProperty(): int
    {
        return User::query()->whereNull('tanggal_keluar')
            ->where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', Auth::user()->outsourcing_id)
            ->count();
    }

    // ──────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────

    private function getRekapDateRange()
    {
        $carbon = Carbon::createFromFormat('Y-m', $this->rekapBulan);
        $prevMonthStart = $carbon->copy()->subMonth();
        
        $start = $prevMonthStart->day(26)->format('Y-m-d');
        $end = $carbon->day(25)->format('Y-m-d');
        
        return [$start, $end];
    }

    /**
     * Render component ke view
     */
    protected function loadData(): void
    {
        $this->karyawanByOutsourcing = (new UserService)->getKaryawanByOutsourcing(Auth::user()->outsourcing_id, "array");

        // inisiasi array
        $this->datas = [];
        $this->koloms = [];

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->copy();
            $this->koloms[] = $date->format('d/m');
        }

        $query = User::where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', Auth::user()->outsourcing_id)
            ->where('status', \App\Enums\Status::Active->value)
            ->whereNull('tanggal_keluar');

        $this->totalKaryawan = $query->count();

        $karyawans = $query
            ->skip(($this->halamanAktif - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        $userIds = $karyawans->pluck('id_user');

        // Ambil seluruh kehadiran dalam range tanggal ini dalam 1 query
        $kehadirans = (new KehadiranService)->ambilBanyakKehadiranByDateRange($userIds, $this->startDate, $this->endDate);

        $kehadiranMap = $kehadirans
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });
            });

        foreach ($karyawans as $karyawan) {
            $absens = [];

            foreach ($dates as $date) {
                $tanggal = $date->format('Y-m-d');

                $kehadiran = $kehadiranMap[$karyawan->id_user][$tanggal] ?? null;

                if (!$kehadiran) {
                    $absens[] = [
                        'value' => '-',
                        'warna' => 'text-gray-300',
                    ];

                    continue;
                }

                switch ($kehadiran->tipe_kehadiran_id) {
                    case 1: // Hadir
                        $absens[] = [
                            'value' => 'H',
                            'warna' => 'bg-green-100 text-green-700',
                        ];
                        break;

                    case 2: // Sakit
                        $absens[] = [
                            'value' => 'S',
                            'warna' => 'bg-yellow-100 text-yellow-700',
                        ];
                        break;

                    case 3: // Mangkir
                        $absens[] = [
                            'value' => 'M',
                            'warna' => 'bg-red-100 text-red-700',
                        ];
                        break;

                    case 5: // Izin
                        $absens[] = [
                            'value' => 'I',
                            'warna' => 'bg-blue-100 text-blue-700',
                        ];
                        break;

                    default:
                        $absens[] = [
                            'value' => '-',
                            'warna' => 'text-gray-300',
                        ];
                        break;
                }
            }

            $this->datas[] = [
                'no' => $karyawan->id_user,
                'nama' => $karyawan->nama_lengkap,
                'inisial' => $karyawan->inisial,
                'perusahaan' => $karyawan->perusahaan,
                'posisi' => $karyawan->posisi,
                'warna' => $karyawan->warna,
                'absens' => $absens,
            ];
        }

        $this->loadRekapRecord();
    }

    public $rekapRecord = null;

    public function loadRekapRecord()
    {
        if (empty($this->rekapBulan)) {
            $this->rekapRecord = null;
            return;
        }

        [$start, $end] = $this->getRekapDateRange();
        $karyawanIds = $this->karyawanByOutsourcing;

        $firstKehadiranWithRekapan = Kehadiran::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$start, $end])
            ->whereNotNull('rekapan_kehadiran_id')
            ->first();

        if ($firstKehadiranWithRekapan) {
            $this->rekapRecord = \App\Models\RekapKehadiran::find($firstKehadiranWithRekapan->rekapan_kehadiran_id);
        } else {
            $this->rekapRecord = null;
        }
    }

    public function kirimRekapan()
    {
        $this->karyawanByOutsourcing = (new UserService)->getKaryawanByOutsourcing(Auth::user()->outsourcing_id, "array");
        [$start, $end] = $this->getRekapDateRange();
        $karyawanIds = $this->karyawanByOutsourcing;

        $kehadiranQuery = Kehadiran::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$start, $end]);

        if ($kehadiranQuery->count() === 0) {
            session()->flash('error', 'Tidak ada data absensi pada periode rekap ini untuk dikirim.');
            return;
        }

        $this->loadRekapRecord();
        if ($this->rekapRecord) {
            session()->flash('error', 'Rekapan absensi untuk periode ini sudah pernah dikirim.');
            return;
        }

        $kehadiranService = new KehadiranService;
        $totalHadir = (new KehadiranService)->totalHadirByDateRange($karyawanIds, $start, $end);
        $totalAlpha = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('mankir', $karyawanIds, $start, $end);
        $totalSakit = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('sakit', $karyawanIds, $start, $end);
        $totalIzin = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('izin', $karyawanIds, $start, $end);
        $totalCuti = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('cuti', $karyawanIds, $start, $end);
        $totalTerlambat = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('terlambat', $karyawanIds, $start, $end);

        $rekap = \App\Models\RekapKehadiran::create([
            'pengaju' => Auth::id(),
            'status_validasi' => \App\Enums\Validasi::Pending->value,
            'status' => \App\Enums\Status::Active->value,
            'total_hadir' => $totalHadir,
            'total_mankir' => $totalAlpha,
            'total_sakit' => $totalSakit,
            'total_izin' => $totalIzin,
            'total_cuti' => $totalCuti,
            'total_terlambat' => $totalTerlambat,
            'total_jam_kerja' => $totalHadir * 8,
        ]);

        $kehadiranQuery->update([
            'rekapan_kehadiran_id' => $rekap->id_rekapan
        ]);

        $this->loadRekapRecord();
        session()->flash('success', 'Rekapan absensi berhasil dikirim.');
    }

    public function kirimUlang()
    {
        $this->karyawanByOutsourcing = (new UserService)->getKaryawanByOutsourcing(Auth::user()->outsourcing_id, "array");
        $this->loadRekapRecord();
        if ($this->rekapRecord && $this->rekapRecord->status_validasi === \App\Enums\Validasi::Invalid->value) {
            [$start, $end] = $this->getRekapDateRange();
            $karyawanIds = $this->karyawanByOutsourcing;
            $kehadiranService = new KehadiranService;

            $totalHadir = (new KehadiranService)->totalHadirByDateRange($karyawanIds, $start, $end);
            $totalAlpha = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('mankir', $karyawanIds, $start, $end);
            $totalSakit = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('sakit', $karyawanIds, $start, $end);
            $totalIzin = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('izin', $karyawanIds, $start, $end);
            $totalCuti = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('cuti', $karyawanIds, $start, $end);
            $totalTerlambat = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('terlambat', $karyawanIds, $start, $end);

            $this->rekapRecord->update([
                'status_validasi' => \App\Enums\Validasi::Pending->value,
                'total_hadir' => $totalHadir,
                'total_mankir' => $totalAlpha,
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_cuti' => $totalCuti,
                'total_terlambat' => $totalTerlambat,
                'total_jam_kerja' => $totalHadir * 8,
            ]);

            session()->flash('success', 'Rekapan absensi berhasil dikirim ulang.');
        }
    }

    public function render()
    {
        $label = Carbon::parse($this->startDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($this->endDate)->translatedFormat('d M Y');
        return view('livewire.admin-outsourcing.rekapan-karyawan', [
            'totalHadir' => $this->totalHadir,
            'totalAlpha' => $this->totalAlpha,
            'totalIzinSakit' => $this->totalIzinSakit,
            'totalKaryawan' => $this->totalKaryawan,
            'labelBulan' => $label,
        ]);
    }
}