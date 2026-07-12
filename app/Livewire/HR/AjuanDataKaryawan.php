<?php

namespace App\Livewire\HR;
use App\Services\UserService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class AjuanDataKaryawan
 *
 * Livewire component untuk mengelola ajuan data karyawan outsourcing.
 *
 * Fitur:
 * - Menampilkan daftar karyawan outsourcing yang berstatus pending (menunggu persetujuan)
 * - Pencarian berdasarkan NIP, nama, atau asal vendor
 * - Pagination data karyawan
 * - Menyetujui ajuan karyawan (mengubah status menjadi active)
 * - Menolak ajuan karyawan dengan alasan penolakan
 * - Modal detail karyawan
 */
class AjuanDataKaryawan extends Component
{
    use WithPagination;

    /* ──────────────────────────────────────────────────────────────
     |  Properties
     * ──────────────────────────────────────────────────────────── */

    /** @var string Kata kunci pencarian */
    public string $search = '';

    /** @var int Jumlah data per halaman */
    public int $perPage = 10;

    /** @var string Alasan penolakan dari textarea modal */
    public string $alasanPenolakan = '';

    /** @var int|null ID karyawan yang sedang dipilih untuk aksi */
    public ?int $selectedUserId = null;

    /** @var array Data karyawan yang ditampilkan pada modal detail */
    public array $selectedUser = [];

    /** @var bool Kontrol visibilitas modal detail karyawan */
    public bool $showDetailModal = false;

    /** @var bool Kontrol visibilitas modal alasan penolakan */
    public bool $showRejectModal = false;

    /**
     * @var bool Kontrol visibilitas modal konfirmasi setujui.
     */
    public bool $showApproveModal = false;

    /**
     * @var bool Kontrol visibilitas modal konfirmasi penolakan.
     */
    public bool $showRejectConfirmModal = false;


    /* ──────────────────────────────────────────────────────────────
     |  Lifecycle Hooks
     * ──────────────────────────────────────────────────────────── */

    /**
     * Reset pagination saat kata kunci pencarian berubah.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /* ──────────────────────────────────────────────────────────────
     |  Modal Actions
     * ──────────────────────────────────────────────────────────── */

    /**
     * Membuka modal detail karyawan berdasarkan ID user.
     *
     * @param int $userId
     */

    // melakukan pembukaan modal detail karyawan berdasarkan ID user, mengambil data karyawan dari service, dan menampilkan informasi karyawan di modal
    // input ID user, memberikan output modal detail karyawan terbuka dengan data karyawan yang sesuai
    public function openDetail(int $userId, UserService $userService): void
    {
        $user = $userService->getUserWithOutsourcing($userId);

        if (!$user) {
            return;
        }

        $this->selectedUserId = $userId;
        $this->selectedUser   = [
            'nip'          => $user->nip ?? '-',
            'nama_lengkap' => $user->nama_lengkap,
            'email'        => $user->email,
            'nomor_tlp'    => $user->nomor_tlp ?? '-',
            'alamat'       => $user->alamat ?? '-',
            'asal_vendor'  => $user->outsourcing?->nama_outsourcing ?? '-',
        ];

        $this->showDetailModal = true;
    }

