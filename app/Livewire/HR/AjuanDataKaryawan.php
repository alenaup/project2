<?php

namespace App\Livewire\HR;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
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
    public function openDetail(int $userId): void
    {
        $user = User::with('outsourcing')->find($userId);

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
    public function proceedApproveConfirm(): void
    {
        $this->approve($this->selectedUserId);
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
    public function approve(?int $userId = null): void
    {
        $id   = $userId ?? $this->selectedUserId;
        $user = User::find($id);


        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            return;
        }

        $user->update(['status' => Status::Active->value]);

        session()->flash('success', "Karyawan {$user->nama_lengkap} berhasil disetujui.");

        $this->closeDetail();
    }

    /**
     * Menolak ajuan karyawan outsourcing.
     * Mengubah status user dari pending menjadi inactive.
     * tanggal_keluar tetap null.
     */
    public function reject(): void
    {
        $this->validate([
            'alasanPenolakan' => 'required|string|min:5',
        ], [
            'alasanPenolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasanPenolakan.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $user = User::find($this->selectedUserId);

        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            return;
        }

        $namaKaryawan = $user->nama_lengkap;

        // Ubah status menjadi inactive (ditolak), tanggal_keluar tetap null
        $user->update([
            'status'         => Status::Inactive->value,
            'tanggal_keluar' => null,
        ]);

        session()->flash('success', "Karyawan {$namaKaryawan} ditolak dengan alasan: {$this->alasanPenolakan}");

        $this->closeReject();
    }

    /* ──────────────────────────────────────────────────────────────
     |  Query Builder
     * ──────────────────────────────────────────────────────────── */

    /**
     * Mengambil data karyawan outsourcing yang berstatus pending (menunggu persetujuan).
     * Mendukung pencarian berdasarkan NIP, nama, dan nama vendor.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getKaryawanPending()
    {
        return User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Pending->value)
            ->whereNull('tanggal_keluar')
            ->when($this->search, function ($query) {
                $keyword = '%' . $this->search . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('nip', 'like', $keyword)
                      ->orWhere('nama_lengkap', 'like', $keyword)
                      ->orWhereHas('outsourcing', function ($sub) use ($keyword) {
                          $sub->where('nama_outsourcing', 'like', $keyword);
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    /* ──────────────────────────────────────────────────────────────
     |  Render
     * ──────────────────────────────────────────────────────────── */

    /**
     * Render component ke view livewire.hr.ajuan-data-karyawan.
     */
    public function render()
    {
        return view('livewire.hr.ajuan-data-karyawan', [
            'karyawanList' => $this->getKaryawanPending(),
        ]);
    }
}
