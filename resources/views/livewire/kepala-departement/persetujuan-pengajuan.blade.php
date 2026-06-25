<div>
    <!-- Title -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Persetujuan Lembur</h2>
        <p class="text-gray-500 text-sm">
            Kepala Departemen: <span class="font-semibold text-gray-700">{{ Auth::user()->nama_lengkap ?? '-' }}</span> 
            | Departemen: <span class="font-semibold text-emerald-600">{{ Auth::user()->departemen?->nama_departemen ?? 'Tidak Ada Departemen' }}</span>
        </p>
        <p class="text-gray-400 text-xs mt-1">Klik salah satu data di bawah untuk melihat detail dan memberikan keputusan.</p>
    </div>

    <!-- Alert Success -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-check text-green-600"></i>
            <div>
                <span class="font-semibold">Sukses!</span> {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow overflow-hidden border border-gray-100">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                <tr>
                    <th class="p-4 text-left">Nama</th>
                    <th class="p-4 text-left">Tanggal</th>
                    <th class="p-4 text-left">Jam</th>
                    <th class="p-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($lemburList as $item)
                    <tr wire:click="selectLembur({{ $item->id_lembur }})" class="cursor-pointer hover:bg-green-50/50 transition duration-150">
                        <td class="p-4 font-medium text-gray-800">{{ $item->karyawan->nama_lengkap ?? '-' }}</td>
                        <td class="p-4 text-gray-600">{{ \Carbon\Carbon::parse($item->mulai_lembur)->translatedFormat('d F Y') }}</td>
                        <td class="p-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($item->mulai_lembur)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->selesai_lembur)->format('H:i') }}
                        </td>
                        <td class="p-4">
                            @if ($item->status_validasi === \App\Enums\Validasi::Pending->value)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-600 border border-yellow-100 flex items-center gap-1.5 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                    Pending
                                </span>
                            @elseif ($item->status_validasi === \App\Enums\Validasi::Valid->value)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100 flex items-center gap-1.5 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Disetujui
                                </span>
                            @elseif ($item->status_validasi === \App\Enums\Validasi::Invalid->value)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100 flex items-center gap-1.5 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fa-solid fa-inbox text-3xl text-gray-300"></i>
                                <span>Tidak ada data pengajuan lembur.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Detail -->
    @if ($selectedLemburId && $this->selectedLembur)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-200" wire:click.self="closeModal">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 mx-4 transform transition-all duration-200">

                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice text-blue-500"></i>
                        Detail Lembur
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="space-y-4 text-sm text-gray-600">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-500">Nama Karyawan</span>
                        <span class="col-span-2 text-gray-800 font-medium">: {{ $this->selectedLembur->karyawan->nama_lengkap ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-500">Tanggal Lembur</span>
                        <span class="col-span-2 text-gray-800 font-medium">: {{ \Carbon\Carbon::parse($this->selectedLembur->mulai_lembur)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-500">Jam Lembur</span>
                        <span class="col-span-2 text-gray-800 font-medium">: {{ \Carbon\Carbon::parse($this->selectedLembur->mulai_lembur)->format('H:i') }} - {{ \Carbon\Carbon::parse($this->selectedLembur->selesai_lembur)->format('H:i') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-semibold text-gray-500">Status Validasi</span>
                        <span class="col-span-2 font-medium flex items-center gap-1.5">
                            :
                            @if ($this->selectedLembur->status_validasi === \App\Enums\Validasi::Pending->value)
                                <span class="px-2 py-0.5 rounded bg-yellow-55/50 text-yellow-700 border border-yellow-100 text-xs font-semibold">Pending</span>
                            @elseif ($this->selectedLembur->status_validasi === \App\Enums\Validasi::Valid->value)
                                <span class="px-2 py-0.5 rounded bg-green-55/50 text-green-700 border border-green-100 text-xs font-semibold">Disetujui</span>
                            @elseif ($this->selectedLembur->status_validasi === \App\Enums\Validasi::Invalid->value)
                                <span class="px-2 py-0.5 rounded bg-red-55/50 text-red-700 border border-red-100 text-xs font-semibold">Ditolak</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex flex-col gap-1.5 pt-2">
                        <span class="font-semibold text-gray-500">Keterangan / Deskripsi</span>
                        <div class="bg-gray-50 border border-gray-150 p-4 rounded-xl text-gray-700 whitespace-pre-line leading-relaxed">
                            {{ $this->selectedLembur->keterangan ?? 'Tidak ada keterangan.' }}
                        </div>
                    </div>
                </div>

                <!-- Action -->
                <div class="flex justify-end gap-3 mt-6 pt-3 border-t border-gray-100">
                    @if ($this->selectedLembur->status_validasi === \App\Enums\Validasi::Pending->value)
                        <button wire:click="reject"
                            class="px-4 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 transition shadow-sm font-semibold text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-times-circle"></i>
                            Tolak
                        </button>
                        <button wire:click="approve"
                            class="px-4 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition shadow-sm font-semibold text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-check-circle"></i>
                            Terima
                        </button>
                    @else
                        <button wire:click="closeModal"
                            class="px-4 py-2.5 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition shadow-sm font-semibold text-xs flex items-center gap-1.5">
                            Tutup
                        </button>
                    @endif
                </div>

            </div>
        </div>
    @endif
</div>
