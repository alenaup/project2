@extends('layouts.karyawanOutsourcing')

@push('styles')
    {{-- kode CDN untuk leaflet map ini hanya unntuk user karyawan outsorcing --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- kode CDN untuk leaflet map ini hanya unntuk user karyawan outsorcing --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <!-- CONTENT -->
    <div class="flex-1 p-4 md:p-6 max-w-7xl mx-auto">
        {{-- Card yang menampilkan jadwal karyawan pada hari ini --}}
        <livewire:karyawan.dashboard-karyawan/>

        {{-- menampilkan status absensi masuk dan absensi keluar --}}
        {{-- mengecek lokasi --}}
        <livewire:karyawan.dashboard-absensi/>

        <!-- GRAFIK (LIVEWIRE) -->
        <livewire:karyawan.grafik-rekap-kehadiran />

    </div>

@endsection
@push('scripts')
    {{-- kode CDN untuk leaflet map ini hanya unntuk user karyawan outsorcing --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/karyawan_js/dashboard.js') }}"></script>
    {{-- kode CDN untuk chart.js ini hanya unntuk user karyawan outsorcing --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
