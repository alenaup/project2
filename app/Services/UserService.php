<?php 

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

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

    public function getKaryawanByOutsourcing($outsourcing)
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', $outsourcing)
            ->get(); 
    }

    

    /* public function getDataPengajuanPengguna()
    {
        return 
    } */
}