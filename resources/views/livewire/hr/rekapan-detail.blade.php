<div>

    {{-- ════════════════════════════════════════════════════
         BAGIAN 1: FILTER REKAP
    ════════════════════════════════════════════════════ --}}
    <div class="mt-6 bg-white p-8 rounded-lg shadow-lg border border-gray-100">
        <div class="flex items-center gap-3 mb-1">
            <i class="fas fa-search text-blue-600"></i>
            <h2 class="text-lg font-bold text-gray-800">Filter Rekap</h2>
        </div>
        <p class="text-sm text-gray-500 mb-6">
            Pilih vendor pada tabel di bawah, lalu tentukan periode untuk menampilkan rekap absensi
        </p>

        {{-- Tabel Pilih Vendor --}}
        <div class="w-full mb-6">
            <label class="block text-xs font-semibold text-gray-600 mb-2">
                Pilih Vendor / Admin Outsourcing
            </label>

            <div class="overflow-y-auto max-h-64 border border-gray-200 rounded-lg shadow-inner mb-3">
                <table class="min-w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 sticky top-0 border-b border-gray-200 z-10 shadow-sm">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700 w-12 text-center">Pilih</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Nama Vendor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        {{-- Opsi "Semua Vendor" --}}
                        <tr class="transition cursor-pointer {{ $vendorId === null ? 'bg-green-50 border-l-4 border-green-500' : 'hover:bg-green-50' }}"
                            wire:click="pilihVendor(null)">
                            <td class="px-4 py-3 text-center">
                                <input type="radio" name="vendor_id" value=""
                                    @checked($vendorId === null)
                                    class="w-4 h-4 text-green-600 focus:ring-green-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 font-medium {{ $vendorId === null ? 'text-green-700 font-semibold' : 'text-gray-500' }} italic">Semua Vendor</td>
                        </tr>

                        {{-- Loop vendor dari DB --}}
                        @foreach ($vendors as $vendor)
                        <tr class="transition cursor-pointer {{ $vendorId == $vendor->id_outsourcing ? 'bg-green-50 border-l-4 border-green-500' : 'hover:bg-green-50' }}"
                            wire:click="pilihVendor({{ $vendor->id_outsourcing }})">
                            <td class="px-4 py-3 text-center">
                                <input type="radio" name="vendor_id"
                                    value="{{ $vendor->id_outsourcing }}"
                                    @checked($vendorId == $vendor->id_outsourcing)
                                    class="w-4 h-4 text-green-600 focus:ring-green-500 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 font-medium {{ $vendorId == $vendor->id_outsourcing ? 'text-green-700 font-semibold' : 'text-gray-800' }}">
                                {{ $vendor->nama_outsourcing }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-500">Menampilkan {{ count($vendors) }} vendor</p>
        </div>

        <hr class="border-gray-100 mb-6">

        {{-- Input Bulan + Tombol --}}
        <div class="flex flex-col md:flex-row gap-4 md:items-end">
            <div class="w-full md:w-auto">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan</label>
                {{-- type="month" → format nilai: "2026-03" --}}
                <input type="month"
                    wire:model="bulan"
                    class="w-full md:w-56 border rounded-lg px-3 py-2 text-sm text-gray-700 transition-all
                           focus:ring-2 focus:ring-green-500 outline-none bg-white shadow-sm cursor-pointer">
            </div>

            <div class="flex flex-row gap-2 w-full md:w-auto">
                <button wire:click="tampilkanRekap"
                    class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-medium
                           text-sm px-5 py-2 rounded-lg transition shadow-sm text-center">
                    <span wire:loading wire:target="tampilkanRekap">
                        <i class="fas fa-spinner fa-spin mr-1"></i>
                    </span>
                    Tampilkan Rekap
                </button>

                <button wire:click="resetFilter"
                    class="flex-1 md:flex-none bg-white border border-gray-300 hover:bg-gray-50
                           text-gray-700 font-medium text-sm px-5 py-2 rounded-lg transition shadow-sm text-center">
                    Reset
                </button>
            </div>
        </div>

        {{-- Pesan validasi --}}
        @error('bulan')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- ════════════════════════════════════════════════════
         BAGIAN 2: TABEL REKAP (muncul setelah filter)
    ════════════════════════════════════════════════════ --}}
    @if ($sudahFilter)
    <div class="bg-white mt-6 rounded-lg shadow-lg border border-gray-100 flex flex-col w-full max-w-full overflow-hidden relative">

        {{-- Header Tabel --}}
        <div class="p-5 border-b border-gray-100 bg-white">
            <div class="flex items-center gap-3">
                <i class="far fa-calendar text-2xl md:text-xl text-gray-900"></i>
                <h2 class="text-lg md:text-xl font-bold text-gray-900">Rekapan Detail Karyawan per Bulan</h2>
            </div>
        </div>

        {{-- Sub-header: badge status & tombol export --}}
        <div class="p-4 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                {{-- Badge vendor --}}
                <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-semibold rounded-full flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    {{ $vendorId ? $vendors->firstWhere('id_outsourcing', $vendorId)?->nama_outsourcing : 'Semua Vendor' }}
                </span>

                {{-- Badge bulan --}}
                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
                </span>

                {{-- Badge status --}}
                <span class="px-3 py-1 text-xs font-semibold rounded-full flex items-center gap-1
                    {{ $statusRekap === 'Disetujui' ? 'bg-green-50 text-green-700' :
                       ($statusRekap === 'Ditolak'  ? 'bg-red-50 text-red-700'    :
                                                      'bg-yellow-50 text-yellow-700') }}">
                    <i class="fas fa-hourglass-half"></i> {{ $statusRekap }}
                </span>
            </div>

            {{-- Export Excel (opsional: bisa tambah logika export nanti) --}}
            <button class="bg-green-600 shadow-lg text-white hover:text-green-700 px-4 py-2 rounded-lg
                           text-sm flex items-center gap-2 cursor-pointer transition-colors duration-200
                           hover:bg-white border-transparent border hover:border-green-600">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>

        {{-- Tabel Absensi --}}
        <div class="w-full overflow-x-auto pb-2">
            <table class="w-full text-left text-xs whitespace-nowrap min-w-max border-collapse">
                <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-3 border-b border-gray-200">#</th>
                        <th class="px-4 py-3 border-b border-gray-200 sticky left-0 z-20 bg-gray-50 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                            NAMA KARYAWAN
                        </th>
                        <th class="px-4 py-3 border-b border-gray-200">POSISI</th>

        {{-- Kolom tanggal sesuai periode (25 bulan lalu s/d 24 bulan berjalan) --}}
                        @php
                            $periodeAwalCarbon = \Carbon\Carbon::parse($periodeAwal);
                        @endphp
                        @for ($i = 1; $i <= $jumlahHariDalamBulan; $i++)
                            @php
                                // hari ke-$i = periodeAwal + ($i-1) hari
                                $tgl = $periodeAwalCarbon->copy()->addDays($i - 1);
                                $isSunday = $tgl->isSunday();
                            @endphp
                            <th class="px-1 py-3 border-b border-gray-200 text-center w-6 {{ $isSunday ? 'text-red-500' : '' }}">
                                {{ $tgl->day }}
                            </th>
                        @endfor

                        {{-- Kolom summary --}}
                        <th class="px-4 py-3 border-b border-gray-200 text-center border-l bg-gray-50">H</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-center bg-gray-50">A</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-center bg-gray-50">S/I</th>
                        <th class="px-4 py-3 border-b border-gray-200 text-center bg-gray-50">L</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $index => $data)
                        @php
                            $user   = $data['user'];
                            $map    = $data['kehadiran_map'];
                            $sum    = $data['summary'];
                            $initials = collect(explode(' ', $user->nama_lengkap))
                                            ->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
                            $colors = ['bg-green-600','bg-emerald-600','bg-blue-500','bg-purple-600','bg-orange-500'];
                            $bg = $colors[$index % count($colors)];
                        @endphp
                        <tr class="group hover:bg-gray-50 bg-white transition-colors cursor-pointer">
                            <td class="px-4 py-3 text-gray-500">{{ (($halamanAktif - 1) * $perPage) + ($index + 1) }}</td>

                            {{-- Nama sticky --}}
                            <td class="px-4 py-3 sticky left-0 z-10 bg-white group-hover:bg-gray-50
                                       shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] transition-colors">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full text-white flex items-center justify-center
                                                font-bold {{ $bg }} shrink-0">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $user->nama_lengkap }}</div>
                                        <div class="text-[10px] text-gray-400">
                                            {{ $user->outsourcing?->nama_outsourcing ?? '-' }}

                                        </div>


                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-gray-600">
                                {{ $user->departemen?->nama_departemen ?? '-' }}
                            </td>

                            {{-- Kolom per hari --}}
                            @for ($i = 1; $i <= $jumlahHariDalamBulan; $i++)
                                @php
                                    $kode = $map[$i] ?? null;
                                    // Tentukan style berdasarkan kode
                                    $style = match($kode) {
                                        'H'     => 'bg-green-100 text-green-700',
                                        'A'     => 'bg-red-100 text-red-700',
                                        'S'     => 'bg-yellow-100 text-yellow-700',
                                        'I'     => 'bg-blue-100 text-blue-700',
                                        'L'     => 'bg-purple-100 text-purple-700',
                                        default => null,
                                    };
                                @endphp
                                <td class="px-1 py-3 text-center">
                                    @if ($kode && $style)
                                        <span class="inline-flex w-5 h-5 items-center justify-center rounded text-[10px] font-bold {{ $style }}">
                                            {{ $kode }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                            @endfor

                            {{-- Summary --}}
                            <td class="px-4 py-3 text-center font-bold text-green-600 border-l border-gray-100 bg-green-50/30">{{ $sum['h'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-red-600 bg-red-50/30">{{ $sum['a'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-yellow-600 bg-yellow-50/30">{{ $sum['si'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-purple-600 bg-purple-50/30">{{ $sum['l'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $jumlahHariDalamBulan + 7 }}" class="px-4 py-8 text-center text-gray-400">
                                Tidak ada data karyawan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Footer total --}}
                <tfoot class="bg-gray-100 border-t-2 border-gray-200 font-bold text-sm">
                    <tr>
                        <td class="px-4 py-3 text-gray-700"></td>
                        <td class="px-4 py-3 text-gray-800 text-right sticky left-0 z-10 bg-gray-100
                                   shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                            TOTAL REKAP
                        </td>
                        <td class="px-4 py-3 text-gray-700"></td>
                        <td colspan="{{ $jumlahHariDalamBulan }}"></td>
                        <td class="px-4 py-3 text-center text-green-700 border-l border-gray-200 bg-green-100/50">{{ $totalH }}</td>
                        <td class="px-4 py-3 text-center text-red-700 bg-red-100/50">{{ $totalA }}</td>
                        <td class="px-4 py-3 text-center text-yellow-700 bg-yellow-100/50">{{ $totalSI }}</td>
                        <td class="px-4 py-3 text-center text-purple-700 bg-purple-100/50">{{ $totalL }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($sudahFilter && $totalKaryawan > $perPage)
            <div class="px-4 py-3 border-t border-gray-100 bg-white flex items-center justify-between gap-3 text-xs">
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

        {{-- Keterangan --}}
        <div class="p-4 border-t border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 text-xs text-gray-500 bg-white">
            <div class="flex items-center flex-wrap gap-x-4 gap-y-2">
                <span class="font-semibold text-gray-700">Keterangan:</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 bg-green-100 text-green-700 flex items-center justify-center rounded font-bold">H</span> Hadir</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 bg-red-100 text-red-700 flex items-center justify-center rounded font-bold">A</span> Alpha</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 bg-yellow-100 text-yellow-700 flex items-center justify-center rounded font-bold">S</span> Sakit</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 bg-blue-100 text-blue-700 flex items-center justify-center rounded font-bold">I</span> Izin</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 bg-purple-100 text-purple-700 flex items-center justify-center rounded font-bold">L</span> Lembur</span>
                <span class="flex items-center gap-1.5"><span class="text-gray-300 font-bold">-</span> Libur</span>
            </div>
            <span>Menampilkan {{ count($users) }} karyawan</span>
        </div>

        {{-- Tombol Setujui / Tolak --}}
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Status rekap:</span>
                <span class="text-sm font-bold flex items-center gap-1 px-3 py-1 rounded-full
                    @php
                        $statusClass = $statusRekap === 'Disetujui'
                            ? 'text-green-600 bg-green-100'
                            : ($statusRekap === 'Ditolak'
                                ? 'text-red-600 bg-red-100'
                                : ($statusRekap === 'Belum Diajukan'
                                    ? 'text-gray-500 bg-gray-100 border border-gray-300'
                                    : 'text-yellow-600 bg-yellow-100'));
                    @endphp
                    {{ $statusClass }}">
                    <i class="fas fa-hourglass-half"></i> {{ $statusRekap }}
                </span>
            </div>

            @if ($statusRekap === 'Menunggu Persetujuan')
            <div class="flex gap-3 w-full md:w-auto" x-data="{ open: null }">
                {{-- Tolak --}}
                <button type="button" @click="open='tolak'"
                    class="flex-1 md:flex-none px-6 py-2.5 rounded-lg border border-red-200 bg-red-50
                           text-red-600 hover:bg-red-100 font-semibold text-sm flex items-center
                           justify-center gap-2 transition">
                    <i class="fas fa-times"></i> Tolak Rekap
                </button>

                {{-- Setujui --}}
                <button type="button" @click="open='setujui'"
                    class="flex-1 md:flex-none px-6 py-2.5 rounded-lg border border-green-200 bg-green-600
                           text-white hover:bg-green-700 font-semibold text-sm flex items-center
                           justify-center gap-2 transition shadow-sm">
                    <i class="fas fa-check"></i> Setujui Rekap
                </button>

                {{-- Modal konfirmasi --}}
                <div
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    x-show="open !== null"
                    x-transition.opacity
                    style="display: none;" x-cloak>
                    <div class="absolute inset-0 bg-black/40" @click="open=null"></div>

                    <div class="relative w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-100">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900">
                                <template x-if="open==='tolak'">Konfirmasi Tolak</template>
                                <template x-if="open==='setujui'">Konfirmasi Setujui</template>
                            </h3>
                            <button type="button" class="text-gray-500 hover:text-gray-700" @click="open=null">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>

                        <div class="p-4">
                            <p class="text-sm text-gray-700" x-show="open==='tolak'">
                                Apakah kamu yakin ingin menolak rekapan ini?
                            </p>
                            <p class="text-sm text-gray-700" x-show="open==='setujui'">
                                Apakah kamu yakin ingin menyetujui rekapan ini?
                            </p>
                        </div>

                        <div class="p-4 border-t border-gray-100 flex gap-3 justify-end">
                            <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm"
                                @click="open=null">
                                Batal
                            </button>

                            <button type="button" class="px-4 py-2 rounded-lg text-sm font-semibold"
                                :class="open==='tolak' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-green-600 text-white hover:bg-green-700'"
                                x-show="open==='tolak'"
                                @click="open=null; $wire.tolakRekap()">
                                Ya, Tolak
                            </button>

                            <button type="button" class="px-4 py-2 rounded-lg text-sm font-semibold"
                                :class="open==='setujui' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-green-600 text-white hover:bg-green-700'"
                                x-show="open==='setujui'"
                                @click="open=null; $wire.setujuiRekap()">
                                Ya, Setujui
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="mx-4 mb-4 px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>
    @endif

</div>

