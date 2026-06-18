<?php

namespace App\Livewire\AdminOutsourcing;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
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

    /** @var int Jumlah data per halaman */
    public int $perPage = 10;

    /* ──────────────────────────────────────────────────────────────
     |  Properties — State Modal
     * ──────────────────────────────────────────────────────────── */

    /** @var int|null ID karyawan yang sedang dipilih */
    public ?int $selectedId = null;

    /** @var bool Kontrol visibilitas modal detail */
    public bool $showDetailModal = false;

    /** @var bool Kontrol visibilitas modal edit */
    public bool $showEditModal = false;

    /** @var bool Kontrol visibilitas modal konfirmasi edit */
    public bool $showConfirmEditModal = false;

    /** @var bool Kontrol visibilitas modal hapus */
    public bool $showDeleteModal = false;


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

    /* ──────────────────────────────────────────────────────────────
     |  Modal Detail
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal detail karyawan.
     */
    public function openDetail(int $userId): void
    {
        $user = User::with('outsourcing', 'departemen')->find($userId);

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

        $this->showDetailModal = true;
    }

    /**
     * Tutup modal detail.
     */
    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailKaryawan  = [];
    }

    /* ──────────────────────────────────────────────────────────────
     |  Modal Edit
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal edit dan isi form dengan data karyawan.
     */
    public function openEdit(int $userId): void
    {
        $user = User::find($userId);

        if (!$user) return;

        $this->selectedId   = $userId;
        $this->editNama     = $user->nama_lengkap;
        $this->editEmail    = $user->email ?? '';
        $this->editTelepon  = $user->nomor_tlp ?? '';
        $this->editAlamat   = $user->alamat ?? '';

        $this->showEditModal = true;
    }

    /**
     * Tutup modal edit dan reset form.
     */
    public function closeEdit(): void
    {
        $this->showEditModal = false;
        $this->resetEditForm();
    }

    /**
     * Buka modal konfirmasi edit.
     */
    public function openConfirmEdit(): void
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

        $this->showConfirmEditModal = true;
    }

    /**
     * Tutup modal konfirmasi edit.
     */
    public function closeConfirmEdit(): void
    {
        $this->showConfirmEditModal = false;
    }

    /**
     * Simpan perubahan data karyawan.
     */
    public function saveEdit(): void
    {
        $user = User::find($this->selectedId);

        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            $this->closeConfirmEdit();
            return;
        }

        $user->update([
            'nama_lengkap' => $this->editNama,
            'email'        => $this->editEmail,
            'nomor_tlp'    => $this->editTelepon,
            'alamat'       => $this->editAlamat,
        ]);

        session()->flash('success', "✅ Data karyawan {$user->nama_lengkap} berhasil diperbarui.");

        $this->closeConfirmEdit();
        $this->closeEdit();
    }

    /* ──────────────────────────────────────────────────────────────
     |  Aksi Hapus
     * ──────────────────────────────────────────────────────────── */

    /**
     * Buka modal konfirmasi hapus.
     */
    public function openDelete(int $userId): void
    {
        $this->selectedId = $userId;
        $this->showDeleteModal = true;
    }

    /**
     * Tutup modal hapus.
     */
    public function closeDelete(): void
    {
        $this->showDeleteModal = false;
        $this->selectedId = null;
    }

    /**
     * Hapus data karyawan.
     */
    public function delete(): void
    {
        $user = User::find($this->selectedId);

        if (!$user) {
            session()->flash('error', 'Data karyawan tidak ditemukan.');
            $this->closeDelete();
            return;
        }

        $nama = $user->nama_lengkap;
        $user->delete();

        session()->flash('success', "🗑️ Karyawan {$nama} berhasil dihapus.");

        $this->closeDelete();
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

    /**
     * Query karyawan aktif dengan pencarian.
     */
    private function getKaryawan()
    {
        return User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Active->value)
            ->when($this->search, function ($query) {
                $keyword = '%' . $this->search . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('nip', 'like', $keyword)
                      ->orWhere('nama_lengkap', 'like', $keyword)
                      ->orWhere('email', 'like', $keyword);
                });
            })
            ->orderBy('nama_lengkap')
            ->paginate($this->perPage);
    }

    /* ──────────────────────────────────────────────────────────────
     |  Render
     * ──────────────────────────────────────────────────────────── */

    public function render()
    {
        return view('livewire.admin-outsourcing.kelola-karyawan', [
            'karyawanList' => $this->getKaryawan(),
        ]);
    }
}
