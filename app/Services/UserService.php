<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function getUserById()
    {
        return Auth::user()->id_user;
    }

    public function getUserSuperAdmin()
    {
        return User::where('role', UserRole::SuperAdmin->value)->get();
    }

    public function getUserKepalaDepartemen()
    {
        return User::where('role', UserRole::KepalaDepartemen->value)->get();
    }

    public function getUserAdmin()
    {
        return User::where('role', UserRole::AdminVendor->value)->get();
    }

    public function getUserHr()
    {
        return User::where('role', UserRole::Hr->value)->get();
    }

    public function getUserKaryawanByDepartemen($departemen)
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('departemen_id', $departemen)
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
}
