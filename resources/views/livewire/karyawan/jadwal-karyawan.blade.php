<div x-data="{ showExportModal: false, exportMonth: '{{ $currentYear }}-{{ sprintf('%02d', $currentMonth) }}' }">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800 md:text-2xl">Jadwal Kerjaku </h1>
            <p class="text-gray-500 text-sm">Lihat detail waktu shift kerjamu untuk bulan ini.</p>
        </div>
        <button @click="showExportModal = true"
            class="bg-white border-2 border-[#3C8B5E] text-[#3C8B5E] px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-emerald-50 transition flex items-center justify-center gap-2 cursor-pointer">
            <i class="fa-solid fa-download"></i> <span>Download PDF</span>
        </button>
    </div>

    <div class="bg-white rounded-xl shadow p-4 md:p-6 overflow-hidden border-t-4 border-[#3C8B5E]">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 border-b pb-4">
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5 border border-slate-200 rounded-lg p-1 bg-slate-50">
                    <button wire:click="previousMonth" x-on:click="$dispatch('show-loading', { message: 'Memuat data...' })"
                        class="p-1.5 bg-white border border-slate-200 rounded-md hover:bg-slate-50 text-slate-700 shadow-xs transition cursor-pointer">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <span class="text-sm font-semibold text-slate-800 px-3 min-w-32 md:min-w-40 text-center">{{ $monthName }}</span>
                    <button wire:click="nextMonth" x-on:click="$dispatch('show-loading', { message: 'Memuat data...' })"
                        class="p-1.5 bg-white border border-slate-200 rounded-md hover:bg-slate-50 text-slate-700 shadow-xs transition cursor-pointer">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>

                {{-- Direct Month Picker --}}
                <div class="flex items-center gap-1">
                    <input type="month" wire:model.live="filterBulan" x-on:change="$dispatch('show-loading', { message: 'Memuat data...' })"
                        class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-700 outline-none bg-white shadow-xs cursor-pointer focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <button wire:click="goToToday" x-on:click="$dispatch('show-loading', { message: 'Memuat data...' })"
                class="text-xs font-semibold bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-3.5 py-2 rounded-lg transition shadow-xs cursor-pointer">
                <i class="fa-solid fa-calendar-day mr-1"></i> Ke Hari Ini
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-[700px]">

                <div class="grid grid-cols-7 gap-2 mb-2 text-center">
                    <div class="font-bold text-sm text-gray-500 py-2">Senin</div>
                    <div class="font-bold text-sm text-gray-500 py-2">Selasa</div>
                    <div class="font-bold text-sm text-gray-500 py-2">Rabu</div>
                    <div class="font-bold text-sm text-gray-500 py-2">Kamis</div>
                    <div class="font-bold text-sm text-gray-500 py-2">Jumat</div>
                    <div class="font-bold text-sm text-red-500 py-2">Sabtu</div>
                    <div class="font-bold text-sm text-red-500 py-2">Minggu</div>
                </div>

                <div class="grid grid-cols-7 gap-2">

                    {{-- Empty days before the 1st of the month --}}
                    @for ($i = 1; $i < $firstDayOfWeek; $i++)
                        <div class="border rounded-lg min-h-[100px] p-2 bg-gray-50 border-gray-100 opacity-50 flex flex-col">
                        </div>
                    @endfor

                    {{-- Actual days --}}
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                            $isToday = $dateStr === $currentDate;
                            $hasJadwal = isset($calendarData[$dateStr]);

                            $shiftClass = 'bg-white border-gray-200';
                            $shiftBg = '';
                            $shiftText = '';
                            $shiftLabel = '';

                            if ($hasJadwal) {
                                $shift = $calendarData[$dateStr];
                                $tipe = strtolower($shift->nama_shift);

                                if (str_contains($tipe, 'pagi')) {
                                    $shiftClass = 'border-emerald-400 bg-emerald-50 hover:border-emerald-500';
                                    $shiftBg = 'bg-emerald-200';
                                    $shiftText = 'text-emerald-800';
                                    $shiftLabel = '<i class="fa-solid fa-sun mr-1"></i> ' . $shift->nama_shift . ' (' . substr($shift->jam_masuk,0,5) . ' - ' . substr($shift->jam_keluar,0,5) . ')';
                                } elseif (str_contains($tipe, 'malam')) {
                                    $shiftClass = 'border-blue-400 bg-blue-50 hover:border-blue-500';
                                    $shiftBg = 'bg-blue-200';
                                    $shiftText = 'text-blue-800';
                                    $shiftLabel = '<i class="fa-solid fa-moon mr-1"></i> ' . $shift->nama_shift . ' (' . substr($shift->jam_masuk,0,5) . ' - ' . substr($shift->jam_keluar,0,5) . ')';
                                } else {
                                    $shiftClass = 'border-orange-400 bg-orange-50 hover:border-orange-500';
                                    $shiftBg = 'bg-orange-200';
                                    $shiftText = 'text-orange-800';
                                    $shiftLabel = '<i class="fa-solid fa-clock mr-1"></i> ' . $shift->nama_shift . ' (' . substr($shift->jam_masuk,0,5) . ' - ' . substr($shift->jam_keluar,0,5) . ')';
                                }
                            }
                        @endphp

                        <div class="border-2 rounded-lg min-h-[100px] p-2 shadow-sm relative group cursor-pointer flex flex-col transition {{ $isToday && !$hasJadwal ? 'border-emerald-400 bg-emerald-50' : $shiftClass }}">
                            @if($isToday)
                                <div class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow z-10">Hari Ini</div>
                            @endif

                            <p class="text-right text-sm font-bold mb-1 {{ $isToday || $hasJadwal ? 'text-gray-800' : 'text-gray-500' }}">{{ $day }}</p>

                            @if($hasJadwal)
                                <div class="{{ $shiftBg }} {{ $shiftText }} text-[11px] font-bold p-1.5 rounded text-center truncate mt-auto mb-1" title="{{ $shift->nama_shift }}">
                                    {!! $shiftLabel !!}
                                </div>
                            @else
                                <div class="text-gray-300 text-[11px] font-bold p-1.5 rounded text-center truncate mt-auto mb-1">
                                    <i class="fa-solid fa-mug-hot mr-1 text-red-300"></i> OFF / Libur
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-6 border-t pt-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-sun text-emerald-500"></i>
                <span class="text-sm font-medium text-gray-600">Shift Pagi</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-moon text-blue-500"></i>
                <span class="text-sm font-medium text-gray-600">Shift Malam</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock text-orange-500"></i>
                <span class="text-sm font-medium text-gray-600">Shift Lainnya</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-mug-hot text-red-500"></i>
                <span class="text-sm font-medium text-gray-600">Libur / OFF</span>
            </div>
        </div>

    </div>

    {{-- Modal Download PDF --}}
    <div x-show="showExportModal" 
        class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        x-cloak>
        
        {{-- Backdrop --}}
        <div x-show="showExportModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showExportModal = false"
            class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>

        {{-- Modal Content Card --}}
        <div x-show="showExportModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative w-full max-w-md mx-4 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden transform transition-all z-10 p-6">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf text-red-500 text-lg"></i>
                    Unduh Jadwal Kerja (PDF)
                </h3>
                <button @click="showExportModal = false" class="text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <p class="text-xs text-slate-500 leading-relaxed">
                    Silakan pilih bulan dan tahun jadwal kerja yang ingin Anda unduh dalam format dokumen PDF.
                </p>

                <div class="flex flex-col gap-1.5">
                    <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Pilih Bulan & Tahun</label>
                    <input type="month" x-model="exportMonth"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-700 outline-none bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all cursor-pointer">
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <button @click="showExportModal = false"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                    Batal
                </button>
                <button @click="
                        if (exportMonth) {
                            const [year, month] = exportMonth.split('-');
                            window.open('/karyawan-outsourcing/jadwal-karyawan/pdf/' + year + '/' + parseInt(month), '_blank');
                            showExportModal = false;
                        }
                    "
                    class="inline-flex items-center gap-2 bg-[#3C8B5E] hover:bg-[#2D6A47] text-white text-xs font-semibold px-5 py-2.5 rounded-xl shadow-md transition-all cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    Unduh PDF
                </button>
            </div>
        </div>
    </div>
</div>
