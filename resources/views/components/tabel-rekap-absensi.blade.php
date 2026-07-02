@props(['datas', 'koloms'])

@php
    $totalH = 0; // Hadir
    $totalA = 0; // Alpha
    $totalS = 0; // Sakit
    $totalI = 0; // Izin
    $totalL = 0; // Lembur
    $totalLibur = 0; // -

    foreach ($datas as $data) {
        foreach ($data['absens'] as $absen) {
            switch ($absen['value']) {
                case 'H':
                    $totalH++;
                    break;
                case 'A':
                    $totalA++;
                    break;
                case 'S':
                    $totalS++;
                    break;
                case 'I':
                    $totalI++;
                    break;
                case 'L':
                    $totalL++;
                    break;
                case '-':
                    $totalLibur++;
                    break;
            }
        }
    }
@endphp

<div class="w-full overflow-x-auto pb-2 custom-scrollbar">
    <table class="w-full text-left text-xs whitespace-nowrap min-w-max border-collapse">
        <thead class="bg-gray-50 text-gray-500 font-semibold uppercase">
            <tr>
                <th class="px-4 py-3 border-b border-gray-200">#</th>
                <th
                    class="px-4 py-3 border-b border-gray-200 md:sticky md:left-0 md:z-20 bg-gray-50 md:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                    NAMA KARYAWAN</th>
                <th class="px-4 py-3 border-b border-gray-200">POSISI</th>
                @foreach ($koloms as $kolom)
                    <th class="px-4 py-3 border-b border-gray-200 text-center w-6">{{ $kolom }}</th>
                @endforeach
                <th class="px-4 py-3 text-center border-b">H</th>
                <th class="px-4 py-3 text-center border-b">A</th>
                <th class="px-4 py-3 text-center border-b">S</th>
                <th class="px-4 py-3 text-center border-b">I</th>
                <th class="px-4 py-3 text-center border-b">L</th>
                <th class="px-4 py-3 text-center border-b">-</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($datas as $data)
                @php
                    $rowBg = $loop->even ? 'bg-emerald-50' : 'bg-white';
                @endphp
                <tr class="animate-bitem group hover:bg-emerald-100 {{ $rowBg }} transition-colors cursor-pointer"
                    onclick="openModal('{{ $data['nama'] }}')">
                    <td class="px-4 py-3 text-gray-500">{{ $data['no'] }}</td>
                    <td
                        class="px-4 py-3 md:sticky md:left-0 md:z-10 {{ $rowBg }} group-hover:bg-emerald-100 md:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] transition-colors">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-full text-white flex items-center justify-center font-bold {{ $data['warna'] }} shrink-0">
                                {{ $data['inisial'] }}</div>
                            <div>
                                <div class="font-bold text-gray-800">{{ $data['nama'] }}</div>
                                <div class="text-[10px] text-gray-400">{{ $data['perusahaan'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $data['posisi'] }}</td>
                    @foreach ($data['absens'] as $absen)
                        <td class="px-1 py-3 text-center"><span
                                class="inline-flex w-5 h-5 items-center justify-center rounded text-[10px] font-bold {{ $absen['warna'] }} text-{{ $absen['warna'] }}">{{ $absen['value'] }}</span>
                        </td>
                    @endforeach
                    @php
                        $h = $a = $s = $i = $l = $libur = 0;

                        foreach ($data['absens'] as $absen) {
                            switch ($absen['value']) {
                                case 'H':
                                    $h++;
                                    break;
                                case 'A':
                                    $a++;
                                    break;
                                case 'S':
                                    $s++;
                                    break;
                                case 'I':
                                    $i++;
                                    break;
                                case 'L':
                                    $l++;
                                    break;
                                case '-':
                                    $libur++;
                                    break;
                            }
                        }
                    @endphp
                    <td class="px-4 py-3 text-center font-bold text-green-600 border-l bg-green-50/30">
                        {{ $h }}
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-red-600 bg-red-50/30">
                        {{ $a }}
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-yellow-600 bg-yellow-50/30">
                        {{ $s }}
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-blue-600 bg-blue-50/30">
                        {{ $i }}
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-purple-600 bg-purple-50/30">
                        {{ $l }}
                    </td>
                    <td class="px-4 py-3 text-center font-bold text-gray-500 bg-gray-100/30">
                        {{ $libur }}
                    </td>
                </tr>
            @endforeach

        </tbody>
        <tfoot class="bg-gray-100 border-t-2 border-gray-200 font-bold text-sm">
            <tr>
                <td class="px-4 py-3 text-gray-700"></td>
                <td
                    class="px-4 py-3 text-gray-800 text-right md:sticky md:left-0 md:z-10 bg-gray-100 md:shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                    TOTAL REKAP</td>
                <td class="px-4 py-3 text-gray-700"></td>
                <td colspan="31"></td>
                <td class="px-4 py-3 text-center text-green-700 border-l border-gray-200 bg-green-100/50">
                    {{ $totalH }}
                </td>
                <td class="px-4 py-3 text-center text-red-700 bg-red-100/50">
                    {{ $totalA }}
                </td>
                <td class="px-4 py-3 text-center text-yellow-700 bg-yellow-100/50">
                    {{ $totalS }}
                </td>
                <td class="px-4 py-3 text-center text-blue-700 bg-blue-100/50">
                    {{ $totalI }}
                </td>
                <td class="px-4 py-3 text-center text-purple-700 bg-purple-100/50">
                    {{ $totalL }}
                </td>
                <td class="px-4 py-3 text-center text-gray-500 bg-gray-100/50">
                    {{ $totalLibur }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div
    class="p-4 border-t border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 text-xs text-gray-500 bg-white">
    <div class="flex items-center flex-wrap gap-x-4 gap-y-2">
        <span class="font-semibold text-gray-700">Keterangan:</span>
        <span class="flex items-center gap-1.5"><span
                class="w-5 h-5 bg-green-100 text-green-700 flex items-center justify-center rounded font-bold">H</span>
            Hadir</span>
        <span class="flex items-center gap-1.5"><span
                class="w-5 h-5 bg-red-100 text-red-700 flex items-center justify-center rounded font-bold">A</span>
            Alpha</span>
        <span class="flex items-center gap-1.5"><span
                class="w-5 h-5 bg-yellow-100 text-yellow-700 flex items-center justify-center rounded font-bold">S</span>
            Sakit</span>
        <span class="flex items-center gap-1.5"><span
                class="w-5 h-5 bg-blue-100 text-blue-700 flex items-center justify-center rounded font-bold">I</span>
            Izin</span>
        <span class="flex items-center gap-1.5"><span
                class="w-5 h-5 bg-purple-100 text-purple-700 flex items-center justify-center rounded font-bold">L</span>
            Lembur</span>
        <span class="flex items-center gap-1.5"><span class="text-gray-300 font-bold">-</span> Libur</span>
    </div>

    <div class="flex items-center gap-4 shrink-0 w-full lg:w-auto justify-between lg:justify-end mt-2 lg:mt-0">
        <span>Menampilkan 1-3 dari 3 karyawan</span>
        <div class="bg-green-700 text-white w-7 h-7 flex items-center justify-center rounded-lg shadow-sm font-medium">
            1</div>
    </div>
</div>

<x-hr.modal-dokumen-bukti />
