<div x-data="{ isOpen: false }">
    <!-- TITLE & DEPARTEMENT INFO -->
    <div class="animate-bitem flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-200/60 pb-5 mb-6 gap-4 bg-gradient-to-r from-emerald-100 p-6 rounded-xl">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent flex items-center gap-2">
                <i class="fa-solid fa-users text-emerald-600"></i> Daftar Karyawan
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Mengelola dan melihat informasi profil lengkap seluruh karyawan di departemen Anda.
            </p>
        </div>
        <div class="bg-white/80 backdrop-blur-sm border border-slate-200 px-4 py-2 rounded-xl shadow-xs">
            <span class="text-xs text-slate-450 font-semibold uppercase tracking-wider block">Departemen Anda</span>
            <span class="text-sm font-extrabold text-emerald-700 flex items-center gap-1.5 mt-0.5">
                <i class="fa-solid fa-building text-emerald-600"></i>
                {{ $departemen->nama_departemen ?? 'Tidak Terkait Departemen' }}
            </span>
        </div>
    </div>

    <!-- SEARCH & FILTER -->
    <div class="animate-bitem flex flex-col md:flex-row items-center justify-between gap-4 mb-6 bg-white/80 p-4 rounded-xl border border-slate-200/60 shadow-xs">
        <!-- Search -->
        <div class="relative w-full md:w-80 group">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan nama, email, NIP..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/50 focus:bg-white border border-slate-200 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none text-xs transition duration-200 font-medium text-slate-700 shadow-inner">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition"></i>
        </div>

        <div class="text-xs text-slate-450 font-bold">
            Total: {{ $karyawans->total() }} Karyawan
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full min-w-max border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Karyawan</th>
                        <th class="px-6 py-4">NIP / Kode</th>
                        <th class="px-6 py-4">No. Telepon</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($karyawans as $index => $karyawan)
                        <tr class="animate-bitem hover:bg-slate-50/50 transition group">
                            <!-- No -->
                            <td class="px-6 py-4 text-center text-xs font-semibold text-slate-500 whitespace-nowrap">
                                {{ ($karyawans->currentPage() - 1) * $karyawans->perPage() + $index + 1 }}
                            </td>
                            <!-- Nama & Email -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @php
                                        // Singkatan nama
                                        $initials = collect(explode(' ', $karyawan->nama_lengkap))
                                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                            ->take(2)
                                            ->implode('');
                                        
                                        $bgColors = [
                                            'from-blue-500 to-indigo-500',
                                            'from-teal-400 to-cyan-500',
                                            'from-emerald-400 to-teal-500',
                                            'from-amber-400 to-orange-500',
                                            'from-rose-400 to-pink-500',
                                            'from-violet-400 to-purple-500'
                                        ];
                                        $color = $bgColors[$karyawan->id_user % count($bgColors)];
                                    @endphp
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $color }} text-white flex items-center justify-center font-bold text-xs shadow-xs group-hover:scale-105 transition-transform duration-200">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-800 group-hover:text-emerald-600 transition block text-sm">
                                            {{ $karyawan->nama_lengkap }}
                                        </span>
                                        <span class="text-xs text-slate-400 font-medium block">
                                            {{ $karyawan->email }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <!-- NIP -->
                            <td class="px-6 py-4 text-xs font-semibold text-slate-650 whitespace-nowrap">
                                {{ $karyawan->nip ?? '-' }}
                            </td>
                            <!-- Nomor Telepon -->
                            <td class="px-6 py-4 text-xs font-semibold text-slate-650 whitespace-nowrap">
                                {{ $karyawan->nomor_tlp ?? '-' }}
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($karyawan->status === 'active')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wide">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <button @click="isOpen = true; $wire.showDetail({{ $karyawan->id_user }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-emerald-700 hover:border-emerald-200 hover:bg-emerald-50/30 transition text-xs font-bold active:scale-95">
                                    <i class="fa-solid fa-eye text-[11px]"></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-450 text-xs font-semibold">
                                <i class="fa-solid fa-folder-open text-2xl mb-2 text-slate-300 block"></i>
                                Tidak ada data karyawan yang ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($karyawans->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $karyawans->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL DETAIL KARYAWAN -->
    <div x-show="isOpen" style="display: none;"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
        
        <div @click.outside="isOpen = false; $wire.closeDetail()" x-show="isOpen"
            class="relative w-full max-w-xl bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            {{-- LOADING OVERLAY MODAL --}}
            <div wire:loading.flex wire:target="showDetail"
                class="absolute inset-0 w-full h-full bg-white/80 z-[100] flex flex-col items-center justify-center backdrop-blur-sm">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-emerald-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-emerald-700 animate-pulse">Memuat Profil...</span>
                </div>
            </div>

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-address-card text-emerald-500"></i> Detail Profil Karyawan
                </h2>
                <button @click="isOpen = false; $wire.closeDetail()"
                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-500 transition border border-slate-200">
                    ✕
                </button>
            </div>

            <!-- Profile Info Grid -->
            @if($selectedUser)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                    <!-- Nama Lengkap -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Nama Lengkap</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-bold text-slate-800">
                            {{ $selectedUser['nama_lengkap'] }}
                        </div>
                    </div>

                    <!-- NIP -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">NIP / Kode Karyawan</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-bold text-slate-800">
                            {{ $selectedUser['nip'] }}
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Alamat Email</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-bold text-slate-850">
                            {{ $selectedUser['email'] }}
                        </div>
                    </div>

                    <!-- No. Telp -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Nomor Telepon</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-bold text-slate-800">
                            {{ $selectedUser['nomor_tlp'] }}
                        </div>
                    </div>

                    <!-- Departemen -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Departemen</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-bold text-slate-800">
                            {{ $selectedUser['departemen_nama'] }}
                        </div>
                    </div>

                    <!-- Vendor -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Vendor / Mitra Outsourcing</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-bold text-slate-800">
                            {{ $selectedUser['vendor_nama'] }}
                        </div>
                    </div>

                    <!-- Tanggal Masuk -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Mulai Bergabung</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-sm font-bold text-slate-800">
                            {{ $selectedUser['tanggal_masuk'] }}
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Status Akun</span>
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center">
                            @if($selectedUser['status'] === 'active')
                                <span class="px-3 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wide">
                                    Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-lg text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wide">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Alamat (Colspan 2) -->
                    <div class="space-y-1 md:col-span-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide block">Alamat Tempat Tinggal</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs font-semibold text-slate-700 min-h-16 leading-relaxed">
                            {{ $selectedUser['alamat'] }}
                        </div>
                    </div>
                </div>
            @endif

            <!-- Footer Modal -->
            <div class="flex justify-end pt-4 border-t border-slate-100 mt-6">
                <button type="button" @click="isOpen = false; $wire.closeDetail()"
                    class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition text-xs font-bold">
                    Tutup Profil
                </button>
            </div>
        </div>
    </div>
</div>
