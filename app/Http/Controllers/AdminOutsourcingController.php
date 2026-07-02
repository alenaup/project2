<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outsourcing;
use App\Enums\UserRole;
use Carbon\Carbon;

class AdminOutsourcingController extends Controller
{
    public function dashboard()
    {
        $datas = [
            [
                'no' => 1,
                'nama' => 'Rizky Darmawan',
                'inisial' => 'RD',
                'perusahaan' => 'PT. Chemistry Jaya',
                'posisi' => 'Operator',
                'warna' => 'bg-green-600',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],

            [
                'no' => 2,
                'nama' => 'Siti Aminah',
                'inisial' => 'SA',
                'perusahaan' => 'PT. TechSolution',
                'posisi' => 'Teknisi',
                'warna' => 'bg-emerald-600',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                ],
            ],

            [
                'no' => 3,
                'nama' => 'Budi Santoso',
                'inisial' => 'BS',
                'perusahaan' => 'PT. GlobalMaju',
                'posisi' => 'Helper',
                'warna' => 'bg-blue-500',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],
            [
                'no' => 4,
                'nama' => 'Andi Pratama',
                'inisial' => 'AP',
                'perusahaan' => 'PT. Sinar Abadi',
                'posisi' => 'Operator',
                'warna' => 'bg-indigo-600',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],

            [
                'no' => 5,
                'nama' => 'Dewi Lestari',
                'inisial' => 'DL',
                'perusahaan' => 'PT. Nusantara Tech',
                'posisi' => 'Admin',
                'warna' => 'bg-pink-500',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],
            [
                'no' => 6,
                'nama' => 'Rian Saputra',
                'inisial' => 'RS',
                'perusahaan' => 'PT. Globalindo',
                'posisi' => 'Driver',
                'warna' => 'bg-orange-500',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],

            [
                'no' => 7,
                'nama' => 'Maya Sari',
                'inisial' => 'MS',
                'perusahaan' => 'PT. Mitra Jaya',
                'posisi' => 'HR',
                'warna' => 'bg-teal-500',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],

            [
                'no' => 8,
                'nama' => 'Fajar Nugroho',
                'inisial' => 'FN',
                'perusahaan' => 'PT. Karya Mandiri',
                'posisi' => 'Security',
                'warna' => 'bg-gray-600',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],

            [
                'no' => 9,
                'nama' => 'Tika Ramadhani',
                'inisial' => 'TR',
                'perusahaan' => 'PT. Sumber Rejeki',
                'posisi' => 'Admin',
                'warna' => 'bg-fuchsia-600',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],

            [
                'no' => 10,
                'nama' => 'Yoga Prasetyo',
                'inisial' => 'YP',
                'perusahaan' => 'PT. Mega Industri',
                'posisi' => 'Operator',
                'warna' => 'bg-cyan-600',
                'absens' => [
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'S', 'warna' => 'bg-yellow-100 text-yellow-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'I', 'warna' => 'bg-blue-100 text-blue-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'A', 'warna' => 'bg-red-100 text-red-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => 'L', 'warna' => 'bg-purple-100 text-purple-700'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                    ['value' => '-', 'warna' => 'text-gray-300'],
                    ['value' => 'H', 'warna' => 'bg-green-100 text-green-700'],
                ],
            ],
        ];

