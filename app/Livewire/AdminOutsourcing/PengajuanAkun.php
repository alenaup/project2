<?php

namespace App\Livewire\AdminOutsourcing;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Departemen;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PengajuanAkun extends Component
{
    use WithPagination;

    // Search & Pagination
    public string $search = '';
    public int $perPage = 10;

    // Modal Control properties
    public ?int $selectedId = null;

    // Form fields for submitting a new employee
    public string $nip = '';
    public string $nama_lengkap = '';
    public string $email = '';
    public string $nomor_tlp = '';
    public string $alamat = '';
    public ?int $departemen_id = null;

    // Form fields for editing/resubmitting
    public string $editNip = '';
    public string $editNama = '';
    public string $editEmail = '';
    public string $editTelepon = '';
    public string $editAlamat = '';
    public ?int $editDepartemenId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'nip' => 'required|string|max:50',
            'nama_lengkap' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:user,email',
            'nomor_tlp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'departemen_id' => 'required|exists:departemen,id_departemen',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'nomor_tlp.required' => 'Nomor telepon wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'departemen_id.required' => 'Departemen wajib dipilih.',
        ]);

        User::create([
            'nama_lengkap' => $this->nama_lengkap,
            'email' => $this->email,
            'password' => bcrypt('admin123'),
            'nomor_tlp' => $this->nomor_tlp,
            'alamat' => $this->alamat,
            'nip' => 'NIP-' . ltrim(str_replace('NIP-', '', $this->nip)),
            'departemen_id' => $this->departemen_id,
            'tanggal_keluar' => null,
            'tanggal_masuk' => null,
            'role' => UserRole::Karyawan->value,
            'status' => Status::Pending->value,
            'user_id' => auth()->id(),
            'outsourcing_id' => auth()->user()->outsourcing_id,
        ]);

        session()->flash('success', '✅ Pengajuan data karyawan berhasil dikirim.');

        $this->resetForm();
        $this->dispatch('close-add-modal');
    }

    private function resetForm(): void
    {
        $this->nip = '';
        $this->nama_lengkap = '';
        $this->email = '';
        $this->nomor_tlp = '';
        $this->alamat = '';
        $this->departemen_id = null;
    }

    public function openCancel(int $userId): void
    {
        $this->selectedId = $userId;
    }

    public function cancelSubmission(): void
    {
        $user = User::where('id_user', $this->selectedId)
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Pending->value)
            ->first();

        if ($user) {
            $user->delete();
            session()->flash('success', '🗑️ Pengajuan data karyawan berhasil dibatalkan.');
        } else {
            session()->flash('error', 'Gagal membatalkan pengajuan.');
        }

        $this->selectedId = null;
        $this->dispatch('close-cancel-modal');
    }

    public function openEdit(int $userId): void
    {
        $user = User::where('id_user', $userId)
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Inactive->value)
            ->whereNull('tanggal_keluar')
            ->first();

        if ($user) {
            $this->selectedId = $userId;
            $this->editNip = str_replace('NIP-', '', $user->nip);
            $this->editNama = $user->nama_lengkap;
            $this->editEmail = $user->email;
            $this->editTelepon = $user->nomor_tlp ?? '';
            $this->editAlamat = $user->alamat ?? '';
            $this->editDepartemenId = $user->departemen_id;
        }
    }

    public function closeEdit(): void
    {
        $this->selectedId = null;
        $this->editNip = '';
        $this->editNama = '';
        $this->editEmail = '';
        $this->editTelepon = '';
        $this->editAlamat = '';
        $this->editDepartemenId = null;
    }

    public function resubmit(): void
    {
        $validated = $this->validate([
            'editNip' => 'required|string|max:50',
            'editNama' => 'required|string|min:3|max:100',
            'editEmail' => 'required|email|unique:user,email,' . $this->selectedId . ',id_user',
            'editTelepon' => 'required|string|max:20',
            'editAlamat' => 'required|string|max:255',
            'editDepartemenId' => 'required|exists:departemen,id_departemen',
        ], [
            'editNip.required' => 'NIP wajib diisi.',
            'editNama.required' => 'Nama lengkap wajib diisi.',
            'editNama.min' => 'Nama lengkap minimal 3 karakter.',
            'editEmail.required' => 'Email wajib diisi.',
            'editEmail.email' => 'Format email tidak valid.',
            'editEmail.unique' => 'Email sudah digunakan.',
            'editTelepon.required' => 'Nomor telepon wajib diisi.',
            'editAlamat.required' => 'Alamat wajib diisi.',
            'editDepartemenId.required' => 'Departemen wajib dipilih.',
        ]);

        $user = User::where('id_user', $this->selectedId)
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Inactive->value)
            ->whereNull('tanggal_keluar')
            ->first();

        if ($user) {
            $user->update([
                'nip' => 'NIP-' . ltrim(str_replace('NIP-', '', $this->editNip)),
                'nama_lengkap' => $this->editNama,
                'email' => $this->editEmail,
                'nomor_tlp' => $this->editTelepon,
                'alamat' => $this->editAlamat,
                'departemen_id' => $this->editDepartemenId,
                'status' => Status::Pending->value, // set back to pending
            ]);

            session()->flash('success', '✅ Pengajuan data karyawan berhasil diperbarui dan dikirim kembali.');
        } else {
            session()->flash('error', 'Gagal mengirim kembali pengajuan.');
        }

        $this->closeEdit();
        $this->dispatch('close-edit-modal');
    }

    private function getSubmissions()
    {
        return User::with('departemen')
            ->where('role', UserRole::Karyawan->value)
            ->where('user_id', auth()->id())
            ->where('outsourcing_id', auth()->user()->outsourcing_id)
            ->when($this->search, function ($query) {
                $keyword = '%' . $this->search . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('nip', 'like', $keyword)
                      ->orWhere('nama_lengkap', 'like', $keyword)
                      ->orWhere('email', 'like', $keyword);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin-outsourcing.pengajuan-akun', [
            'submissions' => $this->getSubmissions(),
            'departments' => Departemen::all(),
        ]);
    }
}
