<?php

namespace App\Livewire\HR;

use App\Enums\Validasi;
use App\Services\HRDashboardService;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Dashboard extends Component
{
    use WithPagination;

    /**
     * Filter tanggal dan departemen untuk tabel lembur.
     */
    public string $startDate    = '';
    public string $endDate      = '';
    public string $departemenId = '';

    // ── Kalkulator Lembur ────────────────────────────────────────
    public string $kalkulatorBulan = '';
    public string $kalkulatorTahun = '';
    public string $kalkulatorDepartemenId = '';
    public int $totalMenitLembur = 0;
    public bool $sudahHitung = false;
    public string $kalkulatorPeriodeMulai = '';
    public string $kalkulatorPeriodeSelesai = '';
    public string $kalkulatorError = '';

    // ── Daftar departemen untuk dropdown ─────────────────────────
    public array $departemens = [];

    // ── Jumlah data per halaman ──────────────────────────────────
    public int $perPage = 10;

    // ── Saat pertama dibuka, load departemen melalui service ──────
    public function mount(HRDashboardService $dashboardService): void
    {
        $this->startDate    = '';
        $this->endDate      = '';
        $this->departemenId = '';
        $this->departemens  = $dashboardService->getDepartemenList();
    }

    // ── Reset halaman saat filter berubah ───────────────────────
    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function updatedDepartemenId(): void
    {
        $this->resetPage();
    }

    // ── Hitung durasi lembur dalam menit (Helper View) ───────────
    public function hitungDurasi(?string $mulai, ?string $selesai): int
    {
        if (!$mulai || !$selesai) {
            return 0;
        }
        return (int) Carbon::parse($mulai)->diffInMinutes(Carbon::parse($selesai));
    }

    // ── Label & warna status validasi (Helper View) ──────────────
    public function statusBadge(string $status): array
    {
        return match ($status) {
            Validasi::Valid->value   => [
                'label' => 'Disetujui',
                'class' => 'bg-green-100 text-green-600',
            ],
            Validasi::Pending->value => [
                'label' => 'Menunggu',
                'class' => 'bg-yellow-100 text-yellow-700',
            ],
            Validasi::Invalid->value => [
                'label' => 'Ditolak',
                'class' => 'bg-red-100 text-red-600',
            ],
            default => [
                'label' => '-',
                'class' => 'bg-gray-100 text-gray-500',
            ],
        };
    }

    /**
     * Hitung rekapitulasi lembur menggunakan HRDashboardService.
     */
    public function hitungLemburKalkulator(HRDashboardService $dashboardService): void
    {
        $this->kalkulatorError = '';
        $this->sudahHitung = false;

        if (!$this->kalkulatorBulan || !$this->kalkulatorTahun) {
            $this->kalkulatorError = 'Harap pilih bulan dan tahun terlebih dahulu!';
            return;
        }

        $result = $dashboardService->calculateTotalLembur(
            $this->kalkulatorBulan,
            $this->kalkulatorTahun,
            $this->kalkulatorDepartemenId
        );

        $this->totalMenitLembur = $result['total_menit'];
        $this->kalkulatorPeriodeMulai = $result['start_date'];
        $this->kalkulatorPeriodeSelesai = $result['end_date'];
        $this->sudahHitung = true;
    }

    /**
     * Format menit ke string waktu Indonesia.
     */
    public function formatMenitKeWaktu(int $totalMenit): string
    {
        $jam = floor($totalMenit / 60);
        $menit = $totalMenit % 60;
        
        if ($jam > 0) {
            return "{$jam} Jam {$menit} Menit";
        }
        return "{$menit} Menit";
    }

    // ── Render Halaman ───────────────────────────────────────────
    public function render(HRDashboardService $dashboardService)
    {
        return view('livewire.hr.dashboard', [
            'stats'   => $dashboardService->getStats(),
            'lemburs' => $dashboardService->getLemburQuery(
                $this->startDate,
                $this->endDate,
                $this->departemenId
            )->paginate($this->perPage),
        ]);
    }
}
