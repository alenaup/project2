@extends('layouts.karyawanOutsourcing')

@section('content')
        <!-- MAIN CONTENT -->
        <div class="flex-1 p-4 md:p-6 max-w-7xl mx-auto">

            <!-- PAGE TITLE -->
            <div class="mb-6">
                <h1 class="text-xl font-bold text-gray-800 md:text-2xl">
                    Perizinan Sakit <i class="fa-solid fa-notes-medical text-emerald-600 ml-1"></i>
                </h1>
                <p class="text-gray-500 text-sm">Unggah dan kelola surat keterangan sakit kamu di sini</p>
            </div>

            <!-- FORM PENGAJUAN (LIVEWIRE) -->
            <livewire:karyawan.form-perizinan-sakit />

            <!-- RIWAYAT PENGAJUAN (LIVEWIRE) -->
            <livewire:karyawan.riwayat-perizinan-sakit />

        </div>
@endsection
