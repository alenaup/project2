<?php

namespace App\Http\Controllers;

/* digunakan untuk menagambil  */
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    * Method untuk memproses login pengguna

    */
    public function login()
    {
        /* ini adalah untuk melakukan fungsi cek session */
        /* jika session masih aktif maka langsung ke halaman dashboard berdasarkan role */
        if (Auth::check()) {

            $role = Auth::user()->role;

            return match ($role->value) {
                'super_admin' => redirect()->route('super.dashboard'),
                'admin_vendor' => redirect()->route('admin.dashboard'),
                'hr' => redirect()->route('hr.dashboard'),
                'kepala_departemen' => redirect()->route('kepala_departemen.dashboard'),
                'karyawan' => redirect()->route('dashboard'),
            };
        }

        /* jika tidak memiliki session maka dikembalikan ke view */
        return view('Auth.login');
    }

    
}
