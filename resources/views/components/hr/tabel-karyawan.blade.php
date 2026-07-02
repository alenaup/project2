<div>
    <div x-data="karyawanTable()" x-init="initComponent()">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-y-2">
                <thead class="bg-green-100 text-gray-600">
                    <tr class="shadow-sm hover:shadow-md hover:-translate-y-0.5 transition border border-gray-100">
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm whitespace-nowrap">No</th>
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm whitespace-nowrap">NIP</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm whitespace-nowrap">Nama Karyawan</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm whitespace-nowrap">Asal Vendor</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm whitespace-nowrap">Email</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm min-w-50">Alamat</th>
                        <th class="p-2 md:p-6 text-center text-xs md:text-sm whitespace-nowrap">Tanggal Masuk</th>
                        <th class="p-2 md:p-6 text-center text-xs md:text-sm whitespace-nowrap">Tanggal Keluar</th>
                        <th class="p-2 md:p-6 text-center text-xs md:text-sm whitespace-nowrap">No Telp</th>
                        <th class="p-2 md:p-6 text-center text-xs md:text-sm whitespace-nowrap">Status</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Loading -->
                    <tr x-show="isLoading">
                        <td colspan="10" class="p-6 text-center text-gray-500 bg-white rounded-lg">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-spinner fa-spin text-green-600 text-lg"></i>
                                <span>Memuat data karyawan...</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Jika Data Kosong -->
                    <tr x-show="!isLoading && employees.length === 0">
                        <td colspan="10" class="p-6 text-center text-gray-500 bg-white rounded-lg">
                            Tidak ada data karyawan ditemukan.
                        </td>
                    </tr>

                    <!-- Baris Data -->
                    <template x-for="(emp, index) in employees" :key="emp.id_user">
                        <tr class="odd:bg-white even:bg-gray-100 shadow-sm hover:bg-green-50 transition cursor-pointer"
                            :class="emp.status === 'inactive' ? 'text-gray-500' : ''">
                            <td class="p-3 text-center" x-text="startIndex + index"></td>
                            <td class="p-3 text-center whitespace-nowrap" x-text="emp.nip"></td>
                            <td class="p-3 text-left whitespace-nowrap font-medium text-gray-900"
                                x-text="emp.nama_lengkap"></td>
                            <td class="p-3 text-left whitespace-nowrap"
                                x-text="emp.outsourcing ? emp.outsourcing.nama_outsourcing : '-'"></td>
                            <td class="p-3 text-left" x-text="emp.email"></td>
                            <td class="p-3 text-left" x-text="emp.alamat || '-'"></td>
                            <td class="p-3 text-center whitespace-nowrap" x-text="formatDate(emp.tanggal_masuk)"></td>
                            <td class="p-3 text-center whitespace-nowrap" x-text="formatDate(emp.tanggal_keluar)"></td>
                            <td class="p-3 text-center whitespace-nowrap" x-text="emp.nomor_tlp || '-'"></td>
                            <td class="p-3 text-center whitespace-nowrap">
                                <span class="px-2 py-1 rounded text-xs font-semibold"
                                    :class="emp.status === 'active' ? 'bg-green-100 text-green-600' :
                                        'bg-red-100 text-red-600'"
                                    x-text="emp.status === 'active' ? 'Aktif' : 'Non-Aktif'">
                                </span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        <div class="flex flex-wrap justify-end mt-6 gap-1 text-sm" x-show="lastPage > 1">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                class="px-3 py-1 transition-colors hover:bg-blue-500 hover:text-white border hover:border-transparent rounded cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-inherit">
                Previous
            </button>

            <template x-for="page in paginationRange()" :key="page">
                <button @click="changePage(page)" class="px-3 py-1 rounded transition-colors"
                    :class="currentPage === page ? 'bg-green-600 text-white' :
                        'hover:bg-green-600 hover:text-white border hover:border-transparent'"
                    x-text="page">
                </button>
            </template>

            <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage"
                class="px-3 py-1 transition-colors hover:bg-blue-500 hover:text-white border hover:border-transparent rounded cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-inherit">
                Next
            </button>
        </div>
    </div>
</div>

<script src="{{ asset('js/hr/tabel-karyawan.js') }}"></script>
