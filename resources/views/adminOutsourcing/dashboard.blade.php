{{-- Mengambil data dari layout untuk tamplating halaman adminOutsourcing --}}
@extends('layouts.admin-outsourcing')

{{-- mengambil style dari halaman adminOutsourcing dashboard --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-outsourcing/dashboard.css') }}">
@endPush

{{-- mengisi konten halaman adminOutsourcing --}}
@section('content')
    <div class="flex flex-col gap-4 overflow-y-auto">

        {{-- Bagian statistik karyawan — menggunakan Livewire (terhubung ke database) --}}
        <div>
            @livewire('admin-outsourcing.dashboard-stats')
        </div> 

        <!-- ───  TABEL REKAP ────────────────────── -->
        @livewire('admin-outsourcing.rekapan-karyawan')

        <!-- ─── CHART ──────────────────────────── -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <!-- Header chart -->
            <div class="flex items-center justify-between mb-2 flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-800">Rekap Kehadiran Tahunan
                        <div>
                            <select id="filterTahunChart" name="filterTahunChart"
                                class="w-full sm:w-40 border border-gray-400 rounded-lg px-3 py-2 text-sm text-gray-500 focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm">
                                <option value="">Pilih Tahun</option>
                                <option value="2022">2022</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2024">2025</option>
                                <option value="2024">2026</option>
                            </select>
                        </div>
                    </h2>

                </div>
                <div class="flex items-center gap-4">
                    <!-- Legend -->
                    <div class="flex items-center gap-3 text-[11px] text-slate-500">
                        <span class="flex items-center gap-1.5"><span
                                class="inline-block w-2.5 h-2.5 rounded-sm bg-green-600"></span>Hadir</span>
                        <span class="flex items-center gap-1.5"><span
                                class="inline-block w-2.5 h-2.5 rounded-sm bg-red-500"></span>Alpha</span>
                        <span class="flex items-center gap-1.5"><span
                                class="inline-block w-2.5 h-2.5 rounded-sm bg-yellow-500"></span>Sakit/Izin</span>
                        <span class="flex items-center gap-1.5"><span
                                class="inline-block w-2.5 h-2.5 rounded-sm bg-purple-500"></span>Lembur</span>
                    </div>
                </div>
            </div>

            {{-- media tempat untuk chart --}}
            <div class="relative w-full">
                <canvas id="chartRekap"></canvas>
            </div>
        </div>
    </div><!-- /content -->
@endsection

{{-- mengambil script untuk halaman adminOutsourcing dashboard dari folder public js --}}
@push('scripts')
    <script src="{{ asset('js/admin-outsourcing/dashboard.js') }}"></script>
    {{-- kode CDN untuk chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
@endpush
