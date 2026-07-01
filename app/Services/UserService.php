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
    public function generateUser($nama_lengkap, $email, $nomor_tlp, $role, $password)
    {
        $query = User::create([
            'nama_lengkap' => $nama_lengkap,
            'email'        => $email,
            'nomor_tlp'    => $nomor_tlp,
            'role'         => $role,
            'password'     => Hash::make($password),
            'status'       => Status::Active->value,
            'user_id'      => Auth::id(),
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
    public function getUserById()
    {
        return Auth::user()->id_user;
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

    public function getLokasiDepartemenUser()
    {
        $user = Auth::user();
        if ($user && $user->departemen_id) {
            return Departemen::with('lokasi')->find($user->departemen_id);
        }
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
            ->get();
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
}

