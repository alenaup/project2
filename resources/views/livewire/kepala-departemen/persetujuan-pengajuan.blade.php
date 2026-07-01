<div x-data="{ isOpen: false, detail: {}, isApproveAllModalOpen: false, approveAllType: 'all', approveAllDate: '' }">
    <!-- Title -->
    <div class="animate-bitem mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4 bg-gradient-to-r from-emerald-100 p-6 rounded-xl">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent flex items-center">
                    <i class="fas fa-user-check pr-2 text-emerald-600"></i>Persetujuan Lembur
                </h2>

                <!-- Kotak Kecil Notif -->
                <div x-data="{ popupOpen: false }" class="relative inline-block">
                    @if(count($pendingLemburs) > 0)
                        <div class="relative inline-block animate-bitem">
                            <!-- Bulat kecil warna merah -->
                            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 border border-white rounded-full"></span>
                            <button @click="popupOpen = !popupOpen" 
                                class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold text-white shadow-sm cursor-pointer hover:scale-105 active:scale-95 transition-all duration-200
                                {{ $indicatorColor === 'red' ? 'bg-rose-500 animate-pulse' : '' }}
                                {{ $indicatorColor === 'yellow' ? 'bg-amber-500 animate-pulse' : '' }}
                                {{ $indicatorColor === 'green' ? 'bg-emerald-500' : '' }}
                                "
                                title="Ada pengajuan pending!">
                                <i class="fa-solid fa-bell text-[10px]"></i>
                            </button>
                            
                        </div>
                        
                        <!-- Popup di atas kotak -->
                        <div x-show="popupOpen" @click.outside="popupOpen = false" style="display: none;"
                            class="fixed bottom-8 right-0 translate-x-0 sm:left-1/2 sm:right-auto sm:-translate-x-1/2 bg-white border border-slate-200 shadow-xl rounded-2xl p-4 w-72 max-w-[85vw] z-40 text-left text-xs text-slate-700  ">
                            <!-- Arrow pointer pointing down -->
                            <div class="absolute -bottom-1.5 right-2 translate-x-0 sm:left-1/2 sm:right-auto sm:-translate-x-1/2 w-3 h-3 bg-white border-r border-b border-slate-200 rotate-45"></div>
                            
                            <div class="relative z-10">
                                <div class="font-extrabold text-slate-800 border-b border-slate-100 pb-2 mb-2 flex items-center justify-between">
                                    <span>Persetujuan Tertunda</span>
                                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider
                                        {{ $indicatorColor === 'red' ? 'bg-rose-50 text-rose-700' : '' }}
                                        {{ $indicatorColor === 'yellow' ? 'bg-amber-50 text-amber-700' : '' }}
                                        {{ $indicatorColor === 'green' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    ">
                                        {{ count($pendingLemburs) }} Terlama
                                    </span>
                                </div>
                                
                                <div class="space-y-2 max-h-60 overflow-y-auto">
                                    @foreach($pendingLemburs as $pLembur)
                                        <a href="#" wire:key="pending-bell-{{ $pLembur['id'] }}" @click.prevent='detail = {{ json_encode($pLembur) }}; isOpen = true; popupOpen = false'
                                            class="block p-2 rounded-xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-100 hover:border-emerald-100 transition-all duration-200">
                                            <div class="flex justify-between items-start">
                                                <span class="font-bold text-slate-700 block truncate max-w-[120px]">{{ $pLembur['nama'] }}</span>
                                                <span class="text-[9px] text-slate-400 font-semibold">{{ $pLembur['selisih'] }}</span>
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                                                <i class="fa-regular fa-calendar text-[9px]"></i>
                                                <span>{{ $pLembur['tanggal'] }} ({{ $pLembur['jam'] }})</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <p class="text-2xl font-bold bg-gradient-to-r from-emerald-700 to-teal-500 bg-clip-text text-transparent text-sm mt-1">
                Kepala Departemen: <span class="font-semibold text-gray-700">{{ Auth::user()->nama_lengkap ?? '-' }}</span>
                | Departemen: <span class="font-semibold text-emerald-600">{{ Auth::user()->departemen?->nama_departemen ?? 'Tidak Ada Departemen' }}</span>
            </p>
            <p class="text-gray-400 text-xs mt-1">Klik salah satu data di bawah untuk melihat detail dan memberikan keputusan.</p>
        </div>
        <div>
            @if($hasPending)
                <button @click="isApproveAllModalOpen = true"
                    class="animate-bitem px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-sm font-semibold text-sm flex items-center gap-2 cursor-pointer border border-transparent">
                    <i class="fa-solid fa-check-double"></i>
                    Setujui Semua Pending
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('success'))
        @php
            $alertType = session('alert-type', 'success-approved');
        @endphp
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="mb-4 animate-bitem">
            @if ($alertType === 'success-rejected')
                <div class="p-4 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-2" role="alert">
                    <i class="fa-solid fa-circle-check text-amber-600"></i>
                    <div>
                        <span class="font-semibold">Sukses Ditolak!</span> {{ session('success') }}
                    </div>
                </div>
            @else
                <div class="p-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2" role="alert">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    <div>
                        <span class="font-semibold">Sukses Diterima!</span> {{ session('success') }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Filter Section -->
    <div class="animate-bitem mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white/80 p-4 rounded-xl border border-slate-200/60 shadow-xs">
        <div class="text-xs font-bold text-slate-450 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-filter text-emerald-600"></i> Filter Data
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <!-- DATE FILTER -->
            <div class="relative w-full sm:w-56">
                <div class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <i class="fas fa-calendar text-xs"></i>
                </div>
                <input
                    type="date"
                    wire:model.live="filterDate"
                    class="w-full pl-9 pr-8 py-2 border border-slate-200 rounded-xl text-sm shadow-xs focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition cursor-pointer bg-white"
                >
                @if($filterDate)
                    <button
                        type="button"
                        wire:click="$set('filterDate', '')"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-red-500 transition cursor-pointer"
                    >
                        <i class="fas fa-times text-xs"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="w-full bg-white rounded-2xl shadow overflow-hidden border border-gray-100">
        <div class="overflow-x-auto w-full animate-bitem">
            <table class="w-full min-w-max text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                    <tr class="whitespace-nowrap">
                        <th class="p-4 text-left">Nama</th>
                        <th class="p-4 text-left">Tanggal</th>
                        <th class="p-4 text-left">Jam</th>
                        <th class="p-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($lemburList as $item)
                        <tr wire:key="lembur-row-{{ $item->id_lembur }}" @click='detail = {{ json_encode([
                                "id" => $item->id_lembur,
                                "nama" => $item->karyawan->nama_lengkap ?? "-",
                                "tanggal" => \Carbon\Carbon::parse($item->mulai_lembur)->translatedFormat("d F Y"),
                                "jam" => \Carbon\Carbon::parse($item->mulai_lembur)->format("H:i") . " - " . \Carbon\Carbon::parse($item->selesai_lembur)->format("H:i"),
                                "status" => $item->status_validasi,
                                "keterangan" => $item->keterangan ?? "Tidak ada keterangan."
                            ]) }}; isOpen = true'
                            class="animate-bitem cursor-pointer hover:bg-green-50/50 transition duration-150">
                            <td class="p-4 font-medium text-gray-800 whitespace-nowrap">{{ $item->karyawan->nama_lengkap ?? '-' }}</td>
                            <td class="p-4 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->mulai_lembur)->translatedFormat('d F Y') }}</td>
                            <td class="p-4 text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->mulai_lembur)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->selesai_lembur)->format('H:i') }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if ($item->status_validasi === \App\Enums\Validasi::Pending->value)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-55/50 text-yellow-600 border border-yellow-100 flex items-center gap-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                        Pending
                                    </span>
                                @elseif ($item->status_validasi === \App\Enums\Validasi::Valid->value)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-55/50 text-green-600 border border-green-100 flex items-center gap-1.5 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Disetujui
                                    </span>
                                @elseif ($item->status_validasi === \App\Enums\Validasi::Invalid->value)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-55/50 text-red-600 border border-red-100 flex items-center gap-1.5 w-fit">
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

        @if($lemburList->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $lemburList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Setujui Semua (Alpine.js) -->
    <div x-show="isApproveAllModalOpen" style="display: none;"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-200"
        @click.self="isApproveAllModalOpen = false">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg p-6 mx-4 transform transition-all duration-200 max-h-[90vh] overflow-y-auto"
            @click.stop>

            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                <h3 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-check-double text-emerald-500"></i>
                    Setujui Semua Pengajuan
                </h3>
                <button @click="isApproveAllModalOpen = false" class="text-slate-400 hover:text-slate-650 transition">
                    ✕
                </button>
            </div>

            <div class="space-y-4 text-sm text-slate-600">
                <p class="font-medium text-slate-700">Pilih metode persetujuan massal:</p>
                
                <div class="space-y-3">
                    <!-- Opsi 1: Setujui Semua Tanpa Filter -->
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-200 hover:bg-emerald-50/20 cursor-pointer transition">
                        <input type="radio" name="approve_type" value="all" checked x-model="approveAllType"
                            class="mt-0.5 w-4 h-4 text-emerald-600 border-slate-350 focus:ring-emerald-500 cursor-pointer">
                        <div>
                            <span class="font-bold text-slate-850 block">Setujui Seluruh Pengajuan</span>
                            <span class="text-xs text-slate-500">Setujui semua pengajuan pending tanpa batas waktu/tanggal.</span>
                        </div>
                    </label>

                    <!-- Opsi 2: Setujui Berdasarkan Tanggal -->
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-200 hover:bg-emerald-50/20 cursor-pointer transition">
                        <input type="radio" name="approve_type" value="date" x-model="approveAllType"
                            class="mt-0.5 w-4 h-4 text-emerald-600 border-slate-350 focus:ring-emerald-500 cursor-pointer">
                        <div>
                            <span class="font-bold text-slate-850 block">Pilih Berdasarkan Tanggal</span>
                            <span class="text-xs text-slate-500 block mb-2">Setujui semua pengajuan pending khusus pada tanggal tertentu.</span>
                            
                            <!-- Input Tanggal (hanya muncul/aktif jika opsi ini terpilih) -->
                            <div x-show="approveAllType === 'date'" class="relative mt-1">
                                <div class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                    <i class="fas fa-calendar text-xs"></i>
                                </div>
                                <input
                                    type="date"
                                    x-model="approveAllDate"
                                    class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm shadow-xs focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none transition bg-white cursor-pointer"
                                >
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Footer / Actions -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                <button @click="isApproveAllModalOpen = false"
                    class="px-4 py-2 rounded-xl bg-slate-50 border border-slate-250 hover:bg-slate-100 text-slate-600 text-xs font-bold transition">
                    Batal
                </button>
                <button :disabled="approveAllType === 'date' && !approveAllDate"
                    @click="
                        $wire.approveAllPending(approveAllType === 'date' ? approveAllDate : null);
                        isApproveAllModalOpen = false;
                    "
                    class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-md shadow-emerald-500/10 hover:shadow-lg disabled:opacity-40 disabled:pointer-events-none">
                    Proses Persetujuan
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Detail (Alpine.js) -->
    <div x-show="isOpen" style="display: none;"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm transition-opacity duration-200"
        @click.self="isOpen = false; showApproveConfirm = false; showRejectConfirm = false"
        x-data="{ showApproveConfirm: false, showRejectConfirm: false }">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 mx-4 transform transition-all duration-200 max-h-[90vh] overflow-y-auto"
            @click.stop>

            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-blue-500"></i>
                    Detail Lembur
                </h3>
                <button @click="isOpen = false; showApproveConfirm = false; showRejectConfirm = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-4 text-sm text-gray-600">
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-500">Nama Karyawan</span>
                    <span class="col-span-2 text-gray-800 font-medium" x-text="': ' + detail.nama"></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-500">Tanggal Lembur</span>
                    <span class="col-span-2 text-gray-800 font-medium" x-text="': ' + detail.tanggal"></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-500">Jam Lembur</span>
                    <span class="col-span-2 text-gray-800 font-medium" x-text="': ' + detail.jam"></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="font-semibold text-gray-500">Status Validasi</span>
                    <span class="col-span-2 font-medium flex items-center gap-1.5">
                        :
                        <template x-if="detail.status === '{{ \App\Enums\Validasi::Pending->value }}'">
                            <span class="px-2 py-0.5 rounded bg-yellow-55/50 text-yellow-700 border border-yellow-100 text-xs font-semibold">Pending</span>
                        </template>
                        <template x-if="detail.status === '{{ \App\Enums\Validasi::Valid->value }}'">
                            <span class="px-2 py-0.5 rounded bg-green-55/50 text-green-700 border border-green-100 text-xs font-semibold">Disetujui</span>
                        </template>
                        <template x-if="detail.status === '{{ \App\Enums\Validasi::Invalid->value }}'">
                            <span class="px-2 py-0.5 rounded bg-red-55/50 text-red-700 border border-red-100 text-xs font-semibold">Ditolak</span>
                        </template>
                    </span>
                </div>

                <div class="flex flex-col gap-1.5 pt-2">
                    <span class="font-semibold text-gray-500">Keterangan / Deskripsi</span>
                    <div class="bg-gray-50 border border-gray-150 p-4 rounded-xl text-gray-700 whitespace-pre-line leading-relaxed" x-text="detail.keterangan">
                    </div>
                </div>
            </div>

            <!-- Action -->
            <div class="flex justify-end gap-3 mt-6 pt-3 border-t border-gray-100">
                <template x-if="detail.status === '{{ \App\Enums\Validasi::Pending->value }}'">
                    <div class="flex gap-3 items-center">
                        <!-- Default view -->
                        <div x-show="!showApproveConfirm && !showRejectConfirm" class="flex gap-3">
                            <button @click="showRejectConfirm = true"
                                class="px-4 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 transition shadow-sm font-semibold text-xs flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-times-circle"></i>
                                Tolak
                            </button>
                            <button @click="showApproveConfirm = true"
                                class="px-4 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition shadow-sm font-semibold text-xs flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-check-circle"></i>
                                Terima
                            </button>
                        </div>

                        <!-- Reject Confirmation view -->
                        <div x-show="showRejectConfirm" class="flex items-center gap-2 bg-red-50 p-2 rounded-xl border border-red-100">
                            <span class="text-xs text-red-700 font-semibold">Yakin tolak?</span>
                            <button @click="$wire.reject(detail.id); isOpen = false; showRejectConfirm = false"
                                class="px-2.5 py-1 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 cursor-pointer">Ya</button>
                            <button @click="showRejectConfirm = false"
                                class="px-2.5 py-1 bg-slate-200 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-300 cursor-pointer">Batal</button>
                        </div>

                        <!-- Approve Confirmation view -->
                        <div x-show="showApproveConfirm" class="flex items-center gap-2 bg-green-50 p-2 rounded-xl border border-green-100">
                            <span class="text-xs text-green-700 font-semibold">Yakin setujui?</span>
                            <button @click="$wire.approve(detail.id); isOpen = false; showApproveConfirm = false"
                                class="px-2.5 py-1 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700 cursor-pointer">Ya</button>
                            <button @click="showApproveConfirm = false"
                                class="px-2.5 py-1 bg-slate-200 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-300 cursor-pointer">Batal</button>
                        </div>
                    </div>
                </template>
                <template x-if="detail.status !== '{{ \App\Enums\Validasi::Pending->value }}'">
                    <button @click="isOpen = false; showApproveConfirm = false; showRejectConfirm = false"
                        class="px-4 py-2.5 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition shadow-sm font-semibold text-xs flex items-center gap-1.5 cursor-pointer">
                        Tutup
                    </button>
                </template>
            </div>

        </div>
    </div>
</div>
