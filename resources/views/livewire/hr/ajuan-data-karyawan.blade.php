<div>

    {{-- ─── Flash Messages ───────────────────────────────────── --}}
    @if (session()->has('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ─── Search Bar ───────────────────────────────────────── --}}
    <div class="mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari berdasarkan NIP, nama, atau asal vendor..."
                class="w-full sm:w-80 pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500
                       bg-gray-50/50 placeholder-gray-400 transition-all"
            >
        </div>
    </div>

    {{-- ─── Tabel Data Karyawan ──────────────────────────────── --}}
    <table class="w-full text-sm border-separate border-spacing-y-2">
        <thead class="bg-green-100 text-gray-600">
            <tr class="shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition">
                <th class="p-2 md:p-3 text-center text-xs md:text-sm w-12 whitespace-nowrap">No</th>
                <th class="p-2 md:p-3 text-left text-xs md:text-sm whitespace-nowrap">NIP</th>
                <th class="p-2 md:p-3 text-left text-xs md:text-sm uppercase whitespace-nowrap">Nama Karyawan</th>
                <th class="p-2 md:p-3 text-left text-xs md:text-sm uppercase whitespace-nowrap">Asal Vendor</th>
                <th class="p-2 md:p-3 text-center text-xs md:text-sm w-32 whitespace-nowrap">AKSI</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($karyawanList as $index => $karyawan)
                <tr wire:click="openDetail({{ $karyawan->id_user }})"
                    class="odd:bg-white even:bg-gray-100 shadow-sm hover:bg-green-50 cursor-pointer transition-colors">

                    <td class="p-3 text-center font-medium">
                        {{ $karyawanList->firstItem() + $index }}
                    </td>
                    <td class="p-3 text-left font-mono whitespace-nowrap">
                        {{ $karyawan->nip }}
                    </td>
                    <td class="p-3 text-left whitespace-nowrap">
                        {{ $karyawan->nama_lengkap }}
                    </td>
                    <td class="p-3 text-left whitespace-nowrap">
                        {{ $karyawan->outsourcing?->nama_outsourcing ?? '-' }}
                    </td>
                    <td class="p-3 text-center whitespace-nowrap">
                        <button wire:click.stop="approve({{ $karyawan->id_user }})"
                            class="bg-green-500 hover:bg-green-600 transition text-white px-2 py-1 rounded shadow-sm">
                            <i class="fas fa-check"></i>
                        </button>
                        <button wire:click.stop="openRejectInline({{ $karyawan->id_user }})"
                            class="bg-red-500 hover:bg-red-600 transition text-white px-2 py-1 rounded shadow-sm">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2 block"></i>
                        Tidak ada data ajuan karyawan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ─── Pagination ───────────────────────────────────────── --}}
    @if ($karyawanList->hasPages())
        <div class="flex justify-end mt-4">
            {{ $karyawanList->links() }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         MODAL: Detail Karyawan
         ══════════════════════════════════════════════════════════ --}}
    @if ($showDetailModal)
        <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity duration-200"
             wire:click.self="closeDetail">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden flex flex-col max-h-[90vh] transform transition-transform duration-200">

                {{-- Header --}}
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <i class="fas fa-user-tie text-green-600"></i>
                        Detail Karyawan
                    </h3>
                    <button wire:click="closeDetail"
                        class="text-gray-400 hover:text-red-500 transition-colors bg-white rounded-full w-8 h-8
                               flex items-center justify-center shadow-sm border border-gray-200 focus:outline-none">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 overflow-y-auto flex flex-col gap-4 bg-gray-50/50">
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <div class="space-y-3 text-sm">
                            @foreach ([
                                'NIP'           => $selectedUser['nip'] ?? '-',
                                'Nama Lengkap'  => $selectedUser['nama_lengkap'] ?? '-',
                                'Email'         => $selectedUser['email'] ?? '-',
                                'Nomor Telepon' => $selectedUser['nomor_tlp'] ?? '-',
                                'Alamat'        => $selectedUser['alamat'] ?? '-',
                            ] as $label => $value)
                                <div class="flex items-center justify-between border-b border-gray-50 pb-2">
                                    <span class="font-medium text-gray-500 w-1/3">{{ $label }}</span>
                                    <span class="font-bold text-gray-800 w-2/3 text-right">{{ $value }}</span>
                                </div>
                            @endforeach

                            <div class="flex items-center justify-between pt-1">
                                <span class="font-medium text-gray-500 w-1/3">Berasal dari</span>
                                <span class="font-bold text-green-700 bg-green-50 px-2 py-1 rounded-md text-xs border border-green-100 text-right">
                                    {{ $selectedUser['asal_vendor'] ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="openReject"
                        class="px-6 py-2.5 rounded-lg border border-red-200 bg-red-50 text-red-600
                               hover:bg-red-100 font-semibold text-sm flex items-center justify-center gap-2 transition">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                    <button wire:click="approve"
                        class="px-6 py-2.5 rounded-lg border border-green-200 bg-green-600 text-white
                               hover:bg-green-700 font-semibold text-sm flex items-center justify-center gap-2 transition shadow-sm">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         MODAL: Alasan Penolakan
         ══════════════════════════════════════════════════════════ --}}
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center transition-opacity duration-200"
             wire:click.self="closeReject">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden flex flex-col transform transition-transform duration-200">

                {{-- Header --}}
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                        Alasan Penolakan
                    </h3>
                    <button wire:click="closeReject"
                        class="text-gray-400 hover:text-red-500 transition-colors bg-white rounded-full w-8 h-8
                               flex items-center justify-center shadow-sm border border-gray-200 focus:outline-none">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 bg-white">
                    <label for="alasanPenolakan" class="block text-sm font-medium text-gray-700 mb-2">
                        Berikan alasan mengapa pengajuan ini ditolak:
                    </label>
                    <textarea
                        wire:model="alasanPenolakan"
                        id="alasanPenolakan"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none
                               focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-gray-50/50
                               text-sm placeholder-gray-400 transition-all shadow-inner"
                        placeholder="Ketik alasan penolakan di sini..."
                    ></textarea>

                    @error('alasanPenolakan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="closeReject"
                        class="px-5 py-2.5 rounded-lg border border-gray-200 bg-white text-gray-700
                               hover:bg-gray-50 font-semibold text-sm flex items-center justify-center transition shadow-sm focus:outline-none">
                        Batal
                    </button>
                    <button wire:click="reject"
                        class="px-6 py-2.5 rounded-lg border border-red-200 bg-red-600 text-white
                               hover:bg-red-700 font-semibold text-sm flex items-center justify-center gap-2 transition shadow-sm focus:outline-none">
                        <i class="fas fa-paper-plane"></i> Kirim Penolakan
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
