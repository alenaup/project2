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
 * - Menampilkan daftar karyawan outsourcing yang berstatus inactive (pending approval)
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

    /* ──────────────────────────────────────────────────────────────
     |  Business Logic
     * ──────────────────────────────────────────────────────────── */

    /**
     * Menyetujui ajuan karyawan outsourcing.
     * Mengubah status user dari inactive menjadi active.
     *
     * @param int|null $userId  ID user yang disetujui (null = dari modal detail)
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
     * Menghapus data user yang ditolak beserta alasan penolakan.
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

        $user->delete();

        session()->flash('success', "Karyawan {$namaKaryawan} ditolak dengan alasan: {$this->alasanPenolakan}");

        $this->closeReject();
    }

    /* ──────────────────────────────────────────────────────────────
     |  Query Builder
     * ──────────────────────────────────────────────────────────── */

    /**
     * Mengambil data karyawan outsourcing yang berstatus inactive (pending).
     * Mendukung pencarian berdasarkan NIP, nama, dan nama vendor.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getKaryawanPending()
    {
        return User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Inactive->value)
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
