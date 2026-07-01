
@extends('layouts.hr')

@section('content')
    <div class="animate-bitem flex flex-col md:flex-row justify-between items-start md:items-center border border-emerald-100/60 bg-gradient-to-r from-emerald-50/50 via-teal-50/20 to-transparent p-6 rounded-2xl mb-6 gap-4 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent flex items-center gap-2">
                <i class="fas fa-chart-pie text-emerald-600"></i> Dashboard HR & Monitoring Lembur
            </h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Pantau kehadiran, kelola persetujuan lembur, hitung rekapitulasi pembayaran, dan pantau aktivitas karyawan outsourcing secara terpusat.
            </p>
        </div>
        <div class="bg-white border border-emerald-100/80 px-4 py-2 rounded-xl shadow-xs flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <div>
                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Mode Akses</span>
                <span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5 mt-0.5">
                    <i class="fa-solid fa-user-shield"></i> HR Outsourcing
                </span>
            </div>
        </div>
    </div>
    <livewire:hr.dashboard />
@endsection
