@extends('layouts.karyawanOutsourcing')

<link rel="stylesheet" href="{{ asset('css/karyawan/pengajuan.css') }}">

@section('content')
    <!-- PAGE CONTENT -->
    <div class="space-y-6">

        <!-- Page title -->
        <div class="fade-in-up">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Pengajuan Lembur ⏱️</h1>
            <p class="text-sm text-gray-500 mt-0.5">Isi form pengajuan lembur atau lihat riwayat pengajuan</p>
        </div>

        <!-- Form Pengajuan Lembur Component -->
        <livewire:karyawan.form-pengajuan-lembur />

        <!-- Tabel Riwayat Pengajuan Lembur Component -->
        <livewire:karyawan.tabel-pengajuan-lembur />

    </div>
@endsection
