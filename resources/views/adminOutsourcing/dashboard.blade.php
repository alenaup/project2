{{-- Mengambil data dari layout untuk tamplating halaman adminOutsourcing --}}
@extends('layouts.admin-outsourcing')

{{-- mengambil style dari halaman adminOutsourcing dashboard --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-outsourcing/dashboard.css') }}">
@endPush

{{-- mengisi konten halaman adminOutsourcing --}}
@section('content')
    <div class="flex flex-col gap-4 overflow-y-auto">
        <!-- ───  TABEL REKAP ────────────────────── -->
        @livewire('admin-outsourcing.rekapan-karyawan')

        <!-- ─── CHART ──────────────────────────── -->
        @livewire('admin-outsourcing.grafik')
    </div><!-- /content -->
@endsection

{{-- mengambil script untuk halaman adminOutsourcing dashboard dari folder public js --}}
@push('scripts')
    <script src="{{ asset('js/admin-outsourcing/dashboard.js') }}"></script>
    {{-- kode CDN untuk chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
@endpush
