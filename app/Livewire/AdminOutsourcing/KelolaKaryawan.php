<?php

namespace App\Livewire\AdminOutsourcing;

use App\Services\UserService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class KelolaKaryawan
 *
 * Livewire component untuk mengelola data karyawan outsourcing.
 *
 * Fitur:
 * - Menampilkan daftar karyawan outsourcing aktif
 * - Pencarian berdasarkan NIP, nama, email
 * - Modal detail karyawan
 * - Edit data karyawan
 * - Hapus data karyawan dengan konfirmasi
 */
class KelolaKaryawan extends Component
{
    use WithPagination;

    /* ──────────────────────────────────────────────────────────────
     |  Properties — Pencarian & Pagination
     * ──────────────────────────────────────────────────────────── */

    /** @var string Kata kunci pencarian */
    public string $search = '';

    /** @var string Filter status */
    public string $filterStatus = 'semua';

    /** @var int Jumlah data per halaman */
    public int $perPage = 10;

    /* ──────────────────────────────────────────────────────────────
     |  Properties — State Modal
     * ──────────────────────────────────────────────────────────── */

    /* ──────────────────────────────────────────────────────────────
     |  Properties — State Modal (Managed by Alpine.js)
     * ──────────────────────────────────────────────────────────── */

    /** @var int|null ID karyawan yang sedang dipilih */
    public ?int $selectedId = null;


    /* ──────────────────────────────────────────────────────────────
     |  Properties — Form Edit
     * ──────────────────────────────────────────────────────────── */

    /** @var string Nama lengkap karyawan yang diedit */
    public string $editNama    = '';

    /** @var string Email karyawan yang diedit */
    public string $editEmail   = '';

    /** @var string Nomor telepon karyawan yang diedit */
    public string $editTelepon = '';

    /** @var string Alamat karyawan yang diedit */
    public string $editAlamat  = '';

    /* ──────────────────────────────────────────────────────────────
     |  Properties — Data Detail (read-only)
     * ──────────────────────────────────────────────────────────── */

    /** @var array Data karyawan untuk modal detail */
    public array $detailKaryawan = [];

    /* ──────────────────────────────────────────────────────────────
     |  Lifecycle Hooks
     * ──────────────────────────────────────────────────────────── */

    /**
     * Reset pagination saat pencarian berubah.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    /* ──────────────────────────────────────────────────────────────
     |  Modal Detail
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal detail karyawan.
     */

    // megambil data karyawan untuk diisi pada modal
    public function openDetail(int $userId, UserService $userService): void
    {
        $user = $userService->getUserWithOutsourcing($userId);

        if (!$user) return;

        $this->detailKaryawan = [
            'nip'          => $user->nip && (int) $user->nip !== 0 ? 'NIP-' . $user->nip : '-',
            'nama_lengkap' => $user->nama_lengkap,
            'email'        => $user->email ?? '-',
            'nomor_tlp'    => $user->nomor_tlp ?? '-',
            'alamat'       => $user->alamat ?? '-',
            'vendor'       => $user->outsourcing?->nama_outsourcing ?? '-',
            'departemen'   => $user->departemen?->nama_departemen ?? '-',
        ];
    }

    /**
     * Tutup modal detail.
     */
    public function closeDetail(): void
    {
        $this->detailKaryawan  = [];
    }

    /* ──────────────────────────────────────────────────────────────
     |  Modal Edit
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal edit dan isi form dengan data karyawan.
     */

    // melakukan validasi input secara realtime
    public function openEdit(int $userId, UserService $userService): void
    {
        $user = $userService->getUserWithOutsourcing($userId);

        if (!$user) return;

        $this->selectedId   = $userId;
        $this->editNama     = $user->nama_lengkap;
        $this->editEmail    = $user->email ?? '';
        $this->editTelepon  = $user->nomor_tlp ?? '';
        $this->editAlamat   = $user->alamat ?? '';
    }

    /**
     * Tutup modal edit dan reset form.
     */
    public function closeEdit(): void
    {
        $this->resetEditForm();
    }

    /**
     * Simpan perubahan data karyawan.
     */
    // melakukan update pada data yang dilakukan perubahan
    public function saveEdit(UserService $userService): void
    {
        $this->validate([
            'editNama'    => 'required|string|min:3|max:100',
            'editEmail'   => 'required|email|max:100',
            'editTelepon' => 'nullable|string|max:20',
            'editAlamat'  => 'nullable|string|max:255',
        ], [
            'editNama.required'  => 'Nama lengkap wajib diisi.',
            'editNama.min'       => 'Nama minimal 3 karakter.',
            'editEmail.required' => 'Email wajib diisi.',
            'editEmail.email'    => 'Format email tidak valid.',
        ]);

        $user = $userService->updateKaryawan($this->selectedId, [
            'nama_lengkap' => $this->editNama,
            'email'        => $this->editEmail,
            'nomor_tlp'    => $this->editTelepon,
            'alamat'       => $this->editAlamat,
        ]);

        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            return;
        }

        session()->flash('success', "✅ Data karyawan {$user->nama_lengkap} berhasil diperbarui.");

        $this->dispatch('close-edit');
    }

    /* ──────────────────────────────────────────────────────────────
     |  Aksi Hapus & Ubah Status
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal konfirmasi hapus.
     */
    public function openDelete(int $userId): void
    {
        $this->selectedId = $userId;
    }

    /**
     * Tutup modal hapus.
     */
    public function closeDelete(): void
    {
        $this->selectedId = null;
    }

    /**
     * Hapus data karyawan (tambah tanggal keluar).
     */
    // menonaktifkan data karyawan yang dipilih berdasarkan id user nya (dengan tanggal keluar)
    public function delete(UserService $userService): void
    {
        $user = $userService->deleteKaryawan($this->selectedId);

        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            $this->dispatch('close-delete');
            return;
        }

        session()->flash('success', "🚫 Karyawan {$user->nama_lengkap} berhasil dihapus permanen.");

        $this->dispatch('close-delete');
    }

    /**
     * Ubah status karyawan tanpa menambah tanggal keluar.
     */
    public function toggleStatus(int $userId, UserService $userService): void
    {
        $result = $userService->toggleUserStatus($userId);
        $user = $result['user'];
        $label = $result['label'];

        session()->flash('success', "Status karyawan {$user->nama_lengkap} berhasil {$label}.");
    }

    /* ──────────────────────────────────────────────────────────────
     |  Helpers
     * ──────────────────────────────────────────────────────────── */

    /**
     * Reset semua field form edit.
     */
    private function resetEditForm(): void
    {
        $this->editNama    = '';
        $this->editEmail   = '';
        $this->editTelepon = '';
        $this->editAlamat  = '';
        $this->selectedId  = null;
    }

    /* ──────────────────────────────────────────────────────────────
     |  Query
     * ──────────────────────────────────────────────────────────── */

    public function render(UserService $userService)
    {
        $status = $this->filterStatus === 'semua' ? '' : $this->filterStatus;

        return view('livewire.admin-outsourcing.kelola-karyawan', [
            'karyawanList' => $userService->getKaryawanPaginated($this->search, $status, auth()->user()->outsourcing_id, $this->perPage),
        ]);
    }
}
