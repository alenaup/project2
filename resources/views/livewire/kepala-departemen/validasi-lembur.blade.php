<div>
    {{-- Success/Error Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm"
            role="alert">
            <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-6xl mx-auto p-6 bg-white/70 rounded-2xl shadow">

        {{-- Title --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Persetujuan Lembur</h2>
            <p class="text-gray-500 text-sm">Klik salah satu data untuk melihat detail dan memberikan keputusan.</p>
        </div>

        {{-- Filter --}}
        <div class="mb-4 flex justify-end">
            <select wire:model.live="filterStatus"
                class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="semua">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="valid">Disetujui</option>
                <option value="invalid">Ditolak</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3 text-left">No</th>
                        <th class="p-3 text-left">Nama</th>
                        <th class="p-3 text-left">Tanggal</th>
                        <th class="p-3 text-left">Jam</th>
                        <th class="p-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($pengajuan as $index => $item)
                        <tr wire:click="lihatDetail({{ $item->id_lembur }})"
                            class="cursor-pointer hover:bg-green-50 transition">
                            <td class="p-3">{{ $pengajuan->firstItem() + $index }}</td>
                            <td class="p-3 font-medium text-gray-800">
                                {{ $item->karyawan->nama_lengkap ?? '-' }}</td>
                            <td class="p-3">
                                {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '-' }}
                            </td>
                            <td class="p-3">
                                {{ $item->mulai_lembur ? \Carbon\Carbon::parse($item->mulai_lembur)->format('H:i') : '-' }}
                                -
                                {{ $item->selesai_lembur ? \Carbon\Carbon::parse($item->selesai_lembur)->format('H:i') : '-' }}
                            </td>
                            <td class="p-3">
                                @if ($item->status_validasi === 'pending')
                                    <span class="text-yellow-600 font-semibold">Menunggu</span>
                                @elseif ($item->status_validasi === 'valid')
                                    <span class="text-green-600 font-semibold">Disetujui</span>
                                @elseif ($item->status_validasi === 'invalid')
                                    <span class="text-red-600 font-semibold">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <i class="fas fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                                <p>Tidak ada data pengajuan lembur.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $pengajuan->links() }}
        </div>

        {{-- Modal Detail --}}
        @if ($selectedLembur)
            <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
                wire:click.self="tutupDetail">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 mx-4">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Detail Lembur</h3>
                        <button wire:click="tutupDetail"
                            class="text-gray-500 hover:text-red-500 text-xl transition">✕</button>
                    </div>

                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Nama:</strong> {{ $selectedLembur->karyawan->nama_lengkap ?? '-' }}</p>
                        <p><strong>Tanggal:</strong>
                            {{ $selectedLembur->created_at ? \Carbon\Carbon::parse($selectedLembur->created_at)->format('d M Y') : '-' }}
                        </p>
                        <p><strong>Jam:</strong>
                            {{ $selectedLembur->mulai_lembur ? \Carbon\Carbon::parse($selectedLembur->mulai_lembur)->format('H:i') : '-' }}
                            -
                            {{ $selectedLembur->selesai_lembur ? \Carbon\Carbon::parse($selectedLembur->selesai_lembur)->format('H:i') : '-' }}
                        </p>
                        <p><strong>Keterangan:</strong></p>
                        <p class="bg-gray-100 p-3 rounded-lg">{{ $selectedLembur->keterangan }}</p>
                        <p><strong>Status:</strong>
                            @if ($selectedLembur->status_validasi === 'pending')
                                <span class="text-yellow-600 font-semibold">Menunggu</span>
                            @elseif ($selectedLembur->status_validasi === 'valid')
                                <span class="text-green-600 font-semibold">Disetujui</span>
                            @elseif ($selectedLembur->status_validasi === 'invalid')
                                <span class="text-red-600 font-semibold">Ditolak</span>
                            @endif
                        </p>
                    </div>

                    {{-- Action buttons — only for pending --}}
                    @if ($selectedLembur->status_validasi === 'pending')
                        <div class="flex justify-end gap-3 mt-6">
                            <button wire:click="setujui({{ $selectedLembur->id_lembur }})"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-500 text-white rounded-xl hover:bg-green-600 transition flex items-center gap-2">
                                <span wire:loading.remove wire:target="setujui">Terima</span>
                                <span wire:loading wire:target="setujui"><i
                                        class="fa-solid fa-spinner fa-spin"></i></span>
                            </button>
                            <button wire:click="tolak({{ $selectedLembur->id_lembur }})"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition flex items-center gap-2">
                                <span wire:loading.remove wire:target="tolak">Tolak</span>
                                <span wire:loading wire:target="tolak"><i
                                        class="fa-solid fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        @endif

    </div>
</div>
