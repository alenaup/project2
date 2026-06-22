<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuti & Izin</title>
    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <!-- Alpine -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body x-data="cutiPage()" class="bg-gradient-to-br from-gray-100 to-gray-200">

<div class="flex">

    <!-- SIDEBAR -->
    <x-sidebar :menus="[
            ['title' => 'Penjadwalan', 'icon' => 'fa-solid fa-calendar', 'ref' => '/kepala-departement/dashboard'],
            ['title' => 'Karyawan', 'icon' => 'fas fa-users', 'ref' => '/kepala-departement/karyawan'],
            ['title' => 'Pengajuan', 'icon' => 'fa-solid fa-file-arrow-up', 'ref' => '/kepala-departement/pengajuan'],
            ['title' => 'Laporan', 'icon' => 'fa-solid fa-file-lines', 'ref' => '/kepala-departement/laporan'],
            ['title' => 'Shift', 'icon' => 'fa-solid fa-user-clock', 'ref' => '/kepala-departement/shift'],
        ]">kepala-departement</x-sidebar>

    <div class="flex-1 p-6">

        <!-- HEADER -->
        <x-header>Kepala Departemen</x-header>

        <div class="max-w-6xl mx-auto p-6 bg-white/70 rounded-2xl shadow space-y-8 mt-4">

            <!-- TITLE -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-blue-500"></i>
                        Cuti & Izin
                    </h1>
                    <p class="text-sm text-gray-500">Riwayat pengajuan cuti, izin, dan sakit</p>
                </div>

                <div class="relative group w-full md:w-64">

    <!-- ICON -->
    <div class="absolute inset-y-0 left-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition">
        <i class="fas fa-calendar"></i>
    </div>

    <!-- INPUT -->
    <input
        type="date"
        x-model="filterDate"
        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white/70 backdrop-blur border border-gray-300 text-sm shadow-sm
               focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition
               hover:border-gray-400 cursor-pointer">

    <!-- CLEAR BUTTON -->
    <button
        x-show="filterDate"
        @click="filterDate = ''"
        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-red-500 transition">
        <i class="fas fa-times text-sm"></i>
    </button>

