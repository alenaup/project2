<div>
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

    {{-- ── TABEL LEMBUR ─────────────────────────────────────────── --}}
    <div class="bg-white p-4 md:p-8 rounded-lg shadow-lg mt-6">

        {{-- Filter & Export --}}
        <div class="flex flex-col md:flex-row md:justify-between gap-4 sm:gap-3 mb-4">
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-auto">
                    <input
                        type="date"
                        wire:model.live.debounce.300ms="startDate"
                        class="w-full sm:w-40 border rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer"
                        title="Tanggal Mulai">
                </div>

                <span class="text-gray-500 text-sm font-medium hidden sm:block">ke</span>
                <span class="text-gray-500 text-sm font-medium sm:hidden">sampai dengan</span>

                <div class="relative w-full sm:w-auto">
                    <input
                        type="date"
                        wire:model.live.debounce.300ms="endDate"
                        class="w-full sm:w-40 border rounded-lg px-3 py-2 text-sm text-gray-700 transition-all focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer"
                        title="Tanggal Akhir">
                </div>
            </div>

            <button
                class="bg-green-600 shadow-lg text-white hover:text-green-700 px-4 py-2 rounded-lg text-sm flex items-center gap-2 cursor-pointer transition-colors duration-200 hover:bg-white border-transparent border hover:border-green-600">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </button>
        </div>

        {{-- Tabel --}}
        <div class="w-full overflow-x-auto">
            <table class="w-full text-sm border-separate border-spacing-y-2">
                <thead class="bg-green-100 text-gray-600">
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
                    @forelse ($lemburs as $index => $lembur)
                        @php
                            $badge     = $this->statusBadge($lembur->status_validasi ?? '');
                            $durasi    = $this->hitungDurasi($lembur->mulai_lembur, $lembur->selesai_lembur);
                            $nomorUrut = ($lemburs->currentPage() - 1) * $lemburs->perPage() + $index + 1;
                        @endphp
                        <tr class="odd:bg-white even:bg-gray-100 shadow-sm hover:bg-green-50 cursor-pointer">
                            <td class="p-3 text-center">{{ $nomorUrut }}</td>
                            <td class="p-3 text-left">{{ $lembur->karyawan->nip ?? '-' }}</td>
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
        <div class="flex flex-wrap justify-end mt-4 gap-1 text-sm">
            <button
                wire:click="previousPage"
                @disabled($lemburs->onFirstPage())
                class="px-3 py-1 transition-colors hover:bg-blue-500 hover:text-white border hover:border-transparent rounded cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                Previous
            </button>

            @for ($i = 1; $i <= $lemburs->lastPage(); $i++)
                <button
                    wire:click="gotoPage({{ $i }})"
                    class="px-3 py-1 rounded {{ $lemburs->currentPage() === $i ? 'bg-green-600 text-white' : 'border transition-colors hover:bg-green-600 hover:text-white hover:border-transparent cursor-pointer' }}">
                    {{ $i }}
                </button>
            @endfor

            <button
                wire:click="nextPage"
                @disabled(!$lemburs->hasMorePages())
                class="px-3 py-1 transition-colors hover:bg-blue-500 hover:text-white border hover:border-transparent rounded cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                Next
            </button>
        </div>

    </div>
</div>