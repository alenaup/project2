<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800 md:text-2xl">Jadwal Kerjaku </h1>
            <p class="text-gray-500 text-sm">Lihat detail waktu shift kerjamu untuk bulan ini.</p>
        </div>
        <a href="/karyawan-outsourcing/jadwal-karyawan/pdf/{{ $currentYear }}/{{ $currentMonth }}"
            class="bg-white border-2 border-[#3C8B5E] text-[#3C8B5E] px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-emerald-50 transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-download"></i> <span>Download PDF</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-4 md:p-6 overflow-hidden border-t-4 border-[#3C8B5E]">

        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div class="flex items-center gap-2 md:gap-4">
                <button wire:click="previousMonth" class="p-1 md:p-2 border rounded-lg hover:bg-gray-100 transition"><i
                        class="fa-solid fa-chevron-left text-gray-600"></i></button>
                <h2 class="text-lg md:text-xl font-bold text-gray-800 w-40 md:w-56 text-center">{{ $monthName }}</h2>
                <button wire:click="nextMonth" class="p-1 md:p-2 border rounded-lg hover:bg-gray-100 transition"><i
                        class="fa-solid fa-chevron-right text-gray-600"></i></button>
            </div>

            <button wire:click="goToToday" class="text-sm font-medium text-emerald-600 hover:underline">Ke Hari Ini</button>
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
                                $tipe = strtolower($shift->tipe_shift);
                                
                                if (str_contains($tipe, 'pagi')) {
                                    $shiftClass = 'border-emerald-400 bg-emerald-50 hover:border-emerald-500';
                                    $shiftBg = 'bg-emerald-200';
                                    $shiftText = 'text-emerald-800';
                                    $shiftLabel = '<i class="fa-solid fa-sun mr-1"></i> ' . $shift->tipe_shift . ' (' . substr($shift->jam_masuk,0,5) . ' - ' . substr($shift->jam_keluar,0,5) . ')';
                                } elseif (str_contains($tipe, 'malam')) {
                                    $shiftClass = 'border-blue-400 bg-blue-50 hover:border-blue-500';
                                    $shiftBg = 'bg-blue-200';
                                    $shiftText = 'text-blue-800';
                                    $shiftLabel = '<i class="fa-solid fa-moon mr-1"></i> ' . $shift->tipe_shift . ' (' . substr($shift->jam_masuk,0,5) . ' - ' . substr($shift->jam_keluar,0,5) . ')';
                                } else {
                                    $shiftClass = 'border-orange-400 bg-orange-50 hover:border-orange-500';
                                    $shiftBg = 'bg-orange-200';
                                    $shiftText = 'text-orange-800';
                                    $shiftLabel = '<i class="fa-solid fa-clock mr-1"></i> ' . $shift->tipe_shift . ' (' . substr($shift->jam_masuk,0,5) . ' - ' . substr($shift->jam_keluar,0,5) . ')';
                                }
                            }
                        @endphp

                        <div class="border-2 rounded-lg min-h-[100px] p-2 shadow-sm relative group cursor-pointer flex flex-col transition {{ $isToday && !$hasJadwal ? 'border-emerald-400 bg-emerald-50' : $shiftClass }}">
                            @if($isToday)
                                <div class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow z-10">Hari Ini</div>
                            @endif
                            
                            <p class="text-right text-sm font-bold mb-1 {{ $isToday || $hasJadwal ? 'text-gray-800' : 'text-gray-500' }}">{{ $day }}</p>
                            
                            @if($hasJadwal)
                                <div class="{{ $shiftBg }} {{ $shiftText }} text-[11px] font-bold p-1.5 rounded text-center truncate mt-auto mb-1" title="{{ $shift->tipe_shift }}">
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
</div>
