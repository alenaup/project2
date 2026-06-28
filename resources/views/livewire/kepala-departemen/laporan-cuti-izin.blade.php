<div class="max-w-6xl mx-auto p-6 bg-white/70 rounded-2xl shadow space-y-8 mt-4">

    <!-- TITLE & FILTERS -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-calendar-check text-blue-500"></i>
                Cuti & Izin
            </h1>
            <p class="text-sm text-gray-500">Riwayat pengajuan cuti, izin, dan sakit karyawan di departemen Anda</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- SEARCH USER -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari karyawan..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm shadow-xs focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition bg-white"
                >
            </div>

            <!-- DATE FILTER -->
            <div class="relative w-full sm:w-48">
                <div class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fas fa-calendar text-xs"></i>
                </div>
                <input
                    type="date"
                    wire:model.live="filterDate"
                    class="w-full pl-9 pr-8 py-2 border border-gray-300 rounded-xl text-sm shadow-xs focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition cursor-pointer bg-white"
                >
                @if($filterDate)
                    <button
                        type="button"
                        wire:click="$set('filterDate', '')"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-red-500 transition"
                    >
                        <i class="fas fa-times text-xs"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- CARD STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- SAKIT -->
        <div class="bg-white/90 border border-slate-100 rounded-2xl p-5 flex items-center justify-between shadow-xs transition duration-300">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-xl text-red-500">
                <i class="fas fa-notes-medical"></i>
            </div>
            <div class="text-right flex-1">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Sakit</div>
                <div class="text-3xl font-extrabold text-red-500 mt-1">{{ $totalSakit }}</div>
            </div>
        </div>

        <!-- IZIN -->
        <div class="bg-white/90 border border-slate-100 rounded-2xl p-5 flex items-center justify-between shadow-xs transition duration-300">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-xl text-amber-500">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="text-right flex-1">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Izin</div>
                <div class="text-3xl font-extrabold text-amber-500 mt-1">{{ $totalIzin }}</div>
            </div>
        </div>

        <!-- CUTI -->
        <div class="bg-white/90 border border-slate-100 rounded-2xl p-5 flex items-center justify-between shadow-xs transition duration-300">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-xl text-blue-500">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="text-right flex-1">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Cuti</div>
                <div class="text-3xl font-extrabold text-blue-600 mt-1">{{ $totalCuti }}</div>
            </div>
        </div>
    </div>

    <!-- TABLE AREA -->
    <div class="bg-white rounded-2xl shadow-xs overflow-hidden border border-slate-100">
        <div class="px-5 py-4 border-b text-sm font-semibold text-slate-700 bg-slate-50 flex items-center gap-2">
            <i class="fas fa-list text-slate-450"></i>
            Daftar Pengajuan Karyawan
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-semibold">
                        <th class="p-4">Karyawan</th>
                        <th class="p-4">Tanggal Pengajuan</th>
                        <th class="p-4">Tanggal Mulai</th>
                        <th class="p-4">Tanggal Selesai</th>
                        <th class="p-4">Tipe</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4">Lampiran</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $item)
                        @php
                            $jenis = $this->getJenis($item);
                            $initials = strtoupper(substr($item->karyawan->nama_lengkap ?? 'K', 0, 2));
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition duration-150">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shadow-xs">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-sm">{{ $item->karyawan->nama_lengkap ?? '-' }}</div>
                                        <div class="text-[10px] text-slate-400">NIP: {{ $item->karyawan->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-slate-600 text-xs">
                                {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="p-4 text-slate-600 text-xs">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                            </td>
                            <td class="p-4 text-slate-600 text-xs">
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold
                                    {{ $jenis === 'Sakit' ? 'bg-red-50 text-red-700' : '' }}
                                    {{ $jenis === 'Cuti' ? 'bg-blue-50 text-blue-700' : '' }}
                                    {{ $jenis === 'Izin' ? 'bg-amber-50 text-amber-700' : '' }}
                                ">
                                    @if($jenis === 'Sakit')
                                        <i class="fas fa-notes-medical text-[10px]"></i>
                                    @elseif($jenis === 'Cuti')
                                        <i class="fas fa-calendar text-[10px]"></i>
                                    @else
                                        <i class="fas fa-file-alt text-[10px]"></i>
                                    @endif
                                    {{ $jenis }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600 max-w-xs truncate" title="{{ $item->keterangan }}">
                                {{ $item->keterangan }}
                            </td>
                            <td class="p-4">
                                @if($item->file_surat)
                                    <a href="{{ Storage::url($item->file_surat) }}" target="_blank" 
                                        class="inline-flex items-center gap-1 text-xs text-emerald-600 hover:text-emerald-800 font-semibold hover:underline">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        <span>Lihat File</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-350">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $item->status === 'menunggu' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                    {{ $item->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    {{ $item->status === 'ditolak' ? 'bg-rose-50 text-rose-700' : '' }}
                                ">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                <i class="fas fa-folder-open text-2xl mb-2 block"></i>
                                Tidak ada data pengajuan perizinan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
