@extends('layouts.admin-outsourcing')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-2">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Data Karyawan</h2>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = true"
                    class="px-4 py-2 bg-emerald-500 text-white rounded-xl shadow hover:bg-emerald-600 transition flex items-center gap-2">
                    + Tambah Karyawan
                </button>

                <div x-show="open" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div @click.away="open = false" class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-6">

                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-xl font-bold text-gray-800">Tambah Karyawan</h2>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
                        </div>

                        <form class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-600">NIP</label>
                                <input type="text"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                    placeholder="Masukkan NIP">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Nama Lengkap</label>
                                <input type="text"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                    placeholder="Nama lengkap">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Email</label>
                                <input type="email"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                    placeholder="email@domain.com">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">No Telepon</label>
                                <input type="text"
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Alamat</label>
                                <textarea class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
                                    rows="3" placeholder="Alamat lengkap"></textarea>
                            </div>
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

        {{-- LIVEWIRE COMPONENT - Tabel, Search, Modal Detail/Edit/Hapus --}}
        @livewire(\App\Livewire\AdminOutsourcing\KelolaKaryawan::class)

    </div>
@endsection