    /**
     * Menutup modal detail karyawan.
     */
    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedUser    = [];
        $this->selectedUserId  = null;
        $this->showApproveModal = false;
    }


    /**
     * Membuka modal alasan penolakan (dari modal detail).
     */
    public function openReject(): void
    {
        $this->showDetailModal = false;
        $this->showRejectModal = true;
        $this->alasanPenolakan = '';
    }

    /**
     * Membuka modal alasan penolakan langsung dari tabel (inline).
     *
     * @param int $userId
     */
    public function openRejectInline(int $userId): void
    {
        $this->selectedUserId  = $userId;
        $this->showRejectModal = true;
        $this->alasanPenolakan = '';
    }

    /**
     * Menutup modal alasan penolakan.
     */
    public function closeReject(): void
    {
        $this->showRejectModal = false;
        $this->alasanPenolakan = '';
    }

    /**
     * Membuka modal konfirmasi penolakan (tanpa alasan).
     */
    public function openRejectConfirm(int $userId): void
    {
        $this->selectedUserId = $userId;

        // Pastikan hanya satu jenis modal yang aktif.
        $this->showDetailModal = false;
        $this->showApproveModal = false;
        $this->showRejectModal = false;

        // Setelah user klik "ya", baru masuk ke modal alasan penolakan.
        $this->showRejectConfirmModal = true;
    }


    /**
     * Menutup modal konfirmasi penolakan.
     */
    public function closeRejectConfirm(): void
    {
        $this->showRejectConfirmModal = false;
    }

    /**
     * Lanjutkan proses penolakan dengan membuka modal alasan penolakan.
     */
    public function proceedRejectConfirm(): void
    {
        $this->showRejectConfirmModal = false;
        $this->openRejectInline($this->selectedUserId);
    }

    /**
     * Menutup modal konfirmasi setujui.
     */
    public function closeApproveConfirm(): void
    {
        $this->showApproveModal = false;
    }

    /**
     * Lanjutkan proses setujui berdasarkan user terpilih.
     */
    public function proceedApproveConfirm(UserService $userService): void
    {
        $this->approve($this->selectedUserId, $userService);
        $this->showApproveModal = false;
    }



    /* ──────────────────────────────────────────────────────────────
     |  Business Logic
     * ──────────────────────────────────────────────────────────── */

    /**
     * Menyetujui ajuan karyawan outsourcing.
     * Mengubah status user dari pending menjadi active.
     *
     * @param int|null $userId  ID user yang disetujui (null = dari modal detail)
     */
    public function openApproveConfirm(?int $userId = null): void
    {
        $this->selectedUserId = $userId ?? $this->selectedUserId;

        if (!$this->selectedUserId) {
            return;
        }

        $this->showDetailModal = false;
        $this->showRejectModal = false;
        $this->showApproveModal = true;
    }


    /**
     * Konfirmasi persetujuan ajuan karyawan.
     *
     * @param int|null $userId
     */

    // melakukan persetujuan ajuan karyawan outsourcing, mengubah status user menjadi active, dan menampilkan pesan sukses
    // input ID user (opsional), memberikan output status user berubah menjadi active dan pesan sukses
    public function approve(?int $userId = null, UserService $userService): void
    {
        $id   = $userId ?? $this->selectedUserId;
        $user = $userService->approveKaryawan($id);

        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            return;
        }

        session()->flash('success', "Karyawan {$user->nama_lengkap} berhasil disetujui.");

        $this->closeDetail();
    }

    /**
     * Menolak ajuan karyawan outsourcing.
     * Mengubah status user dari pending menjadi inactive.
     * tanggal_keluar tetap null.
     */
    // melakukan penolakan ajuan karyawan outsourcing, mengubah status user menjadi inactive, dan menampilkan pesan sukses
    // input ID user (opsional) dan alasan penolakan, memberikan output status user berubah menjadi inactive dan pesan sukses
   
    public function reject(UserService $userService): void
    {
        $this->validate([
            'alasanPenolakan' => 'required|string|min:5',
        ], [
            'alasanPenolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasanPenolakan.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $user = $userService->rejectKaryawan($this->selectedUserId, $this->alasanPenolakan);

        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            return;
        }

        session()->flash('success', "Karyawan {$user->nama_lengkap} ditolak dengan alasan: {$this->alasanPenolakan}");

        $this->closeReject();
    }

    /* ──────────────────────────────────────────────────────────────
     |  Render
     * ──────────────────────────────────────────────────────────── */

    /**
     * Render component ke view livewire.hr.ajuan-data-karyawan.
     */
    public function render(UserService $userService)
    {
        return view('livewire.hr.ajuan-data-karyawan', [
            'karyawanList' => $userService->getKaryawanPendingPaginated($this->search, $this->perPage),
        ]);
    }
}
