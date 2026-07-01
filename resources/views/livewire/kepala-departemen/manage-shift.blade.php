<div>
    <!-- TITLE & ACTION -->
    <div class="animate-bitem flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-200/60 pb-5 mb-6 gap-4 bg-gradient-to-r from-emerald-100 p-6 rounded-xl">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent flex items-center gap-2">
                <i class="fa-solid fa-user-clock text-emerald-600"></i> Pengaturan Shift Kerja
            </h1>
            <p class="text-xs text-slate-500 mt-1">Sesuaikan jam masuk dan jam keluar untuk masing-masing shift kerja default.</p>
        </div>
    </div>

    <!-- CARD GRID SHIFT -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($shifts as $shift)
            @php
                // Tentukan data visual berdasarkan ID Shift
                $shiftId = (int) $shift['id_shift'];
                $namaDisplay = match($shiftId) {
                    1 => 'Pagi',
                    2 => 'Siang',
                    3 => 'Malam',
                    default => $shift['nama_shift']
                };

                $themeClass = match($shiftId) {
                    1 => [
                        'card' => 'hover:shadow-emerald-500/5 border-emerald-100/60 bg-emerald-50/5',
                        'title' => 'text-emerald-700 bg-emerald-50 border-emerald-100',
                        'icon' => '🌄',
                        'accent' => 'bg-emerald-500'
                    ],
                    2 => [
                        'card' => 'hover:shadow-amber-500/5 border-amber-100/60 bg-amber-50/5',
                        'title' => 'text-amber-700 bg-amber-50 border-amber-100',
                        'icon' => '☀️',
                        'accent' => 'bg-amber-500'
                    ],
                    3 => [
                        'card' => 'hover:shadow-indigo-500/5 border-indigo-100/60 bg-indigo-50/5',
                        'title' => 'text-indigo-700 bg-indigo-50 border-indigo-100',
                        'icon' => '🌙',
                        'accent' => 'bg-indigo-500'
                    ],
                    default => [
                        'card' => 'hover:shadow-slate-500/5 border-slate-100 bg-slate-50/5',
                        'title' => 'text-slate-700 bg-slate-50 border-slate-100',
                        'icon' => '⏰',
                        'accent' => 'bg-slate-500'
                    ]
                };
            @endphp

            <div class="animate-bitem bg-white rounded-2xl border shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden group {{ $themeClass['card'] }}">
                <!-- Garis Accent Samping -->
                <div class="absolute top-0 left-0 w-1.5 h-full {{ $themeClass['accent'] }}"></div>

                <div class="p-6">
                    <!-- Header Card -->
                    <div class="animate-bitem flex justify-between items-center mb-6">
                        <span class="text-sm font-bold px-3 py-1 rounded-full border {{ $themeClass['title'] }}">
                            {{ $themeClass['icon'] }} Shift {{ $namaDisplay }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                            ID: {{ $shiftId }}
                        </span>
                    </div>

                    <!-- Jam Kerja -->
                    <div class="space-y-4 bg-slate-50/80 p-4 rounded-xl border border-slate-100/50 shadow-inner">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold text-slate-450 uppercase tracking-wide">Jam Masuk</span>
                            <span class="text-sm font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                {{ date('H:i', strtotime($shift['jam_masuk'])) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-200/50 pt-3">
                            <span class="text-xs font-semibold text-slate-450 uppercase tracking-wide">Jam Keluar</span>
                            <span class="text-sm font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                {{ date('H:i', strtotime($shift['jam_keluar'])) }}
                            </span>
                        </div>
                    </div>

                    <!-- Aksi -->
                    <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-slate-100">
                        <button wire:click="editShift({{ $shiftId }})"
                            class="px-4 py-2 text-xs font-bold rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-600 hover:text-slate-800 transition active:scale-95 flex items-center gap-1.5 shadow-xs">
                            <i class="fa-regular fa-pen-to-square"></i> Edit Waktu Kerja
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- MODAL EDIT SHIFT -->
    <div x-data="{ isOpen: @entangle('isModalOpen') }" x-show="isOpen" style="display: none;"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">

        <div @click.outside="$wire.closeModal()" x-show="isOpen"
            class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 border border-slate-100 overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            {{-- LOADING OVERLAY MODAL --}}
            <div wire:loading.flex wire:target="updateShift, editShift"
                class="absolute inset-0 w-full h-full bg-white/80 z-[100] flex flex-col items-center justify-center backdrop-blur-sm">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-emerald-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-semibold text-emerald-700 animate-pulse">Menyimpan...</span>
                </div>
            </div>

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-emerald-500"></i> Edit Jam Shift {{ $editingNama }}
                </h2>
                <button wire:click="closeModal"
                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-500 transition border border-slate-200">
                    ✕
                </button>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="updateShift" class="space-y-4">
                <!-- Nama Shift (Locked / Readonly) -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-450 uppercase tracking-wider">Nama Shift (Kunci)</label>
                    <input type="text" value="Shift {{ $editingNama }}" readonly
                        class="w-full mt-1 px-3 py-2.5 rounded-xl border border-slate-250 bg-slate-100 text-slate-500 outline-none text-sm font-semibold cursor-not-allowed">
                </div>

                <!-- Jam Masuk -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-450 uppercase tracking-wider">Jam Masuk</label>
                    <input type="time" wire:model="jam_masuk"
                        class="w-full mt-1 p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none text-sm text-slate-700 font-semibold bg-white">
                    @error('jam_masuk')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Jam Keluar -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-450 uppercase tracking-wider">Jam Keluar</label>
                    <input type="time" wire:model="jam_keluar"
                        class="w-full mt-1 p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none text-sm text-slate-700 font-semibold bg-white">
                    @error('jam_keluar')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Aksi -->
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 transition text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 shadow-md shadow-emerald-500/10 hover:shadow-lg active:scale-95 transition-all text-sm font-semibold">
                        💾 Simpan Waktu Kerja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
