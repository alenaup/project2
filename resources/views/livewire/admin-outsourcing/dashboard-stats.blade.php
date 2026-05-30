    <div wire:id="{{ $this->getId() }}">

        {{-- ─── Filter Bulan ─────────────────────────────────── --}}
{{--         <div class="flex flex-wrap items-center gap-3 mb-5">
            <div class="flex flex-col gap-1">
                <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
                    Filter Statistik Bulan
                </label>
                <input
                    type="month"
                    wire:model.live="bulan"
                    id="filterBulanStats"
                    class="w-full sm:w-44 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700
                        transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer"
                >
            </div>


            <div class="mt-4">
                <span class="text-xs px-3 py-1.5 rounded-full bg-green-50 text-green-700
                            border border-green-200 font-semibold">
                    <i class="fa-regular fa-calendar text-[10px] mr-1"></i>
                    {{ $labelBulan }}
                </span>
            </div>

        </div> --}}

        {{-- ─── Stat Cards Grid ───────────────────────────────── --}}
        <div class="animate-item grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Card: Total Hadir --}}
            <x-stat-card
                title="Total Karyawan Hadir"
                :value="$totalHadir"
                subtext="Selama {{ $labelBulan }}"
                icon="fa-solid fa-user-check"
                borderColor="border-gray-200"
                textColor="text-green-600"
            />

            {{-- Card: Total Alpha --}}
            <x-stat-card
                title="Total Karyawan Alpha"
                :value="$totalAlpha"
                subtext="Tanpa keterangan"
                icon="fa-solid fa-user-xmark"
                borderColor="border-gray-200"
                textColor="text-red-600"
            />

            {{-- Card: Izin / Sakit --}}
            <x-stat-card
                title="Karyawan Izin/Sakit"
                :value="$totalIzinSakit"
                subtext="Dengan keterangan"
                icon="fa-solid fa-file-medical"
                borderColor="border-gray-200"
                textColor="text-yellow-600"
            />

            {{-- Card: Total Karyawan Aktif --}}
            <x-stat-card
                title="Jumlah Karyawan"
                :value="$totalKaryawan"
                subtext="Karyawan aktif"
                icon="fa-solid fa-user-group"
                borderColor="border-gray-200"
                textColor="text-purple-700"
            />

        </div>

    </div>
