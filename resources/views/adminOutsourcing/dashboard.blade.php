{{-- Mengambil data dari layout untuk tamplating halaman adminOutsourcing --}}
@extends('layouts.admin-outsourcing')

{{-- mengambil style dari halaman adminOutsourcing dashboard --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-outsourcing/dashboard.css') }}">
@endPush

{{-- mengisi konten halaman adminOutsourcing --}}
@section('content')
    <div class="animate-bitem flex flex-col md:flex-row justify-between items-start md:items-center border border-emerald-100/60 bg-gradient-to-r from-emerald-50/50 via-teal-50/20 to-transparent p-6 rounded-2xl mb-6 gap-4 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent flex items-center gap-2">
                <i class="fas fa-building text-emerald-600"></i> Dashboard Admin Outsourcing
            </h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Pantau rekapitulasi absensi karyawan, kirim ajuan rekap bulanan, dan tinjau status kehadiran karyawan outsourcing Anda.
            </p>
        </div>
        <div class="bg-white border border-emerald-100/80 px-4 py-2 rounded-xl shadow-xs flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <div>
                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Mode Akses</span>
                <span class="text-xs font-bold text-emerald-700 flex items-center gap-1.5 mt-0.5">
                    <i class="fa-solid fa-user-tie"></i> Admin Outsourcing
                </span>
            </div>
        </div>
    </div>
    <div class="flex flex-col gap-4 overflow-y-auto">
        <!-- ───  TABEL REKAP ────────────────────── -->
        <livewire:admin-outsourcing.rekapan-karyawan/>

        <!-- ─── CHART ──────────────────────────── -->
        <livewire:admin-outsourcing.grafik/>
    </div><!-- /content -->
@endsection

{{-- mengambil script untuk halaman adminOutsourcing dashboard dari folder public js --}}
@push('scripts')
    <script src="{{ asset('js/admin-outsourcing/dashboard.js') }}"></script>
    {{-- kode CDN untuk chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
@endpush
