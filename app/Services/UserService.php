<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    // CREATE user baru untuk user Super Admin, Kepala Departemen, Admin Vendor, dan HR
    // dipakai di:
    // UserManagement super Admin
    public function generateUser($nama_lengkap, $email, $nomor_tlp, $role, $password, $departemen_id = null)
    {
        if(Auth::check() && Auth::user()->role !== UserRole::SuperAdmin->value) {
            throw new \Exception('Hanya Super Admin yang dapat membuat user baru.');
        }
        $query = User::create([
            'nama_lengkap'  => $nama_lengkap,
            'email'         => $email,
            'nomor_tlp'     => $nomor_tlp,
            'role'          => $role,
            'password'      => Hash::make($password),
            'status'        => Status::Active->value,
            'user_id'       => Auth::id(),
            'departemen_id' => $departemen_id,
        ]);

        return $query;
    }

    // method mengambil detail user
    public function getUserDetail(int $userId): ?array
    {
        $user = User::with([
            'departemen',
            'outsourcing'
        ])->find($userId);

        if (!$user) {
            return null;
        }
        return [
            'nama_lengkap'    => $user->nama_lengkap,
            'email'           => $user->email,
            'nomor_tlp'       => $user->nomor_tlp ?? '-',
            'nip'             => $user->nip ?? '-',
            'alamat'          => $user->alamat ?? '-',
            'status'          => $user->status,
            'tanggal_masuk'   => $user->tanggal_masuk
                ? date('d F Y', strtotime($user->tanggal_masuk))
                : '-',
            'departemen_nama' => $user->departemen->nama_departemen ?? '-',
            'vendor_nama'     => $user->outsourcing->nama_outsourcing ?? '-',
        ];
    }

    //
    public function getUserById($id)
    {
        return User::findOrFail($id);;
    }

    public function getUserSuperAdmin()
    {
        return User::where('role', UserRole::SuperAdmin->value)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserKepalaDepartemen()
    {
        return User::where('role', UserRole::KepalaDepartemen->value)
            ->where('status', Status::Active->value)
            ->get();
    }

    
    public function getUserAdmin()
    {
        return User::where('role', UserRole::AdminVendor->value)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserHr()
    {
        return User::where('role', UserRole::Hr->value)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserKaryawanByDepartemen($departemen)
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('departemen_id', $departemen)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserKaryawanById($user)
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('id_user', $user)
            ->first();
    }

    public function getKaryawanByOutsourcing($outsourcing, $jenis)
    {
        if ($jenis == 'array') {
            return User::where('role', UserRole::Karyawan->value)
                ->where('outsourcing_id', $outsourcing)
                ->where('status', Status::Active->value)
                ->whereNull('tanggal_keluar')
                ->pluck('id_user')
                ->toArray();
        } elseif ($jenis == 'object') {
            return User::where('role', UserRole::Karyawan->value)
                ->where('outsourcing_id', $outsourcing)
                ->where('status', Status::Active->value)
                ->whereNull('tanggal_keluar')
                ->get();
        }
    }

    public function getOutsourcing()
    {
        if (!Auth::check()) {
            return [];
        }
        $query = User::where('outsourcing_id', Auth::user()->outsourcing_id)
            ->where('status', Status::Active->value)
            ->where('role', UserRole::Karyawan->value)
            ->pluck('id_user')
            ->toArray();

        return $query;
    }

    public function updateUser(int $userId, array $data): bool
    {
        $user = User::findOrFail($userId);
        return $user->update($data);
    }

    public function toggleUserStatus(int $userId): array
    {
        $user = User::findOrFail($userId);
        $newStatus = $user->status === Status::Active->value
            ? Status::Inactive->value
            : Status::Active->value;

        $user->update(['status' => $newStatus]);

        $label = $newStatus === Status::Active->value ? 'diaktifkan' : 'dinonaktifkan';

        return [
            'user' => $user,
            'label' => $label
        ];
    }

    public function deleteUser(int $userId): bool
    {
        $user = User::findOrFail($userId);
        return $user->delete();
    }

    /**
     * Mengambil data karyawan per departemen dengan filter pencarian dan paginasi.
     */
    public function getKaryawanByDepartemenPaginated($deptId, string $search = '', int $perPage = 10)
    {
        $query = User::where('role', UserRole::Karyawan->value);

        if ($deptId) {
            $query->where('departemen_id', $deptId);
        } else {
            $query->whereNull('id_user');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('nama_lengkap', 'asc')->paginate($perPage);
    }

    /**
     * Mengambil objek departemen berdasarkan ID.
     */
    public function getDepartemenById($deptId)
    {
        return \App\Models\Departemen::find($deptId);
    }

    /**
     * Mengambil data user terpaginasi dengan filter activeTab, search query, dan filterStatus.
     */

    // dipakai di :
    // UserManagement super Admin
    public function getUsersPaginated(string $activeTab, string $search = '', string $filterStatus = 'semua', int $perPage = 10)
    {
        $query = User::query();

        // Filter berdasarkan tab aktif
        match ($activeTab) {
            'admin_outsourcing' => $query->where('role', UserRole::AdminVendor->value),
            'hr'                => $query->where('role', UserRole::Hr->value),
            'kepala_departemen' => $query->where('role', UserRole::KepalaDepartemen->value),
            default             => $query->whereIn('role', [
                UserRole::AdminVendor->value,
                UserRole::Hr->value,
                UserRole::KepalaDepartemen->value,
            ]),
        };

        // Pencarian nama atau email
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan status aktif/nonaktif
        if ($filterStatus !== 'semua') {
            $query->where('status', $filterStatus);
        }

        return $query->latest('id_user')->paginate($perPage);
    }
}