</div>
            </div>

            <!-- CARD -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- SAKIT -->
                <div class="group bg-white/70 backdrop-blur border border-red-300 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300">

                    <div class="w-14 h-14 bg-white rounded-xl shadow flex items-center justify-center text-xl group-hover:scale-110 transition">
                        <i class="fas fa-notes-medical text-red-500"></i>
                    </div>

                    <div class="text-center flex-1">
                        <div class="text-sm text-gray-600">Total Sakit</div>
                        <div class="text-4xl font-bold text-red-500" x-text="totalSakit"></div>
                        <div class="text-xs text-gray-400">Karyawan sakit</div>
                    </div>

                </div>

                <!-- IZIN -->
                <div class="group bg-white/70 backdrop-blur border border-yellow-300 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300">

                    <div class="w-14 h-14 bg-white rounded-xl shadow flex items-center justify-center text-xl group-hover:scale-110 transition">
                        <i class="fas fa-file-alt text-yellow-500"></i>
                    </div>

                    <div class="text-center flex-1">
                        <div class="text-sm text-gray-600">Total Izin</div>
                        <div class="text-4xl font-bold text-yellow-500" x-text="totalIzin"></div>
                        <div class="text-xs text-gray-400">Karyawan izin</div>
                    </div>

                </div>

                <!-- CUTI -->
                <div class="group bg-white/70 backdrop-blur border border-blue-300 rounded-2xl p-5 flex items-center justify-between shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300">

                    <div class="w-14 h-14 bg-white rounded-xl shadow flex items-center justify-center text-xl group-hover:scale-110 transition">
                        <i class="fas fa-calendar text-blue-500"></i>
                    </div>

                    <div class="text-center flex-1">
                        <div class="text-sm text-gray-600">Total Cuti</div>
                        <div class="text-4xl font-bold text-blue-600" x-text="totalCuti"></div>
                        <div class="text-xs text-gray-400">Karyawan cuti</div>
                    </div>

                </div>

            </div>

            <!-- LIST -->
            <div class="bg-white/80 backdrop-blur rounded-2xl shadow-sm overflow-hidden border">

                <div class="px-5 py-3 border-b text-sm font-semibold text-gray-600 bg-gray-50 flex items-center gap-2">
                    <i class="fas fa-list"></i>
                    Daftar Pengajuan
                </div>

                <div class="divide-y">

                    <template x-for="item in filteredData" :key="item.id">
                        <div class="group flex items-center justify-between p-5 transition hover:bg-gray-50 hover:shadow-sm">

                            <!-- LEFT -->
                            <div class="flex items-center gap-4">

                                <div class="w-11 h-11 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-semibold shadow group-hover:scale-110 transition">
                                    <span x-text="item.initials"></span>
                                </div>

                                <div>
                                    <div class="font-semibold text-gray-800 group-hover:text-blue-600 transition"
                                         x-text="item.nama"></div>

                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                        <span x-text="item.tanggal"></span>
                                        <span>•</span>

                                        <span class="flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-md text-gray-600">

                                            <template x-if="item.jenis === 'Cuti'">
                                                <i class="fas fa-calendar text-blue-500 text-xs"></i>
                                            </template>

                                            <template x-if="item.jenis === 'Sakit'">
                                                <i class="fas fa-notes-medical text-red-500 text-xs"></i>
                                            </template>

                                            <template x-if="item.jenis === 'Izin'">
                                                <i class="fas fa-file-alt text-yellow-500 text-xs"></i>
                                            </template>

                                            <span x-text="item.jenis"></span>
                                        </span>

                                    </div>
                                </div>

                            </div>

                            <!-- STATUS -->
                            <div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full flex items-center gap-1 transition"
                                    :class="statusClass(item.status)">

                                    <template x-if="item.status === 'Menunggu'">
                                        <i class="fas fa-clock text-xs"></i>
                                    </template>

                                    <template x-if="item.status === 'Disetujui'">
                                        <i class="fas fa-check text-xs"></i>
                                    </template>

                                    <span x-text="item.status"></span>
                                </span>
                            </div>

                        </div>
                    </template>

                    <!-- EMPTY -->
                    <template x-if="filteredData.length === 0">
                        <div class="p-8 text-center text-gray-400 text-sm">
                            <i class="fas fa-folder-open text-2xl mb-2"></i>
                            <div>Tidak ada data pada tanggal tersebut</div>
                        </div>
                    </template>

                </div>

            </div>

        </div>

    </div>
</div>

<script>
function cutiPage() {
    return {
        filterDate: '',

        data: [
            {id:1,nama:'Budi Santoso',initials:'BS',tanggal:'2025-04-28',jenis:'Cuti',status:'Menunggu'},
            {id:2,nama:'Sari Kusuma',initials:'SK',tanggal:'2025-04-25',jenis:'Sakit',status:'Disetujui'},
            {id:3,nama:'Deni Pratama',initials:'DP',tanggal:'2025-04-30',jenis:'Izin',status:'Menunggu'}
        ],

        get filteredData() {
            if (!this.filterDate) return this.data;
            return this.data.filter(d => d.tanggal === this.filterDate);
        },

        get totalSakit() {
            return this.data.filter(d => d.jenis === 'Sakit').length;
        },

        get totalIzin() {
            return this.data.filter(d => d.jenis === 'Izin').length;
        },

        get totalCuti() {
            return this.data.filter(d => d.jenis === 'Cuti').length;
        },

        statusClass(status) {
            return {
                'Menunggu': 'bg-yellow-100 text-yellow-700',
                'Disetujui': 'bg-green-100 text-green-700',
                'Ditolak': 'bg-red-100 text-red-700'
            }[status];
        }
    }
}
</script>

</body>
</html>
