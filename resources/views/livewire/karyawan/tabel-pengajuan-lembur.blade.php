<div>
    {{-- TABEL RIWAYAT --}}
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
                <select wire:model.live="filterStatus"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="semua">Semua Status</option>
                    <option value="pending">Menunggu</option>
                    <option value="valid">Disetujui</option>
                    <option value="invalid">Ditolak</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Tgl Pengajuan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Jam Lembur</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($pengajuan as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $pengajuan->firstItem() + $index }}</td>
                            <td class="px-5 py-3.5 text-gray-700">
                                {{ $item->tanggal_dibuat ? \Carbon\Carbon::parse($item->tanggal_dibuat)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-700">
                                {{ $item->mulai_lembur ? \Carbon\Carbon::parse($item->mulai_lembur)->format('H:i') : '-' }}
                                -
                                {{ $item->selesai_lembur ? \Carbon\Carbon::parse($item->selesai_lembur)->format('H:i') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 max-w-xs truncate">{{ $item->keterangan }}</td>
                            <td class="px-5 py-3.5">
                                @if ($item->status_validasi === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i>Menunggu
                                    </span>
                                @elseif ($item->status_validasi === 'valid')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                                        <i class="fa-solid fa-check text-[10px]"></i>Disetujui
                                    </span>
                                @elseif ($item->status_validasi === 'invalid')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600">
                                        <i class="fa-solid fa-times text-[10px]"></i>Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-300">
                                <i class="fa-solid fa-folder-open text-4xl mb-2 block"></i>
                                <p class="text-sm">Belum ada riwayat pengajuan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-gray-50">
            {{ $pengajuan->links() }}
        </div>
    </div>
</div>
