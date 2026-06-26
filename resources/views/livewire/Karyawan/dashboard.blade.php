{{-- BAGIAN YANG MENAMPILKAN INFO DARI JADWAL KARYAWAN --}}
<div
    class="animate-item bg-linear-to-br from-white to-gray-50 rounded-3xl shadow-md p-6 mb-6 border border-gray-100 relative overflow-hidden">
    <!-- subtle background accent -->
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-100 rounded-full blur-3xl opacity-30"></div>
    <!-- Bagian header atau judul dari card-->
    <div class="flex items-center justify-between mb-5 relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-500/10 text-blue-600 flex items-center justify-center rounded-xl shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6.75 3v2.25M17.25 3v2.25M3.75 7.5h16.5M5.25 5.25h13.5A1.5 1.5 0 0 1 21 6.75v12A1.5 1.5 0 0 1 19.5 21h-15A1.5 1.5 0 0 1 3 18.75v-12A1.5 1.5 0 0 1 5.25 5.25Z" />
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-800 leading-tight">Jadwal Hari Ini</h2>
                <p class="text-xs text-gray-400">Aktivitas kamu hari ini</p>
            </div>
        </div>
    </div>

    {{-- BAGIAN YANG MENAMPILKAN KETERANGAN KEHADIRAN KARYAWAN --}}
    {{-- Card Status Kehadiran --}}

    @if (($status_kehadiran === false && $tipe_kehadiran === null) || $waktu_masuk)
        <div
            class="animate-item group p-5 rounded-2xl border border-gray-100 bg-white/80 backdrop-blur-sm mb-2
                hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-hidden">
            <!-- hover glow -->
            <div
                class="absolute inset-0 bg-linear-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition">
            </div>
            <div class="relative z-10 flex items-center justify-between">
                @if ($jadwal && $jadwal->shift)
                    <div>
                        <h3 class="font-semibold text-gray-800 text-base">Shift {{ $jadwal->shift->nama_shift }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($jadwal->shift->jam_masuk)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($jadwal->shift->jam_keluar)->format('H:i') }}
                        </p>
                    </div>
                @else
                    <div>
                        <h3 class="font-semibold text-gray-800 text-base">Tidak Ada Jadwal</h3>
                        <p class="text-sm text-gray-500 mt-1">Hari ini Anda bebas tugas / libur</p>
                    </div>
                @endif
            </div>
        </div>
        @if ($jadwal && $jadwal->shift)
            <div class="animate-bitem grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

                <!-- card absensi masuk -->
                <div
                    class="group relative bg-linear-to-br from-white to-emerald-50/40 p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">

                    <!-- glow -->
                    <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-200 opacity-20 blur-3xl"></div>
                    <!-- STATUS -->
                    @if ($status_kehadiran === true && $waktu_masuk)
                        <div class="relative z-10 flex items-start justify-between mb-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Anda sudah melakukan Absensi
                                    Masuk
                                </p>
                                <div class="flex items-center justify-between">
                                    <p id="statusKeluar" class="text-sm text-gray-400">
                                        {{ $waktu_masuk }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="w-11 h-11 bg-emerald-100 text-emerald-600 flex items-center justify-center rounded-2xl shadow-inner group-hover:scale-105 transition">
                                <i class="fas fa-sign-in-alt text-sm"></i>
                            </div>
                        </div>
                    @else
                        <div class="relative z-10 flex items-start justify-between mb-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Absensi Masuk</p>
                                <p class="font-semibold text-gray-800 text-sm mt-1">
                                    {{ \Carbon\Carbon::parse($jadwal->shift->jam_masuk)->format('H:i') }}
                                </p>
                            </div>

                            <div
                                class="w-11 h-11 bg-emerald-100 text-emerald-600 flex items-center justify-center rounded-2xl shadow-inner group-hover:scale-105 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M18 12H9m9 0-3-3m3 3-3 3" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p id="statusMasuk" class="text-sm text-gray-400">
                                Belum absen masuk hari ini
                            </p>
                        </div>
                    @endif

                    <!-- bottom accent -->
                    <div
                        class="absolute bottom-0 left-0 h-1 w-0 bg-emerald-500 group-hover:w-full transition-all duration-300">
                    </div>
                </div>
                <!-- card absensi keluar -->
                <div
                    class="group relative bg-linear-to-br from-white to-red-50/40 p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">

                    <!-- glow -->
                    <div class="absolute -top-10 -right-10 w-28 h-28 bg-red-200 opacity-20 blur-3xl"></div>
                    <!-- STATUS -->
                    @if ($status_kehadiran === true && $waktu_keluar)
                        <div class="relative z-10 flex items-start justify-between mb-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Anda sudah melakukan Absensi
                                    Keluar
                                </p>
                                <div class="flex items-center justify-between">
                                    <p id="statusKeluar" class="text-sm text-gray-400">
                                        {{ $waktu_keluar }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="w-11 h-11 bg-red-100 text-red-600 flex items-center justify-center rounded-2xl shadow-inner group-hover:scale-105 transition">
                                <i class="fas fa-sign-out-alt text-sm"></i>
                            </div>
                        </div>
                    @else
                        <div class="relative z-10 flex items-start justify-between mb-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Absensi Keluar</p>
                                <p class="font-semibold text-gray-800 text-sm mt-1">
                                    {{ \Carbon\Carbon::parse($jadwal->shift->jam_keluar)->format('H:i') }}
                                </p>
                            </div>

                            <div
                                class="w-11 h-11 bg-red-100 text-red-600 flex items-center justify-center rounded-2xl shadow-inner group-hover:scale-105 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9m9 0-3-3m3 3-3 3" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p id="statusKeluar" class="text-sm text-gray-400">
                                Belum absen keluar hari ini
                            </p>
                        </div>
                    @endif

                    <!-- bottom accent -->
                    <div
                        class="absolute bottom-0 left-0 h-1 w-0 bg-red-500 group-hover:w-full transition-all duration-300">
                    </div>
                </div>

            </div>
        @else
            <x-karyawan.dashboard.libur-info-card />
        @endif
    @elseif ($status_kehadiran === true && $tipe_kehadiran === 'Cuti')
        <x-karyawan.dashboard.cuti-info-card />
    @elseif ($status_kehadiran === true && $tipe_kehadiran === 'Sakit')
        <x-karyawan.dashboard.sakit-info-card />
    @elseif ($status_kehadiran === true && $tipe_kehadiran === 'Izin')
        <x-karyawan.dashboard.izin-info-card />
    @elseif ($status_kehadiran === true && $tipe_kehadiran === 'Mankir')
        <x-karyawan.dashboard.mankir-info-card />
    @elseif ($status_kehadiran === true && $tipe_kehadiran === 'Terlambat')
        <x-karyawan.dashboard.terlambat-info-card />
    @endif
    <!-- CARD -->

</div>
