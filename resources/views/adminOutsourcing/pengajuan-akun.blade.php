@extends('layouts.admin-outsourcing')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-2">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Pengajuan Akun Karyawan</h2>
        </div>

        {{-- LIVEWIRE COMPONENT --}}
        @livewire(\App\Livewire\AdminOutsourcing\PengajuanAkun::class)

    </div>
@endsection
