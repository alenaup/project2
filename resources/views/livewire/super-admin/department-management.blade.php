<div x-data="{ showModal: @entangle('showModal'), showDeleteConfirm: @entangle('showDeleteConfirm') }">

    {{-- =========================================================== --}}
    {{-- MAIN TABLE --}}
    {{-- =========================================================== --}}
    <div class="bg-white p-8 rounded-lg shadow-lg mt-6">
        <div class="flex flex-col md:flex-row md:justify-between gap-3 mb-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama departemen"
                    class="border border-gray-500 rounded-lg px-3 py-2 text-sm w-full md:w-64 focus:ring-2 focus:ring-green-500 outline-none">
                <select wire:model.live="filterStatus"
                    class="border border-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none bg-white">
                    <option value="semua">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
            <button @click="showModal = true; $wire.openModal()" type="button"
                class="bg-green-600 shadow-lg flex items-center gap-2 text-white px-4 py-2 rounded-lg text-sm transition-all hover:bg-green-700">
                <svg viewBox="0 0 24 24" fill="none" class="w-5" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd"
                        d="M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z"
                        fill="#ffff" fill-rule="evenodd"></path>
                </svg>Tambah Departemen
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-y-0">
                <thead class="bg-gray-100 text-gray-600">
                    <tr class="shadow-sm border border-gray-100">
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm rounded-l-lg">NO</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">NAMA DEPARTEMEN</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">LOKASI ABSENSI</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">STATUS</th>
                        <th class="p-2 px-8 md:p-3 text-left text-xs md:text-sm">DIBUAT</th>
                        <th class="p-2 px-8 md:p-3 text-center text-xs md:text-sm rounded-r-lg">AKSI</th>
                    </tr>
                </thead>

                <tbody class="relative">
                    @forelse($departemens as $index => $dept)
                        <tr
                            class="animate-bitem py-2 bg-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer border border-gray-100 mt-2">
                            <td class="p-3">{{ $departemens->firstItem() + $index }}</td>
                            <td class="p-3 font-medium text-gray-800">{{ $dept->nama_departemen }}</td>
                            <td class="p-3 text-gray-600">
                                @if($dept->lokasi)
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-800">{{ $dept->lokasi->nama_lokasi }}</span>
                                        <span class="text-xs text-gray-400">Radius: {{ $dept->lokasi->radius }}m</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">- Belum Atur Lokasi -</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="{{ $dept->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} px-2 py-1 rounded text-xs font-semibold">
                                    {{ $dept->status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-3 text-gray-600">
                                {{ $dept->created_at ? $dept->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($dept->status === 'active')
                                        <button @click="showModal = true; $wire.editDepartemen({{ $dept->id_departemen }})" title="Edit"
                                            type="button"
                                            class="bg-yellow-400 text-white px-2 py-1.5 rounded hover:bg-yellow-500 transition">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M4 20H8L19 9C20.1 7.9 20.1 6.1 19 5C17.9 3.9 16.1 3.9 15 5L4 16V20Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </button>
                                        <button @click="showDeleteConfirm = true; $wire.confirmHapus({{ $dept->id_departemen }})"
                                            title="Nonaktifkan" type="button"
                                            class="bg-red-500 text-white px-2.5 py-1.5 rounded hover:bg-red-600 transition">
                                            <i class="fas fa-ban w-4 h-4"></i>
                                        </button>
                                    @else
                                        <button wire:click="aktifkanDepartemen({{ $dept->id_departemen }})" title="Aktifkan"
                                            type="button"
                                            class="bg-green-600 text-white px-2.5 py-1.5 rounded hover:bg-green-700 transition">
                                            <i class="fas fa-check w-4 h-4"></i>
                                        </button>
                                        <button @click="showDeleteConfirm = true; $wire.confirmHapus({{ $dept->id_departemen }})"
                                            title="Hapus Permanen" type="button"
                                            class="bg-red-500 text-white px-2 py-1.5 rounded hover:bg-red-600 transition">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                <path
                                                    d="M6 7H18M10 11V17M14 11V17M8 7V4H16V7M9 20H15C16.1 20 17 19.1 17 18V7H7V18C7 19.1 7.9 20 9 20Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                <i class="fas fa-folder-open text-3xl mb-2 text-gray-300"></i>
                                <p>Tidak ada data departemen ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $departemens->links() }}
        </div>
    </div>


    {{-- =========================================================== --}}
    {{-- MODAL: TAMBAH / EDIT DEPARTEMEN --}}
    {{-- =========================================================== --}}
    <div wire:key="modal-tambah-edit" x-show="showModal" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
        style="display: none;">
        <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white w-full max-w-lg rounded-xl overflow-hidden shadow-2xl relative flex flex-col max-h-[90vh]"
            @click.outside="$wire.closeModal()">
            <div wire:loading.flex wire:target="openModal, editDepartemen, simpanDepartemen, updateDepartemen"
                class="absolute inset-0 w-full h-full bg-white/80 z-[100] flex items-center justify-center backdrop-blur-sm rounded-xl">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-green-600 mb-3" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-sm font-semibold text-green-700 animate-pulse">Memproses...</span>
                </div>
            </div>

            {{-- HEADER --}}
            <div class="bg-green-700 text-white px-5 py-4 flex justify-between items-center rounded-t-xl">
                <div>
                    <h3 class="font-bold text-lg">{{ $isEditing ? 'Edit Departemen' : 'Tambah Departemen' }}</h3>
                    <p class="text-xs text-green-200">
                        {{ $isEditing ? 'Perbarui data departemen perusahaan' : 'Isi semua kolom yang wajib diisi' }}</p>
                </div>
                <button @click="showModal = false; $wire.closeModal()"
                    class="text-white hover:text-green-200 text-xl transition">&times;</button>
            </div>

            {{-- BODY --}}
            <div class="p-6 md:p-8 flex flex-col gap-4 text-sm overflow-y-auto">
                <div>
                    <label class="font-bold text-gray-700">Nama Departemen <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nama_departemen" placeholder="Contoh: IT, Marketing, HRD"
                        class="w-full border @error('nama_departemen') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                    @error('nama_departemen')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="font-bold text-gray-700">Lokasi Absensi</label>
                    <select wire:model="lokasi_id"
                        class="w-full border @error('lokasi_id') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none bg-white transition">
                        <option value="">- Tanpa Lokasi Absensi / Belum Ditentukan -</option>
                        @foreach($lokasis as $lok)
                            <option value="{{ $lok->id_lokasi }}">{{ $lok->nama_lokasi }} (Radius: {{ $lok->radius }}m)</option>
                        @endforeach
                    </select>
                    @error('lokasi_id')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="font-bold text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select wire:model="status"
                        class="w-full border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none bg-white transition">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                    @error('status')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- FOOTER --}}
            <div
                class="bg-gray-50 flex flex-col md:flex-row justify-end gap-3 px-6 py-4 border-t border-gray-200 rounded-b-xl">
                <button type="button" @click="showModal = false; $wire.closeModal()"
                    class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg w-full md:w-auto hover:bg-gray-100 transition-all font-medium">
                    Batal
                </button>
                @if ($isEditing)
                    <button wire:click="updateDepartemen"
                        class="px-5 py-2 bg-green-700 text-white rounded-lg w-full md:w-auto shadow-md hover:bg-green-800 transition-all font-medium flex justify-center items-center gap-2">
                        <span>Simpan Perubahan</span>
                    </button>
                @else
                    <button wire:click="simpanDepartemen"
                        class="px-5 py-2 bg-green-700 text-white rounded-lg w-full md:w-auto shadow-md hover:bg-green-800 transition-all font-medium flex justify-center items-center gap-2">
                        <span>Simpan Departemen</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- MODAL: KONFIRMASI HAPUS --}}
    {{-- =========================================================== --}}
    <div wire:key="modal-konfirmasi-hapus" x-show="showDeleteConfirm"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4" style="display: none;">
        <div x-show="showDeleteConfirm" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden relative">
            <div wire:loading.flex wire:target="confirmHapus, prosesAksiHapus"
                class="absolute inset-0 w-full h-full bg-white/80 z-[100] flex items-center justify-center backdrop-blur-sm rounded-2xl">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-red-500 mb-2" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-sm font-semibold text-red-600 animate-pulse">Memproses...</span>
                </div>
            </div>

            <div class="p-6 text-center">
                <div class="w-16 h-16 {{ $deleteActionType === 'deactivate' ? 'bg-amber-100' : 'bg-red-100' }} rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="{{ $deleteActionType === 'deactivate' ? 'fas fa-ban text-amber-600' : 'fas fa-trash-alt text-red-500' }} text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-800 mb-2">
                    {{ $deleteActionType === 'deactivate' ? 'Nonaktifkan Departemen?' : 'Hapus Departemen?' }}
                </h3>
                <p class="text-gray-500 text-sm mb-6">
                    {{ $deleteActionType === 'deactivate' ? 'Yakin ingin menonaktifkan departemen' : 'Yakin ingin menghapus departemen' }}
                    <span class="font-semibold text-gray-800">{{ $deletingDepartemenName }}</span>?
                    @if($deleteActionType === 'delete')
                        <br><span class="text-red-500 text-xs font-semibold">Tindakan ini tidak dapat dibatalkan.</span>
                    @else
                        <br><span class="text-amber-600 text-xs font-semibold">Status departemen akan diubah menjadi Inactive.</span>
                    @endif
                </p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="showDeleteConfirm = false; $wire.cancelHapus()"
                        class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-sm">
                        Batal
                    </button>
                    <button wire:click="prosesAksiHapus"
                        class="px-5 py-2 {{ $deleteActionType === 'deactivate' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-red-500 hover:bg-red-600' }} text-white rounded-lg transition font-medium text-sm flex items-center gap-2">
                        <i class="{{ $deleteActionType === 'deactivate' ? 'fas fa-ban' : 'fas fa-trash' }}"></i>
                        {{ $deleteActionType === 'deactivate' ? 'Ya, Nonaktifkan' : 'Ya, Hapus' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
