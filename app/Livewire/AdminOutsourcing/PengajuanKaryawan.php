<?php

namespace App\Livewire\AdminOutsourcing;

use App\Enums\UserRole;
use App\Models\PerizinanSakit;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

/**
 * Class PengajuanKaryawan
 *
 * Livewire component untuk memvalidasi permohonan izin sakit/cuti karyawan outsourcing.
 *
 * Fitur:
 * - Menampilkan daftar pengajuan yang menunggu validasi (status: menunggu)
 * - Pencarian berdasarkan nama karyawan
 * - Modal detail pengajuan
 * - Terima / Tolak pengajuan
 * - Riwayat validasi hari ini
 */
class PengajuanKaryawan extends Component
{
    /* ──────────────────────────────────────────────────────────────
     |  Properties — Pencarian & Filter
     * ──────────────────────────────────────────────────────────── */

    /** @var string Kata kunci pencarian nama karyawan */
    public string $search = '';

    /* ──────────────────────────────────────────────────────────────
     |  Properties — State Modal
     * ──────────────────────────────────────────────────────────── */

    /** @var int|null ID perizinan yang dipilih */
    public ?int $selectedId = null;

    /** @var bool Kontrol visibilitas modal detail */
    public bool $showDetailModal = false;

    /** @var bool Kontrol visibilitas modal konfirmasi terima */
    public bool $showApproveModal = false;

    /** @var bool Kontrol visibilitas modal konfirmasi tolak */
    public bool $showRejectModal = false;

    /** @var array Data pengajuan untuk modal detail */
    public array $detailPengajuan = [];

    /* ──────────────────────────────────────────────────────────────
     |  Lifecycle Hooks
     * ──────────────────────────────────────────────────────────── */

    /**
     * Reset pagination saat pencarian berubah.
     */
    public function updatedSearch(): void
    {
        // Tidak ada pagination khusus di sini, tapi hook disiapkan
    }

    /* ──────────────────────────────────────────────────────────────
     |  Modal Detail
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal detail pengajuan.
     */
    public function openDetail(int $id): void
    {
        $perizinan = PerizinanSakit::with('karyawan.departemen', 'karyawan.outsourcing')->find($id);

        if (!$perizinan) return;

        $karyawan = $perizinan->karyawan;

        $this->detailPengajuan = [
            'id'               => $perizinan->id_perizinan,
            'nama'             => $karyawan->nama_lengkap ?? '-',
            'departemen'       => $karyawan->departemen?->nama_departemen ?? '-',
            'vendor'           => $karyawan->outsourcing?->nama_outsourcing ?? '-',
            'tanggal_mulai'    => $perizinan->tanggal_mulai,
            'tanggal_selesai'  => $perizinan->tanggal_selesai,
            'keterangan'       => $perizinan->keterangan,
            'file_surat'       => $perizinan->file_surat,
            'status'           => $perizinan->status,
            'tanggal_pengajuan' => $perizinan->tanggal_pengajuan,
        ];

        $this->selectedId     = $id;
        $this->showDetailModal = true;
    }

    /**
     * Tutup modal detail.
     */
    public function closeDetail(): void
    {
        $this->showDetailModal  = false;
        $this->detailPengajuan  = [];
        $this->selectedId       = null;
    }

    /* ──────────────────────────────────────────────────────────────
     |  Modal Konfirmasi Terima
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal konfirmasi terima.
     */
    public function openApprove(int $id): void
    {
        $this->selectedId       = $id;
        $this->showApproveModal = true;
    }

    /**
     * Tutup modal konfirmasi terima.
     */
    public function closeApprove(): void
    {
        $this->showApproveModal = false;
        $this->selectedId       = null;
    }

    /**
     * Terima pengajuan perizinan.
     */
    public function approve(): void
    {
        $perizinan = PerizinanSakit::find($this->selectedId);

        if (!$perizinan) {
            session()->flash('error', 'Data pengajuan tidak ditemukan.');
            $this->closeApprove();
            return;
        }

        $perizinan->update(['status' => 'disetujui']);

        session()->flash('success', "✅ Pengajuan dari {$perizinan->karyawan->nama_lengkap} telah diterima.");

        $this->closeApprove();
        $this->showDetailModal = false;
        $this->detailPengajuan = [];
    }

    /* ──────────────────────────────────────────────────────────────
     |  Modal Konfirmasi Tolak
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal konfirmasi tolak.
     */
    public function openReject(int $id): void
    {
        $this->selectedId      = $id;
        $this->showRejectModal = true;
    }

    /**
     * Buka modal konfirmasi terima dari dalam modal detail.
     * Modal detail ditutup dulu agar tidak tumpuk.
     */
    public function openApproveFromDetail(int $id): void
    {
        $this->showDetailModal  = false;
        $this->detailPengajuan  = [];
        $this->selectedId       = $id;
        $this->showApproveModal = true;
    }

    /**
     * Buka modal konfirmasi tolak dari dalam modal detail.
     * Modal detail ditutup dulu agar tidak tumpuk.
     */
    public function openRejectFromDetail(int $id): void
    {
        $this->showDetailModal = false;
        $this->detailPengajuan = [];
        $this->selectedId      = $id;
        $this->showRejectModal = true;
    }

    /**
     * Tutup modal konfirmasi tolak.
     */
    public function closeReject(): void
    {
        $this->showRejectModal = false;
        $this->selectedId      = null;
    }

    /**
     * Tolak pengajuan perizinan.
     */
    public function reject(): void
    {
        $perizinan = PerizinanSakit::find($this->selectedId);

        if (!$perizinan) {
            session()->flash('error', 'Data pengajuan tidak ditemukan.');
            $this->closeReject();
            return;
        }

        $perizinan->update(['status' => 'ditolak']);

        session()->flash('success', "❌ Pengajuan dari {$perizinan->karyawan->nama_lengkap} telah ditolak.");

        $this->closeReject();
        $this->showDetailModal = false;
        $this->detailPengajuan = [];
    }

    /* ──────────────────────────────────────────────────────────────
     |  Query
     * ──────────────────────────────────────────────────────────── */

    /**
     * Ambil data pengajuan yang menunggu validasi.
     */
    private function getPengajuanMenunggu()
    {
        return PerizinanSakit::with('karyawan.departemen', 'karyawan.outsourcing')
            ->where('status', 'menunggu')
            ->whereHas('karyawan', function ($q) {
                if ($this->search) {
                    $keyword = '%' . $this->search . '%';
                    $q->where('nama_lengkap', 'like', $keyword);
                }
                $q->where('role', UserRole::Karyawan->value);
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
    }

    /**
     * Ambil riwayat validasi (disetujui / ditolak) hari ini.
     */
    private function getRiwayatValidasi()
    {
        return PerizinanSakit::with('karyawan.departemen')
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->whereDate('updated_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /* ──────────────────────────────────────────────────────────────
     |  Render
     * ──────────────────────────────────────────────────────────── */

    public function render()
    {
        return view('livewire.admin-outsourcing.pengajuan-karyawan', [
            'pengajuanList' => $this->getPengajuanMenunggu(),
            'riwayatList'   => $this->getRiwayatValidasi(),
            'pendingCount'  => $this->getPengajuanMenunggu()->count(),
        ]);
    }
}
