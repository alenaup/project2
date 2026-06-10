<?php

namespace App\Livewire\HR;

use App\Enums\Status;
use App\Enums\Validasi;
use App\Models\Lembur;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class DashboardHR extends Component
{
    use WithPagination;

    // ── Filter tanggal ──────────────────────────────────────────
    public string $startDate = '';
    public string $endDate   = '';

    // ── Jumlah data per halaman ──────────────────────────────────
    public int $perPage = 7;

    // ── Reset halaman saat filter berubah ───────────────────────
    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    // ── Stat Cards: hitung data dari database ───────────────────
    private function getStats(): array
{
    $totalOutsourcingAktif = User::whereNotNull('outsourcing_id')
        ->where('status', Status::Active->value)
        ->count();

    $totalOutsourcingTerdaftar = User::whereNotNull('outsourcing_id')->count();

    $totalLemburPending = Lembur::where('status_validasi', Validasi::Pending->value)->count();

    return [
        'outsourcing_aktif'     => $totalOutsourcingAktif,
        'outsourcing_terdaftar' => $totalOutsourcingTerdaftar,
        'lembur_pending'        => $totalLemburPending,
        'rekap_pending'         => 0, // model belum ada
        'ajuan_pending'         => 0, // model belum ada
    ];
}
    // ── Query tabel lembur dengan filter tanggal ─────────────────
    private function getLemburQuery()
    {
        $query = Lembur::with(['karyawan.outsourcing', 'karyawan.departemen'])
            ->where('status', Status::Active->value)
            ->orderByDesc('mulai_lembur');

        if ($this->startDate !== '') {
            $query->whereDate('mulai_lembur', '>=', $this->startDate);
        }

        if ($this->endDate !== '') {
            $query->whereDate('mulai_lembur', '<=', $this->endDate);
        }

        return $query;
    }

    // ── Hitung durasi lembur dalam menit ─────────────────────────
    public function hitungDurasi(?string $mulai, ?string $selesai): int
    {
        if (!$mulai || !$selesai) return 0;

        return (int) Carbon::parse($mulai)->diffInMinutes(Carbon::parse($selesai));
    }

    // ── Label & warna status validasi ────────────────────────────
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

    // ── Render ───────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.hr.dashboard-h-r', [
            'stats'   => $this->getStats(),
            'lemburs' => $this->getLemburQuery()->paginate($this->perPage),
        ]);
    }
}