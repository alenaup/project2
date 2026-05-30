{{-- Mengambil data dari layout untuk tamplating halaman adminOutsourcing --}}
@extends('layouts.admin-outsourcing')

{{-- mengisi konten halaman adminOutsourcing --}}
@section('content')
    <div x-data="modalKaryawan()" class="p-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-2">
            <div class="flex items-center justify-between mb-6">    
                <h2 class="text-xl font-semibold text-gray-800">Data Karyawan</h2>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = true"
                        class="px-4 py-2 bg-emerald-500 text-white rounded-xl shadow hover:bg-emerald-600 transition flex items-center gap-2">
                        + Tambah Karyawan
                    </button>
                    <div x-show="open" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <!-- CARD -->
                        <div @click.away="open = false" class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6">

                            <!-- HEADER -->
                            <div class="flex justify-between items-center mb-5">
                                <h2 class="text-xl font-bold text-gray-800">Tambah Karyawan</h2>

                                <button @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl">
                                    ✕
                                </button>
                            </div>

                            <!-- FORM -->
                            <form class="space-y-4">

                                <!-- NIP -->
                                <div>
                                    <label class="text-sm text-gray-600">NIP</label>
                                    <input type="text"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                        placeholder="Masukkan NIP">
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label class="text-sm text-gray-600">Nama Lengkap</label>
                                    <input type="text"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                        placeholder="Nama lengkap">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="text-sm text-gray-600">Email</label>
                                    <input type="email"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                        placeholder="email@domain.com">
                                </div>

                                <!-- No Telepon -->
                                <div>
                                    <label class="text-sm text-gray-600">No Telepon</label>
                                    <input type="text"
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                        placeholder="08xxxxxxxxxx">
                                </div>

                                <!-- Alamat -->
                                <div>
                                    <label class="text-sm text-gray-600">Alamat</label>
                                    <textarea class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" rows="3"
                                        placeholder="Alamat lengkap"></textarea>
                                </div>

                                <!-- ACTION -->
                                <div class="flex justify-end gap-2 pt-2">

                                    <button type="button" @click="open = false"
                                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                                        Batal
                                    </button>

                                    <button type="submit"
                                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow">
                                        Ajukan
                                    </button>

                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
            <div class="relative mb-2">
                <input type="text" x-model="search" placeholder="Cari karyawan..."
                    class="pl-10 pr-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-400 focus:outline-none text-sm">
                <span class="absolute left-3 top-2.5 text-gray-400 text-sm"><i
                        class="fa-solid fa-magnifying-glass"></i></span>
            </div>

            {{-- component table siap pakai --}}

            {{-- data headers menyatakan kolom tabel --}}
            <x-table-reusable :headers="['No', 'Nama', 'Email', 'Telepon', 'Alamat', 'Aksi']">

                {{-- data body menyatakan baris tabel dan diisi dengan data --}}

                {{-- x-for menyatakan perulangan data dan :key menyatakan key unik untuk setiap data --}}
                {{-- tamplate menyatakan template --}}
                <template x-for="(karyawan, index) in filteredKaryawans()" :key="karyawan.id">
                    <tr class="odd:bg-white even:bg-gray-100 shadow-sm hover:bg-green-50 cursor-pointer">

                        {{-- x-text menyatakan menampilkan data --}}
                        <td class="px-4 py-2 text-gray-500" x-text="index + 1"></td>

                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800" x-text="karyawan.nama_lengkap"></div>
                            <div class="text-xs text-gray-400" x-text="karyawan.nim"></div>
                        </td>


                        <td class="px-4 py-2 text-gray-600" x-text="karyawan.email"></td>
                        <td class="px-4 py-2 text-gray-600" x-text="karyawan.nomor_telepon"></td>
                        <td class="px-4 py-2 text-gray-500 truncate max-w-xs" x-text="karyawan.alamat"></td>

                        <td class="px-4 py-2">
                            <div class="flex justify-center gap-2">
                                {{-- tombol berfungsi untuk membuka modal detail dengan triger onclick yang terhung dengan x-click --}}
                                <button @click="open('detail', karyawan)"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                    <i class="fa-solid fa-eye text-gray-600"></i>
                                </button>

                                {{-- tombol berfungsi untuk membuka modal edit dengan triger onclick yang terhung dengan x-click --}}
                                <button @click="open('edit', karyawan)"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                {{-- tombol berfungsi untuk membuka modal delete dengan triger onclick yang terhung dengan x-click --}}
                                <button @click="open('delete', karyawan)"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                </template>

            </x-table-reusable>

            {{-- MODAL BASE STYLE --}}

            <div x-show="modal" x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">

                {{-- DETAIL --}}
                <div x-show="modal === 'detail'" x-transition class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">

                    {{-- HEADER --}}
                    <div class="flex items-center gap-4 mb-5">
                        <div
                            class="w-12 h-12 rounded-xl bg-linear-to-br from-green-400 to-emerald-500 text-white flex items-center justify-center font-semibold text-lg shadow-md">
                            <span x-text="selected.nama_lengkap?.charAt(0)"></span>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800" x-text="selected.nama_lengkap"></h3>
                            <p class="text-xs text-gray-400" x-text="selected.nim"></p>
                        </div>

                        <button @click="close()" class="ml-auto text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>

                    {{-- CONTENT --}}
                    <div class="grid grid-cols-1 gap-4 text-sm">

                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-1">Email</p>
                            <p class="font-medium text-gray-700" x-text="selected.email"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-1">Nomor Telepon</p>
                            <p class="font-medium text-gray-700" x-text="selected.nomor_telepon"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-1">Alamat</p>
                            <p class="font-medium text-gray-700 leading-relaxed" x-text="selected.alamat"></p>
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="mt-6 flex justify-end">
                        <button @click="close()"
                            class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Tutup
                        </button>
                    </div>
                </div>


                {{-- EDIT --}}
                <div x-show="modal === 'edit'" x-transition class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">

                    {{-- HEADER --}}
                    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl w-full max-w-lg p-6 border border-white/40"
                        @click.outside="close()">

                        {{-- HEADER --}}
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Edit Karyawan</h3>
                            <button @click="close()" class="text-gray-400 hover:text-gray-600"><i
                                    class="fa-solid fa-xmark"></i></button>
                        </div>

                        {{-- FORM --}}
                        <div class="space-y-5">

                            <div class="relative">
                                <input type="text" x-model="selected.nim" placeholder=" "
                                    class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                <label
                                    class="absolute left-3 top-2 text-xs text-gray-400
                                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                                        peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500">
                                    NIM
                                </label>
                            </div>

                            <div class="relative">
                                <input type="text" x-model="selected.nama_lengkap" placeholder=" "
                                    class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                <label
                                    class="absolute left-3 top-2 text-xs text-gray-400
                                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                                        peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500">

                                    Nama Lengkap
                                </label>
                            </div>

                            <div class="relative">
                                <input type="email" x-model="selected.email" placeholder=" "
                                    class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                <label
                                    class="absolute left-3 top-2 text-xs text-gray-400
                                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                                        peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500">

                                    Email
                                </label>
                            </div>

                            <div class="relative">
                                <input type="text" x-model="selected.nomor_telepon" placeholder=" "
                                    class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                                <label
                                    class="absolute left-3 top-2 text-xs text-gray-400
                                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                                        peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500">

                                    Nomor Telepon
                                </label>
                            </div>

                            <div class="relative">
                                <textarea x-model="selected.alamat" rows="3" placeholder=" "
                                    class="peer w-full border border-gray-200 rounded-xl px-3 pt-5 pb-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
                                <label
                                    class="absolute left-3 top-2 text-xs text-gray-400
                                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
                                        peer-focus:top-2 peer-focus:text-xs peer-focus:text-green-500">
                                    Alamat
                                </label>
                            </div>

                        </div>

                        {{-- FOOTER --}}
                        <div class="flex justify-end gap-2 mt-6">
                            <button @click="close()"
                                class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                Batal
                            </button>

                            <button @click="saveEdit()"
                                class="px-4 py-2 text-sm bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-md transition">
                                <i class="fa-solid fa-floppy-disk mr-1"></i>Simpan
                            </button>
                        </div>

                    </div>
                </div>

                {{-- DELETE --}}
                <div x-show="modal === 'delete'" x-transition
                    class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">

                    <div class="text-3xl mb-2"><i class="fa-solid fa-triangle-exclamation text-yellow-500 text-3xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Hapus Data?</h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="selected.nama_lengkap"></p>

                    <div class="flex gap-2 mt-5">
                        <button @click="close()" class="flex-1 bg-gray-100 py-2 rounded-lg text-sm">Batal</button>
                        <button @click="deleteData()"
                            class="flex-1 bg-red-500 text-white py-2 rounded-lg text-sm">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-outsourcing/kelola-karyawan.js') }}"></script>
@endpush
