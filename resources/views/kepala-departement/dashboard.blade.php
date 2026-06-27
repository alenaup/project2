@extends('layouts.kepala-departement')

@push('styles')
    <link rel="stylesheet" href="/css/alert.css">
    <script defer src="/js/kepala-departemen/dashboard.js"></script>
@endpush

@section('content')
    {{-- NONTIFIKASI, CETAK SEBAGAI EXCEL, MODAL MENAMBAHKAN JADWAL --}}
    <div class='flex justify-between border-b pb-4'>
        <div class="flex justify-center items-center py-2">
            <h1 class="text-xl text-emerald-700 md:text-md inline-block font-semibold">Penjadwalan
                Mingguan Karyawan</h1>
        </div>
        <div class='flex gap-2'>


            <button
                class="px-5 py-2.5 bg-emerald-400 hover:bg-primary-700 text-white/90 font-medium rounded-xl shadow-md hover:shadow-lg hover:bg-emerald-500 hover:text-white active:scale-95 transition-all duration-200 ease-in-out">
                <p class="text-bold text-md"><i class="fa-solid fa-print"></i></p>
            </button>
            <!-- Tambah Jadwal Button -->
            <div>
                <button @click="openModal()"
                    class="px-5 py-2.5 bg-emerald-400 hover:bg-primary-700 text-white/90 font-medium rounded-xl shadow-md hover:shadow-lg hover:bg-emerald-500 hover:text-white active:scale-95 transition-all duration-200 ease-in-out">
                    <i class="fa-solid fa-plus"></i> Tambah Jadwal
                </button>
            </div>
        </div>
    </div>

    <!-- CARD -->
    <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">

        <!-- Card 1 -->
        <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Karyawan</h3>

            </div>
            <p class="mt-2 text-sm text-gray-600">
                Total karyawan di departemen yang aktif dalam sistem.
            </p>
            <div class="flex justify-between items-center">
                <div class="mt-4 text-2xl font-bold text-gray-800" x-text="summary.totalKaryawan">0</div>
                <div class="mt-4 ml-2 flex flex-col justify-end items-end">
                    <span class="text-sm text-gray-600">terakhir update hari ini</span>

                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Absensi Hari Ini</h3>

            </div>
            <p class="mt-2 text-sm text-gray-600">
                Jumlah kehadiran karyawan hari ini.
            </p>
            <div class="flex justify-between items-center">
                <div class="mt-4 text-2xl font-bold text-gray-800" x-text="summary.hadir">0</div>
                <div class="mt-4 ml-2 flex flex-col justify-end items-end">
                    <span class="text-sm text-gray-600">terakhir update hari ini</span>

                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Terlambat</h3>

            </div>
            <p class="mt-2 text-sm text-gray-600">
                Karyawan yang datang terlambat.
            </p>
            <div class="flex  justify-between items-center">
                <div class="mt-4 text-2xl font-bold text-gray-800" x-text="summary.terlambat">0</div>
                <div class="mt-4 ml-2 flex flex-col justify-end items-end ">
                    <span class="text-sm text-gray-600">terakhir update hari ini</span>

                </div>

            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl shadow-md p-5 hover:shadow-lg transition">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Izin / Cuti</h3>

            </div>
            <p class="mt-2 text-sm text-gray-600">
                Jumlah karyawan tidak hadir.
            </p>
            <div class="flex justify-between items-center">
                <div class="mt-4 text-2xl font-bold text-gray-800" x-text="summary.izinCuti">0</div>
                <div class="mt-4 ml-2 flex flex-col justify-end items-end">
                    <span class="text-sm text-gray-600">terakhir update hari ini</span>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL PENJADWLAN --}}
    <div class="max-w-7xl mx-auto p-4 bg-white rounded-2xl shadow mt-4">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <!-- Navigasi tanggal -->
            <div class="flex items-center gap-3">
                <button @click="prevWeek()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition cursor-pointer"><i
                        class="fa-solid fa-arrow-left"></i></button>

                <h2 class="font-semibold text-gray-800" x-text="currentWeek"></h2>

                <button @click="nextWeek()"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition cursor-pointer"><i
                        class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="grid grid-cols-8 border rounded-xl overflow-hidden text-sm overflow-x-auto min-w-[900px]">
            <div class="bg-gray-50 p-3 font-semibold text-gray-600">KARYAWAN</div>
            <template x-for="d in days">
                <div class="bg-gray-50 p-3 text-center">
                    <span x-text="d.day"></span><br>
                    <span class="font-semibold" :class="d.active ? 'text-blue-600' : ''" x-text="d.date">
                    </span>
                </div>
            </template>

            <template x-for="emp in employees">
                <div class="contents">

                    <!-- Karyawan -->
                    <div @click="openModal(emp.id, days[0].date_full, days[6].date_full)"
                        class="p-3 flex items-center gap-2 border-t hover:bg-gray-50 transition cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs"
                            x-text="emp.initials"></div>

                        <div>
                            <div class="font-medium" x-text="emp.name"></div>
                            <div class="text-xs text-gray-500" x-text="emp.role"></div>
                        </div>
                    </div>

                    <!-- Shift -->
                    <template x-for="(shift, index) in emp.shifts">
                        <div @click="openModal(emp.id, days[index].date_full, days[index].date_full)"
                            class="px-2 py-1 rounded-lg text-xs text-center
                                            hover:shadow-md hover:-translate-y-0.5
                                            transition-all duration-200 cursor-pointer"
                            :class="shiftClass(shift)">

                            <!-- Kalau tidak ada shift -->
                            <template x-if="!shift">
                                <div class="text-gray-300 text-lg hover:text-blue-500 cursor-pointer transition">
                                    +
                                </div>
                            </template>

                            <!-- Kalau ada -->
                            <template x-if="shift">
                                <div class="px-2 py-1 rounded-lg text-xs text-center hover:shadow transition"
                                    :class="shiftClass(shift)">

                                    <div class="font-semibold capitalize" x-text="shift"></div>
                                    <div class="text-[10px] m-2">
                                        <template x-if="shift === 'pagi'"><span>06:00 - 14:00</span></template>
                                        <template x-if="shift === 'siang'"><span>14:00 -
                                                22:00</span></template>
                                        <template x-if="shift === 'sore'"><span>15:00 - 23:00</span></template>
                                        <template x-if="shift === 'malam'"><span>22:00 -
                                                06:00</span></template>
                                        <template x-if="shift === 'libur'"><span>Hari Libur</span></template>
                                    </div>

                                </div>
                            </template>

                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Paginasi -->
        <div class="flex items-center justify-between mt-4 px-2">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage <= 1"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg disabled:opacity-50 hover:bg-gray-200 transition cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-chevron-left"></i> Prev
            </button>

            <span class="text-sm text-gray-600">
                Halaman <span class="font-semibold" x-text="currentPage"></span> dari <span class="font-semibold"
                    x-text="lastPage"></span>
            </span>

            <button @click="changePage(currentPage + 1)" :disabled="currentPage >= lastPage"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg disabled:opacity-50 hover:bg-gray-200 transition cursor-pointer flex items-center gap-2">
                Next <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

    </div>

    <!-- Modal Tambah Jadwal -->
    <div x-show="isModalOpen" style="display: none;"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

        <div @click.outside="closeModal()" x-show="isModalOpen"
            class="relative w-full max-w-xl bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/30 overflow-visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-6"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-plus"></i> Tambah Jadwal</h2>
                <button @click="closeModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-100 transition">
                    ✖
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitSchedule" class="space-y-5">
                <!-- Karyawan -->
                <div>
                    <label class="text-sm font-medium text-gray-600">Karyawan</label>
                    <select x-model="form.user_id"
                        class="w-full mt-1 px-3 py-2.5 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">-- Pilih Karyawan --</option>
                        <template x-for="emp in employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Shift -->
                <div>
                    <label class="text-sm font-medium text-gray-600">Shift</label>
                    <select x-model="form.shift_id"
                        class="w-full mt-1 px-3 py-2.5 rounded-xl border focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="">-- Pilih Shift --</option>
                        <template x-for="s in shifts" :key="s.id_shift">
                            <option :value="s.id_shift" x-text="s.nama_shift"></option>
                        </template>
                    </select>
                </div>

                <!-- Tanggal -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm text-gray-600">Mulai</label>
                        <input type="date" x-model="form.start_date"
                            class="w-full mt-1 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Selesai</label>
                        <input type="date" x-model="form.end_date"
                            class="w-full mt-1 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                <!-- Aksi -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="closeModal()"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSaving"
                        class="px-5 py-2 rounded-xl bg-linear-to-r from-green-500 to-emerald-600 text-white hover:scale-105 transition disabled:opacity-50 flex items-center">
                        <span x-show="isSaving" class="mr-2 animate-spin"><i class="fa-solid fa-spinner"></i></span>
                        <span>💾 Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
