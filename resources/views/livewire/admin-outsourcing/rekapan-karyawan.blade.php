<div x-data="{ showKirimUlangModal: false }">
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
                        <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">Pilih Bulan Rekap</label>
                        <input type="month" wire:model.live="rekapBulan" x-on:change="$dispatch('show-loading', { message: 'Memuat data rekap...' })"
                            class="w-full sm:w-56 border border-gray-400 rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer">
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full sm:w-auto justify-end sm:justify-start" x-data="{
                startExport() {
                    const token = 'export_' + Date.now();
                    const url = new URL('{{ route('admin.export_absensi') }}', window.location.origin);
                    url.searchParams.set('rekap_bulan', $wire.rekapBulan);
                    url.searchParams.set('download_token', token);
                    
                    this.$dispatch('show-loading', { message: 'Menyiapkan file Excel...' });
                    
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = url.toString();
                    document.body.appendChild(iframe);
                    
                    const checkCookie = setInterval(() => {
                        const match = document.cookie.match(new RegExp('(^| )download_token=([^;]+)'));
                        if (match && match[2] === token) {
                            clearInterval(checkCookie);
                            document.cookie = 'download_token=; Max-Age=-99999999; path=/;';
                            this.$dispatch('hide-loading');
                            document.body.removeChild(iframe);
                        }
                    }, 250);
                }
            }">

                {{-- button export excel --}}
                <button @click="startExport()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-brand hover:bg-brand-dark text-white text-xs font-semibold px-3.5 py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm">
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
                        x-on:click="$dispatch('show-loading', { message: 'Memuat data...' })"
                        @if($halamanAktif <= 1) disabled @endif>
                        Prev
                    </button>

                    @foreach ($pages as $p)
                        @if ($p === '...')
                            <span class="px-3 py-1 text-gray-500">...</span>
                        @else
                            <button
                                class="px-3 py-1 rounded-lg border text-sm {{ $p === $halamanAktif ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                                wire:click="gantiHalaman({{ $p }})"
                                x-on:click="$dispatch('show-loading', { message: 'Memuat data...' })">
                                {{ $p }}
                            </button>
                        @endif
                    @endforeach

                    <button
                        class="px-3 py-1 rounded-lg border text-gray-700 bg-white hover:bg-gray-50 {{ $halamanAktif >= $totalHalaman ? 'opacity-50 cursor-not-allowed' : '' }}"
                        wire:click="gantiHalaman({{ $halamanAktif + 1 }})"
                        x-on:click="$dispatch('show-loading', { message: 'Memuat data...' })"
                        @if($halamanAktif >= $totalHalaman) disabled @endif>
                        Next
                    </button>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100 flex-wrap gap-3">
            <div class="flex items-center gap-3 flex-wrap text-sm text-slate-600">
                <div class="flex items-center gap-1.5 mt-4">
                    <span>Status rekap:</span>
                    @if (!$rekapRecord || !$rekapRecord->tanggal_rekap)
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

            <div class="flex gap-2">
                @if (!$rekapRecord || !$rekapRecord->tanggal_rekap)
                    <button wire:click="kirimRekapan" x-on:click="$dispatch('show-loading', { message: 'Mengirim rekapan...' })"
                        class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Rekapan
                    </button>
                @elseif ($rekapRecord->status_validasi === \App\Enums\Validasi::Invalid->value)
                    <button @click="showKirimUlangModal = true"
                        class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm">
                        <i class="fa-solid fa-rotate-right"></i> Kirim Ulang
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Kirim Ulang Rekapan --}}
    <div x-show="showKirimUlangModal" 
        class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        x-cloak>
        
        {{-- Backdrop --}}
        <div x-show="showKirimUlangModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showKirimUlangModal = false"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

        {{-- Modal Content Card --}}
        <div x-show="showKirimUlangModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-md mx-4 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden transform transition-all z-10 p-6">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg"></i>
                    Pemberitahuan Cek Rekapan
                </h3>
                <button @click="showKirimUlangModal = false" class="text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="mt-4 space-y-3">
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl flex gap-3">
                    <i class="fa-solid fa-circle-xmark text-red-500 text-lg mt-0.5"></i>
                    <div class="text-xs text-red-700 font-medium">
                        Rekapan absensi Anda sebelumnya telah diperiksa oleh HR dan ditolak (Status: Invalid).
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Silakan pastikan seluruh data kehadiran dan jam kerja karyawan Anda sudah sesuai sebelum melakukan pengiriman ulang. Mengirim ulang akan mengembalikan status rekapan ke status menunggu persetujuan HR.
                </p>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <button @click="showKirimUlangModal = false"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button @click="
                        showKirimUlangModal = false;
                        $dispatch('show-loading', { message: 'Mengirim ulang rekapan...' });
                        $wire.kirimUlang();
                    "
                    class="inline-flex items-center gap-2 bg-[#3C8B5E] hover:bg-[#2D6A47] text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-md transition-all cursor-pointer">
                    <i class="fa-solid fa-paper-plane"></i>
                    Ya, Kirim Ulang
                </button>
            </div>
        </div>
    </div>
</div>
