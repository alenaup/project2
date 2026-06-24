<div x-data="{ searchQuery: @entangle('search').live }">

    {{-- ─── Flash Messages ───────────────────────────────────────── --}}
    @if (session()->has('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ─── Header Section ─────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Validasi Perizinan</h1>
            <p class="text-base text-gray-500 mt-2 max-w-xl">Kelola permohonan izin sakit dan cuti karyawan outsourcing.</p>
        </div>

        {{-- Stats --}}
        <div class="flex space-x-4">
            <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                <div class="p-3 bg-amber-50 rounded-xl text-amber-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menunggu Validasi</p>
                    <p class="text-3xl font-black text-gray-900 mt-0.5">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Search Bar ──────────────────────────────────────────── --}}
    <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-2">
        <div class="relative w-full md:w-72">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama karyawan..."
                class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-[#009254] focus:border-[#009254] outline-none transition-all duration-300 text-sm font-medium text-gray-700 placeholder-gray-400">
            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    {{-- ─── Daftar Pengajuan Menunggu ───────────────────────────── --}}
    <div class="space-y-4">
        {{-- Table Header --}}
        <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest">
            <div class="col-span-4">Karyawan</div>
            <div class="col-span-3">Jenis & Tanggal</div>
            <div class="col-span-3">Keterangan Singkat</div>
            <div class="col-span-2 text-right">Aksi</div>
        </div>

        @forelse ($pengajuanList as $pengajuan)
            @php
                $jenisPerizinan = 'Izin Sakit'; // karena tabel ini khusus perizinan sakit
                $tanggalMulai   = \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->translatedFormat('d M Y');
                $tanggalSelesai = \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->translatedFormat('d M Y');
                $tanggalLabel   = $tanggalMulai === $tanggalSelesai
                    ? $tanggalMulai
                    : $tanggalMulai . ' - ' . $tanggalSelesai;
                $namaInisial    = mb_substr($pengajuan->karyawan->nama_lengkap ?? '?', 0, 1);
            @endphp

            <div
                wire:key="pengajuan-{{ $pengajuan->id_perizinan }}"
                class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md hover:border-green-100 transition-all duration-300 group">
                <div class="flex flex-col md:grid md:grid-cols-12 gap-4 items-start md:items-center">

                    {{-- Informasi Karyawan --}}
                    <div class="col-span-4 flex items-center space-x-4 w-full">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 text-white flex items-center justify-center font-bold text-lg shadow-sm border-2 border-white">
                                {{ $namaInisial }}
                            </div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white bg-red-500"></div>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 group-hover:text-[#009254] transition-colors">
                                {{ $pengajuan->karyawan->nama_lengkap ?? '-' }}
                            </h3>
                            <p class="text-xs font-medium text-gray-500">
                                {{ $pengajuan->karyawan->departemen?->nama_departemen ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Jenis & Tanggal --}}
                    <div class="col-span-3 flex flex-col space-y-1.5 w-full">
                        <div>
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide bg-red-50 text-red-600 border border-red-100">
                                {{ $jenisPerizinan }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm font-semibold text-gray-600">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $tanggalLabel }}
                        </div>
                    </div>

                    {{-- Keterangan Singkat --}}
                    <div class="col-span-3 w-full">
                        <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">{{ $pengajuan->keterangan }}</p>
                        @if ($pengajuan->file_surat)
                            <div class="mt-1 flex items-center text-xs font-semibold text-[#009254]">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Terlampir 1 File
                            </div>
                        @endif
                    </div>

                    {{-- Aksi --}}
                    <div class="col-span-2 flex items-center justify-end space-x-2 w-full mt-4 md:mt-0">
                        <button
                            wire:click="openDetail({{ $pengajuan->id_perizinan }})"
                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200"
                            title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                        <button
                            wire:click="openApprove({{ $pengajuan->id_perizinan }})"
                            class="flex-1 md:flex-none px-4 py-2 bg-[#009254] text-white hover:bg-[#007a46] rounded-xl text-sm font-bold shadow-sm shadow-green-200 transition-all duration-200 transform hover:-translate-y-0.5">
                            Terima
                        </button>
                        <button
                            wire:click="openReject({{ $pengajuan->id_perizinan }})"
                            class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-100 rounded-xl text-sm font-bold transition-all duration-200 transform hover:-translate-y-0.5">
                            Tolak
                        </button>
                    </div>

                </div>
            </div>

        @empty
            {{-- Empty State --}}
            <div class="text-center py-16 bg-white/50 backdrop-blur-sm rounded-3xl border-2 border-gray-100 border-dashed">
                <div class="w-20 h-20 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-5 border border-gray-50">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-2">Tidak ada pengajuan ditemukan</h3>
                <p class="text-gray-500 text-base max-w-sm mx-auto">Selesai! Belum ada permohonan izin atau cuti yang memerlukan validasi saat ini.</p>
            </div>
        @endforelse
    </div>

    {{-- ─── Riwayat Validasi Hari Ini ──────────────────────────── --}}
    @if ($riwayatList->count() > 0)
        <div class="mt-12"
             x-data
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-[#009254]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Validasi Hari Ini
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
                                <th class="px-6 py-4">Tanggal Izin</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Waktu Proses</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($riwayatList as $riwayat)
                                <tr
                                    wire:click="openDetail({{ $riwayat->id_perizinan }})"
                                    class="hover:bg-gray-50/50 transition-colors cursor-pointer">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 text-white flex items-center justify-center font-bold text-sm">
                                                {{ mb_substr($riwayat->karyawan->nama_lengkap ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $riwayat->karyawan->nama_lengkap ?? '-' }}</p>
                                                <p class="text-xs text-gray-500">{{ $riwayat->karyawan->departemen?->nama_departemen ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold text-gray-600">Izin Sakit</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold text-gray-600">
                                            {{ \Carbon\Carbon::parse($riwayat->tanggal_mulai)->translatedFormat('d M Y') }}
                                            @if ($riwayat->tanggal_mulai !== $riwayat->tanggal_selesai)
                                                &ndash; {{ \Carbon\Carbon::parse($riwayat->tanggal_selesai)->translatedFormat('d M Y') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($riwayat->status === 'disetujui')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Diterima
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-medium text-gray-500">
                                            {{ \Carbon\Carbon::parse($riwayat->updated_at)->format('H:i') }} WIB
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Detail Pengajuan                                    --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if ($showDetailModal && $detailPengajuan)
        {{-- Wrapper: backdrop + centering --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="modal-detail-title">

            {{-- Backdrop (di bawah panel) --}}
            <div
                wire:click="closeDetail"
                class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm"
                aria-hidden="true">
            </div>

            {{-- Modal Panel --}}
            <div class="relative z-10 bg-white rounded-3xl shadow-2xl w-full max-w-xl border border-gray-100 overflow-hidden max-h-[90vh] flex flex-col">

                {{-- Header --}}
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 flex-shrink-0">
                    <div class="flex items-center space-x-3">
                        <h3 class="text-xl font-extrabold text-gray-900" id="modal-detail-title">Detail Permohonan</h3>
                        @if ($detailPengajuan['status'] === 'disetujui')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Diterima
                            </span>
                        @elseif ($detailPengajuan['status'] === 'ditolak')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Ditolak
                            </span>
                        @endif
                    </div>
                    <button wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 bg-white p-2 rounded-full shadow-sm border border-gray-100 hover:bg-gray-50 transition-all">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Content (scrollable) --}}
                <div class="px-8 py-6 overflow-y-auto flex-1">
                    {{-- User Header --}}
                    <div class="flex items-center space-x-5 mb-8">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-500 text-white flex items-center justify-center font-bold text-2xl shadow-sm flex-shrink-0">
                            {{ mb_substr($detailPengajuan['nama'], 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">{{ $detailPengajuan['nama'] }}</h4>
                            <p class="text-sm font-semibold text-[#009254]">{{ $detailPengajuan['departemen'] }}</p>
                            <p class="text-xs text-gray-400">{{ $detailPengajuan['vendor'] }}</p>
                        </div>
                    </div>

                    {{-- Detail Grid --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-2">Jenis Permohonan</p>
                                <span class="px-3 py-1 rounded-lg text-sm font-bold uppercase tracking-wide bg-red-50 text-red-600 border border-red-100">Izin Sakit</span>
                            </div>
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-2">Tanggal Pelaksanaan</p>
                                <p class="text-sm font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($detailPengajuan['tanggal_mulai'])->translatedFormat('d M Y') }}
                                    @if ($detailPengajuan['tanggal_mulai'] !== $detailPengajuan['tanggal_selesai'])
                                        <br><span class="text-gray-500 font-normal">&ndash; {{ \Carbon\Carbon::parse($detailPengajuan['tanggal_selesai'])->translatedFormat('d M Y') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-3">Alasan / Keterangan</p>
                            <div class="bg-gray-50 rounded-2xl p-5 text-sm text-gray-700 leading-relaxed border border-gray-100 shadow-inner">
                                {{ $detailPengajuan['keterangan'] }}
                            </div>
                        </div>

                        @if ($detailPengajuan['file_surat'])
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-3">Dokumen Lampiran</p>
                                <a href="{{ Storage::url($detailPengajuan['file_surat']) }}" target="_blank"
                                   class="border-2 border-dashed border-gray-200 rounded-2xl p-5 flex items-center justify-between bg-white group hover:border-[#009254] hover:bg-green-50 transition-all cursor-pointer">
                                    <div class="flex items-center text-gray-700 group-hover:text-[#009254]">
                                        <div class="p-2 bg-gray-50 group-hover:bg-white rounded-lg mr-4 border border-gray-100">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold block">{{ basename($detailPengajuan['file_surat']) }}</span>
                                            <span class="text-xs text-gray-500 font-medium">Klik untuk melihat file</span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#009254] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 flex-shrink-0">
                    <button wire:click="closeDetail" class="w-full sm:w-auto px-6 py-3 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-sm font-bold transition-all duration-200">
                        Tutup
                    </button>
                    @if ($detailPengajuan['status'] === 'menunggu')
                        <button
                            wire:click="openRejectFromDetail({{ $detailPengajuan['id'] }})"
                            class="w-full sm:w-auto px-6 py-3 bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 rounded-xl text-sm font-bold transition-all duration-200 flex items-center justify-center">
                            Tolak Permohonan
                        </button>
                        <button
                            wire:click="openApproveFromDetail({{ $detailPengajuan['id'] }})"
                            class="w-full sm:w-auto px-6 py-3 bg-[#009254] text-white hover:bg-[#007a46] rounded-xl text-sm font-bold shadow-md shadow-green-200 transition-all duration-200 flex items-center justify-center transform hover:-translate-y-0.5">
                            Terima Permohonan
                        </button>
                    @endif
                </div>

            </div>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Konfirmasi Terima                                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if ($showApproveModal)
        <div class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center"
             wire:click.self="closeApprove">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-extrabold text-gray-800 text-lg">Terima Permohonan?</h3>
                <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin <strong class="text-green-600">menerima</strong> permohonan izin ini?</p>

                <div class="flex gap-3 mt-6">
                    <button wire:click="closeApprove"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 py-2.5 rounded-xl text-sm font-semibold transition">
                        Batal
                    </button>
                    <button wire:click="approve"
                        class="flex-1 bg-[#009254] hover:bg-[#007a46] text-white py-2.5 rounded-xl text-sm font-bold shadow-md shadow-green-200 transition">
                        Ya, Terima
                    </button>
                </div>
            </div>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Konfirmasi Tolak                                    --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if ($showRejectModal)
        <div class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center"
             wire:click.self="closeReject">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 class="font-extrabold text-gray-800 text-lg">Tolak Permohonan?</h3>
                <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin <strong class="text-red-600">menolak</strong> permohonan izin ini?</p>

                <div class="flex gap-3 mt-6">
                    <button wire:click="closeReject"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 py-2.5 rounded-xl text-sm font-semibold transition">
                        Batal
                    </button>
                    <button wire:click="reject"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl text-sm font-bold shadow-md shadow-red-200 transition">
                        Ya, Tolak
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
