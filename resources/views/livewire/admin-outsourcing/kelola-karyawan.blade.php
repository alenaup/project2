<div x-data="{ 
    showDetailModal: false, 
    showEditModal: false, 
    showConfirmEditModal: false, 
    showDeleteModal: false 
}"
x-on:open-confirm-edit.window="showConfirmEditModal = true"
x-on:close-confirm-edit.window="showConfirmEditModal = false"
x-on:close-edit.window="showEditModal = false; showConfirmEditModal = false"
x-on:close-delete.window="showDeleteModal = false">

    {{-- ─── Flash Messages ───────────────────────────────────── --}}
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
            class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-green-500"></i>
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
            class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ─── Search Bar ───────────────────────────────────────── --}}
    <div class="relative mb-4">
        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">
            <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari karyawan..."
            class="pl-10 pr-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-400 focus:outline-none text-sm">
    </div>

    {{-- ─── Tabel Karyawan ───────────────────────────────────── --}}
    <x-table-reusable :headers="['No', 'Nama', 'Email', 'Telepon', 'Alamat', 'Aksi']">

        @forelse ($karyawanList as $index => $karyawan)
            <tr class="odd:bg-white even:bg-gray-100 shadow-sm hover:bg-green-50 cursor-pointer transition-colors">

                <td class="px-4 py-2 text-gray-500">
                    {{ $karyawanList->firstItem() + $index }}
                </td>

                <td class="px-4 py-2">
                    <div class="font-medium text-gray-800">{{ $karyawan->nama_lengkap }}</div>
                    <div class="text-xs text-gray-400">
                        {{ $karyawan->nip && (int) $karyawan->nip !== 0 ? 'NIP-' . $karyawan->nip : '-' }}
                    </div>
                </td>

                <td class="px-4 py-2 text-gray-600">{{ $karyawan->email ?? '-' }}</td>
                <td class="px-4 py-2 text-gray-600">{{ $karyawan->nomor_tlp ?? '-' }}</td>
                <td class="px-4 py-2 text-gray-500 truncate max-w-xs">{{ $karyawan->alamat ?? '-' }}</td>

                <td class="px-4 py-2">
                    <div class="flex justify-center gap-2">

                        {{-- Tombol Detail --}}
                        <button
                            @click="showDetailModal = true"
                            wire:click="openDetail({{ $karyawan->id_user }})"
                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition"
                            title="Lihat Detail">
                            <i class="fa-solid fa-eye text-gray-600"></i>
                        </button>

                        {{-- Tombol Edit --}}
                        <button
                            @click="showEditModal = true"
                            wire:click="openEdit({{ $karyawan->id_user }})"
                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition"
                            title="Edit Data">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        {{-- Tombol Hapus --}}
                        <button
                            @click="showDeleteModal = true"
                            wire:click="openDelete({{ $karyawan->id_user }})"
                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition"
                            title="Hapus Data">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    Tidak ada data karyawan.
                </td>
            </tr>
        @endforelse

    </x-table-reusable>

    {{-- ─── Pagination ───────────────────────────────────────── --}}
    @if ($karyawanList->hasPages())
        <div class="flex justify-end mt-4">
            {{ $karyawanList->links() }}
        </div>
    @endif

    {{-- MODAL: Detail Karyawan --}}
    <div x-show="showDetailModal" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center"
         @click.self="showDetailModal = false; $wire.closeDetail()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden" x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 flex items-center gap-4 bg-gray-50">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 text-white flex items-center justify-center font-semibold text-lg shadow-md">
                    {{ mb_substr($detailKaryawan['nama_lengkap'] ?? '?', 0, 1) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $detailKaryawan['nama_lengkap'] ?? '-' }}</h3>
                    <p class="text-xs text-gray-400">{{ $detailKaryawan['nip'] ?? '-' }}</p>
                </div>
                <button @click="showDetailModal = false; $wire.closeDetail()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 grid grid-cols-1 gap-3 text-sm">
                @foreach ([
                    'Email'      => $detailKaryawan['email'] ?? '-',
                    'Telepon'    => $detailKaryawan['nomor_tlp'] ?? '-',
                    'Alamat'     => $detailKaryawan['alamat'] ?? '-',
                    'Vendor'     => $detailKaryawan['vendor'] ?? '-',
                    'Departemen' => $detailKaryawan['departemen'] ?? '-',
                ] as $label => $value)
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1">{{ $label }}</p>
                        <p class="font-medium text-gray-700">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button @click="showDetailModal = false; $wire.closeDetail()"
                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL: Edit Karyawan --}}
    <div x-show="showEditModal" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center"
         @click.self="showEditModal = false; $wire.closeEdit()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden" x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Edit Karyawan</h3>
                <button @click="showEditModal = false; $wire.closeEdit()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-4">

                {{-- Nama Lengkap --}}
                <div class="relative">
                    <input
                        type="text"
                        wire:model="editNama"
                        placeholder=" "
                        class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 @error('editNama') border-red-400 @enderror">
                    <label class="absolute left-3 top-2 text-xs text-gray-400 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500 transition-all">
                        Nama Lengkap
                    </label>
                    @error('editNama')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="relative">
                    <input
                        type="email"
                        wire:model="editEmail"
                        placeholder=" "
                        class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 @error('editEmail') border-red-400 @enderror">
                    <label class="absolute left-3 top-2 text-xs text-gray-400 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500 transition-all">
                        Email
                    </label>
                    @error('editEmail')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor Telepon --}}
                <div class="relative">
                    <input
                        type="text"
                        wire:model="editTelepon"
                        placeholder=" "
                        class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    <label class="absolute left-3 top-2 text-xs text-gray-400 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500 transition-all">
                        Nomor Telepon
                    </label>
                </div>

                {{-- Alamat --}}
                <div class="relative">
                    <textarea
                        wire:model="editAlamat"
                        rows="3"
                        placeholder=" "
                        class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                    <label class="absolute left-3 top-2 text-xs text-gray-400 peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-sm peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500 transition-all">
                        Alamat
                    </label>
                </div>

            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                <button @click="showEditModal = false; $wire.closeEdit()"
                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Batal
                </button>
                <button wire:click="openConfirmEdit"
                    class="px-4 py-2 text-sm bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-md transition">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL: Konfirmasi Simpan Edit --}}
    <div x-show="showConfirmEditModal" x-transition.opacity
         class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center"
         @click.self="showConfirmEditModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center" x-show="showConfirmEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="text-4xl mb-3">
                <i class="fa-solid fa-circle-question text-blue-500"></i>
            </div>
            <h3 class="font-semibold text-gray-800 text-lg">Simpan Perubahan?</h3>
            <p class="text-sm text-gray-500 mt-1">Apakah kamu yakin ingin menyimpan perubahan data karyawan ini?</p>

            <div class="flex gap-2 mt-5">
                <button @click="showConfirmEditModal = false"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 py-2 rounded-lg text-sm transition">
                    Batal
                </button>
                <button wire:click="saveEdit"
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm transition">
                    <i class="fa-solid fa-check mr-1"></i> Ya, Simpan
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL: Konfirmasi Hapus --}}
    <div x-show="showDeleteModal" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center"
         @click.self="showDeleteModal = false; $wire.closeDelete()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center" x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="text-4xl mb-3">
                <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
            </div>
            <h3 class="font-semibold text-gray-800 text-lg">Hapus Data Karyawan?</h3>
            <p class="text-sm text-gray-500 mt-1">Tindakan ini tidak dapat dibatalkan.</p>

            <div class="flex gap-2 mt-5">
                <button @click="showDeleteModal = false; $wire.closeDelete()"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 py-2 rounded-lg text-sm transition">
                    Batal
                </button>
                <button wire:click="delete"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg text-sm transition">
                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                </button>
            </div>

        </div>
    </div>

</div>
