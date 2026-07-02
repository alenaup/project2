<div x-data="vendorFilter()" x-init="fetchVendors()"
    class="mt-6 bg-white p-8 pb-2 rounded-lg shadow-lg border border-gray-100">
    <div class="flex items-center gap-3 mb-1">
        <i class="fas fa-search text-blue-600"></i>
        <h2 class="text-lg font-bold text-gray-800">Filter Data Karyawan</h2>
    </div>
    <p class="text-sm text-gray-500 mb-6">Pilih asal vendor pada tabel di bawah untuk menyaring data karyawan</p>

    <div class="w-full mb-6">
        <label class="block text-xs font-semibold text-gray-600 mb-2">Pilih Vendor / Admin Outsourcing</label>

        <div class="overflow-y-auto max-h-64 border border-gray-200 rounded-lg shadow-inner mb-3 relative">
            <!-- Loading -->
            <div x-show="isLoading" class="p-4 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-blue-600"></i> Memuat data vendor...
            </div>

            <table class="min-w-full text-left text-sm text-gray-600" style="display: none;" x-show="!isLoading">
                <thead class="bg-gray-50 sticky top-0 border-b border-gray-200 z-10 shadow-sm">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700 w-12 text-center">Pilih</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Nama Vendor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <!-- Semua Vendor -->
                    <tr class="hover:bg-green-50 transition cursor-pointer" @click="$refs.allRadio.click()">
                        <td class="px-4 py-3 text-center">
                            <input type="radio" x-ref="allRadio" name="vendor_id" value="" checked
                                class="w-4 h-4 text-green-600 focus:ring-green-500 cursor-pointer">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">Semua Vendor (Tampilkan Semua)</td>
                    </tr>

                    <!-- Vendor dari database -->
                    <template x-for="vendor in vendors" :key="vendor.id_outsourcing">
                        <tr class="hover:bg-green-50 transition cursor-pointer"
                            @click="$event.currentTarget.querySelector('input[type=radio]').click()">
                            <td class="px-4 py-3 text-center">
                                <input type="radio" name="vendor_id" :value="vendor.id_outsourcing"
                                    class="w-4 h-4 text-green-600 focus:ring-green-500 cursor-pointer" @click.stop>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800" x-text="vendor.nama_outsourcing"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col lg:flex-row items-center gap-4 shrink-0 w-full justify-between mt-2 text-xs text-gray-500"
            style="display: none;" x-show="!isLoading">
            <span x-text="`Menampilkan ${startIndex}-${endIndex} dari ${total} vendor`"></span>

            <!-- Paginasi -->
            <div class="flex gap-1" x-show="lastPage > 1">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                    class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 w-7 h-7 flex items-center justify-center rounded-lg shadow-sm font-medium cursor-pointer transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>

                <template x-for="page in paginationRange()" :key="page">
                    <button @click="changePage(page)"
                        class="w-7 h-7 flex items-center justify-center rounded-lg shadow-sm font-medium cursor-pointer transition"
                        :class="currentPage === page ? 'bg-green-700 text-white border-transparent' :
                            'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'"
                        x-text="page">
                    </button>
                </template>

                <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage"
                    class="bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 w-7 h-7 flex items-center justify-center rounded-lg shadow-sm font-medium cursor-pointer transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/hr/filter-data-karyawan.js') }}"></script>
