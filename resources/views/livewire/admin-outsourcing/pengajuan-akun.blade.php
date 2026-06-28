<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    showCancelModal: false 
}"
x-on:close-add-modal.window="showAddModal = false"
x-on:close-edit-modal.window="showEditModal = false"
x-on:close-cancel-modal.window="showCancelModal = false"
class="space-y-6">

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
            class="px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-medium flex items-center gap-2 shadow-sm">
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
            class="px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ─── Control Section: Search & Add Button ──────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
        
        {{-- Search Input --}}
        <div class="relative w-full sm:w-80">
            <span class="absolute left-3 top-2.5 text-gray-400 text-sm">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama atau NIP..."
                class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none text-sm bg-white transition-all">
        </div>

        {{-- Add Button --}}
        <button
            @click="showAddModal = true"
            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl shadow-md shadow-green-150 transition-all flex items-center justify-center gap-2 text-sm">
            <i class="fa-solid fa-user-plus text-xs"></i>
            Ajukan Akun Karyawan
        </button>

    </div>

    {{-- ─── Table Section ────────────────────────────────────── --}}
    <x-table-reusable :headers="['No', 'Nama / NIP', 'Email', 'Telepon', 'Departemen', 'Status', 'Aksi']">
        @forelse ($submissions as $index => $sub)
            <tr class="odd:bg-white even:bg-gray-50/50 hover:bg-green-50/30 transition-colors">
                
                {{-- Number --}}
                <td class="px-4 py-3 text-gray-500 text-sm">
                    {{ $submissions->firstItem() + $index }}
                </td>

                {{-- Name / NIP --}}
                <td class="px-4 py-3">
                    <div class="font-semibold text-gray-800 text-sm">{{ $sub->nama_lengkap }}</div>
                    <div class="text-xs text-gray-400">{{ $sub->nip }}</div>
                </td>

                {{-- Email --}}
                <td class="px-4 py-3 text-gray-600 text-sm">
                    {{ $sub->email }}
                </td>

                {{-- Telepon --}}
                <td class="px-4 py-3 text-gray-600 text-sm">
                    {{ $sub->nomor_tlp ?? '-' }}
                </td>

                {{-- Departemen --}}
                <td class="px-4 py-3 text-gray-600 text-sm">
                    {{ $sub->departemen?->nama_departemen ?? '-' }}
                </td>

                {{-- Status Badges --}}
                <td class="px-4 py-3">
                    @if ($sub->status === \App\Enums\Status::Pending->value || $sub->status === \App\Enums\Status::Pending)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                            <i class="fa-regular fa-clock mr-1"></i> Menunggu
                        </span>
                    @elseif (($sub->status === \App\Enums\Status::Inactive->value || $sub->status === \App\Enums\Status::Inactive) && is_null($sub->tanggal_keluar))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                            <i class="fa-solid fa-circle-xmark mr-1"></i> Ditolak
                        </span>
                    @elseif ($sub->status === \App\Enums\Status::Active->value || $sub->status === \App\Enums\Status::Active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                            <i class="fa-solid fa-circle-check mr-1"></i> Karyawan yang diterima
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-50 text-gray-600 border border-gray-150">
                            {{ is_string($sub->status) ? $sub->status : $sub->status->value }}
                        </span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        @if ($sub->status === \App\Enums\Status::Pending->value || $sub->status === \App\Enums\Status::Pending)
                            {{-- Cancel Button --}}
                            <button
                                @click="showCancelModal = true"
                                wire:click="openCancel({{ $sub->id_user }})"
                                class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition flex items-center gap-1"
                                title="Batalkan Pengajuan">
                                <i class="fa-solid fa-trash-can"></i>
                                Batalkan
                            </button>
                        @elseif (($sub->status === \App\Enums\Status::Inactive->value || $sub->status === \App\Enums\Status::Inactive) && is_null($sub->tanggal_keluar))
                            {{-- Edit & Resubmit Button --}}
                            <button
                                @click="showEditModal = true"
                                wire:click="openEdit({{ $sub->id_user }})"
                                class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold transition flex items-center gap-1"
                                title="Edit & Kirim Kembali">
                                <i class="fa-solid fa-pen-to-square"></i>
                                Edit & Kirim Kembali
                            </button>
                        @else
                            <span class="text-xs text-gray-400 italic">Tidak ada aksi</span>
                        @endif
                    </div>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    Belum ada data pengajuan akun karyawan.
                </td>
            </tr>
        @endforelse
    </x-table-reusable>

    {{-- Pagination --}}
    @if ($submissions->hasPages())
        <div class="flex justify-end mt-4">
            {{ $submissions->links() }}
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Ajukan Akun Baru (Add Modal)                       --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="showAddModal" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center"
         @click.self="showAddModal = false">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden" 
             x-show="showAddModal" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Form Pengajuan Akun Karyawan</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-4">
                
                {{-- NIP --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">NIP</label>
                    <input
                        type="text"
                        wire:model="nip"
                        placeholder="Contoh: 210102034"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 @error('nip') border-red-400 @enderror">
                    @error('nip')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                    <input
                        type="text"
                        wire:model="nama_lengkap"
                        placeholder="Masukkan nama lengkap karyawan"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 @error('nama_lengkap') border-red-400 @enderror">
                    @error('nama_lengkap')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Email</label>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="contoh@perusahaan.com"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No Telepon --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">No Telepon</label>
                    <input
                        type="text"
                        wire:model="nomor_tlp"
                        placeholder="Contoh: 081234567890"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 @error('nomor_tlp') border-red-400 @enderror">
                    @error('nomor_tlp')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Departemen --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Departemen / Penempatan</label>
                    <select
                        wire:model="departemen_id"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white @error('departemen_id') border-red-400 @enderror">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    @error('departemen_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Alamat Rumah</label>
                    <textarea
                        wire:model="alamat"
                        rows="3"
                        placeholder="Masukkan alamat domisili lengkap"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 @error('alamat') border-red-400 @enderror"></textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                <button @click="showAddModal = false"
                    class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-xl transition text-gray-700 font-semibold">
                    Batal
                </button>
                <button wire:click="submit"
                    class="px-5 py-2 text-sm bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-md transition font-semibold">
                    <i class="fa-solid fa-paper-plane mr-1 text-xs"></i> Ajukan Akun
                </button>
            </div>

        </div>
    </div>


    {{-- ─── MODAL: Edit & Kirim Kembali (Edit Modal) ─────────── --}}
    <div x-show="showEditModal" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center"
         @click.self="showEditModal = false; $wire.closeEdit()">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden" 
             x-show="showEditModal" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Header --}}
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-gray-800">Edit & Kirim Kembali Pengajuan</h3>
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase bg-red-50 text-red-700 border border-red-100">Ditolak</span>
                </div>
                <button @click="showEditModal = false; $wire.closeEdit()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-4">
                
                {{-- NIP --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">NIP</label>
                    <input
                        type="text"
                        wire:model="editNip"
                        placeholder="Contoh: 210102034"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-455 @error('editNip') border-red-400 @enderror">
                    @error('editNip')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                    <input
                        type="text"
                        wire:model="editNama"
                        placeholder="Masukkan nama lengkap karyawan"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-455 @error('editNama') border-red-400 @enderror">
                    @error('editNama')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Email</label>
                    <input
                        type="email"
                        wire:model="editEmail"
                        placeholder="contoh@perusahaan.com"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-455 @error('editEmail') border-red-400 @enderror">
                    @error('editEmail')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No Telepon --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">No Telepon</label>
                    <input
                        type="text"
                        wire:model="editTelepon"
                        placeholder="Contoh: 081234567890"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-455 @error('editTelepon') border-red-400 @enderror">
                    @error('editTelepon')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Departemen --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Departemen / Penempatan</label>
                    <select
                        wire:model="editDepartemenId"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-455 bg-white @error('editDepartemenId') border-red-400 @enderror">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    @error('editDepartemenId')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Alamat Rumah</label>
                    <textarea
                        wire:model="editAlamat"
                        rows="3"
                        placeholder="Masukkan alamat domisili lengkap"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-455 @error('editAlamat') border-red-400 @enderror"></textarea>
                    @error('editAlamat')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                <button @click="showEditModal = false; $wire.closeEdit()"
                    class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-xl transition text-gray-700 font-semibold">
                    Batal
                </button>
                <button wire:click="resubmit"
                    class="px-5 py-2 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-xl shadow-md transition font-semibold">
                    <i class="fa-solid fa-share-from-square mr-1 text-xs"></i> Kirim Kembali
                </button>
            </div>

        </div>
    </div>


    {{-- ─── MODAL: Batalkan Pengajuan (Delete Modal) ─────────── --}}
    <div x-show="showCancelModal" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center"
         @click.self="showCancelModal = false">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center" 
             x-show="showCancelModal" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100">

            <div class="text-4xl mb-3 text-red-500">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            
            <h3 class="font-extrabold text-gray-800 text-lg">Batalkan Pengajuan?</h3>
            <p class="text-sm text-gray-500 mt-2">Apakah Anda yakin ingin membatalkan data pengajuan ini? Data tersebut akan dihapus secara permanen dari database.</p>

            <div class="flex gap-3 mt-6">
                <button @click="showCancelModal = false"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 py-2.5 rounded-xl text-sm font-semibold transition text-gray-700">
                    Kembali
                </button>
                <button wire:click="cancelSubmission"
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl text-sm font-bold shadow-md shadow-red-200 transition">
                    Ya, Batalkan
                </button>
            </div>

        </div>
    </div>

</div>