        return view('adminOutsourcing.dashboard', compact('datas'));
    }

    public function exportAbsensi(\Illuminate\Http\Request $request)
    {
        $rekapBulan = $request->input('rekap_bulan', Carbon::now()->format('Y-m'));
        $outsourcingId = auth()->user()->outsourcing_id;
        
        $outsourcing = Outsourcing::find($outsourcingId);
        $outsourcingNama = $outsourcing ? $outsourcing->nama_outsourcing : 'Vendor';

        // Calculate dates (26th of previous month to 25th of current month)
        $carbon = Carbon::createFromFormat('Y-m', $rekapBulan);
        $prevMonthStart = $carbon->copy()->subMonth();
        
        $startDateStr = $prevMonthStart->day(26)->format('Y-m-d');
        $endDateStr = $carbon->day(25)->format('Y-m-d');

        $start = Carbon::parse($startDateStr);
        $end = Carbon::parse($endDateStr);
        
        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->copy();
        }

        $karyawans = User::with('departemen')
            ->where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', $outsourcingId)
            ->where('status', \App\Enums\Status::Active->value)
            ->whereNull('tanggal_keluar')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $userIds = $karyawans->pluck('id_user');
        
        $kehadirans = (new \App\Services\KehadiranService)->ambilBanyakKehadiranByDateRange($userIds, $startDateStr, $endDateStr);
        
        $kehadiranMap = $kehadirans
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });
            });

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Row 1: Title
        $sheet->setCellValue('A1', 'ECOGREEN REKAPITULASI ABSENSI');
        $lastColIndex = 5 + count($dates) + 6; // No, NIP, Nama, Posisi, Departemen + dates + summaries (6)
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);
        $sheet->mergeCells('A1:' . $lastColLetter . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('065F46'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Row 2: Vendor / Perusahaan
        $sheet->setCellValue('A2', 'Vendor: ' . $outsourcingNama);
        $sheet->mergeCells('A2:' . $lastColLetter . '2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Row 3: Periode
        $periodeStr = Carbon::parse($startDateStr)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($endDateStr)->translatedFormat('d M Y');
        $sheet->setCellValue('A3', 'Periode: ' . $periodeStr);
        $sheet->mergeCells('A3:' . $lastColLetter . '3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $karyawansByDept = $karyawans->groupBy(function ($k) {
            return $k->departemen->nama_departemen ?? 'Tanpa Departemen';
        });

        $row = 5;

        // Loop per Departemen
        foreach ($karyawansByDept as $deptName => $deptKaryawans) {
            // 1. Department Section Header
            $sheet->setCellValue('A' . $row, 'DEPARTEMEN: ' . strtoupper($deptName));
            $sheet->mergeCells('A' . $row . ':' . $lastColLetter . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('065F46'));
            $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E2F0D9');
            $sheet->getRowDimension($row)->setRowHeight(25);
            $sheet->getStyle('A' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setIndent(1);
            $row++;

            // 2. Table Header
            $headers = ['No', 'NIP', 'Nama Karyawan', 'Posisi', 'Departemen'];
            
            $colIdx = 1;
            foreach ($headers as $h) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($colLetter . $row, $h);
                $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $sheet->getStyle($colLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('047857');
                $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $colIdx++;
            }

            // Date Headers
            foreach ($dates as $date) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($colLetter . $row, $date->format('d/m'));
                $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $sheet->getStyle($colLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('047857');
                $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $colIdx++;
            }

            // Summary Headers
            $summaries = ['H', 'A', 'S', 'I', 'L', '-'];
            foreach ($summaries as $s) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($colLetter . $row, $s);
                $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
                $sheet->getStyle($colLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('047857');
                $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $colIdx++;
            }
            
            // Set header border
            $borderHeaderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '047857'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($borderHeaderStyle);
            $sheet->getRowDimension($row)->setRowHeight(20);
            
            $startDataRow = $row + 1;
            $row++;

            // 3. Employee Rows
            $deptH = $deptA = $deptS = $deptI = $deptL = $deptLibur = 0;

            foreach ($deptKaryawans as $index => $karyawan) {
                $nip = ($karyawan->nip ?? null) && (int) $karyawan->nip !== 0
                    ? 'NIP-' . $karyawan->nip
                    : '-';
                
                $sheet->setCellValueByColumnAndRow(1, $row, $index + 1);
                $sheet->setCellValueByColumnAndRow(2, $row, $nip);
                $sheet->setCellValueByColumnAndRow(3, $row, $karyawan->nama_lengkap);
                $sheet->setCellValueByColumnAndRow(4, $row, $karyawan->posisi ?? '-');
                $sheet->setCellValueByColumnAndRow(5, $row, $karyawan->departemen->nama_departemen ?? '-');

                $colIdx = 6;
                $h = $a = $s = $i = $l = $libur = 0;

                foreach ($dates as $date) {
                    $tanggal = $date->format('Y-m-d');
                    $kehadiran = $kehadiranMap[$karyawan->id_user][$tanggal] ?? null;

                    $val = '-';
                    if ($kehadiran) {
                        switch ($kehadiran->tipe_kehadiran_id) {
                            case 1: $val = 'H'; $h++; break;
                            case 2: $val = 'S'; $s++; break;
                            case 3: $val = 'A'; $a++; break;
                            case 4: $val = 'L'; $l++; break;
                            case 5: $val = 'I'; $i++; break;
                            case 6: $val = 'H'; $h++; break;
                            default: $val = '-'; $libur++; break;
                        }
                    } else {
                        $libur++;
                    }

                    $sheet->setCellValueByColumnAndRow($colIdx, $row, $val);
                    $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $colIdx++;
                }

                // Summaries
                $sheet->setCellValueByColumnAndRow($colIdx, $row, $h); $colIdx++;
                $sheet->setCellValueByColumnAndRow($colIdx, $row, $a); $colIdx++;
                $sheet->setCellValueByColumnAndRow($colIdx, $row, $s); $colIdx++;
                $sheet->setCellValueByColumnAndRow($colIdx, $row, $i); $colIdx++;
                $sheet->setCellValueByColumnAndRow($colIdx, $row, $l); $colIdx++;
                $sheet->setCellValueByColumnAndRow($colIdx, $row, $libur); $colIdx++;

                // Department Sums
                $deptH += $h; $deptA += $a; $deptS += $s; $deptI += $i; $deptL += $l; $deptLibur += $libur;

                // Style data row
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                
                // Alternating bg
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F9FAFB');
                }
                
                $row++;
            }
            
            $endDataRow = $row - 1;

            // 4. Department Totals Row
            $sheet->setCellValueByColumnAndRow(1, $row, '');
            $sheet->setCellValueByColumnAndRow(2, $row, '');
            $sheet->setCellValueByColumnAndRow(3, $row, 'TOTAL REKAP ' . strtoupper($deptName));
            $sheet->getStyle('C' . $row)->getFont()->setBold(true);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValueByColumnAndRow(4, $row, '');
            $sheet->setCellValueByColumnAndRow(5, $row, '');

            $colIdx = 6 + count($dates);
            $sheet->setCellValueByColumnAndRow($colIdx, $row, $deptH); $colIdx++;
            $sheet->setCellValueByColumnAndRow($colIdx, $row, $deptA); $colIdx++;
            $sheet->setCellValueByColumnAndRow($colIdx, $row, $deptS); $colIdx++;
            $sheet->setCellValueByColumnAndRow($colIdx, $row, $deptI); $colIdx++;
            $sheet->setCellValueByColumnAndRow($colIdx, $row, $deptL); $colIdx++;
            $sheet->setCellValueByColumnAndRow($colIdx, $row, $deptLibur); $colIdx++;

            // Total row style
            $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('EAEAEA');
            
            // Apply borders to this department's table block
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'D1D5DB'],
                    ],
                ],
            ];
            $sheet->getStyle('A' . $startDataRow . ':' . $lastColLetter . $row)->applyFromArray($borderStyle);
            
            $row += 3; // spacing of 2 empty rows between tables
        }

        for ($i = 1; $i <= $lastColIndex; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Ecogreen_Absensi_' . str_replace(' ', '_', $outsourcingNama) . '_' . $rekapBulan . '.xlsx';

        $downloadToken = $request->input('download_token');
        if ($downloadToken) {
            setcookie('download_token', $downloadToken, time() + 60, '/');
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function kelolaKaryawan()
    {
        return view('adminOutsourcing.kelola-karyawan');
    }

    public function pengajuanAkun()
    {
        return view('adminOutsourcing.pengajuan-akun');
    }

    public function getDepartemen()
    {
        $departemens = \App\Models\Departemen::all();
        return response()->json($departemens);
    }

    public function storeKaryawan(\Illuminate\Http\Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'email' => 'required|email|unique:user,email',
                'nomor_tlp' => 'required|string|max:15',
                'alamat' => 'required|string',
                'nip' => 'required|string',
                'departemen_id' => 'required|exists:departemen,id_departemen',
            ]);

            $user = \App\Models\User::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => bcrypt('admin123'),
                'nomor_tlp' => $validated['nomor_tlp'],
                'alamat' => $validated['alamat'],
                'nip' => 'NIP-' . $validated['nip'],
                'departemen_id' => $validated['departemen_id'],
                'tanggal_keluar' => null,
                'tanggal_masuk' => null,
                'role' => \App\Enums\UserRole::Karyawan->value,
                'status' => \App\Enums\Status::Inactive->value,
                'user_id' => auth()->id(),
                'outsourcing_id' => auth()->user()->outsourcing_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diajukan',
                'data' => $user
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
