<?php

namespace App\Livewire\SuperAdmin;

use App\Enums\Status;
use App\Models\Departemen;
use App\Models\Lokasi;
use App\Services\DepartemenService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'semua';

    // Form fields
    public string $nama_departemen = '';
    public string $status = 'active';
    public ?int $lokasi_id = null;

    // Modals
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingDepartemenId = null;

    // Delete Confirm Modal
    public bool $showDeleteConfirm = false;
    public ?int $deletingDepartemenId = null;
    public string $deletingDepartemenName = '';
    public string $deleteActionType = 'delete'; // 'deactivate' | 'delete'

    protected DepartemenService $departemenService;

    public function boot(DepartemenService $departemenService): void
    {
        $this->departemenService = $departemenService;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->editingDepartemenId = null;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'nama_departemen', 'status', 'lokasi_id'
        ]);
        $this->resetValidation();
    }

    public function simpanDepartemen(): void
    {
        $this->validate([
            'nama_departemen' => 'required|string|max:255|unique:departemen,nama_departemen',
            'status' => ['required', new Enum(Status::class)],
            'lokasi_id' => 'nullable|exists:lokasi,id_lokasi',
        ], [
            'nama_departemen.required' => 'Nama departemen tidak boleh kosong.',
            'nama_departemen.unique' => 'Nama departemen sudah terdaftar.',
            'status.required' => 'Status tidak boleh kosong.',
            'lokasi_id.exists' => 'Lokasi tidak valid.',
        ]);

        try {
            $this->departemenService->createDepartemen($this->nama_departemen, $this->status, $this->lokasi_id);
            $this->dispatch('flash-success', message: 'Departemen berhasil ditambahkan!');
        } catch (\Exception $e) {
            $this->dispatch('flash-error', message: 'Gagal menambahkan departemen. ' . $e->getMessage());
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function editDepartemen(int $id): void
    {
        $departemen = $this->departemenService->getDepartemenById($id);

        $this->editingDepartemenId = $id;
        $this->nama_departemen = $departemen->nama_departemen;
        $this->status = $departemen->status instanceof Status ? $departemen->status->value : (string) $departemen->status;
        $this->lokasi_id = $departemen->lokasi_id;

        $this->isEditing = true;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function updateDepartemen(): void
    {
        $this->validate([
            'nama_departemen' => [
                'required', 'string', 'max:255',
                Rule::unique('departemen', 'nama_departemen')->ignore($this->editingDepartemenId, 'id_departemen'),
            ],
            'status' => ['required', new Enum(Status::class)],
            'lokasi_id' => 'nullable|exists:lokasi,id_lokasi',
        ], [
            'nama_departemen.required' => 'Nama departemen tidak boleh kosong.',
            'nama_departemen.unique' => 'Nama departemen sudah terdaftar.',
            'status.required' => 'Status tidak boleh kosong.',
            'lokasi_id.exists' => 'Lokasi tidak valid.',
        ]);

        $this->departemenService->updateDepartemen($this->editingDepartemenId, [
            'nama_departemen' => $this->nama_departemen,
            'status' => $this->status,
            'lokasi_id' => $this->lokasi_id,
        ]);

        $this->closeModal();
        $this->dispatch('flash-success', message: 'Departemen berhasil diperbarui!');
    }

    public function toggleStatus(int $id): void
    {
        $result = $this->departemenService->toggleDepartemenStatus($id);
        $departemen = $result['departemen'];
        $label = $result['label'];

        $this->dispatch('flash-success', message: "Departemen {$departemen->nama_departemen} berhasil {$label}.");
    }

    public function confirmHapus(int $id): void
    {
        $departemen = $this->departemenService->getDepartemenById($id);
        $this->deletingDepartemenId = $id;
        $this->deletingDepartemenName = $departemen->nama_departemen;

        if ($departemen->status === Status::Active->value) {
            $this->deleteActionType = 'deactivate';
        } else {
            $this->deleteActionType = 'delete';
        }

        $this->showDeleteConfirm = true;
    }

    public function prosesAksiHapus(): void
    {
        if (!$this->deletingDepartemenId) {
            return;
        }

        if ($this->deleteActionType === 'deactivate') {
            $this->departemenService->updateStatus($this->deletingDepartemenId, Status::Inactive->value);
            $this->dispatch('flash-success', message: "Departemen {$this->deletingDepartemenName} berhasil dinonaktifkan.");
        } else {
            $this->departemenService->deleteDepartemen($this->deletingDepartemenId);
            $this->dispatch('flash-success', message: 'Departemen berhasil dihapus.');
        }

        $this->showDeleteConfirm = false;
        $this->deletingDepartemenId = null;
        $this->deletingDepartemenName = '';

        $this->resetPage();
    }

    public function aktifkanDepartemen(int $id): void
    {
        $departemen = $this->departemenService->getDepartemenById($id);
        $this->departemenService->updateStatus($id, Status::Active->value);
        $this->dispatch('flash-success', message: "Departemen {$departemen->nama_departemen} berhasil diaktifkan.");
    }

    public function cancelHapus(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingDepartemenId = null;
        $this->deletingDepartemenName = '';
    }

    public function render()
    {
        $departemens = $this->departemenService->getDepartemenPaginated(
            $this->search,
            $this->filterStatus,
            10
        );
        $lokasis = Lokasi::where('status', Status::Active->value)->get();

        return view('livewire.super-admin.department-management', [
            'departemens' => $departemens,
            'lokasis' => $lokasis,
        ]);
    }
}
