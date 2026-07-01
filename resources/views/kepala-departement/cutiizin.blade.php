@extends('layouts.kepala-departement')

@section('content')
    <div class="animate-bitem flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-200/60 pb-5 mb-6 gap-4 bg-gradient-to-r from-emerald-100 p-6 rounded-xl">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Daftar Cuti & Izin Karyawan
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Berikut adalah daftar pengajuan cuti, izin, dan sakit karyawan yang telah diajukan di departemen Anda. Anda dapat meninjau
            </p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm border border-slate-200 px-4 py-2 rounded-xl shadow-xs">
            <span class="text-xs text-slate-450 font-semibold uppercase tracking-wider block">Daftar Pengajuan</span>
            {{-- <span class="text-sm font-extrabold text-emerald-700 flex items-center gap-1.5 mt-0.5">
                <i class="fa-solid fa-building text-emerald-600"></i>
                {{ $departemen->nama_departemen ?? 'Tidak Terkait Departemen' }}
            </span> --}}
        </div>
    </div>
    <livewire:kepala-departemen.laporan-cuti-izin />
@endsection
