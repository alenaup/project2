<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    * Method untuk memproses login pengguna

    */
    public function login()
    {
        if (Auth::check()) {

            $role = Auth::user()->role;

            return match ($role) {
                'super_admin' => redirect()->route('super.dashboard'),
                'admin_vendor' => redirect()->route('admin.dashboard'),
                'hr' => redirect()->route('hr.dashboard'),
                'kepala_departemen' => redirect()->route('kepala_departemen.dashboard'),

                default => redirect()->route('dashboard'),
            };
        }

        return view('auth.login');
    }
}
