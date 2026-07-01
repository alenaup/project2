<div x-data="{ showKalkulator: false, showExport: false }">
    {{-- ── STAT CARDS ─────────────────────────────────────────── --}}

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-6 mb-6 mt-6">
        <x-stat-card
            title="Total Karyawan Outsourcing Aktif"
            value="{{ $stats['outsourcing_aktif'] }}"
            subtext="dari {{ $stats['outsourcing_terdaftar'] }} terdaftar"
            icon="fas fa-users"
            borderColor="border-green-500"
            textColor="text-green-500">
        </x-stat-card>

        <x-stat-card
            title="Total Lembur Pending"
            value="{{ $stats['lembur_pending'] }}"
            subtext="Menunggu Persetujuan"
            icon="fas fa-clock"
            borderColor="border-orange-400"
            textColor="text-orange-500">
        </x-stat-card>

        <x-stat-card
            title="Ajuan Rekap Pending"
            value="{{ $stats['rekap_pending'] }}"
            subtext="Menunggu persetujuan"
            icon="fas fa-clipboard-list"
            borderColor="border-indigo-500"
            textColor="text-indigo-600">
        </x-stat-card>

        <x-stat-card
            title="Ajuan Karyawan Pending"
            value="{{ $stats['ajuan_pending'] }}"
            subtext="Menunggu persetujuan"
            icon="fas fa-user-clock"
            borderColor="border-teal-500"
            textColor="text-teal-600">
        </x-stat-card>
    </div>

    {{-- TABLE: LEMBUR (HR) --}}

    <div class="bg-white p-4 md:p-8 rounded-lg shadow-lg mt-6">


        {{-- Filter (tanggal) & Export --}}

        <div class="flex flex-col md:flex-row md:justify-between gap-4 sm:gap-3 mb-4">
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto flex-wrap">
                {{-- Single Input Date Range Picker (Flatpickr) --}}
                <div class="relative w-full sm:w-64" wire:ignore>
                    <input
                        id="overtime-daterange"
                        type="text"
                        placeholder="Pilih Rentang Tanggal"
                        class="animate-bitem w-full border rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-sm cursor-pointer"
                        x-data
                        x-init="
                            flatpickr($el, {
                                mode: 'range',
                                showMonths: 2,
                                dateFormat: 'Y-m-d',
                                defaultDate: [$wire.startDate ? $wire.startDate : null, $wire.endDate ? $wire.endDate : null],
                                onChange: function(selectedDates, dateStr, instance) {
                                    if (selectedDates.length === 2) {
                                        let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                        $wire.set('startDate', start);
                                        $wire.set('endDate', end);
                                    } else if (selectedDates.length === 0) {
                                        $wire.set('startDate', '');
                                        $wire.set('endDate', '');
                                    }
                                }
                            });
                        "
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-calendar-alt text-xs"></i>
                    </div>
                </div>

                {{-- Filter Departemen --}}
                <div class="relative w-full sm:w-auto">
                    <select
                        wire:model.live="departemenId"
                        class="animate-bitem w-full sm:w-48 border rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-sm cursor-pointer appearance-none pr-8">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemens as $dep)
                            <option value="{{ $dep['id_departemen'] }}">{{ $dep['nama_departemen'] }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div class="animate-bitem flex gap-2 w-full sm:w-auto">
                <button
                    @click="showKalkulator = true; $wire.set('sudahHitung', false, false); $wire.set('kalkulatorError', '', false); $wire.set('kalkulatorBulan', '', false); $wire.set('kalkulatorTahun', '', false);"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-md px-4 py-2 rounded-lg text-sm flex items-center gap-2 cursor-pointer transition font-semibold w-full justify-center sm:w-auto">
                    <i class="fas fa-calculator"></i>
                    Hitung Lembur
                </button>

                <button
                    @click="showExport = true"
                    class="bg-green-600 shadow-lg text-white hover:text-green-700 px-4 py-2 rounded-lg text-sm flex items-center gap-2 cursor-pointer transition-colors duration-200 hover:bg-white border-transparent border hover:border-green-600 w-full justify-center sm:w-auto">
                    <i class="fas fa-file-excel"></i>
                    Export Excel
                </button>
            </div>
        </div>

        {{-- Tabel data lembur --}}
        <div class="w-full overflow-x-auto">

            <table class="animate-bitem w-full text-sm border-separate border-spacing-y-2">
                <thead class="animate-bitem bg-green-100 text-gray-600">
                    <tr class="shadow-sm hover:shadow-md hover:-translate-y-0.5 transition cursor-pointer border border-gray-100">
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm">No</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">NIP</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">Nama Karyawan</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">Departemen</th>
                        <th class="p-2 md:p-3 text-left text-xs md:text-sm">Vendor</th>
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm">Mulai Lembur</th>
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm">Selesai Lembur</th>
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm">Durasi (menit)</th>
                        <th class="p-2 md:p-3 text-center text-xs md:text-sm">Status</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- BODY: List lembur per halaman --}}
                    @forelse ($lemburs as $index => $lembur)
                        {{-- BODY ITEM (computed) --}}
                        @php


                            $badge     = $this->statusBadge($lembur->status_validasi ?? '');
                            $durasi    = $this->hitungDurasi($lembur->mulai_lembur, $lembur->selesai_lembur);
                            $nomorUrut = ($lemburs->currentPage() - 1) * $lemburs->perPage() + $index + 1;
                            $nip       = ($lembur->karyawan->nip ?? null) && (int) $lembur->karyawan->nip !== 0
                                            ? 'NIP-' . $lembur->karyawan->nip
                                            : '-';
                        @endphp
                        <tr class="animate-bitem odd:bg-white even:bg-gray-100 shadow-sm hover:bg-green-50 cursor-pointer">
                            <td class="p-3 text-center">{{ $nomorUrut }}</td>
                            <td class="p-3 text-left">{{ $nip }}</td>
                            <td class="p-3 text-left">{{ $lembur->karyawan->nama_lengkap ?? '-' }}</td>
                            <td class="p-3 text-left">{{ $lembur->karyawan->departemen->nama_departemen ?? '-' }}</td>
                            <td class="p-3 text-left">{{ $lembur->karyawan->outsourcing->nama_outsourcing ?? '-' }}</td>
                            <td class="p-3 text-center">
                                {{ \Carbon\Carbon::parse($lembur->mulai_lembur)->translatedFormat('d M Y') }}
                            </td>
                            <td class="p-3 text-center">
                                {{ \Carbon\Carbon::parse($lembur->selesai_lembur)->translatedFormat('d M Y') }}
                            </td>
                            <td class="p-3 text-center">{{ $durasi }} menit</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 rounded text-xs {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                        </tr>
                    {{-- EMPTY STATE --}}
                    @empty


                        <tr>

                            <td colspan="9" class="p-6 text-center text-gray-400">
                                Tidak ada data lembur.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $lemburs->links() }}
        </div>

    </div>

    {{-- MODAL KALKULATOR LEMBUR --}}
    <div x-data="{ tarif: 0, totalMenit: @entangle('totalMenitLembur'), bulan: @entangle('kalkulatorBulan'), tahun: @entangle('kalkulatorTahun') }"
         x-show="showKalkulator"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
         style="display: none;"
         wire:key="calculator-modal">
        
        <div x-show="showKalkulator"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]"
             @click.away="showKalkulator = false">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-green-600 text-white flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-calculator"></i>
                    Kalkulator Rekap Lembur
                </h3>
                <button type="button" @click="showKalkulator = false" class="text-white/80 hover:text-white text-xl focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="p-6 space-y-4 overflow-y-auto flex-1">
                <p class="text-xs text-gray-500 leading-relaxed">
                    Hitung total durasi lembur (yang telah disetujui) untuk periode dari **tanggal 26 bulan lalu** hingga **tanggal 25 bulan terpilih**.
                </p>
                
                <div class="grid grid-cols-2 gap-4">
                    {{-- Pilih Bulan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan Terpilih</label>
                        <select wire:model="kalkulatorBulan" class="w-full border rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                            <option value="">-- Pilih Bulan --</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    
                    {{-- Pilih Tahun --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun Terpilih</label>
                        <select wire:model="kalkulatorTahun" class="w-full border rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                            <option value="">-- Pilih Tahun --</option>
                            @php
                                $currentYear = (int)date('Y');
                            @endphp
                            @for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                
                {{-- Pilih Departemen --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Departemen</label>
                    <select wire:model="kalkulatorDepartemenId" class="w-full border rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemens as $dep)
                            <option value="{{ $dep['id_departemen'] }}">{{ $dep['nama_departemen'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Button Hitung --}}
                <div class="pt-2">
                    <button type="button" 
                            wire:click="hitungLemburKalkulator"
                            @click="if (bulan && tahun) { $dispatch('show-loading', { message: 'Menghitung lembur...' }) }"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition">
                        Proses Hitung Total Lembur
                    </button>
                </div>

                {{-- Error Notification --}}
                @if ($kalkulatorError)
                    <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-200 flex items-start gap-2.5 text-red-700">
                        <div class="text-red-500 text-sm mt-0.5">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-red-800">Pilih Input</h4>
                            <p class="text-[11px] leading-normal">{{ $kalkulatorError }}</p>
                        </div>
                    </div>
                @endif

                {{-- Hasil Hitung --}}
                @if ($sudahHitung)
                    <div class="mt-4 p-4 bg-emerald-50/50 rounded-xl border border-emerald-100 space-y-3">
                        <div class="text-xs text-emerald-800">
                            <strong>Periode:</strong> {{ \Carbon\Carbon::parse($kalkulatorPeriodeMulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($kalkulatorPeriodeSelesai)->translatedFormat('d M Y') }}
                        </div>

                        @if ($totalMenitLembur > 0)
                            <div class="flex justify-between items-center py-1 border-b border-emerald-100">
                                <span class="text-sm text-gray-600 font-medium">Total Waktu Lembur</span>
                                <span class="text-base font-bold text-emerald-700">
                                    {{ $this->formatMenitKeWaktu($totalMenitLembur) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-emerald-100">
                                <span class="text-sm text-gray-600 font-medium">Total Durasi (Menit)</span>
                                <span class="text-sm font-semibold text-gray-800">{{ $totalMenitLembur }} Menit</span>
                            </div>

                            {{-- Input tarif per menit --}}
                            <div class="space-y-1.5 pt-1">
                                <label class="block text-xs font-semibold text-gray-700">Tarif Lembur per Menit (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm font-medium">Rp</span>
                                    <input type="number" 
                                           x-model.number="tarif" 
                                           min="0"
                                           class="w-full border rounded-lg pl-9 pr-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 outline-none bg-white" 
                                           placeholder="Contoh: 500">
                                </div>
                            </div>

                            {{-- Hasil Bayaran --}}
                            <div class="flex justify-between items-center p-3 bg-emerald-600 text-white rounded-lg mt-2">
                                <span class="text-sm font-semibold">Total Estimasi Bayaran</span>
                                <span class="text-lg font-bold">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(tarif * totalMenit)">0</span>
                                </span>
                            </div>
                        @else
                            <div class="p-3.5 bg-amber-50 rounded-lg border border-amber-200 flex items-start gap-2.5 mt-2">
                                <div class="text-amber-500 text-sm mt-0.5">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-amber-800">Tidak Ada Lembur</h4>
                                    <p class="text-[11px] text-amber-700 mt-0.5 leading-normal">
                                        Tidak ditemukan data lembur yang disetujui untuk periode dan departemen ini.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            
            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end shrink-0">
                <button type="button" @click="showKalkulator = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition">
                    Tutup
                </button>
            </div>
            
        </div>
    </div>

    {{-- MODAL EXPORT EXCEL --}}
    <div x-data="{ exportBulan: '{{ date('m') }}', exportTahun: '{{ date('Y') }}', exportDeptId: '' }"
         x-show="showExport"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
         style="display: none;"
         wire:key="export-excel-modal">
        
        <div x-show="showExport"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-gray-100 flex flex-col max-h-[90vh]"
             @click.away="showExport = false">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-file-excel"></i>
                    Export Rekap Lembur
                </h3>
                <button type="button" @click="showExport = false" class="text-white/80 hover:text-white text-xl focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <form action="{{ route('hr.export_lembur') }}" method="GET" 
                  x-data="{
                      startDownload() {
                          const token = Date.now().toString();
                          const url = new URL(this.$el.action);
                          url.searchParams.set('month', exportBulan);
                          url.searchParams.set('year', exportTahun);
                          url.searchParams.set('departemen_id', exportDeptId);
                          url.searchParams.set('download_token', token);
                          
                          this.$dispatch('show-loading', { message: 'Menyiapkan file Excel...' });
                          showExport = false;
                          
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
                  }"
                  @submit.prevent="startDownload()" 
                  class="flex flex-col flex-1 overflow-hidden">
                <div class="p-6 space-y-4 overflow-y-auto flex-1">
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Unduh data rekap lembur karyawan yang disetujui untuk periode dari **tanggal 26 bulan lalu** hingga **tanggal 25 bulan terpilih** dalam bentuk spreadsheet Excel.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Pilih Bulan --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan Rekap</label>
                            <select name="month" x-model="exportBulan" class="w-full border rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 outline-none bg-white">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        
                        {{-- Pilih Tahun --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun Rekap</label>
                            <select name="year" x-model="exportTahun" class="w-full border rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 outline-none bg-white">
                                @php
                                    $currentYear = (int)date('Y');
                                @endphp
                                @for ($y = $currentYear - 2; $y <= $currentYear + 2; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    {{-- Pilih Departemen --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Departemen</label>
                        <select name="departemen_id" x-model="exportDeptId" class="w-full border rounded-lg px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-green-500 outline-none bg-white">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemens as $dep)
                                <option value="{{ $dep['id_departemen'] }}">{{ $dep['nama_departemen'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                    <button type="button" @click="showExport = false" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition flex items-center gap-1.5">
                        <i class="fas fa-download"></i> Export Excel
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Custom styling for flatpickr to match Tailwind */
        .flatpickr-calendar {
            border-radius: 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #10b981 !important; /* Emerald 500 */
            border-color: #10b981 !important;
        }
        .flatpickr-day.inRange {
            background: #ecfdf5 !important; /* Emerald 50 */
            box-shadow: -5px 0 0 #ecfdf5, 5px 0 0 #ecfdf5 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush
