@extends('layouts.karyawanOutsourcing')

<script src="{{ asset('js/karyawan_js/pengajuan.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/karyawan/pengajuan.css') }}">

@section('content')
    <!-- PAGE CONTENT -->
    <div class="space-y-6">

        <!-- Page title -->
        <div class="fade-in-up">
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Pengajuan Lembur ⏱️</h1>
            <p class="text-sm text-gray-500 mt-0.5">Isi form pengajuan lembur atau laporan selesai lembur</p>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up"
            style="animation-delay:.05s">

            <!-- Card header + tab pills -->
            <div style="background: linear-gradient(to right, #2d6e4a, #3C8B5E);" class="px-5 py-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-white font-bold text-base">
                            <i class="fa-solid fa-file-circle-plus mr-2 opacity-75"></i>Form Pengajuan
                        </h2>
                        <p class="text-green-200 text-xs mt-0.5">Pilih jenis Pengajuan</p>
                    </div>

                    <!-- Tab pills -->
                    <div class="flex rounded-xl overflow-hidden border border-white/40 self-start sm:self-auto">
                        <button onclick="switchTab(0)" id="tabBtn0"
                            class="px-5 py-2 text-sm font-semibold transition-all duration-300 flex items-center gap-2 bg-white text-[#2d6e4a] border-r border-white/30">
                            <i class="fa-solid fa-stopwatch text-xs"></i>
                            Pengajuan Lembur
                        </button>
                        {{-- <button onclick="switchTab(1)" id="tabBtn1"
                            class="px-5 py-2 text-sm font-semibold transition-all duration-300 flex items-center gap-2 text-white hover:bg-white/10">
                            <i class="fa-solid fa-flag-checkered text-xs"></i>
                            Selesai Lembur
                        </button> --}}
                    </div>
                </div>
            </div>

            <!-- Slide track -->
            <div style="overflow:hidden">
                <div class="slide-track" id="slideTrack" style="transform:translateX(0%)">

                    <!-- PANEL 0: Pengajuan Lembur -->
                    <div class="slide-panel px-6 py-6">
                        <!-- Section title -->
                        <div class="flex items-center gap-2 mb-5">
                            <div class="section-bar bg-[#3C8B5E]"></div>
                            <h3 class="font-semibold text-gray-700 text-sm">Data Diri — Pengajuan Lembur</h3>
                        </div>

                        <div class="grid md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label class="inp-label">NIM <span class="req">*</span></label>
                                <input id="nim0" type="text" value="1406200062026" readonly class="inp">
                            </div>
                            <div>
                                <label class="inp-label">Nama Lengkap <span class="req">*</span></label>
                                <input id="nama0" type="text" value="Rangga Racing" readonly class="inp">
                            </div>
                            <div>
                                <label class="inp-label">No. HP <span class="req">*</span></label>
                                <input id="noHp0" type="tel" value="085363356775" readonly class="inp">
                            </div>
                            <div>
                                <label class="inp-label">Email <span class="req">*</span></label>
                                <input id="email0" type="email" value="rangga@gmail.com" readonly class="inp">
                            </div>
                            <div>
                                <label class="inp-label">Tanggal Lembur <span class="req">*</span></label>
                                <input id="tanggal0" type="date" class="inp">
                            </div>
                            <div>
                                <label class="inp-label">
                                    Asal Departemen
                                    <span class="ml-1 text-xs px-2 py-0.5 rounded-full"></span>
                                </label>
                                <input type="text" value="Kebersihan" readonly class="inp">
                            </div>
                            <div class="md:col-span-2">
                                <label class="inp-label">Alamat <span class="req">*</span></label>
                                <input id="alamat0" type="text" value="Nongsa No 12" readonly class="inp">
                            </div>

                            <!-- Keterangan -->
                            <div class="md:col-span-2">
                                <div class="flex items-center gap-2 mb-3 mt-2">
                                    <div class="section-bar bg-blue-400"></div>
                                    <h3 class="font-semibold text-gray-700 text-sm">Keterangan</h3>
                                </div>
                                <label class="inp-label">Apa yang akan dikerjakan hari ini? <span
                                        class="req">*</span></label>
                                <textarea id="ket0" rows="7" oninput="hitungKata(0)"
                                    placeholder="Tuliskan rencana pekerjaan yang akan dilakukan saat lembur..." class="inp resize-none leading-relaxed"
                                    style="min-height:160px;"></textarea>
                                <!-- word counter -->
                                <div class="mt-2 flex items-center justify-end gap-3">
                                    <div class="flex-1 bg-gray-100 rounded-full overflow-hidden" style="height:4px">
                                        <div id="bar0" class="h-full rounded-full transition-all"
                                            style="width:0%;background:#3C8B5E"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">
                                        <span id="wc0" class="font-semibold text-gray-600">0</span> / 300 kata
                                    </span>
                                </div>
                                <p id="warn0" class="text-xs text-red-500 mt-1 hidden">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>Melebihi batas 300 kata!
                                </p>
                            </div>
                        </div>

                        <!-- Footer actions -->
                        <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                            <button onclick="submit(0)"
                                class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition active:scale-95 flex items-center gap-2 shadow-sm"
                                style="background:#3C8B5E">
                                <i class="fa-solid fa-paper-plane"></i>Ajukan Lembur
                            </button>
                        </div>
                    </div>
                    <!-- /panel 0 -->

                    <!-- PANEL 1: Selesai Lembur -->

                </div>
            </div>
        </div>


        <!-- TABEL RIWAYAT -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up"
            style="animation-delay:.10s">

            <div
                class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-gray-800 text-base flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-[#3C8B5E]"></i>
                        Riwayat Pengajuan Lembur
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar seluruh pengajuan yang telah diajukan</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <select id="filterJenis" onchange="renderRiwayat()"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <option value="semua">Semua Jenis</option>
                        <option value="lembur">Pengajuan Lembur</option>
                        <option value="selesai">Selesai Lembur</option>
                    </select>
                    <select id="filterValidasi" onchange="renderRiwayat()"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <option value="semua">Semua Status</option>
                        <option value="validated">Sudah Divalidasi</option>
                        <option value="pending">Belum Divalidasi</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">No
                            </th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Tgl Pengajuan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Jenis</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelRiwayat" class="divide-y divide-gray-50"></tbody>
                </table>
            </div>

            <div id="emptyState" class="hidden text-center py-12 text-gray-300">
                <i class="fa-solid fa-folder-open text-4xl mb-2 block"></i>
                <p class="text-sm">Belum ada riwayat pengajuan</p>
            </div>

            <div class="px-5 py-3 border-t border-gray-50">
                <p class="text-xs text-gray-400" id="totalInfo"></p>
            </div>
        </div>

    </div>

    <!-- MODAL SUKSES -->
    <div id="modalSukses" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 mx-4 max-w-sm w-full text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" id="modalIcon"
                style="background:#dcfce7">
                <i class="fa-solid fa-check text-2xl" style="color:#3C8B5E"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1" id="modalTitle">Pengajuan Terkirim!</h3>
            <p class="text-sm text-gray-500 mb-6" id="modalPesan"></p>
            <button onclick="tutupModal()"
                class="w-full py-3 rounded-xl font-semibold text-white transition active:scale-95" id="modalBtn"
                style="background:#3C8B5E">
                Oke, Mengerti
            </button>
        </div>
    </div>
@endsection
