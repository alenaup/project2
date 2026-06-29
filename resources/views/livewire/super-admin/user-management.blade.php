<div x-data="{ showModal: @entangle('showModal'), showDeleteConfirm: @entangle('showDeleteConfirm') }">

    {{-- tabs untukk memilih role --}}
    <div class="bg-white px-4 md:px-10 py-3 border-gray-200 border-b border-t flex gap-2 text-sm overflow-x-auto">
        <button type="button"
            @click="$dispatch('show-loading', { message: 'Memuat data...' }); $wire.switchTab('admin_outsourcing')"
            wire:key="tab-admin-outsourcing"
            class="px-4 py-2 rounded-lg border transition-all duration-200 whitespace-nowrap
                {{ $activeTab === 'admin_outsourcing'
                    ? 'bg-green-600 border-green-600 text-white font-semibold shadow-sm'
                    : 'bg-white border-gray-200 text-gray-500 hover:text-green-700 hover:border-green-300 hover:bg-green-50' }}">
            Admin Outsourcing
        </button>
        <button type="button" @click="$dispatch('show-loading', { message: 'Memuat data...' }); $wire.switchTab('hr')"
            wire:key="tab-hr"
            class="px-4 py-2 rounded-lg border transition-all duration-200 whitespace-nowrap
                {{ $activeTab === 'hr'
                    ? 'bg-green-600 border-green-600 text-white font-semibold shadow-sm'
                    : 'bg-white border-gray-200 text-gray-500 hover:text-green-700 hover:border-green-300 hover:bg-green-50' }}">
            HR Perusahaan
        </button>
        <button type="button"
            @click="$dispatch('show-loading', { message: 'Memuat data...' }); $wire.switchTab('kepala_departemen')"
            wire:key="tab-kepala-departemen"
            class="px-4 py-2 rounded-lg border transition-all duration-200 whitespace-nowrap
                {{ $activeTab === 'kepala_departemen'
                    ? 'bg-green-600 border-green-600 text-white font-semibold shadow-sm'
                    : 'bg-white border-gray-200 text-gray-500 hover:text-green-700 hover:border-green-300 hover:bg-green-50' }}">
            Kepala Departemen
        </button>
    </div>

    {{-- =========================================================== --}}
    {{-- MAIN TABLE --}}
    {{-- =========================================================== --}}
    <div class="bg-white p-8 rounded-lg shadow-lg mt-6">
        <div class="flex flex-col md:flex-row md:justify-between gap-3 mb-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email"
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
                <svg viewBox="0 0 24.00 24.00" fill="none" class="w-5" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path clip-rule="evenodd"
                            d="m6.75001 7c0-1.79493 1.45508-3.25 3.24999-3.25 1.7949 0 3.25 1.45507 3.25 3.25s-1.4551 3.25-3.25 3.25c-1.79491 0-3.24999-1.45507-3.24999-3.25zm3.24999-4.75c-2.62334 0-4.74999 2.12665-4.74999 4.75s2.12665 4.75 4.74999 4.75c2.6234 0 4.75-2.12665 4.75-4.75s-2.1266-4.75-4.75-4.75zm-5.6865 16.1524c.98693-2.1566 3.16283-3.6524 5.6865-3.6524 2.5237 0 4.6996 1.4958 5.6865 3.6524.2078.4542.1134.8704-.1871 1.2142-.3195.3656-.873.6334-1.4994.6334h-7.99999c-.6264 0-1.17984-.2678-1.49941-.6334-.30047-.3438-.39492-.76-.1871-1.2142zm5.6865-5.1524c-3.13193 0-5.82838 1.8578-7.05046 4.5282-.48164 1.0525-.22026 2.0911.42167 2.8255.62282.7126 1.59835 1.1463 2.6288 1.1463h7.99999c1.0305 0 2.006-.4337 2.6288-1.1463.642-.7344.9033-1.773.4217-2.8255-1.2221-2.6704-3.9185-4.5282-7.0505-4.5282zm8-5c.4142 0 .75.33579.75.75v2.25h2.25c.4142 0 .75.3358.75.75s-.3358.75-.75.75h-2.25v2.25c0 .4142-.3358.75-.75.75s-.75-.3358-.75-.75v-2.25h-2.25c-.4142 0-.75-.3358-.75-.75s.3358-.75.75-.75h2.25v-2.25c0-.41421.3358-.75.75-.75z"
                            fill="#ffff" fill-rule="evenodd"></path>
                    </g>
                </svg>Tambah Akun
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-y-0">
                <thead class="bg-gray-100 text-gray-600">
                    <tr class="shadow-sm border border-gray-100">
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm rounded-l-lg">NO</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">NAMA PENGGUNA</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">EMAIL</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">STATUS</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">NO TELP</th>
                        <th class="p-2 px-8 md:p-3 text-left text-xs md:text-sm">DIBUAT</th>
                        <th class="p-2 px-8 md:p-3 text-center text-xs md:text-sm rounded-r-lg">AKSI</th>
                    </tr>
                </thead>

                <tbody class="relative">
                    @forelse($users as $index => $user)
                        <tr
                            class="animate-bitem py-2 bg-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer border border-gray-100 mt-2">
                            <td class="p-3">{{ $users->firstItem() + $index }}</td>
                            <td class="p-3 font-medium text-gray-800">{{ $user->nama_lengkap }}</td>
                            <td class="p-3 text-gray-600">{{ $user->email }}</td>
                            <td class="p-3">
                                <span class="{{ $user->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} px-2 py-1 rounded text-xs font-semibold">
                                    {{ $user->status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-3 text-gray-600">{{ $user->nomor_tlp ?? '-' }}</td>
                            <td class="p-3 text-gray-600">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($user->status === 'active')
                                        <button @click="showModal = true; $wire.editAkun({{ $user->id_user }})" title="Edit"
                                            type="button"
                                            class="bg-yellow-400 text-white px-2 py-1.5 rounded hover:bg-yellow-500 transition">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M4 20H8L19 9C20.1 7.9 20.1 6.1 19 5C17.9 3.9 16.1 3.9 15 5L4 16V20Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </button>
                                        <button @click="showDeleteConfirm = true; $wire.confirmHapus({{ $user->id_user }})"
                                            title="Nonaktifkan" type="button"
                                            class="bg-red-500 text-white px-2.5 py-1.5 rounded hover:bg-red-600 transition">
                                            <i class="fas fa-ban w-4 h-4"></i>
                                        </button>
                                    @else
                                        <button wire:click="aktifkanUser({{ $user->id_user }})" title="Aktifkan"
                                            type="button"
                                            class="bg-green-600 text-white px-2.5 py-1.5 rounded hover:bg-green-700 transition">
                                            <i class="fas fa-check w-4 h-4"></i>
                                        </button>
                                        <button @click="showDeleteConfirm = true; $wire.confirmHapus({{ $user->id_user }})"
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
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                <i class="fas fa-folder-open text-3xl mb-2 text-gray-300"></i>
                                <p>Tidak ada data pengguna ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>


    {{-- =========================================================== --}}
    {{-- MODAL: TAMBAH / EDIT AKUN (Alpine.js open/close) --}}
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
            class="bg-white w-full max-w-xl rounded-xl overflow-hidden shadow-2xl relative flex flex-col max-h-[90vh]"
            @click.outside="$wire.closeModal()">
            <div wire:loading.flex wire:target="openModal, editAkun, simpanAkun, updateAkun"
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
                    <h3 class="font-bold text-lg">{{ $isEditing ? 'Edit Akun' : 'Tambah Akun' }}</h3>
                    <p class="text-xs text-green-200">
                        {{ $isEditing ? 'Perbarui data akun pengguna' : 'Isi semua kolom yang wajib diisi' }}</p>
                </div>
                <button @click="showModal = false; $wire.closeModal()"
                    class="text-white hover:text-green-200 text-xl transition">&times;</button>
            </div>

            {{-- BODY --}}
            <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm overflow-y-auto">

                <div class="md:col-span-2">
                    <label class="font-bold text-gray-700">Nama <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nama_lengkap" placeholder="Contoh: Rizky Darmawan"
                        class="w-full border @error('nama_lengkap') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                    @error('nama_lengkap')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror

                </div>

                <div class="md:col-span-2">
                    <label class="font-bold text-gray-700">Email <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="email" placeholder="email@domain.co.id"
                        class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>


                <div class="md:col-span-2">
                    <label class="font-bold text-gray-700">No Telepon</label>
                    <input type="text" wire:model="nomor_tlp" placeholder="Contoh: 081234567890"
                        class="w-full border @error('nomor_tlp') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                    @error('nomor_tlp')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="font-bold text-gray-700">Role <span class="text-red-500">*</span></label>
                    <select wire:model="role"
                        class="w-full border @error('role') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                        <option value="">--- Pilih Role ---</option>
                        <option value="{{ \App\Enums\UserRole::AdminVendor->value }}"> Admin Outsourcing </option>
                        <option value="{{ \App\Enums\UserRole::Hr->value }}"> HR Perusahaan </option>
                        <option value="{{ \App\Enums\UserRole::KepalaDepartemen->value }}"> Kepala Departemen
                        </option>
                    </select>
                    @error('role')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                @if (!$isEditing)
                    <div>
                        <label class="font-bold text-gray-700">Password <span class="text-red-500">*</span></label>
                        <input type="password" wire:model="password" placeholder="Min. 8 Karakter"
                            class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                        @error('password')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="font-bold text-gray-700">Konfirmasi Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" wire:model="password_confirmation" placeholder="Ulangi Password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                    </div>
            @endif
        </div>

            {{-- FOOTER --}}
            <div
                class="bg-gray-50 flex flex-col md:flex-row justify-end gap-3 px-6 py-4 border-t border-gray-200 rounded-b-xl">
                <button type="button" @click="showModal = false; $wire.closeModal()"
                    class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg w-full md:w-auto hover:bg-gray-100 transition-all font-medium">
                    Batal
                </button>
                @if ($isEditing)
                    <button wire:click="updateAkun"
                        class="px-5 py-2 bg-green-700 text-white rounded-lg w-full md:w-auto shadow-md hover:bg-green-800 transition-all font-medium flex justify-center items-center gap-2">
                        <span>Simpan Perubahan</span>
                    </button>
                @else
                    <button wire:click="simpanAkun"
                        class="px-5 py-2 bg-green-700 text-white rounded-lg w-full md:w-auto shadow-md hover:bg-green-800 transition-all font-medium flex justify-center items-center gap-2">
                        <span>Simpan Akun</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- MODAL: KONFIRMASI HAPUS (Alpine.js open/close) --}}
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
                    {{ $deleteActionType === 'deactivate' ? 'Nonaktifkan Akun?' : 'Hapus Akun Permanen?' }}
                </h3>
                <p class="text-gray-500 text-sm mb-6">
                    {{ $deleteActionType === 'deactivate' ? 'Yakin ingin menonaktifkan akun' : 'Yakin ingin menghapus akun' }}
                    <span class="font-semibold text-gray-800">{{ $deletingUserName }}</span>?
                    @if($deleteActionType === 'delete')
                        <br><span class="text-red-500 text-xs font-semibold">Tindakan ini tidak dapat dibatalkan.</span>
                    @else
                        <br><span class="text-amber-600 text-xs font-semibold">Status akun akan diubah menjadi Inactive.</span>
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
