<div>
    <div class="bg-white rounded-xl shadow p-6 relative">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                Riwayat Pengajuan
            </h2>

            <select wire:model.live="filterStatus"
                class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <option value="semua">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        @if(session('success_riwayat'))
            <div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-lg text-sm font-semibold flex items-center gap-2 mb-4">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success_riwayat') }}
            </div>
        @endif

        <div class="space-y-3 relative">
            <div wire:loading.flex wire:target="filterStatus, deletePengajuan" class="absolute inset-0 z-10 bg-white/70 backdrop-blur-sm flex items-center justify-center">
                <i class="fa-solid fa-spinner fa-spin text-2xl text-emerald-600"></i>
            </div>

            @forelse($riwayat as $item)
                <div class="border border-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow bg-white">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="font-semibold text-gray-800 text-sm">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                                    @if($item->tanggal_selesai != $item->tanggal_mulai)
                                        — {{ \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d M Y') }}
                                    @endif
                                </span>
                                @if($item->status == 'menunggu')
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-amber-50 text-amber-600"><i class="fa-solid fa-hourglass-half mr-1"></i> Menunggu</span>
                                @elseif($item->status == 'disetujui')
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-emerald-50 text-emerald-600"><i class="fa-solid fa-circle-check mr-1"></i> Disetujui</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-red-50 text-red-600"><i class="fa-solid fa-circle-xmark mr-1"></i> Ditolak</span>
                                @endif
                            </div>

                            <p class="text-gray-500 text-xs mb-2">Diajukan: {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d M Y H:i') }}</p>
                            <p class="text-gray-600 text-sm mb-2">{{ $item->keterangan }}</p>

                            <div class="flex items-center gap-2 mt-2">
                                @php
                                    $ext = strtolower(pathinfo($item->file_surat, PATHINFO_EXTENSION));
                                @endphp
                                @if($ext == 'pdf')
                                    <span class="text-sm"><i class="fa-solid fa-file-pdf text-red-500"></i></span>
                                @else
                                    <span class="text-sm"><i class="fa-solid fa-image text-emerald-500"></i></span>
                                @endif
                                <span class="text-xs text-gray-500 truncate max-w-[180px]">{{ basename($item->file_surat) }}</span>
                                
                                <button type="button" wire:click="previewFile('{{ $item->file_surat }}')" class="text-xs text-emerald-600 hover:underline ml-1">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </button>
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        @if($item->status == 'menunggu')
                            <div class="flex gap-2 shrink-0">
                                <button wire:click="editPengajuan({{ $item->id_perizinan }})"
                                    class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <button wire:confirm="Yakin ingin menghapus pengajuan ini?" wire:click="deletePengajuan({{ $item->id_perizinan }})"
                                    class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg font-medium transition flex items-center gap-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                    <p class="font-medium">Belum ada pengajuan</p>
                    <p class="text-xs mt-1">Pengajuan yang kamu kirim akan muncul di sini</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL EDIT -->
    @if($isEditing)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">Edit Pengajuan</h3>
                    <button wire:click="batalEdit" class="text-gray-400 hover:text-gray-600 transition text-xl">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form wire:submit.prevent="simpanEdit">
                    <div class="p-5 space-y-4 relative">
                        <div wire:loading.flex wire:target="simpanEdit, edit_file_surat" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex items-center justify-center">
                            <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-600"></i>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Tanggal Mulai</label>
                                <input type="date" wire:model.live="edit_tanggal_mulai"
                                    class="w-full border @error('edit_tanggal_mulai') border-red-500 @else border-gray-200 @enderror rounded-lg p-2.5 text-sm">
                                @error('edit_tanggal_mulai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Tanggal Selesai</label>
                                <input type="date" wire:model.live="edit_tanggal_selesai"
                                    class="w-full border @error('edit_tanggal_selesai') border-red-500 @else border-gray-200 @enderror rounded-lg p-2.5 text-sm">
                                @error('edit_tanggal_selesai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 block mb-1">Keterangan</label>
                            <textarea wire:model="edit_keterangan" rows="3"
                                class="w-full border @error('edit_keterangan') border-red-500 @else border-gray-200 @enderror rounded-lg p-2.5 text-sm resize-none"></textarea>
                            @error('edit_keterangan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700 block mb-2">
                                Ganti Surat Sakit
                                <span class="text-gray-400 font-normal text-xs ml-1">(opsional)</span>
                            </label>

                            @if(!$edit_file_surat)
                                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl p-3 mb-2">
                                    <div class="text-2xl w-8 text-center"><i class="fa-solid fa-file text-gray-400"></i></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-700 truncate">{{ basename($edit_file_lama) }}</p>
                                        <p class="text-xs text-gray-400">Surat saat ini</p>
                                    </div>
                                    <label class="text-emerald-600 text-xs font-semibold hover:underline whitespace-nowrap cursor-pointer">
                                        <i class="fa-solid fa-arrow-up-from-bracket mr-1"></i>Ganti
                                        <input type="file" wire:model.live="edit_file_surat" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                                    </label>
                                </div>
                            @else
                                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-2">
                                    <div class="text-2xl w-8 text-center"><i class="fa-solid fa-file-circle-check text-emerald-500"></i></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-700 truncate">{{ $edit_file_surat->getClientOriginalName() }}</p>
                                        <p class="text-xs text-gray-400">File baru akan menggantikan surat lama</p>
                                    </div>
                                    <button type="button" wire:click="$set('edit_file_surat', null)" class="text-red-500 hover:text-red-700 text-sm">
                                        <i class="fa-solid fa-xmark"></i> Batal
                                    </button>
                                </div>
                            @endif
                            @error('edit_file_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="submit"
                                class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg font-semibold transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan Perubahan
                            </button>
                            <button type="button" wire:click="batalEdit"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 rounded-lg font-semibold transition-all active:scale-95">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL PREVIEW -->
    @if($showPreviewModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden relative">
                <div class="flex items-center justify-between px-5 py-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800 truncate pr-4">Preview Surat Sakit</h3>
                    <button wire:click="closePreview" class="text-gray-400 hover:text-gray-600 transition text-xl">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <div class="p-5 flex justify-center bg-gray-100 max-h-[70vh] overflow-auto">
                    @if($preview_file_type == 'image')
                        <img src="{{ $preview_file_url }}" alt="Preview Surat" class="rounded-lg shadow max-w-full">
                    @else
                        <div class="text-center py-10 text-gray-500 w-full">
                            <i class="fa-solid fa-file-pdf text-red-400 text-6xl mb-4 block"></i>
                            <p class="font-medium text-lg">File PDF</p>
                            <p class="text-sm mt-1 mb-6">Silakan unduh untuk melihat isinya secara penuh.</p>
                            <a href="{{ $preview_file_url }}" download target="_blank"
                                class="inline-block bg-emerald-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition shadow-sm">
                                <i class="fa-solid fa-download mr-1"></i> Unduh File PDF
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
