@extends('layouts.karyawanOutsourcing')

<link rel="stylesheet" href="{{ asset('css/karyawan/pengajuan.css') }}">

@section('content')
    <!-- PAGE CONTENT -->   
    <div class="space-y-6">

        <!-- Form Pengajuan Lembur Component -->
        <livewire:Karyawan.form-pengajuan-lembur />

        <!-- Tabel Riwayat Pengajuan Lembur Component -->
        <livewire:Karyawan.tabel-pengajuan-lembur />

    </div>
@endsection
