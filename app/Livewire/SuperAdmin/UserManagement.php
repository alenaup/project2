<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\UserService;
use Livewire\Attributes\On;

class UserManagement extends Component
{
    use WithPagination;

    // =========================================================
    // Filter & Search
    // =========================================================

    /** Tab aktif: 'admin_outsourcing' | 'hr' | 'kepala_departemen' */
    public string $activeTab = 'admin_outsourcing';

    public string $search = '';
    public string $filterStatus = 'semua';

    // =========================================================
    // Form Properties (shared Create / Edit)
    // =========================================================

    public string $nama_lengkap          = '';
    public string $email                 = '';
    public string $nomor_tlp             = '';
    public string $role                  = '';
    public string $password              = '';
    public string $password_confirmation = '';
    public ?int   $departemen_id         = null;

    public function updatedRole($value): void
    {
        if ($value !== UserRole::KepalaDepartemen->value) {
            $this->departemen_id = null;
        }
    }

    // =========================================================
    // Modal: Tambah / Edit
    // =========================================================

    public bool $showModal     = false;
    public bool $isEditing     = false;
    public ?int $editingUserId = null;

    // =========================================================
    // Modal: Konfirmasi Hapus
    // =========================================================

    public bool   $showDeleteConfirm = false;
    public ?int   $deletingUserId    = null;
    public string $deletingUserName  = '';
    public string $deleteActionType  = 'delete'; // 'deactivate' | 'delete'


    protected UserService $userService;

    // =========================================================
    // Pagination reset
    // =========================================================

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingActiveTab(): void
    {
        $this->resetPage();
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->dispatch('hide-loading');
    }

    // =========================================================
    // Modal Tambah — Open / Close / Reset
    // =========================================================

