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
        <livewire:Karyawan.dashboard/>
        </div>
        {{-- menampilkan status absensi masuk dan absensi keluar --}}
        

        {{-- mengecek lokasi --}}
        <livewire:Karyawan.dashboardAbsensi/> 

        <!-- GRAFIK -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-gray-800">Rekap Kehadiran</h2>
                <select class="border rounded-lg px-3 py-1 text-sm">
                    <option>2026</option>
                    <option>2025</option>
                </select>
            </div>

            <div class="w-full overflow-x-auto">
                <div class="min-w-200 h-100">
                    <canvas id="grafikAbsensi"></canvas>
                </div>
            </div>
        </div>

    </div>
    
@endsection
@push('scripts')
    {{-- kode CDN untuk leaflet map ini hanya unntuk user karyawan outsorcing --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/karyawan_js/dashboard.js') }}"></script>
    {{-- kode CDN untuk chart.js ini hanya unntuk user karyawan outsorcing --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush