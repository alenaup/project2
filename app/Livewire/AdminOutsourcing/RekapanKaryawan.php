<?php

namespace App\Livewire\AdminOutsourcing;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use App\Services\KehadiranService;
use App\Services\RekapService;
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
    public int $totalHadir = 0;
    public int $totalAlpha = 0;
    public int $totalIzinSakit = 0;

    public function mount()
    {
        $this->startDate = Carbon::now()->subMonth()->day(26)->format('Y-m-d');
        $this->endDate = Carbon::now()->day(25)->format('Y-m-d');
        $this->rekapBulan = Carbon::now()->format('Y-m');
        $this->loadData();
    }

    public function updatedRekapBulan()
    {
        $this->halamanAktif = 1;
        $this->loadData();
    }

    public function gantiHalaman(int $halaman): void
    {
        $this->halamanAktif = $halaman;
        $this->loadData();
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

    private function updateDateRangeFromBulan()
    {
        [$start, $end] = $this->getRekapDateRange();
        $this->startDate = $start;
        $this->endDate = $end;
    }

    /**
     * Render component ke view
     */
    protected function loadData(): void
    {
        $this->karyawanByOutsourcing = (new UserService)->getKaryawanByOutsourcing(Auth::user()->outsourcing_id, "array");

        $this->updateDateRangeFromBulan();

        // Hitung total statistik dalam 1 query groupBy (Optimasi Performa)
        if (!empty($this->karyawanByOutsourcing)) {
            $stats = \App\Models\Kehadiran::whereIn('karyawan_id', $this->karyawanByOutsourcing)
                ->whereBetween('tanggal', [$this->startDate, $this->endDate])
                ->join('tipe_kehadiran', 'kehadiran.tipe_kehadiran_id', '=', 'tipe_kehadiran.id_tipe_kehadiran')
                ->select('tipe_kehadiran.status_kehadiran', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('tipe_kehadiran.status_kehadiran')
                ->pluck('total', 'status_kehadiran')
                ->toArray();

            $this->totalHadir = $stats['hadir'] ?? 0;
            $this->totalAlpha = $stats['mankir'] ?? 0;
            $this->totalIzinSakit = ($stats['sakit'] ?? 0) + ($stats['izin'] ?? 0);
        } else {
            $this->totalHadir = 0;
            $this->totalAlpha = 0;
            $this->totalIzinSakit = 0;
        }

        $this->totalKaryawan = (new UserService)->getKaryawanByVendorCount(Auth::user()->outsourcing_id);

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

        $karyawans = ( new UserService)->getKaryawanByVendorPaginated(Auth::user()->outsourcing_id, $this->halamanAktif, $this->perPage);

        $userIds = $karyawans->pluck('id_user')->toArray();

        // Ambil seluruh kehadiran dalam range tanggal ini dalam 1 query
        $kehadirans = (new KehadiranService)->ambilBanyakKehadiranByDateRange($userIds, $this->startDate, $this->endDate);

        $kehadiranMap = $kehadirans
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });
            });

        $colors = ['bg-green-600','bg-emerald-600','bg-blue-500','bg-purple-600','bg-orange-500'];
        foreach ($karyawans as $index => $karyawan) {
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

                    case 3: // Mangkir (Alpha)
                        $absens[] = [
                            'value' => 'A',
                            'warna' => 'bg-red-100 text-red-700',
                        ];
                        break;

                    case 4: // Cuti
                        $absens[] = [
                            'value' => 'L',
                            'warna' => 'bg-purple-100 text-purple-700',
                        ];
                        break;

                    case 5: // Izin
                        $absens[] = [
                            'value' => 'I',
                            'warna' => 'bg-blue-100 text-blue-700',
                        ];
                        break;

                    case 6: // Terlambat
                        $absens[] = [
                            'value' => 'H',
                            'warna' => 'bg-green-100 text-green-700',
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

            $inisial = collect(explode(' ', $karyawan->nama_lengkap))
                ->take(2)
                ->map(fn($w) => !empty($w) ? strtoupper($w[0]) : '')
                ->join('');

            $this->datas[] = [
                'no' => (($this->halamanAktif - 1) * $this->perPage) + ($index + 1),
                'nama' => $karyawan->nama_lengkap,
                'inisial' => $inisial,
                'perusahaan' => Auth::user()->outsourcing->nama_outsourcing ?? 'Vendor',
                'posisi' => $karyawan->departemen->nama_departemen ?? '-',
                'warna' => $colors[$index % count($colors)],
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

        $this->rekapRecord = app(RekapService::class)->loadRekapRecordForOutsourcing($karyawanIds, $start, $end);
    }

    public function kirimRekapan()
    {
        [$start, $end] = $this->getRekapDateRange();

        $rekap = app(RekapService::class)->kirimRekapanForOutsourcing(Auth::user()->outsourcing_id, $start, $end, Auth::id());

        if (!$rekap) {
            $this->dispatch('flash-error', message: 'Tidak ada data absensi pada periode rekap ini untuk dikirim.');
            return;
        }

        $this->loadRekapRecord();
        $this->dispatch('flash-success', message: 'Rekapan absensi berhasil dikirim.');
    }

    public function kirimUlang()
    {
        $this->loadRekapRecord();
        if ($this->rekapRecord && $this->rekapRecord->status_validasi === \App\Enums\Validasi::Invalid->value) {
            [$start, $end] = $this->getRekapDateRange();

            app(RekapService::class)->kirimUlangRekapanForOutsourcing($this->rekapRecord, Auth::user()->outsourcing_id, $start, $end);

            $this->dispatch('flash-success', message: 'Rekapan absensi berhasil dikirim ulang.');
            $this->loadRekapRecord();
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
