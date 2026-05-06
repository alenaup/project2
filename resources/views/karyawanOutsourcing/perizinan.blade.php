@extends('layouts.karyawanOutsourcing')
<link rel="stylesheet" href="{{ asset('css/karyawan/perizinan.css') }}">
<script src="{{ asset('js/karyawan_js/perizinan.js') }}" defer></script>

@section('content')
        <!-- MAIN CONTENT -->
        <div class="flex-1 p-4 md:p-6 max-w-7xl mx-auto">

            <!-- PAGE TITLE -->
            <div class="mb-6">
                <h1 class="text-xl font-bold text-gray-800 md:text-2xl">
                    Perizinan Sakit <i class="fa-solid fa-notes-medical text-emerald-600 ml-1"></i>
                </h1>
                <p class="text-gray-500 text-sm">Unggah dan kelola surat keterangan sakit kamu di sini</p>
            </div>


            <!-- FORM PENGAJUAN -->
            <div class="bg-white rounded-xl shadow mb-6 border-l-4 border-emerald-500 overflow-hidden">

                <!-- Header Card + Tab Switch -->
                <div class="px-6 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-file-medical text-emerald-600"></i>
                            Form Pengajuan Izin Sakit
                        </h2>

                        <!-- Tab Switch -->
                        <div class="relative inline-flex bg-[#3C8B5E] rounded-xl p-1 w-full sm:w-auto">
                            <!-- Sliding pill -->
                            <div id="tabPill"
                                class="absolute top-1 left-1 h-[calc(100%-8px)] rounded-lg bg-white shadow-md"
                                style="width:calc(95% - 4px); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);"></div>

                            <button type="button" id="tabMulai" onclick="switchTab('mulai')"
                                class="relative z-10 flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold transition-colors duration-200 text-[#3C8B5E]">
                                <i class="fa-solid fa-calendar-day text-xs"></i>
                                Tanggal Mulai
                            </button>

                        </div>
                    </div>

                    <!-- Ringkasan tanggal -->
                    <div id="ringkasanTanggal" class="hidden mt-2 flex items-center gap-2 text-xs">
                        <i class="fa-solid fa-arrow-right-long text-emerald-500"></i>
                        <span id="teksRingkasan" class="text-gray-500"></span>
                    </div>
                </div>

                <!-- Slide Viewport -->
                <div class="relative overflow-hidden">

                    <!-- SLIDE 1 — Tanggal Mulai -->
                    <div id="slideCardMulai"
                        style="transition: transform 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease;
                               transform: translateX(0); opacity: 1;">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-2">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input id="tglMulai" type="date"
                                    class="w-full border border-gray-200 rounded-lg p-3"
                                    onchange="updateRingkasan()">
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">
                                    Keterangan / Diagnosis<span class="text-red-500">*</span>
                                </label>
                                <textarea id="keterangan" rows="3"
                                    class="w-full border border-gray-200 rounded-lg p-3"></textarea>
                            </div>

                            <!-- Upload Surat -->
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-2">
                                    Upload Surat Sakit<span class="text-red-500">*</span>
                                </label>

                                <div id="dropZone"
                                    class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer"
                                    onclick="document.getElementById('fileSurat').click()">

                                    Klik atau upload file

                                    <div class="flex flex-col items-center gap-2">


                                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-emerald-500"></i>
                                        <p class="text-sm font-medium text-gray-600">Klik atau upload file</p>
                                        <p class="text-xs text-gray-400">JPG, PNG, PDF (Max 5MB)</p>
                                    </div>

                                    <input id="fileSurat" type="file"
                                        accept=".jpg,.jpeg,.png,.pdf"
                                        class="hidden"
                                        onchange="handleFileSelect(this)">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Divider -->
                <div class="border-t border-gray-100 mx-6"></div>

                <!-- Bagian bawah (selalu tampil): Keterangan + Upload + Tombol -->
                <div class="p-6 space-y-4">



                    <!-- SUBMIT -->
                    <div class="flex gap-3 pt-1">
                        <button onclick="submitPerizinan()"
                            class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white py-3 px-8 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            Kirim Pengajuan
                        </button>
                        <button onclick="resetForm()"
                            class="flex-1 md:flex-none bg-gray-100 hover:bg-gray-200 active:scale-95 text-gray-600 py-3 px-8 rounded-lg font-semibold transition-all duration-200">
                            Reset
                        </button>
                    </div>
                </div>
            </div>


            <!-- RIWAYAT PENGAJUAN -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                        Riwayat Pengajuan
                    </h2>

                    <!-- Filter Status -->
                    <select id="filterStatus" onchange="renderRiwayat()"
                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>

                <!-- List Riwayat -->
                <div id="listRiwayat" class="space-y-3"></div>

                <!-- Empty State -->
                <div id="emptyState" class="hidden text-center py-12 text-gray-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                    <p class="font-medium">Belum ada pengajuan</p>
                    <p class="text-xs mt-1">Pengajuan yang kamu kirim akan muncul di sini</p>
                </div>
            </div>

        </div>
    </div>


    <!-- ===== MODAL PREVIEW FILE ===== -->
    <div id="modalPreview"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 modal-backdrop p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h3 id="modalTitle" class="font-semibold text-gray-800 truncate pr-4"></h3>
                <button onclick="tutupModal('modalPreview')"
                    class="text-gray-400 hover:text-gray-600 transition text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 flex justify-center bg-gray-50 max-h-[70vh] overflow-auto">
                <img id="previewImg" src="" alt="Preview Surat" class="rounded-lg shadow">
                <div id="previewPdf"
                    class="text-center py-10 text-gray-500 hidden">
                    <i class="fa-solid fa-file-pdf text-red-400 text-5xl mb-3 block"></i>
                    <p class="font-medium">File PDF tidak bisa dipreview langsung</p>
                    <p class="text-sm mt-1">Silakan unduh untuk melihat isinya</p>
                    <a id="downloadLink" href="#" download
                        class="mt-4 inline-block bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">
                        <i class="fa-solid fa-download mr-1"></i> Unduh File
                    </a>
                </div>
            </div>

        </div>
    </div>


    <!-- ===== MODAL EDIT PENGAJUAN ===== -->
    <div id="modalEdit"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 modal-backdrop p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h3 class="font-semibold text-gray-800">Edit Pengajuan</h3>
                <button onclick="tutupModal('modalEdit')"
                    class="text-gray-400 hover:text-gray-600 transition text-xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-5 space-y-4">

                <input id="editId" type="hidden">

                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Tanggal</label>
                    <input id="editTglMulai" type="date"
                        class="w-full border border-gray-200 rounded-lg p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">Keterangan</label>
                    <textarea id="editKeterangan" rows="3"
                        class="w-full border border-gray-200 rounded-lg p-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                </div>

                <!-- Ganti Surat -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-2">
                        Ganti Surat Sakit
                        <span class="text-gray-400 font-normal text-xs ml-1">(opsional)</span>
                    </label>

                    <!-- Surat Lama -->
                    <div id="suratLamaContainer" class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl p-3 mb-2">
                        <div id="editPreviewIcon" class="text-2xl w-8 text-center"></div>
                        <div class="flex-1 min-w-0">
                            <p id="editNamaFile" class="text-xs font-semibold text-gray-700 truncate"></p>
                            <p class="text-xs text-gray-400">Surat saat ini</p>
                        </div>
                        <button onclick="document.getElementById('editFileSurat').click()"
                            class="text-emerald-600 text-xs font-semibold hover:underline whitespace-nowrap">
                            <i class="fa-solid fa-arrow-up-from-bracket mr-1"></i>Ganti
                        </button>
                    </div>

                    <input id="editFileSurat" type="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden"
                        onchange="handleEditFileSelect(this)">

                    <!-- New File Preview -->
                    <div id="editFilePreview" class="hidden">
                        <div class="file-card card-hover border border-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                            <div id="editNewPreviewIcon" class="text-2xl w-8 text-center"></div>
                            <div class="flex-1 min-w-0">
                                <p id="editNamaFileBaru" class="text-xs font-semibold text-gray-700 truncate"></p>
                                <p class="text-xs text-gray-400">File baru akan menggantikan surat lama</p>
                            </div>
                            <button onclick="batalGantiSurat()"
                                class="text-red-400 text-xs hover:text-red-600 transition">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button onclick="simpanEdit()"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg font-semibold transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Perubahan
                    </button>
                    <button onclick="tutupModal('modalEdit')"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 rounded-lg font-semibold transition-all active:scale-95">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ===== MODAL KONFIRMASI HAPUS ===== -->
    <div id="modalHapus"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 modal-backdrop p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-2xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-2">Hapus Pengajuan?</h3>
            <p class="text-gray-500 text-sm mb-5">Pengajuan yang dihapus tidak dapat dikembalikan.</p>
            <input id="hapusId" type="hidden">
            <div class="flex gap-3">
                <button onclick="konfirmasiHapus()"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-lg font-semibold transition-all active:scale-95">
                    Ya, Hapus
                </button>
                <button onclick="tutupModal('modalHapus')"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 rounded-lg font-semibold transition-all active:scale-95">
                    Batal
                </button>
            </div>
        </div>
    </div>


    <!-- ===== TOAST NOTIFIKASI ===== -->
    <div id="toast"
        class="fixed bottom-6 right-6 z-[200] hidden items-center gap-3 bg-white border shadow-xl rounded-xl px-5 py-3 min-w-[250px] transition-all">
        <div id="toastIcon" class="text-xl"></div>
        <p id="toastMsg" class="text-sm font-medium text-gray-700"></p>
    </div>

@endsection
