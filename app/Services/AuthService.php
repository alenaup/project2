<?php
namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/* Bagian untukk mengatur Logic pada Auth */
class AuthService
{
    public function login(string $email, string $password): array
    {
        // * Mengambil data user berdasarkan email dengan memilih (select) kolom spesifik yang dibutuhkan saja
        $user = User::select('id_user', 'email', 'password', 'status', 'role')
                    ->where('email', $email)
                    ->first();

        // * Mencocokkan data user dan melakukan verifikasi password secara manual menggunakan facade Hash
        if (!$user || !Hash::check($password, $user->password)) {
            return [
                // * mengembalikan data dengan format array, jika gagal maka success bernilai false dan message berisi string
                'success' => false,
                'message' => 'Email atau password salah'
            ];
        }

        // * mengecek apakah status user aktif atau tidak,
        // * jika tidak aktif maka akan mengembalikan data dengan message berisi string
        if ($user->status !== Status::Active->value) {
            return [
                'success' => false,
                'message' => 'Akun Anda sedang tidak aktif'
            ];
        }

        // * Jika lolos semua pengecekan, login user tersebut secara manual menggunakan facade Auth
        Auth::login($user);

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
