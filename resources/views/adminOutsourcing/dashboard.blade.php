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
            @livewire(\App\Livewire\AdminOutsourcing\DashboardStats::class)
        </div>

        <!-- ───  TABEL REKAP ────────────────────── -->
        <div class="animate-item bg-white border border-slate-200 rounded-xl shadow-sm p-5">
            <div class="flex items-start justify-between mb-4 flex-wrap gap-3 border-b border-slate-400 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-slate-500 text-xs"></i>
                        <h2 class="text-sm font-bold text-slate-800">Rekapan Detail Karyawan per Bulan</h2>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">Bulan</label>
                            <input type="month" name="filterBulan" id="filterBulan"
                                class="w-full sm:w-40 border border-gray-400 rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer">
                        </div>
                        <div class="flex items-center gap-2 mt-2 bottom-0">
                            <button onclick="renderTable()"
                                class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i> Tampilkan
                            </button>
                            <button onclick="document.getElementById('filterBulan').value='3-31'; renderTable()"
                                class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                                <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4">

                    {{-- button export excel --}}
                    <button
                        class="inline-flex items-center gap-1.5 bg-brand hover:bg-brand-dark text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>

            {{-- menggunakan komponent tabel rekap absensi dan mengambil data dummy dari array controller adminOutsourcing --}}
            <x-tabel-rekap-absensi :koloms="range(1, 31)" :datas="$datas" />

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100 flex-wrap gap-3">
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span>Status rekap:</span>
                    <span
                        class="inline-flex items-center gap-1.5 bg-yellow-50 border border-yellow-300 text-yellow-700 font-semibold text-xs px-3 py-1.5 rounded-full">
                        <i class="fa-solid fa-clock text-[10px]"></i> Menunggu Persetujuan
                    </span>
                    <span id="badgeBulan"
                        class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-300 font-medium">
                        Maret 2026
                    </span>
                </div>

            </div>
        </div>

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
