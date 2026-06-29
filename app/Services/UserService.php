<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
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
}