    public function openModal(): void
    {
        $this->resetForm();
        $this->isEditing     = false;
        $this->editingUserId = null;
        $this->showModal     = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'nama_lengkap', 'email', 'nomor_tlp',
            'role', 'password', 'password_confirmation', 'departemen_id',
        ]);
        $this->resetValidation();
    }

    // =========================================================
    // Create — Simpan Akun Baru
    // =========================================================

    public function simpanAkun(): void
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:user,email',
            'nomor_tlp'    => 'nullable|string|max:20',
            'role'         => ['required', new Enum(UserRole::class)],
            'password'     => 'required|string|min:8|confirmed',
        ];

        $messages = [
            'nama_lengkap.required' => 'Nama tidak boleh kosong.',
            'email.required'        => 'Email tidak boleh kosong.',
            'email.unique'          => 'Email sudah terdaftar.',
            'role.required'         => 'Role tidak boleh kosong.',
            'password.required'     => 'Password tidak boleh kosong.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ];

        if ($this->role === UserRole::KepalaDepartemen->value) {
            $rules['departemen_id'] = 'required|exists:departemen,id_departemen';
            $messages['departemen_id.required'] = 'Departemen wajib dipilih untuk Kepala Departemen.';
            $messages['departemen_id.exists'] = 'Departemen tidak valid.';
        }

        $this->validate($rules, $messages);
        $this->userService->generateUser(
            $this->nama_lengkap,
            $this->email,
            $this->nomor_tlp,
            $this->role,
            $this->password,
            $this->role === UserRole::KepalaDepartemen->value ? $this->departemen_id : null
        );

        $this->closeModal();
        $this->dispatch('flash-success', message: 'Akun berhasil ditambahkan!');
        $this->dispatch('userAdded');
        $this->resetPage();
    }

    // =========================================================
    // Edit — Buka Modal dengan Data Existing
    // =========================================================

    public function editAkun(int $id): void
    {
        $user = $this->userService->getUserById($id);

        $this->editingUserId = $id;
        $this->nama_lengkap  = $user->nama_lengkap;
        $this->email         = $user->email;
        $this->nomor_tlp     = $user->nomor_tlp ?? '';
        $this->role          = $user->role instanceof UserRole
            ? $user->role->value
            : (string) $user->role;
        $this->departemen_id = $user->departemen_id;

        $this->isEditing = true;
        $this->showModal = true;
        $this->resetValidation();
    }

    // =========================================================
    // Update — Simpan Perubahan Akun
    // =========================================================

    public function updateAkun(): void
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => [
                'required', 'email',
                Rule::unique('user', 'email')->ignore($this->editingUserId, 'id_user'),
            ],
            'nomor_tlp'    => 'nullable|string|max:20',
            'role'         => ['required', new Enum(UserRole::class)],
        ];

        $messages = [
            'nama_lengkap.required' => 'Nama tidak boleh kosong.',
            'email.required'        => 'Email tidak boleh kosong.',
            'email.unique'          => 'Email sudah terdaftar.',
            'role.required'         => 'Role tidak boleh kosong.',
        ];

        if ($this->role === UserRole::KepalaDepartemen->value) {
            $rules['departemen_id'] = 'required|exists:departemen,id_departemen';
            $messages['departemen_id.required'] = 'Departemen wajib dipilih untuk Kepala Departemen.';
            $messages['departemen_id.exists'] = 'Departemen tidak valid.';
        }

        $this->validate($rules, $messages);

        $this->userService->updateUser($this->editingUserId, [
            'nama_lengkap'  => $this->nama_lengkap,
            'email'         => $this->email,
            'nomor_tlp'     => $this->nomor_tlp,
            'role'          => $this->role,
            'departemen_id' => $this->role === UserRole::KepalaDepartemen->value ? $this->departemen_id : null,
        ]);

        $this->closeModal();
        $this->dispatch('flash-success', message: 'Akun berhasil diperbarui!');
    }

    // =========================================================
    // Toggle Status — Aktif ↔ Nonaktif
    // =========================================================

    public function toggleStatus(int $id): void
    {
        $result = $this->userService->toggleUserStatus($id);
        $user = $result['user'];
        $label = $result['label'];

        $this->dispatch('flash-success', message: "Akun {$user->nama_lengkap} berhasil {$label}.");
    }

    // =========================================================
    // Delete — Konfirmasi & Eksekusi Hapus
    // =========================================================

    public function confirmHapus(int $id): void
    {
        $user                    = $this->userService->getUserById($id);
        $this->deletingUserId    = $id;
        $this->deletingUserName  = $user->nama_lengkap;

        if ($user->status === Status::Active->value) {
            $this->deleteActionType = 'deactivate';
        } else {
            $this->deleteActionType = 'delete';
        }

        $this->showDeleteConfirm = true;
    }

    public function prosesAksiHapus(): void
    {
        if (! $this->deletingUserId) {
            return;
        }

        if ($this->deleteActionType === 'deactivate') {
            $result = $this->userService->toggleUserStatus($this->deletingUserId);
            $user = $result['user'];
            $label = $result['label'];
            $this->dispatch('flash-success', message: "Akun {$user->nama_lengkap} berhasil {$label}.");
        } else {
            $this->userService->deleteUser($this->deletingUserId);
            $this->dispatch('flash-success', message: 'Akun berhasil dihapus.');
        }

        $this->showDeleteConfirm = false;
        $this->deletingUserId    = null;
        $this->deletingUserName  = '';

        $this->resetPage();
    }

    public function aktifkanUser(int $id): void
    {
        $result = $this->userService->toggleUserStatus($id);
        $user = $result['user'];
        $label = $result['label'];

        $this->dispatch('flash-success', message: "Akun {$user->nama_lengkap} berhasil {$label}.");
    }

    public function cancelHapus(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingUserId    = null;
        $this->deletingUserName  = '';
    }

    #[On('userImported')]
    public function handleUserImported(): void
    {
        $this->resetPage();
    }

    // =========================================================
    // Render
    // =========================================================

    public function render()
    {
        $users = $this->userService->getUsersPaginated(
            $this->activeTab,
            $this->search,
            $this->filterStatus,
            10
        );
        $departemens = $this->departemenService->getActiveDepartemens();

        return view('livewire.super-admin.user-management', [
            'users'       => $users,
            'departemens' => $departemens,
        ]);
    }
}
