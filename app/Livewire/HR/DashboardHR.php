<?php

namespace App\Livewire\HR;

use App\Enums\Status;
use App\Enums\Validasi;
use App\Models\Departemen;
use App\Models\Lembur;
use App\Models\User;
use App\Models\RekapKehadiran;
use App\Enums\UserRole;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class DashboardHR extends Component
{
    use WithPagination;

    /**
     * Filter tanggal dan departemen untuk tabel lembur.
     */
    public string $startDate    = '';
    public string $endDate      = '';
    public string $departemenId = '';

    // ── Daftar departemen untuk dropdown ─────────────────────────
    public array $departemens = [];

    // ── Jumlah data per halaman ──────────────────────────────────

    public int $perPage = 10;

    // ── Saat pertama dibuka, semua filter dikosongkan ────────────
    // agar semua data lembur karyawan langsung tampil
    public function mount(): void
    {
        $this->startDate    = '';
        $this->endDate      = '';
        $this->departemenId = '';
        $this->departemens  = Departemen::orderBy('nama_departemen')->get(['id_departemen', 'nama_departemen'])->toArray();
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


    // ── Stat Cards: hitung data dari database ───────────────────
    private function getStats(): array
    {
        // Samakan definisi "aktif" dengan dashboard Admin Outsourcing:
        // aktif = tanggal_keluar IS NULL
        $totalOutsourcingAktif = User::whereNotNull('outsourcing_id')
            ->whereNull('tanggal_keluar')
            ->count();

        $totalOutsourcingTerdaftar = User::whereNotNull('outsourcing_id')->count();

        $totalLemburPending = Lembur::where('status_validasi', Validasi::Pending->value)->count();
        $totalRekapPending = RekapKehadiran::where('status_validasi', Validasi::Pending->value)->count();

        $totalAjuanPending = User::whereNotNull('outsourcing_id')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Inactive->value)
            ->count();

        return [
            'outsourcing_aktif'     => $totalOutsourcingAktif,
            'outsourcing_terdaftar' => $totalOutsourcingTerdaftar,
            'lembur_pending'        => $totalLemburPending,
            'rekap_pending'         => $totalRekapPending,
            'ajuan_pending'         => $totalAjuanPending,
        ];
    }

    // ── Query tabel lembur dengan filter tanggal ─────────────────
    private function getLemburQuery()
    {
        // Ambil daftar lembur aktif beserta relasi karyawan untuk ditampilkan di tabel.
        $query = Lembur::with(['karyawan.outsourcing', 'karyawan.departemen'])

            ->where('status', Status::Active->value)
            ->orderByDesc('mulai_lembur');


        if ($this->startDate !== '') {
            $query->whereDate('mulai_lembur', '>=', $this->startDate);
        }

        if ($this->endDate !== '') {
            $query->whereDate('mulai_lembur', '<=', $this->endDate);
        }

        if ($this->departemenId !== '') {
            $query->whereHas('karyawan', function ($q) {
                $q->where('departemen_id', $this->departemenId);
            });
        }

        return $query;
    }

    // ── Hitung durasi lembur dalam menit ─────────────────────────
    /**
     * Hitung durasi lembur dalam menit.
     */
    public function hitungDurasi(?string $mulai, ?string $selesai): int
    {
        if (!$mulai || !$selesai) {
            return 0;
        }


        return (int) Carbon::parse($mulai)->diffInMinutes(Carbon::parse($selesai));
    }


    // ── Label & warna status validasi ────────────────────────────
    /**
     * Label dan warna CSS untuk status validasi.
     */
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
    /**
     * Render halaman dashboard HR.
     */
    public function render()
    {

        return view('livewire.hr.dashboard-h-r', [
            'stats'   => $this->getStats(),
            'lemburs' => $this->getLemburQuery()->paginate($this->perPage),
        ]);
    }
}
