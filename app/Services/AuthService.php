<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

/* Bagian untukk mengatur Logic pada Auth */
class AuthService
{
    public function login(string $email, string $password): array
    {
        //* mengeksekusi Login dengan Auth facades bawaan laravel
        //* pada Auth facades ini terdapat fitur autentikasi terhubung dengan Model User dan database user
        if (!Auth::attempt([
            // * mencocokkan validasi dengan kolom User
            'email' => $email,
            'password' => $password
        ])) {
            return [
                // * mengembalikan data dengan format array, jika gagal maka success bernilai false dan message berisi string
                'success' => false,
                'message' => 'Email atau password salah'
            ];
        }

        // * jika berhasil maka menginisiasi ke varbel $user
        $user = Auth::user();

        // * mengecek apakah status user aktif atau tidak,
        // * jika tidak aktif maka akan logout dan mengembalikan data dengan massege berisi string
        if ($user->status !== Status::Active->value) {
            Auth::logout();
            return [
                'success' => false,
                'message' => 'Akun Anda sedang tidak aktif'
            ];
        }

        // * jika status user active maka session akan di generate ulang demi keamanan
        session()->regenerate();

        //* mengembalikan data dan memanggil fungsi getRedirectByRole
        return [
            'success' => true,
            'user' => $user,
            'redirect' => $this->getRedirectByRole($user->role->value)
        ];
    }

    /* Bagian untuk menentukan redirect berdasarkan role user */
    public function getRedirectByRole(string $role): string
    {
        return match ($role) {
            UserRole::AdminVendor->value => '/admin-outsourcing/dashboard',
            UserRole::Hr->value => '/hr/dashboard',
            UserRole::SuperAdmin->value => '/super-admin/dashboard',
            UserRole::KepalaDepartemen->value => '/kepala-departement/dashboard',
            UserRole::Karyawan->value => '/karyawan-outsourcing/dashboard',
            default => '/dashboard'
        };
    }
}
