<div>
    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-check text-base"></i>
            <div><span class="font-medium">Sukses!</span> {{ session('success') }}</div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-xmark text-base"></i>
            <div><span class="font-medium">Gagal!</span> {{ session('error') }}</div>
        </div>
    @endif
    <div wire:id="{{ $this->getId() }}">

        {{-- ─── Stat Cards Grid ───────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Card: Total Hadir --}}
            <x-stat-card title="Total Karyawan Hadir" :value="$totalHadir" subtext="Selama {{ $labelBulan }}"
                icon="fa-solid fa-user-check" borderColor="border-gray-200" textColor="text-green-600" />

            {{-- Card: Total Alpha --}}
            <x-stat-card title="Total Karyawan Alpha" :value="$totalAlpha" subtext="Tanpa keterangan"
                icon="fa-solid fa-user-xmark" borderColor="border-gray-200" textColor="text-red-600" />

            {{-- Card: Izin / Sakit --}}
            <x-stat-card title="Karyawan Izin/Sakit" :value="$totalIzinSakit" subtext="Dengan keterangan"
                icon="fa-solid fa-file-medical" borderColor="border-gray-200" textColor="text-yellow-600" />

            {{-- Card: Total Karyawan Aktif --}}
            <x-stat-card title="Jumlah Karyawan" :value="$totalKaryawan" subtext="Karyawan aktif"
                icon="fa-solid fa-user-group" borderColor="border-gray-200" textColor="text-purple-700" />

        </div>

    </div>

    <div class="animate-bitem bg-white border border-slate-200 rounded-xl shadow-sm p-5 mt-5">
        <div class="flex items-start justify-between mb-4 flex-wrap gap-3 border-b border-slate-400 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-slate-500 text-xs"></i>
                    <h2 class="text-sm font-bold text-slate-800">Rekapan Detail Karyawan per Bulan</h2>
                </div>
                <div class="flex items-center gap-4 mt-2 flex-wrap">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">Tanggal Mulai</label>
                        <input type="date" wire:model.live="startDate"
                            class="w-full sm:w-45 border border-gray-400 rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">Tanggal Selesai</label>
                        <input type="date" wire:model.live="endDate"
                            class="w-full sm:w-45 border border-gray-400 rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer">
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4">

                {{-- button export excel --}}
                <button
                    class="inline-flex items-center gap-1.5 bg-brand hover:bg-brand-dark text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>

        {{-- menggunakan komponent tabel rekap absensi --}}
        <x-tabel-rekap-absensi :koloms="$koloms" :datas="$datas" />

        {{-- Pagination --}}
        @if ($totalKaryawan > $perPage)
            <div class="px-4 py-3 border-t border-gray-100 bg-white flex items-center justify-between gap-3 text-xs mt-4">
                <div class="text-gray-500">
                    Page <span class="font-semibold text-gray-700">{{ $halamanAktif }}</span>
                    / <span class="font-semibold text-gray-700">{{ (int) ceil($totalKaryawan / $perPage) }}</span>
                    (Total: <span class="font-semibold text-gray-700">{{ $totalKaryawan }}</span> karyawan)
                </div>

                <div class="flex items-center gap-2">
                    @php
                        $totalHalaman = (int) ceil($totalKaryawan / $perPage);
                        $range = 3;
                        $pages = [];
                        $pages[] = 1;
                        $start = max(2, $halamanAktif - $range);
                        $end = min($totalHalaman - 1, $halamanAktif + $range);
                        if ($start > 2) {
                            $pages[] = '...';
                        }
                        for ($i = $start; $i <= $end; $i++) {
                            $pages[] = $i;
                        }
                        if ($end < $totalHalaman - 1) {
                            $pages[] = '...';
                        }
                        if ($totalHalaman > 1) {
                            $pages[] = $totalHalaman;
                        }
                    @endphp

                    <button
                        class="px-3 py-1 rounded-lg border text-gray-700 bg-white hover:bg-gray-50 {{ $halamanAktif <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        wire:click="gantiHalaman({{ $halamanAktif - 1 }})"
                        @if($halamanAktif <= 1) disabled @endif>
                        Prev
                    </button>

                    @foreach ($pages as $p)
                        @if ($p === '...')
                            <span class="px-3 py-1 text-gray-500">...</span>
                        @else
                            <button
                                class="px-3 py-1 rounded-lg border text-sm {{ $p === $halamanAktif ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                                wire:click="gantiHalaman({{ $p }})">
                                {{ $p }}
                            </button>
                        @endif
                    @endforeach

                    <button
                        class="px-3 py-1 rounded-lg border text-gray-700 bg-white hover:bg-gray-50 {{ $halamanAktif >= $totalHalaman ? 'opacity-50 cursor-not-allowed' : '' }}"
                        wire:click="gantiHalaman({{ $halamanAktif + 1 }})"
                        @if($halamanAktif >= $totalHalaman) disabled @endif>
                        Next
                    </button>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100 flex-wrap gap-3">
            <div class="flex items-center gap-3 flex-wrap text-sm text-slate-600">
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">Pilih Bulan Rekap</label>
                    <input type="month" wire:model.live="rekapBulan"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs text-gray-700 outline-none bg-white shadow-sm cursor-pointer w-36">
                </div>

                <div class="flex items-center gap-1.5 mt-4">
                    <span>Status rekap:</span>
                    @if (!$rekapRecord)
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 border border-gray-300 text-gray-700 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <i class="fa-solid fa-circle-info text-[10px]"></i> Belum Diajukan
                        </span>
                    @elseif ($rekapRecord->status_validasi === \App\Enums\Validasi::Pending->value)
                        <span class="inline-flex items-center gap-1.5 bg-yellow-50 border border-yellow-300 text-yellow-700 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <i class="fa-solid fa-clock text-[10px]"></i> Menunggu Persetujuan
                        </span>
                    @elseif ($rekapRecord->status_validasi === \App\Enums\Validasi::Invalid->value)
                        <span class="inline-flex items-center gap-1.5 bg-red-50 border border-red-300 text-red-700 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <i class="fa-solid fa-circle-xmark text-[10px]"></i> Ditolak (Invalid)
                        </span>
                    @elseif ($rekapRecord->status_validasi === \App\Enums\Validasi::Valid->value)
                        <span class="inline-flex items-center gap-1.5 bg-green-50 border border-green-300 text-green-700 font-semibold text-xs px-3 py-1.5 rounded-full">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> Disetujui (Valid)
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                @if (!$rekapRecord)
                    <button wire:click="kirimRekapan"
                        class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Rekapan
                    </button>
                @elseif ($rekapRecord->status_validasi === \App\Enums\Validasi::Invalid->value)
                    <button wire:click="kirimUlang"
                        class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm">
                        <i class="fa-solid fa-rotate-right"></i> Kirim Ulang
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
