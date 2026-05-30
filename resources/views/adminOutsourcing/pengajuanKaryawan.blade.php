@extends('layouts.admin-outsourcing')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 p-4 md:p-8 font-sans relative overflow-hidden" x-data="leaveValidation()">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-green-50/50 blur-3xl pointer-events-none z-0"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-50/50 blur-3xl pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Validasi Perizinan</h1>
                <p class="text-base text-gray-500 mt-2 max-w-xl">Kelola permohonan izin sakit dan cuti karyawan outsourcing .</p>
            </div>

            <!-- Stats -->
            <div class="flex space-x-4">
                <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-amber-50 rounded-xl text-amber-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menunggu Validasi</p>
                        <p class="text-3xl font-black text-gray-900 mt-0.5" x-text="pendingCount"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-2">
            <!-- Tabs -->
            

            <!-- Search -->
            <div class="relative w-full md:w-72">
                <input type="text" x-model="searchQuery" placeholder="Cari nama karyawan..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-[#009254] focus:border-[#009254] outline-none transition-all duration-300 text-sm font-medium text-gray-700 placeholder-gray-400">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <!-- Request List -->
        <div class="space-y-4">
            <!-- Table Header (Visible on md+) -->
            <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest">
                <div class="col-span-4">Karyawan</div>
                <div class="col-span-3">Jenis & Tanggal</div>
                <div class="col-span-3">Keterangan Singkat</div>
                <div class="col-span-2 text-right">Aksi</div>
            </div>

            <template x-for="(request, index) in filteredRequests" :key="request.id">
                <div
                    x-show="true"
                    x-transition:enter="transition ease-out duration-300 delay-[index * 50ms]"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-green-100 transition-all duration-300 group"
                >
                    <div class="flex flex-col md:grid md:grid-cols-12 gap-4 items-start md:items-center">
                        <!-- Employee Info -->
                        <div class="col-span-4 flex items-center space-x-4 w-full">
                            <div class="relative">
                                <img :src="request.avatar" alt="Avatar" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white" :class="request.type === 'Izin Sakit' ? 'bg-red-500' : 'bg-blue-500'"></div>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 group-hover:text-[#009254] transition-colors" x-text="request.name"></h3>
                                <p class="text-xs font-medium text-gray-500" x-text="request.position"></p>
                            </div>
                        </div>

                        <!-- Type & Date -->
                        <div class="col-span-3 flex flex-col space-y-1.5 w-full">
                            <div>
                                <span :class="getTypeBadgeClass(request.type)" class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide">
                                    <span x-text="request.type"></span>
                                </span>
                            </div>
                            <div class="flex items-center text-sm font-semibold text-gray-600">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span x-text="request.date"></span>
                            </div>
                        </div>

                        <!-- Reason Preview -->
                        <div class="col-span-3 w-full">
                            <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed" x-text="request.reason"></p>
                            <div x-show="request.attachment" class="mt-1 flex items-center text-xs font-semibold text-[#009254]">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Terlampir 1 File
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="col-span-2 flex items-center justify-end space-x-2 w-full mt-4 md:mt-0">
                            <button @click="openModal(request)" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 tooltip" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                            <button @click="handleAction(request.id, 'approve')" class="flex-1 md:flex-none px-4 py-2 bg-[#009254] text-white hover:bg-[#007a46] rounded-xl text-sm font-bold shadow-sm shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5">
                                Terima
                            </button>
                            <button @click="handleAction(request.id, 'reject')" class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-100 rounded-xl text-sm font-bold transition-all duration-200 transform hover:-translate-y-0.5">
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="filteredRequests.length === 0" style="display: none;" class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-3xl border-2 border-gray-100 border-dashed">
                <div class="w-20 h-20 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-5 border border-gray-50">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-2">Tidak ada pengajuan ditemukan</h3>
                <p class="text-gray-500 text-base max-w-sm mx-auto">Selesai! Belum ada permohonan izin atau cuti yang memerlukan validasi saat ini.</p>
            </div>
        </div>

        <!-- Riwayat Validasi -->
        <div class="mt-12" x-show="historyRequests.length > 0" style="display: none;"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#009254]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Validasi Terakhir
                </h2>
                <p class="text-sm text-gray-500 mt-1">Daftar permohonan yang telah Anda proses hari ini.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                <th class="px-6 py-4">Karyawan</th>
                                <th class="px-6 py-4">Jenis</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Waktu Proses</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="history in historyRequests" :key="history.id">
                                <tr @click="openModal(history)" class="hover:bg-gray-50/50 transition-colors cursor-pointer">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <img :src="history.avatar" class="w-8 h-8 rounded-full object-cover border border-gray-100">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900" x-text="history.name"></p>
                                                <p class="text-xs text-gray-500" x-text="history.position"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold text-gray-600" x-text="history.type"></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span x-show="history.status === 'Diterima'" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Diterima
                                        </span>
                                        <span x-show="history.status === 'Ditolak'" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Ditolak
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-medium text-gray-500" x-text="history.processedAt"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class=" inset-0 bg-gray-900/70 transition-opacity" aria-hidden="true" @click="closeModal()"></div>

            <span class=" sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-100">
                <template x-if="selectedRequest">
                    <div>
                        <!-- Header -->
                        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-xl font-extrabold text-gray-900" id="modal-title">Detail Permohonan</h3>
                                <span x-show="selectedRequest.status === 'Diterima'" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100" style="display: none;">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Diterima
                                </span>
                                <span x-show="selectedRequest.status === 'Ditolak'" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100" style="display: none;">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Ditolak
                                </span>
                            </div>
                            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 bg-white p-2 rounded-full shadow-sm border border-gray-100 hover:bg-gray-50 transition-all">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="px-8 py-6">
                            <!-- User Header -->
                            <div class="flex items-center space-x-5 mb-8">
                                <img :src="selectedRequest.avatar" alt="Avatar" class="w-16 h-16 rounded-2xl object-cover shadow-sm border border-gray-100">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900" x-text="selectedRequest.name"></h4>
                                    <p class="text-sm font-semibold text-[#009254]" x-text="selectedRequest.position"></p>
                                </div>
                            </div>

                            <!-- Detail Grid -->
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-2">Jenis Permohonan</p>
                                        <div class="flex items-center">
                                            <span :class="getTypeBadgeClass(selectedRequest.type)" class="px-3 py-1 rounded-lg text-sm font-bold uppercase tracking-wide" x-text="selectedRequest.type"></span>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-2">Tanggal Pelaksanaan</p>
                                        <p class="text-sm font-bold text-gray-900" x-text="selectedRequest.date"></p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-3">Alasan / Keterangan</p>
                                    <div class="bg-gray-50 rounded-2xl p-5 text-sm text-gray-700 leading-relaxed border border-gray-100 shadow-inner" x-text="selectedRequest.reason"></div>
                                </div>

                                <div x-show="selectedRequest.attachment">
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-3">Dokumen Lampiran</p>
                                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-5 flex items-center justify-between bg-white group hover:border-[#009254] hover:bg-green-50 transition-all cursor-pointer">
                                        <div class="flex items-center text-gray-700 group-hover:text-[#009254]">
                                            <div class="p-2 bg-gray-50 group-hover:bg-white rounded-lg mr-4 border border-gray-100">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <span class="text-sm font-bold block" x-text="selectedRequest.attachment"></span>
                                                <span class="text-xs text-gray-500 font-medium">Klik untuk melihat file</span>
                                            </div>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-[#009254]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="px-8 py-5 border-t border-gray-50 bg-gray-50/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 rounded-b-3xl">
                            <button @click="closeModal()" class="w-full sm:w-auto px-6 py-3 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-bold transition-all duration-200">
                                Tutup
                            </button>
                            <button x-show="!selectedRequest.status" @click="handleAction(selectedRequest.id, 'reject'); closeModal()" class="w-full sm:w-auto px-6 py-3 bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 rounded-xl text-sm font-bold transition-all duration-200 flex items-center justify-center">
                                Tolak Permohonan
                            </button>
                            <button x-show="!selectedRequest.status" @click="handleAction(selectedRequest.id, 'approve'); closeModal()" class="w-full sm:w-auto px-6 py-3 bg-[#009254] text-white hover:bg-[#007a46] rounded-xl text-sm font-bold shadow-md shadow-green-200 transition-all duration-200 flex items-center justify-center transform hover:-translate-y-0.5">
                                Terima Permohonan
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hide scrollbar for tabs */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leaveValidation', () => ({
            tabs: [
                { id: 'all', name: 'Semua Pengajuan' },
                { id: 'sakit', name: 'Izin Sakit' },
                { id: 'cuti', name: 'Izin Cuti/Lainnya' }
            ],
            activeTab: 'all',
            searchQuery: '',
            isModalOpen: false,
            selectedRequest: null,
            historyRequests: [],

            // Dummy Data
            requests: [
                {
                    id: 1,
                    name: 'Ahmad Syafiq',
                    position: 'Staff IT',
                    type: 'Izin Sakit',
                    date: '4 Mei 2026 - 6 Mei 2026',
                    reason: 'Mengalami demam tinggi dan flu berat, disarankan dokter untuk istirahat total selama 3 hari di rumah. Surat keterangan dokter sudah dilampirkan.',
                    attachment: 'Surat_Keterangan_Dokter_Ahmad.pdf',
                    avatar: 'https://ui-avatars.com/api/?name=Ahmad+Syafiq&background=random'
                },
                {
                    id: 2,
                    name: 'Budi Santoso',
                    position: 'Security',
                    type: 'Izin Cuti',
                    date: '10 Mei 2026 - 12 Mei 2026',
                    reason: 'Mengambil jatah cuti tahunan untuk keperluan keluarga di luar kota (menghadiri pernikahan saudara kandung).',
                    attachment: null,
                    avatar: 'https://ui-avatars.com/api/?name=Budi+Santoso&background=random'
                },
                {
                    id: 3,
                    name: 'Citra Kirana',
                    position: 'Cleaning Service',
                    type: 'Izin Sakit',
                    date: '4 Mei 2026',
                    reason: 'Migrain akut sejak pagi hari ini sehingga tidak memungkinkan untuk berangkat bekerja. Berobat jalan ke klinik terdekat.',
                    attachment: 'Kwitansi_Klinik_Citra.jpg',
                    avatar: 'https://ui-avatars.com/api/?name=Citra+Kirana&background=random'
                },
                {
                    id: 4,
                    name: 'Diana Putri',
                    position: 'Administrasi',
                    type: 'Izin Lainnya',
                    date: '20 Mei 2026',
                    reason: 'Menghadiri acara wisuda adik kandung di universitas luar kota.',
                    attachment: 'Undangan_Wisuda.pdf',
                    avatar: 'https://ui-avatars.com/api/?name=Diana+Putri&background=random'
                }
            ],

            get pendingCount() {
                return this.requests.length;
            },

            get filteredRequests() {
                return this.requests.filter(request => {
                    const typeCategory = request.type === 'Izin Sakit' ? 'sakit' : 'cuti';
                    const matchesTab = this.activeTab === 'all' || this.activeTab === typeCategory;
                    const matchesSearch = request.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return matchesTab && matchesSearch;
                });
            },

            getTypeBadgeClass(type) {
                if (type === 'Izin Sakit') {
                    return 'bg-red-50 text-red-600 border border-red-100';
                }
                return 'bg-blue-50 text-blue-600 border border-blue-100';
            },

            openModal(request) {
                this.selectedRequest = request;
                this.isModalOpen = true;
                document.body.classList.add('overflow-hidden');
            },

            closeModal() {
                this.isModalOpen = false;
                setTimeout(() => {
                    this.selectedRequest = null;
                }, 300);
                document.body.classList.remove('overflow-hidden');
            },

            handleAction(id, action) {
                // Simulasi memproses data
                const index = this.requests.findIndex(r => r.id === id);
                if (index !== -1) {
                    const processed = this.requests[index];
                    this.requests.splice(index, 1);

                    // Simpan ke riwayat
                    const now = new Date();
                    const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                    this.historyRequests.unshift({
                        ...processed,
                        status: action === 'approve' ? 'Diterima' : 'Ditolak',
                        processedAt: timeString + ' WIB'
                    });
                }
            }
        }));
    });
</script>
@endsection
