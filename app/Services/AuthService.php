<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(string $email, string $password): array
    {
        if (!Auth::attempt([
            'email' => $email,
            'password' => $password
        ])) {
            return [
                'success' => false,
                'message' => 'Email atau password salah'
            ];
        }

        $user = Auth::user();

        if ($user->status !== Status::Active->value) {
            Auth::logout();

            return [
                'success' => false,
                'message' => 'Akun Anda sedang tidak aktif'
            ];
        }

        session()->regenerate();

        return [
            'success' => true,
            'user' => $user,
            'redirect' => $this->getRedirectByRole($user->role->value)
        ];
    }

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
