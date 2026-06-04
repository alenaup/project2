<div class="animate-item bg-white border border-slate-200 rounded-xl shadow-sm p-5">
    <div class="flex items-start justify-between mb-4 flex-wrap gap-3 border-b border-slate-400 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <i class="fa-regular fa-calendar text-slate-500 text-xs"></i>
                <h2 class="text-sm font-bold text-slate-800">Rekapan Detail Karyawan per Bulan</h2>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">Bulan</label>
                    <input type="month" name="filterBulan" id="filterBulan"
                        class="w-full sm:w-40 border border-gray-400 rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer">
                </div>
                <div class="flex items-center gap-2 mt-2 bottom-0">
                    <button onclick="renderTable()"
                        class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Tampilkan
                    </button>
                    <button onclick="document.getElementById('filterBulan').value='3-31'; renderTable()"
                        class="inline-flex items-center gap-2 bg-white border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-rotate-left text-xs"></i> Reset
                    </button>
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

    {{-- menggunakan komponent tabel rekap absensi dan mengambil data dummy dari array controller adminOutsourcing --}}
    <x-tabel-rekap-absensi :koloms="range(1, 31)" :datas="$datas" />

    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100 flex-wrap gap-3">
        <div class="flex items-center gap-2 text-sm text-slate-600">
            <span>Status rekap:</span>
            <span
                class="inline-flex items-center gap-1.5 bg-yellow-50 border border-yellow-300 text-yellow-700 font-semibold text-xs px-3 py-1.5 rounded-full">
                <i class="fa-solid fa-clock text-[10px]"></i> Menunggu Persetujuan
            </span>
            <span id="badgeBulan"
                class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-300 font-medium">
                Maret 2026
            </span>
        </div>

    </div>
</div>
