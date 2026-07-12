@extends('layouts.hr')

@section('content')
    <div class="mt-8 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                <i class="fa-solid fa-file-signature text-base"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Pengajuan Data Karyawan Outsourcing</h2>
                <p class="text-xs text-gray-400">Verifikasi dan setujui pengajuan pendaftaran akun karyawan baru</p>
            </div>
        </div>
    </div>

    @livewire(\App\Livewire\Hr\AjuanDataKaryawan::class)
@endsection
