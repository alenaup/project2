<?php
namespace App\Services;

use App\Models\Departemen;
use App\Enums\Status;

class DepartemenService
{
    public function getDepartemenById($id)
    {
        return Departemen::findOrFail($id);
    }

    public function getActiveDepartemen()
    {
        return Departemen::where('status', Status::Active->value)->get();
    }

    public function createDepartemen($departemen, $status, $lokasi_id){
        if (Departemen::where('nama_departemen', $departemen)->exists()) {
            throw new \Exception('Nama departemen sudah terdaftar.');
        }

        return Departemen::create([
            'nama_departemen' => $departemen,
            'status' => $status,
            'lokasi_id' => $lokasi_id ?: null,
        ]);
    }

    /**
     * Mengambil data departemen terpaginasi dengan pencarian dan filter status.
     */
    public function getDepartemenPaginated(string $search = '', string $filterStatus = 'semua', int $perPage = 10)
    {
        $query = Departemen::with('lokasi');

        if (!empty($search)) {
            $query->where('nama_departemen', 'like', '%' . $search . '%');
        }

        if ($filterStatus !== 'semua') {
            $query->where('status', $filterStatus);
        }

        return $query->paginate($perPage);
    }

    /**
     * Memperbarui data departemen.
     */
    public function updateDepartemen(int $id, array $data): bool
    {
        $departemen = $this->getDepartemenById($id);
        return $departemen->update([
            'nama_departemen' => $data['nama_departemen'],
            'status' => $data['status'],
            'lokasi_id' => $data['lokasi_id'] ?: null,
        ]);
    }

    /**
     * Mengubah status aktif/nonaktif departemen (toggle).
     */
    public function toggleDepartemenStatus(int $id): array
    {
        $departemen = $this->getDepartemenById($id);
        $newStatus = $departemen->status === Status::Active->value ? Status::Inactive->value : Status::Active->value;
        $departemen->update(['status' => $newStatus]);

        $label = $newStatus === Status::Active->value ? 'diaktifkan' : 'dinonaktifkan';
        return [
            'departemen' => $departemen,
            'label' => $label,
        ];
    }

    /**
     * Memperbarui status departemen secara langsung.
     */
    public function updateStatus(int $id, string $status): bool
    {
        $departemen = $this->getDepartemenById($id);
        return $departemen->update(['status' => $status]);
    }

    /**
     * Menghapus departemen.
     */
    public function deleteDepartemen(int $id): bool
    {
        $departemen = $this->getDepartemenById($id);
        return $departemen->delete();
    }

    /**
     * Dapatkan daftar departemen untuk dropdown.
     */
    public function getDepartemenList(): array
    {
        return Departemen::orderBy('nama_departemen')->get(['id_departemen', 'nama_departemen'])->toArray();
    }

    /**
     * Dapatkan semua departemen.
     */
    public function getAllDepartemen()
    {
        return Departemen::all();
    }

    public function findDepartemen($id): ?Departemen
    {
        return Departemen::find($id);
    }
}
