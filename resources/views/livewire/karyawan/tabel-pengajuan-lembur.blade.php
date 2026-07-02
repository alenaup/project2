<div x-data="{ 
    openEdit: false, 
    openDelete: false, 
    deleteId: null,

    edit_id: '',
    edit_tanggal: '',
    edit_mulai: '',
    edit_selesai: '',
    edit_keterangan: '',

    openEditModal(id, tanggal, mulai, selesai, keterangan) {
        this.edit_id = id;
        this.edit_tanggal = tanggal;
        this.edit_mulai = mulai;
        this.edit_selesai = selesai;
        this.edit_keterangan = keterangan;
        this.openEdit = true;
    }
}"
     @open-modal-edit.window="openEdit = true"
     @close-modal-edit.window="openEdit = false">
    {{-- TABEL RIWAYAT --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up"
        style="animation-delay:.10s">

        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
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

        @if (session('success_riwayat'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
                 class="mx-5 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold fade-in-up">
                <i class="fa-solid fa-circle-check text-lg"></i>
                {{ session('success_riwayat') }}
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
                 class="mx-5 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold fade-in-up">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                {{ session('error') }}
            </div>
        @endif

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
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Aksi</th>
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
                            <td class="px-5 py-3.5">
                                @if ($item->status_validasi === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEditModal({{ $item->id_lembur }}, '{{ \Carbon\Carbon::parse($item->mulai_lembur)->format('Y-m-d') }}', '{{ \Carbon\Carbon::parse($item->mulai_lembur)->format('H:i') }}', '{{ \Carbon\Carbon::parse($item->selesai_lembur)->format('H:i') }}', {{ json_encode($item->keterangan) }})" class="text-blue-500 hover:text-blue-700 transition w-8 h-8 flex items-center justify-center bg-blue-50 hover:bg-blue-100 rounded-lg cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button @click="deleteId = {{ $item->id_lembur }}; openDelete = true" class="text-red-500 hover:text-red-700 transition w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-100 rounded-lg">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-300 text-xs text-center block">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-300">
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

    <!-- Modal Hapus -->
    <div x-show="openDelete" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="absolute inset-0 bg-gray-900/60" @click="openDelete = false"></div>
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden text-center p-6"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
             
            <div class="w-16 h-16 rounded-full bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
            </div>
            
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Riwayat?</h3>
            <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus pengajuan lembur ini? Data yang dihapus tidak dapat dikembalikan.</p>
            
            <div class="flex gap-3">
                <button @click="openDelete = false" type="button" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">Batal</button>
                <button @click="$wire.deleteLembur(deleteId); openDelete = false" type="button" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition shadow-md">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div x-show="openEdit" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="absolute inset-0 bg-gray-900/60" @click="openEdit = false"></div>
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden text-left"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
             
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Edit Pengajuan Lembur</h3>
                <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <form @submit.prevent="$wire.saveLemburEdit(edit_id, edit_tanggal, edit_mulai, edit_selesai, edit_keterangan)" class="p-6 space-y-4 relative">
                {{-- Spinner Overlay while saving --}}
                <div wire:loading.flex wire:target="saveLemburEdit" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-xs flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-600"></i>
                        <span class="text-sm font-semibold text-gray-700">Menyimpan Perubahan...</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-gray-700">Tanggal Lembur</label>
                    <input type="date" x-model="edit_tanggal" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-sm">
                    @error('edit_tanggal') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-gray-700">Jam Mulai</label>
                        <input type="time" x-model="edit_mulai" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-sm">
                        @error('edit_mulai') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-gray-700">Jam Selesai</label>
                        <input type="time" x-model="edit_selesai" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-sm">
                        @error('edit_selesai') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-gray-700">Keterangan / Alasan</label>
                    <textarea x-model="edit_keterangan" rows="3" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-sm resize-none"></textarea>
                    @error('edit_keterangan') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#3C8B5E] hover:bg-[#2D6A47] text-white font-bold py-2.5 rounded-xl transition shadow-md flex items-center justify-center gap-2 cursor-pointer">
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
