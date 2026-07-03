<?php

namespace App\Livewire\AdminOutsourcing;

use App\Services\PerizinanSakitService;
use Livewire\Component;

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
    public function openDetail(int $id, PerizinanSakitService $perizinanSakitService): void
    {
        $perizinan = $perizinanSakitService->getPerizinanById($id);

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
    public function approve(PerizinanSakitService $perizinanSakitService): void
    {
        $perizinan = $perizinanSakitService->updateStatus($this->selectedId, 'disetujui');

        if (!$perizinan) {
            session()->flash('error', 'Data pengajuan tidak ditemukan.');
            $this->closeApprove();
            return;
        }

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
    public function reject(PerizinanSakitService $perizinanSakitService): void
    {
        $perizinan = $perizinanSakitService->updateStatus($this->selectedId, 'ditolak');

        if (!$perizinan) {
            session()->flash('error', 'Data pengajuan tidak ditemukan.');
            $this->closeReject();
            return;
        }

        session()->flash('success', "❌ Pengajuan dari {$perizinan->karyawan->nama_lengkap} telah ditolak.");

        $this->closeReject();
        $this->showDetailModal = false;
        $this->detailPengajuan = [];
    }

    public function render(PerizinanSakitService $perizinanSakitService)
    {
        $pendingList = $perizinanSakitService->getPengajuanMenunggu($this->search);
        return view('livewire.admin-outsourcing.pengajuan-karyawan', [
            'pengajuanList' => $pendingList,
            'riwayatList'   => $perizinanSakitService->getRiwayatValidasiHariIni(),
            'pendingCount'  => $pendingList->count(),
        ]);
    }
}
