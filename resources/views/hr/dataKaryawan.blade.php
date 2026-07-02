@extends('layouts.hr')

@section('content')
    <x-hr.filter-data-karyawan></x-hr.filter-data-karyawan>

    <div class="mt-8 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                <i class="fa-solid fa-users text-base"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Data Karyawan Outsourcing</h2>
                <p class="text-xs text-gray-400">Daftar seluruh karyawan outsourcing aktif yang terdaftar di sistem</p>
            </div>
        </div>
    </div>

    <x-hr.tabel-karyawan></x-hr.tabel-karyawan>
@endsection
