@extends('layouts.karyawanOutsourcing')

    {{-- kode CDN untuk leaflet map ini hanya unntuk user karyawan outsorcing --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- kode CDN untuk leaflet map ini hanya unntuk user karyawan outsorcing --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@section('content')
    <!-- CONTENT -->
    <div class="flex-1 p-4 md:p-6 max-w-7xl mx-auto">
        {{-- Card yang menampilkan jadwal karyawan pada hari ini --}}
        <div
            class="bg-linear-to-br from-white to-gray-50 rounded-3xl shadow-md p-6 mb-6 border border-gray-100 relative overflow-hidden">
            <!-- subtle background accent -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-100 rounded-full blur-3xl opacity-30"></div>

            <!-- Bagian header atau judul dari card-->
            <div class="flex items-center justify-between mb-5 relative z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-500/10 text-blue-600 flex items-center justify-center rounded-xl shadow-inner">
                        <i class="fa fa-calendar text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-800 leading-tight">Jadwal Hari Ini</h2>
                        <p class="text-xs text-gray-400">Aktivitas kamu hari ini</p>
                    </div>
                </div>

                {{-- bagian info shift --}}>

            </div>

            <!-- CARD -->
            <div
                class="group p-5 rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm
                hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-hidden">

                <!-- hover glow -->
                <div
                    class="absolute inset-0 bg-linear-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition">
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-base">Shift Pagi</h3>
                        <p class="text-sm text-gray-500 mt-1">08:00 - 17:00</p>
                    </div>


                </div>
            </div>

        </div>

        {{-- menampilkan status absensi masuk dan absensi keluar --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

            <!-- card absensi masuk -->
            <div
                class="group relative bg-linear-to-br from-white to-emerald-50/40 p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">

                <!-- glow -->
                <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-200 opacity-20 blur-3xl"></div>

                <div class="relative z-10 flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Absen Masuk</p>
                        <p id="tanggalMasuk" class="font-semibold text-gray-800 text-sm mt-1"></p>
                    </div>

                    <div
                        class="w-11 h-11 bg-emerald-100 text-emerald-600 flex items-center justify-center rounded-2xl shadow-inner group-hover:scale-105 transition">
                        <i class="fas fa-sign-in-alt text-sm"></i>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="flex items-center justify-between">
                    <p id="statusMasuk" class="text-sm text-gray-400">
                        Belum absen masuk hari ini
                    </p>


                </div>

                <!-- bottom accent -->
                <div class="absolute bottom-0 left-0 h-1 w-0 bg-emerald-500 group-hover:w-full transition-all duration-300">
                </div>
            </div>


            <!-- card absensi keluar -->
            <div
                class="group relative bg-linear-to-br from-white to-red-50/40 p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">

                <!-- glow -->
                <div class="absolute -top-10 -right-10 w-28 h-28 bg-red-200 opacity-20 blur-3xl"></div>

                <div class="relative z-10 flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Absen Keluar</p>
                        <p id="tanggalKeluar" class="font-semibold text-gray-800 text-sm mt-1"></p>
                    </div>

                    <div
                        class="w-11 h-11 bg-red-100 text-red-600 flex items-center justify-center rounded-2xl shadow-inner group-hover:scale-105 transition">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="flex items-center justify-between">
                    <p id="statusKeluar" class="text-sm text-gray-400">
                        Belum absen keluar hari ini
                    </p>


                </div>

                <!-- bottom accent -->
                <div class="absolute bottom-0 left-0 h-1 w-0 bg-red-500 group-hover:w-full transition-all duration-300">
                </div>
            </div>

        </div>

        {{-- mengecek lokasi --}}
        <div
            class="relative bg-linear-to-br from-white to-gray-50 rounded-3xl border border-gray-100 shadow-md p-6 space-y-5 overflow-hidden">

            <!-- subtle glow -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-100 blur-3xl opacity-30"></div>

            <!-- HEADER -->
            <div class="relative z-10 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800 text-lg">Form Absensi</h2>
                <span class="text-xs bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full font-medium">
                    Hari Ini
                </span>
            </div>

            <!-- BUTTON MASUK / KELUAR -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 relative z-10">

                {{-- button dengan aksi absen masuk untuk melakukan absensi masuk kerja --}}
                {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion absenMasuk --}}
                <button id="btnMasuk" onclick="absenMasuk()"
                    class="group flex items-center justify-center gap-2 bg-emerald-600 text-white py-2.5 rounded-xl font-medium shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
                    <i class="fas fa-sign-in-alt text-sm group-hover:scale-110 transition"></i>
                    Absen Masuk
                </button>

                {{-- button dengan aksi absen keluar untuk melakukan absensi keluar kerja --}}
                {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion absenKeluar --}}
                <button id="btnKeluar" onclick="absenKeluar()"
                    class="group flex items-center justify-center gap-2 bg-gray-100 text-gray-700 py-2.5 rounded-xl font-medium hover:shadow-md hover:-translate-y-0.5  transition">
                    <i class="fas fa-sign-out-alt text-sm group-hover:scale-110 transition"></i>
                    Absen Keluar
                </button>
            </div>

            <!-- WAKTU -->
            <div class="relative z-10">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu</label>
                <div class="relative mt-1">
                    <input id="waktu" type="text"
                        class="w-full border border-gray-200 rounded-xl p-3 pl-10 bg-white focus:ring-2 focus:ring-emerald-500 outline-none text-sm"
                        readonly>
                    <i class="fas fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </div>

            <!-- LOKASI -->
            <div class="relative z-10 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Lokasi GPS</label>

                    <!-- BUTTON AMBIL LOKASI -->
                    {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion ambilLokasi --}}
                    <button onclick="ambilLokasi()"
                        class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-medium hover:bg-blue-200 transition flex items-center gap-1">
                        <i class="fas fa-location-arrow text-[10px]"></i>
                        Ambil Lokasi
                    </button>
                </div>

                <div id="map" class="w-full h-52 rounded-2xl border border-gray-100 overflow-hidden shadow-inner">
                </div>

                <!-- info lokasi -->
                {{-- mendapatkan data info lokasi --}}
                <p id="infoLokasi" class="text-xs text-gray-400">
                    Lokasi belum diambil
                </p>
            </div>

            <!-- SUBMIT -->
            {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion simpanAbsensi --}}
            <button onclick="simpanAbsensi()"
                class="relative z-10 w-full bg-emerald-600 text-white py-3 rounded-2xl font-semibold shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition flex items-center justify-center gap-2">
                <i class="fas fa-save text-sm"></i>
                Simpan Absensi
            </button>

        </div>

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
    <script src="{{ asset('js/karyawan_js/dashboard.js') }}"></script>
@endsection
