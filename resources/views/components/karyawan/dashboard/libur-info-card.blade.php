<div
    class="group relative bg-linear-to-br from-white to-emerald-50/40 p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
    <!-- glow -->
    <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-200 opacity-20 blur-3xl"></div>

    <div class="relative z-10 flex items-start justify-between mb-4">
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide">Belum ada jadwal hari ini</p>
            <p class="font-semibold text-gray-800 text-sm mt-1">
                Anda sedang libur atau belum memiliki jadwal
            </p>
        </div>
        <div
            class="w-11 h-11 bg-emerald-100 text-emerald-600 flex items-center justify-center rounded-2xl shadow-inner group-hover:scale-105 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M15 12H3m0 0 3-3m-3 3 3 3" />
            </svg>
        </div>
    </div>

    <!-- STATUS -->
    <div class="flex items-center justify-between">
        <p id="statusKeluar" class="text-sm text-gray-400">
            Belum absen keluar hari ini
        </p>
    </div>
    <!-- bottom accent -->
    <div class="absolute bottom-0 left-0 h-1 w-0 bg-emerald-500 group-hover:w-full transition-all duration-300">
    </div>
</div>
