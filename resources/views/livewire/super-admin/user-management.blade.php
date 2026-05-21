<div>
    <!-- Pesan Sukses -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 shadow-sm" role="alert">
            <span class="block sm:inline"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
        </div>
    @endif

    <!-- TABS -->
    <div class="bg-white px-4 md:px-10 py-3 border-gray-200 border-b border-t flex gap-2 text-sm overflow-x-auto">
        <button type="button" wire:click="switchTab('admin_vendor')" wire:key="tab-admin-vendor"
            aria-pressed="{{ $activeTab === 'admin_vendor' ? 'true' : 'false' }}"
            class="px-4 py-2 rounded-lg border transition-all duration-200 whitespace-nowrap {{ $activeTab === 'admin_vendor' ? 'bg-green-600 border-green-600 text-white font-semibold shadow-sm' : 'bg-white border-gray-200 text-gray-500 hover:text-green-700 hover:border-green-300 hover:bg-green-50' }}">
            Admin Outsourcing
        </button>
        <button type="button" wire:click="switchTab('hr')" wire:key="tab-hr"
            aria-pressed="{{ $activeTab === 'hr' ? 'true' : 'false' }}"
            class="px-4 py-2 rounded-lg border transition-all duration-200 whitespace-nowrap {{ $activeTab === 'hr' ? 'bg-green-600 border-green-600 text-white font-semibold shadow-sm' : 'bg-white border-gray-200 text-gray-500 hover:text-green-700 hover:border-green-300 hover:bg-green-50' }}">
            HR Perusahaan
        </button>
        <button type="button" wire:click="switchTab('kepala_departemen')" wire:key="tab-kepala-departemen"
            aria-pressed="{{ $activeTab === 'kepala_departemen' ? 'true' : 'false' }}"
            class="px-4 py-2 rounded-lg border transition-all duration-200 whitespace-nowrap {{ $activeTab === 'kepala_departemen' ? 'bg-green-600 border-green-600 text-white font-semibold shadow-sm' : 'bg-white border-gray-200 text-gray-500 hover:text-green-700 hover:border-green-300 hover:bg-green-50' }}">
            Kepala Departemen
        </button>
    </div>

    <!-- MAIN BOX -->
    <div class="bg-white p-8 rounded-lg shadow-lg mt-6">
        <div class="flex flex-col md:flex-row md:justify-between gap-3 mb-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email"
                    class="border border-gray-500 rounded-lg px-3 py-2 text-sm w-64 focus:ring-2 focus:ring-green-500 outline-none">
            </div>
            <button wire:click="openModal" class="bg-green-600 shadow-lg text-white px-4 py-2 rounded-lg text-sm transition-all hover:bg-green-700">
                <i class="fas fa-plus mr-1"></i> Tambah Akun
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-y-0">
                <thead class="bg-gray-100 text-gray-600">
                    <tr class="shadow-sm border border-gray-100">
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm rounded-l-lg">NO</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">NAMA PENGGUNA</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">EMAIL</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">USERNAME</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">STATUS</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">NO TELP</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">DIBUAT</th>
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm rounded-r-lg">AKSI</th>
                    </tr>
                </thead>

                <tbody class="relative">


                    @forelse($users as $index => $user)
                        <tr class="bg-white shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer border border-gray-100 mt-2">
                            <td class="p-3">{{ $users->firstItem() + $index }}</td>
                            <td class="p-3 font-medium text-gray-800">{{ $user->nama_lengkap }}</td>
                            <td class="p-3 text-gray-600">{{ $user->email }}</td>
                            <td class="p-3 text-gray-600">{{ $user->username }}</td>
                            <td class="p-3">
                                <span class="{{ $user->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} px-2 py-1 rounded text-xs font-semibold">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-gray-600">{{ $user->nomor_tlp ?? '-' }}</td>
                            <td class="p-3 text-gray-600">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                            <td class="p-3 text-center">
                                <button class="bg-yellow-400 text-white px-2 py-1 rounded hover:bg-yellow-500" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500">
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

    <!-- MODAL TAMBAH AKUN -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4 transition-opacity duration-300">
            <div class="bg-white w-full max-w-xl rounded-xl overflow-y-auto shadow-2xl transform scale-100 transition-transform duration-300">
                <!-- HEADER -->
                <div class="bg-green-700 text-white px-5 py-4 flex justify-between items-center rounded-t-xl">
                    <div>
                        <h3 class="font-bold text-lg">Tambah Akun</h3>
                        <p class="text-xs text-green-200">Isi semua kolom yang wajib diisi</p>
                    </div>
                    <button wire:click="closeModal" class="text-white hover:text-green-200 text-xl transition">&times;</button>
                </div>

                <!-- BODY -->
                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                    <div class="md:col-span-2">
                        <label class="font-bold text-gray-700">Nama <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nama_lengkap" placeholder="Contoh: Rizky Darmawan"
                            class="w-full border @error('nama_lengkap') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                        @error('nama_lengkap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email" placeholder="email@domain.co.id"
                            class="w-full border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-gray-700">Username <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="username" placeholder="Contoh: 12345678"
                            class="w-full border @error('username') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                        @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-gray-700">No Telepon <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="nomor_tlp" placeholder="Contoh: 081234567890"
                            class="w-full border @error('nomor_tlp') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                        @error('nomor_tlp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="font-bold text-gray-700">Role <span class="text-red-500">*</span></label>
                        <select wire:model="role" class="w-full border @error('role') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                            <option value="">--- Pilih Role ---</option>
                            <option value="admin_vendor"> Admin Outsourcing </option>
                            <option value="hr"> HR Perusahaan </option>
                            <option value="kepala_departemen"> Kepala Departemen </option>
                        </select>
                        @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-bold text-gray-700">Password <span class="text-red-500">*</span></label>
                        <input type="password" wire:model="password" placeholder="Min. 8 Karakter"
                            class="w-full border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-bold text-gray-700">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" wire:model="password_confirmation" placeholder="Ulangi Password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 outline-none transition">
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="bg-gray-50 flex flex-col md:flex-row justify-end gap-3 px-6 py-4 border-t border-gray-200 rounded-b-xl">
                    <button wire:click="closeModal"
                        class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg w-full md:w-auto hover:bg-gray-100 transition-all font-medium">Batal</button>

                    <button wire:click="simpanAkun"
                        class="px-5 py-2 bg-green-700 text-white rounded-lg w-full md:w-auto shadow-md hover:bg-green-800 transition-all font-medium flex justify-center items-center gap-2">
                        <span>Simpan Akun</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
