<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard adminOutsorcing</title>

    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="stylesheet" href={{ asset('css/alert.css') }}>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body x-data="{ ...dashboard(), open: false }" class="bg-gray-100">


    <div class="flex">
        {{-- SIDEBAR --}}
        <x-sidebar :menus="[
            ['title' => 'Penjadwalan', 'icon' => 'fa-solid fa-calendar', 'ref' => '/kepala-departement/dashboard'],
            ['title' => 'Karyawan', 'icon' => 'fas fa-users', 'ref' => '/kepala-departement/karyawan'],
            ['title' => 'Pengajuan', 'icon' => 'fa-solid fa-file-arrow-up', 'ref' => '/kepala-departement/pengajuan'],
            ['title' => 'Laporan', 'icon' => 'fa-solid fa-file-lines', 'ref' => '/kepala-departement/laporan'],
            ['title' => 'Shift', 'icon' => 'fa-solid fa-user-clock', 'ref' => '/kepala-departement/shift'],
        ]">kepala-departement</x-sidebar>

        <div class="flex-1 p-6 ml-0">
            <x-header>Kepala Departemen {{-- <-- Ganti aja ini kalo mau --}}</x-header>
           

            {{-- NONTIFIKASI, CETAK SEBAGAI EXCEL, MODAL MENAMBAHKAN JADWAL --}}
            <div class='flex justify-between border-b pb-4'>
                <div class="flex justify-center items-center py-2">
                    <h1 class="text-xl text-emerald-700 md:text-md inline-block font-semibold">Penjadwalan
                        Mingguan Karyawan</h1>
                </div>
                <div class='flex gap-2'>
                    <div x-data="{ open: false }">

                        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black/50">

                            <div id="small-modal" tabindex="-1"
                                class="fixed flex items-center justify-center right-0 z-50 w-full p-4 overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative w-full max-w-md max-h-full">
                                    <!-- Modal content -->
                                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                                        <!-- Modal header -->
                                        <div
                                            class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                                            <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                                                Small modal
                                            </h3>
                                            <button @click="open = false"
                                                class="text-gray-400 hover:bg-gray-200 rounded-lg w-8 h-8 flex items-center justify-center">
                                        </div>
                                        <!-- Modal body -->
                                        <div class="p-4 md:p-5 space-y-4">
                                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                                With less than a month to go before the European Union enacts new
                                                consumer privacy laws for its
                                                citizens, companies around the world are updating their terms of service
                                                agreements to comply.
                                            </p>
                                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                                The European Union’s General Data Protection Regulation (G.D.P.R.) goes
                                                into effect on May 25
                                                and is meant to ensure a common set of data rights in the European
                                                Union. It requires
                                                organizations to notify users as soon as possible of high-risk data
                                                breaches that could
                                                personally affect them.
                                            </p>
                                        </div>
                                        <!-- Modal footer -->
                                        <div
                                            class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                                            <button data-modal-hide="small-modal" type="button"
                                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I
                                                accept</button>
                                            <button data-modal-hide="small-modal" type="button"
                                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <button
                        class="px-5 py-2.5 bg-emerald-400 hover:bg-primary-700 text-white/90 font-medium rounded-xl shadow-md hover:shadow-lg hover:bg-emerald-500 hover:text-white active:scale-95 transition-all duration-200 ease-in-out">
                        <p class="text-bold text-md"><i class="fa-solid fa-print"></i></p>
                    </button>
                    <div x-data="{ open: false, closing: false }">

                        <!-- Button -->
                        <button @click="open = true"
                            class="px-5 py-2.5 bg-emerald-400 hover:bg-primary-700 text-white/90 font-medium rounded-xl shadow-md hover:shadow-lg hover:bg-emerald-500 hover:text-white active:scale-95 transition-all duration-200 ease-in-out"">
                            <i class="fa-solid fa-plus"></i> Tambah Jadwal
                        </button>

                        <!-- Overlay -->
                        <div x-show="open" x-transition.opacity
                            class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

                            <!-- Modal -->
                            <div @click.outside="open = false" x-show="open"
                                class="relative w-full max-w-xl bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/30 overflow-visible"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-90 translate-y-6"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-90">



                                <!-- Header -->
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-plus"></i> Tambah Jadwal</h2>

                                    <!-- Close button with rotation -->
                                    <button
                                        @click="closing = true; setTimeout(() => { open = false; closing = false }, 300)"
                                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-100 transition">
                                        <span :class="closing ? 'rotate-180 scale-125' : ''"
                                            class="inline-block transition-transform duration-300">
                                            ✖
                                        </span>
                                    </button>
                                </div>

                                <!-- Form -->
                                <form class="space-y-5">

                                    <!-- Datepicker FIX -->
                                    <div x-data="datepicker()" class="relative">
                                        <label class="text-sm font-medium text-gray-600">Tanggal</label>

                                        <input type="text" x-model="formattedDate" @click="toggle()" readonly
                                            placeholder=" Pilih tanggal"
                                            class="w-full mt-1 px-3 py-2.5 rounded-xl border cursor-pointer">

                                        <!-- Kalender (tidak kepotong lagi) -->
                                        <div x-show="show" x-transition
                                            class="absolute left-0 top-full mt-2 w-full bg-white rounded-2xl shadow-xl p-4 border z-999">

                                            <div class="flex justify-between items-center mb-3">
                                                <button @click="prevMonth()">‹</button>
                                                <span class="font-semibold" x-text="monthYear"></span>
                                                <button @click="nextMonth()">›</button>
                                            </div>

                                            <div class="grid grid-cols-7 gap-1 text-xs text-center text-gray-500 mb-1">
                                                <template x-for="d in ['M','S','S','R','K','J','S']">
                                                    <div x-text="d"></div>
                                                </template>
                                            </div>

                                            <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                                <template x-for="blank in blanks">
                                                    <div></div>
                                                </template>

                                                <template x-for="day in days">
                                                    <div @click="selectDate(day)"
                                                        class="p-2 rounded-xl cursor-pointer hover:bg-blue-100 hover:scale-110 transition"
                                                        :class="isToday(day) ? 'bg-blue-500 text-white' : ''"
                                                        x-text="day"></div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Search -->
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Karyawan</label>
                                        <input type="text" placeholder=" Cari nama..."
                                            class="w-full mt-1 px-3 py-2.5 rounded-xl border focus:ring-2 focus:ring-blue-400">
                                    </div>

                                    <!-- Shift -->
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">Shift</label>
                                        <select
                                            class="w-full mt-1 px-3 py-2.5 rounded-xl border focus:ring-2 focus:ring-indigo-400">
                                            <option>Pilih Shift</option>
                                            <option> Pagi</option>
                                            <option> Sore</option>
                                            <option> Malam</option>
                                            <option> Libur</option>
                                        </select>
                                    </div>

                                    <!-- Action -->
                                    <div class="flex justify-end gap-2 pt-4">
                                        <button type="button" @click="open=false"
                                            class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="px-5 py-2 rounded-xl bg-linear-to-r from-green-500 to-emerald-600 text-white hover:scale-105 transition">
                                            💾 Simpan
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
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
                        Total karyawan aktif dalam sistem.
                    </p>
                    <div class="flex justify-between items-center">
                        <div class="mt-4 text-2xl font-bold text-gray-800">128</div>
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
                        <div class="mt-4 text-2xl font-bold text-gray-800">97</div>
                        <div class="mt-4 ml-2 flex flex-col justify-end items-end">
                            <span class="text-sm text-gray-600">terakhir update 1 hari </span>

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
                        <div class="mt-4 text-2xl font-bold text-gray-800">12</div>
                        <div class="mt-4 ml-2 flex flex-col justify-end items-end ">
                            <span class="text-sm text-gray-600">terakhir update 2 hari </span>

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
                        <div class="mt-4 text-2xl font-bold text-gray-800">19</div>
                        <div class="mt-4 ml-2 flex flex-col justify-end items-end">
                            <span class="text-sm text-gray-600">terakhir update 344 hari </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL PENJADWLAN --}}
            <div class="max-w-7xl mx-auto p-4 bg-white rounded-2xl shadow mt-4">

                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">

                    <!-- Header -->


                    <!-- Navigasi tanggal -->
                    <div class="flex items-center gap-3">
                        <button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100"><i class="fa-solid fa-arrow-left"></i></button>

                        <h2 class="font-semibold text-gray-800" x-text="currentWeek"></h2>

                        <button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100"><i class="fa-solid fa-arrow-right"></i></button>
                    </div>

                    <!-- Filter -->
                    <div class="flex items-center gap-2">
                        <button class="px-3 py-1 rounded-lg bg-gray-100 text-sm">Hari Ini</button>
                        <button class="px-3 py-1 rounded-lg bg-blue-100 text-blue-600 text-sm">Minggu</button>
                        <button class="px-3 py-1 rounded-lg bg-gray-100 text-sm">Bulan</button>

                        <select class="ml-2 px-3 py-1 rounded-lg bg-gray-100 text-sm">
                            <option>Semua Dept</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-8 border rounded-xl overflow-hidden text-sm overflow-x-auto min-w-900px">
                    <!-- Header -->
                    <div class="bg-gray-50 p-3 font-semibold text-gray-600">KARYAWAN</div>

                    <template x-for="d in days">
                        <div class="bg-gray-50 p-3 text-center">
                            <span x-text="d.day"></span><br>
                            <span class="font-semibold" :class="d.active ? 'text-blue-600' : ''" x-text="d.date">
                            </span>
                        </div>
                    </template>

                    <!-- Rows -->
                    <template x-for="emp in employees">
                        <div x-data="{ open: false }" class="contents">

                            <!-- Karyawan -->
                            <div @click="open = true"
                                class="p-3 flex items-center gap-2 border-t hover:bg-gray-50 transition pointer">
                                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs"
                                    x-text="emp.initials"></div>

                                <div>
                                    <div class="font-medium" x-text="emp.name"></div>
                                    <div class="text-xs text-gray-500" x-text="emp.role"></div>
                                </div>
                            </div>

                            <!-- Shift -->
                            <template x-for="shift in emp.shifts">
                                <div class="px-2 py-1 rounded-lg text-xs text-center
                                            hover:shadow-md hover:-translate-y-0.5
                                            transition-all duration-200 cursor-pointer"
                                    :class="shiftClass(shift)">

                                    <!-- Kalau kosong -->
                                    <template x-if="!shift">
                                        <div
                                            class="text-gray-300 text-lg hover:text-blue-500 cursor-pointer transition">
                                            +
                                        </div>
                                    </template>

                                    <!-- Kalau ada shift -->
                                    <template x-if="shift">
                                        <div class="px-2 py-1 rounded-lg text-xs text-center hover:shadow transition"
                                            :class="shiftClass(shift)">

                                            <div class="font-semibold capitalize" x-text="shift"></div>
                                            <div class="text-[10px] m-2">
                                                <template x-if="shift === 'pagi'">06:00 - 14:00</template>
                                                <template x-if="shift === 'siang'">14:00 - 22:00</template>
                                                <template x-if="shift === 'malam'">22:00 - 06:00</template>
                                                <template x-if="shift === 'libur'">Hari Libur</template>
                                            </div>

                                        </div>
                                    </template>

                                </div>
                            </template>

                            <div x-show="open" x-transition
                                class="fixed inset-0 flex items-center justify-center bg-black/40">
                                <div @click.away="open = false"
                                    class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6 space-y-5">

                                    <!-- Header -->
                                    <div class="flex justify-between items-center">
                                        <h2 class="text-lg font-semibold text-gray-800">Atur Shift Kerja</h2>
                                        <button @click="open = false"
                                            class="text-gray-400 hover:text-red-500 text-xl">×</button>
                                    </div>

                                    <!-- Shift Dropdown -->
                                    <div>
                                        <label class="text-sm text-gray-600">Pilih Shift</label>
                                        <select x-model="shift"
                                            class="w-full mt-1 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                                            <option value="">-- pilih shift --</option>
                                            <option value="pagi"> Pagi</option>
                                            <option value="sore"> Sore</option>
                                            <option value="malam"> Malam</option>
                                        </select>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm text-gray-600">Mulai</label>
                                            <input type="date" x-model="start"
                                                class="w-full mt-1 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                                        </div>

                                        <div>
                                            <label class="text-sm text-gray-600">Selesai</label>
                                            <input type="date" x-model="end"
                                                class="w-full mt-1 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="flex justify-end gap-2 pt-3">
                                        <button @click="open = false"
                                            class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300">
                                            Batal
                                        </button>

                                        <button
                                            class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
                                            Simpan
                                        </button>
                                    </div>

                                </div>
                            </div>
                    </template>
                </div>
            </div>
        </div>
    </div>



    <script>
        function dashboard() {
            return {
                view: 'week',
                currentWeek: '20 – 26 Apr 2026',

                days: [{
                        day: 'Sen',
                        date: 20
                    },
                    {
                        day: 'Sel',
                        date: 21
                    },
                    {
                        day: 'Rab',
                        date: 22,
                        active: true
                    },
                    {
                        day: 'Kam',
                        date: 23
                    },
                    {
                        day: 'Jum',
                        date: 24
                    },
                    {
                        day: 'Sab',
                        date: 25
                    },
                    {
                        day: 'Min',
                        date: 26
                    },
                ],

                employees: [{
                        name: 'Rina',
                        role: 'Senior Dev',
                        initials: 'RW',
                        shifts: ['siang', null, 'pagi', 'malam', 'pagi', 'libur', 'libur']
                    },
                    {
                        name: 'Budi',
                        role: 'Staff IT',
                        initials: 'BS',
                        shifts: ['siang', 'pagi', 'malam', 'pagi', 'siang', 'libur', 'pagi']
                    },
                    {
                        name: 'Andi',
                        role: 'Frontend Dev',
                        initials: 'AP',
                        shifts: ['pagi', 'siang', 'siang', 'malam', 'libur', 'libur', 'pagi']
                    },
                    {
                        name: 'Siti',
                        role: 'Backend Dev',
                        initials: 'SA',
                        shifts: ['malam', 'malam', 'libur', 'pagi', 'siang', 'pagi', 'libur']
                    },
                    {
                        name: 'Dewi',
                        role: 'UI/UX',
                        initials: 'DP',
                        shifts: ['pagi', 'pagi', 'siang', 'siang', 'malam', 'libur', 'libur']
                    },
                    {
                        name: 'Eko',
                        role: 'DevOps',
                        initials: 'ES',
                        shifts: ['malam', 'siang', 'pagi', 'libur', 'siang', 'pagi', 'malam']
                    },
                    {
                        name: 'Fajar',
                        role: 'QA Engineer',
                        initials: 'FN',
                        shifts: ['pagi', 'libur', 'siang', 'malam', 'malam', 'pagi', 'libur']
                    },
                    {
                        name: 'Gina',
                        role: 'HR',
                        initials: 'GH',
                        shifts: ['siang', 'siang', 'libur', 'pagi', 'pagi', 'malam', 'libur']
                    },
                    {
                        name: 'Hendra',
                        role: 'Security',
                        initials: 'HP',
                        shifts: ['malam', 'malam', '', 'siang', 'libur', 'siang', 'pagi']
                    },
                ],

                shiftClass(type) {
                    return {
                        pagi: 'bg-emerald-100 text-emerald-700',
                        siang: 'bg-yellow-100 text-yellow-700',
                        malam: 'bg-indigo-100 text-indigo-700',
                        libur: 'bg-red-100 text-red-600'
                    } [type];
                }


            }
        }

        /* BAGIAN UNTUK MODAL MENAMBAHKAN JADWAL */
        function datepicker() {
            return {
                show: false,
                date: new Date(),
                selectedDate: null,

                toggle() {
                    this.show = !this.show
                },

                get monthYear() {
                    return this.date.toLocaleString('id-ID', {
                        month: 'long',
                        year: 'numeric'
                    })
                },

                get days() {
                    let y = this.date.getFullYear()
                    let m = this.date.getMonth()
                    return new Date(y, m + 1, 0).getDate()
                },

                get blanks() {
                    let first = new Date(this.date.getFullYear(), this.date.getMonth(), 1).getDay()
                    return Array(first).fill('')
                },

                prevMonth() {
                    this.date.setMonth(this.date.getMonth() - 1)
                },
                nextMonth() {
                    this.date.setMonth(this.date.getMonth() + 1)
                },

                selectDate(day) {
                    this.selectedDate = new Date(this.date.getFullYear(), this.date.getMonth(), day)
                    this.show = false
                },

                isToday(day) {
                    let t = new Date()
                    return day === t.getDate() &&
                        this.date.getMonth() === t.getMonth() &&
                        this.date.getFullYear() === t.getFullYear()
                },

                get formattedDate() {
                    return this.selectedDate ?
                        this.selectedDate.toLocaleDateString('id-ID') :
                        ''
                }
            }
        }
    </script>
</body>
<script src="{{ asset('js/alert.js') }}"></script>

</html>
