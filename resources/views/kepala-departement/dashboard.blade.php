@extends('layouts.kepala-departement')

@push('styles')
    <link rel="stylesheet" href="/css/alert.css">
    <script defer src="/js/kepala-departemen/dashboard.js"></script>
@endpush

@section('content')
    {{-- TITLE & ACTIONS --}}
    <div x-data="{ ...dashboard(), open: false }">
        <div class='animate-item flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-200/60 pb-5 mb-6 gap-4 bg-gradient-to-r from-emerald-100 p-6 rounded-xl'>
            <div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent flex items-center gap-2">
                    <i class="fa-solid fa-calendar-week text-emerald-600"></i> Penjadwalan Mingguan Karyawan
                </h1>
                <p class="text-xs text-slate-500 mt-1">Kelola pembagian shift kerja karyawan secara efisien dalam kurun waktu tertentu.</p>
            </div>
            <div class='flex items-center gap-3 w-full md:w-auto' x-data="{ isExportOpen: false, exportMonth: new Date().getMonth() + 1, exportYear: new Date().getFullYear() }">
                <!-- Ekspor Jadwal Button -->
                <button @click="isExportOpen = true"
                    class="flex-1 md:flex-none px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold border border-slate-200 rounded-xl shadow-xs active:scale-95 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-print text-sm"></i> Ekspor Jadwal
                </button>

                <!-- Tambah Jadwal Button -->
                <button @click="openModal()"
                    class="flex-1 md:flex-none px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-xl shadow-md shadow-emerald-500/10 hover:shadow-lg hover:shadow-emerald-500/20 active:scale-95 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Jadwal
                </button>

                <!-- MODAL PILIH BULAN EKSPOR -->
                <div x-show="isExportOpen" style="display: none;"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">

                    <div @click.outside="isExportOpen = false"
                        class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 overflow-hidden text-left">

                        <div class="flex justify-between items-center mb-5 pb-2 border-b border-slate-100">
                            <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-file-excel text-emerald-500 text-base"></i>
                                Ekspor Jadwal Bulanan
                            </h3>
                            <button @click="isExportOpen = false" class="text-slate-400 hover:text-slate-650 transition text-sm">✕</button>
                        </div>

                        <div class="space-y-4">
                            <!-- Pilih Bulan -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wide">Pilih Bulan</label>
                                <select x-model="exportMonth" class="w-full mt-1 px-3 py-2 border border-slate-200 rounded-xl outline-none text-xs font-semibold text-slate-700 bg-white">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>

                            <!-- Pilih Tahun -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-450 uppercase tracking-wide">Pilih Tahun</label>
                                <select x-model="exportYear" class="w-full mt-1 px-3 py-2 border border-slate-200 rounded-xl outline-none text-xs font-semibold text-slate-700 bg-white">
                                    @php
                                        $currentYear = date('Y');
                                    @endphp
                                    @for($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Footer / Aksi -->
                        <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-slate-150">
                            <button type="button" @click="isExportOpen = false"
                                class="px-4 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-250 transition text-xs font-bold">
                                Batal
                            </button>
                            <a :href="'/kepala-departement/export-jadwal?month=' + exportMonth + '&year=' + exportYear"
                                @click="isExportOpen = false"
                                class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-xs font-bold flex items-center gap-1.5 shadow-xs shadow-emerald-500/10">
                                <i class="fa-solid fa-download"></i> Unduh Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div @jadwal-imported.window="fetchData(currentPage); fetchSummary();" class="hidden"></div>

        <livewire:components.excel-importer
            templatePath="kepala-departement/download-template-jadwal"
            importClass="App\Imports\JadwalsImport"
            buttonLabel="Impor Jadwal"
            onSuccessEvent="jadwal-imported"
        />

        {{-- CARDS SUMMARY --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Card 1: Karyawan -->
            <div class="animate-bitem bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 shadow-lg shadow-slate-100/50 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</span>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-2" x-text="summary.totalKaryawan">0</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-4 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                    Karyawan aktif di departemen ini
                </p>
            </div>

            <!-- Card 2: Hadir -->
            <div class="animate-bitem bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 shadow-lg shadow-slate-100/50 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Absensi Hari Ini</span>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-2" x-text="summary.hadir">0</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-user-check text-sm"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-4 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Hadir & tepat waktu hari ini
                </p>
            </div>

            <!-- Card 3: Terlambat -->
            <div class="animate-bitem bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 shadow-lg shadow-slate-100/50 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Terlambat</span>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-2" x-text="summary.terlambat">0</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-4 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Terlambat datang hari ini
                </p>
            </div>

            <!-- Card 4: Izin/Cuti -->
            <div class="animate-bitem bg-white/80 backdrop-blur-md rounded-2xl border border-slate-100 shadow-lg shadow-slate-100/50 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-500/5 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-rose-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Izin / Cuti / Sakit</span>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-2" x-text="summary.izinCuti">0</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center shadow-xs group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-user-xmark text-sm"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-4 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                    Tidak hadir/izin hari ini
                </p>
            </div>
        </div>

        {{-- TABLE PENJADWALAN --}}
        <div class="bg-white/90 backdrop-blur-md rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 p-6 mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <!-- Navigasi tanggal -->
                <div class="flex items-center gap-3">
                    <button @click="prevWeek()"
                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/60 text-slate-600 transition hover:shadow-xs cursor-pointer active:scale-95">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>

                    <h2 class="font-bold text-slate-800 text-lg min-w-[220px] text-center" x-text="currentWeek"></h2>

                    <button @click="nextWeek()"
                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/60 text-slate-600 transition hover:shadow-xs cursor-pointer active:scale-95">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Hari Ini
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-slate-200 ml-2"></span> Kemarin / Lampau (Terkunci)
                </div>
            </div>

            <div class="relative">
                {{-- LOADING OVERLAY FOR TABLE --}}
                <div x-show="isLoading" class="absolute inset-0 bg-white/70 z-30 flex flex-col items-center justify-center backdrop-blur-xs transition-opacity duration-200">
                    <div class="flex flex-col items-center">
                        <svg class="animate-spin h-10 w-10 text-emerald-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-emerald-700 animate-pulse font-bold">Memuat Jadwal Kerja...</span>
                    </div>
                </div>

                <div class="border border-slate-100 rounded-2xl overflow-hidden shadow-inner bg-slate-50/20 text-sm overflow-x-auto w-full mb-4">
                    <div class="grid grid-cols-8 min-w-[900px]">
                        <div class="bg-slate-50/80 p-4 font-bold text-slate-500 border-b border-slate-100 flex items-center justify-center">KARYAWAN</div>
                        <template x-for="d in days">
                            <div class="bg-slate-50/80 p-3 text-center border-b border-slate-100 border-l border-slate-100/60 relative"
                                :class="d.active ? 'bg-emerald-50/40' : ''">
                                <span class="text-xs font-semibold text-slate-400 uppercase" x-text="d.day"></span><br>
                                <span class="inline-block mt-1 w-7 h-7 line-height-[28px] rounded-full text-sm font-bold transition duration-300"
                                    :class="d.active ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'text-slate-700'"
                                    x-text="d.date"></span>
                                <template x-if="d.active">
                                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                </template>
                            </div>
                        </template>

                        <template x-for="emp in employees">
                            <div class="contents">

                                <!-- Karyawan Column -->
                                <div @click="openModal(emp.id, days[0].date_full, days[6].date_full)"
                                    class="p-3.5 flex items-center gap-3 border-t border-slate-100 hover:bg-slate-50/70 transition cursor-pointer font-medium text-slate-800">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center text-xs font-bold shadow-xs"
                                        x-text="emp.initials"></div>

                                    <div class="truncate">
                                        <div class="font-semibold text-slate-700 text-sm" x-text="emp.name"></div>
                                        <div class="text-[10px] text-slate-400 font-normal uppercase tracking-wider" x-text="emp.role"></div>
                                    </div>
                                </div>

                                <!-- Shift Days Column -->
                                <template x-for="(shift, index) in emp.shifts">
                                    <div @click="isPastDate(days[index].date_full) ? window.dispatchEvent(new CustomEvent('flash-error', { detail: { message: 'Tidak dapat mengubah atau membuat jadwal untuk hari yang sudah lewat.' } })) : openModal(emp.id, days[index].date_full, days[index].date_full)"
                                        class="p-3 border-t border-l border-slate-100 flex items-center justify-center min-h-[70px] transition-all duration-200"
                                        :class="isPastDate(days[index].date_full) ? 'bg-slate-50/40 cursor-not-allowed opacity-50' : 'hover:bg-slate-50/30 cursor-pointer'">

                                        <!-- Kalau tidak ada shift -->
                                        <template x-if="!shift">
                                            <div class="text-slate-350 text-sm hover:text-emerald-500 transition font-bold w-full h-full flex items-center justify-center">
                                                <template x-if="!isPastDate(days[index].date_full)">
                                                    <span class="w-7 h-7 flex items-center justify-center rounded-lg border border-dashed border-slate-200 text-slate-400 hover:border-emerald-350 hover:bg-emerald-50/50 hover:text-emerald-500 transition-all duration-205">+</span>
                                                </template>
                                                <template x-if="isPastDate(days[index].date_full)">
                                                    <span class="text-slate-300">-</span>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Kalau ada -->
                                        <template x-if="shift">
                                            <div class="px-2.5 py-1.5 rounded-xl text-xs text-center w-full shadow-xs transition duration-200"
                                                :class="shiftClass(shift)">

                                                <div class="font-bold capitalize text-[10px]" x-text="shift"></div>
                                                <div class="text-[9px] font-medium opacity-85 mt-1">
                                                    <template x-if="shift === 'pagi'"><span>06.00</span></template>
                                                    <template x-if="shift === 'siang'"><span>14.00</span></template>
                                                    <template x-if="shift === 'sore'"><span>15.00</span></template>
                                                    <template x-if="shift === 'malam'"><span>22.00</span></template>
                                                    <template x-if="shift === 'libur'"><span>Libur</span></template>
                                                </div>

                                            </div>
                                        </template>

                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Paginasi -->
            <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100 px-2">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage <= 1"
                    class="px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl disabled:opacity-40 transition cursor-pointer flex items-center gap-2 text-xs font-semibold">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Prev
                </button>

                <span class="text-xs text-slate-550 font-medium">
                    Halaman <span class="font-bold text-slate-700" x-text="currentPage"></span> dari <span class="font-bold text-slate-700" x-text="lastPage"></span>
                </span>

                <button @click="changePage(currentPage + 1)" :disabled="currentPage >= lastPage"
                    class="px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-xl disabled:opacity-40 transition cursor-pointer flex items-center gap-2 text-xs font-semibold">
                    Next <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>

        <!-- Modal Tambah Jadwal -->
        <div x-show="isModalOpen" style="display: none;"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">

            <div @click.outside="closeModal()" x-show="isModalOpen"
                class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 overflow-visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-plus text-emerald-500"></i> Tambah Jadwal Shift
                    </h2>
                    <button @click="closeModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-500 transition cursor-pointer border border-slate-200">
                        ✕
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitSchedule" class="space-y-4">
                    <!-- Karyawan -->
                    <div class="space-y-1">
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Karyawan</label>
                            <button type="button" @click="toggleSelectAllEmployees()"
                                class="text-[11px] text-emerald-600 hover:text-emerald-800 font-bold transition flex items-center gap-1 cursor-pointer"
                                x-text="allEmployeesSelected() ? '✕ Batalkan Semua' : '✓ Pilih Semua'">
                            </button>
                        </div>
                        <!-- Search Karyawan -->
                        <div class="relative">
                            <input type="text" x-model="employeeSearchQuery" placeholder="Cari nama karyawan..."
                                class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none text-sm transition bg-white">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </div>
                        <!-- Checkbox List Karyawan -->
                        <div class="border border-slate-100 rounded-2xl p-2.5 max-h-40 overflow-y-auto bg-slate-50/50 mt-2 space-y-1 shadow-inner">
                            <template x-for="emp in filteredEmployees()" :key="emp.id_user">
                                <label class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-white hover:shadow-xs transition cursor-pointer border border-transparent hover:border-slate-100">
                                    <input type="checkbox" :value="emp.id_user" x-model="form.user_ids"
                                        class="w-4 h-4 text-emerald-600 border-slate-350 rounded focus:ring-emerald-500 cursor-pointer">
                                    <span class="text-sm text-slate-700 font-medium" x-text="emp.nama_lengkap"></span>
                                </label>
                            </template>
                            <template x-if="filteredEmployees().length === 0">
                                <div class="text-xs text-slate-400 text-center py-6">Karyawan tidak ditemukan</div>
                            </template>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1 flex justify-between">
                            <span>Terpilih: <strong class="text-emerald-600" x-text="form.user_ids.length"></strong> karyawan</span>
                            <template x-if="form.user_ids.length === 0">
                                <span class="text-rose-500 font-medium">* Pilih minimal 1 karyawan</span>
                            </template>
                        </div>
                    </div>

                    <!-- Shift -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Shift Kerja</label>
                        <select x-model="form.shift_id"
                            class="w-full mt-1 px-3 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none text-sm font-medium text-slate-700 bg-white">
                            <option value="">-- Pilih Shift --</option>
                            <template x-for="s in shifts" :key="s.id_shift">
                                <option :value="s.id_shift" x-text="s.nama_shift"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mulai</label>
                            <input type="date" x-model="form.start_date" :min="getTodayDate()"
                                class="w-full mt-1 p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none text-sm text-slate-700 font-medium">
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai</label>
                            <input type="date" x-model="form.end_date" :min="form.start_date || getTodayDate()"
                                class="w-full mt-1 p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none text-sm text-slate-700 font-medium">
                        </div>
                    </div>

                    <!-- Aksi -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 transition text-sm font-semibold cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSaving || form.user_ids.length === 0"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 shadow-md shadow-emerald-500/10 hover:shadow-lg active:scale-95 transition-all disabled:opacity-40 disabled:pointer-events-none flex items-center justify-center gap-2 text-sm font-semibold cursor-pointer">
                            <span x-show="isSaving" class="animate-spin"><i class="fa-solid fa-spinner"></i></span>
                            <span> Simpan Jadwal</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

