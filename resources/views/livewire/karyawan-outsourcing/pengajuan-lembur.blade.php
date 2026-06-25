<div class="space-y-6" x-data="{ showSuccessModal: false }" @overtime-submitted.window="showSuccessModal = true">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-2 fade-in-up" role="alert">
            <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
            <div>
                <span class="font-bold">Sukses!</span> {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-2 fade-in-up" role="alert">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-lg"></i>
            <div>
                <span class="font-bold">Gagal!</span> {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Page title -->
    <div class="fade-in-up">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Pengajuan Lembur ⏱️</h1>
        <p class="text-sm text-gray-500 mt-0.5">Isi form pengajuan lembur di bawah untuk divalidasi oleh Kepala Departemen</p>
    </div>

    <!-- FORM CARD -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up" style="animation-delay:.05s">
        <!-- Card header -->
        <div style="background: linear-gradient(to right, #2d6e4a, #3C8B5E);" class="px-5 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-white font-bold text-base">
                        <i class="fa-solid fa-file-circle-plus mr-2 opacity-75"></i>Form Pengajuan
                    </h2>
                    <p class="text-green-200 text-xs mt-0.5">Silakan isi detail pengajuan lembur Anda</p>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="submit" class="px-6 py-6 space-y-6">
            <!-- Section title: Data Diri -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="section-bar bg-[#3C8B5E]"></div>
                    <h3 class="font-semibold text-gray-700 text-sm">Data Diri — Karyawan</h3>
                </div>

                <div class="grid md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="inp-label">NIP</label>
                        <input type="text" value="{{ $user->nip ?? '-' }}" readonly class="inp bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="inp-label">Nama Lengkap</label>
                        <input type="text" value="{{ $user->nama_lengkap ?? '-' }}" readonly class="inp bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="inp-label">No. HP</label>
                        <input type="tel" value="{{ $user->nomor_tlp ?? '-' }}" readonly class="inp bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="inp-label">Email</label>
                        <input type="email" value="{{ $user->email ?? '-' }}" readonly class="inp bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="inp-label">Asal Departemen</label>
                        <input type="text" value="{{ $user->departemen?->nama_departemen ?? '-' }}" readonly class="inp bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="inp-label">Alamat</label>
                        <input type="text" value="{{ $user->alamat ?? '-' }}" readonly class="inp bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Section title: Rincian Lembur -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="section-bar bg-[#3C8B5E]"></div>
                    <h3 class="font-semibold text-gray-700 text-sm">Rincian Pengajuan Lembur</h3>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="inp-label">Tanggal Lembur <span class="req">*</span></label>
                        <input wire:model="tanggal_lembur" type="date" class="inp @error('tanggal_lembur') border-red-500 @enderror">
                        @error('tanggal_lembur') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="inp-label">Jam Mulai <span class="req">*</span></label>
                        <input wire:model="jam_mulai" type="time" class="inp @error('jam_mulai') border-red-500 @enderror">
                        @error('jam_mulai') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="inp-label">Jam Selesai <span class="req">*</span></label>
                        <input wire:model="jam_selesai" type="time" class="inp @error('jam_selesai') border-red-500 @enderror">
                        @error('jam_selesai') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section title: Keterangan -->
            <div x-data="{ 
                keterangan: @entangle('keterangan'),
                get wordCount() {
                    let text = this.keterangan ? this.keterangan.trim() : '';
                    return text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;
                },
                get wordPercentage() {
                    return Math.min((this.wordCount / 300) * 100, 100);
                }
            }">
                <div class="flex items-center gap-2 mb-3">
                    <div class="section-bar bg-[#3C8B5E]"></div>
                    <h3 class="font-semibold text-gray-700 text-sm">Keterangan</h3>
                </div>
                <label class="inp-label">Apa yang akan dikerjakan hari ini? <span class="req">*</span></label>
                <textarea x-model="keterangan" rows="7" placeholder="Tuliskan rencana pekerjaan yang akan dilakukan saat lembur..." 
                    class="inp resize-none leading-relaxed @error('keterangan') border-red-500 @enderror" style="min-height:160px;"></textarea>
                @error('keterangan') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                
                <!-- Word counter -->
                <div class="mt-2 flex items-center justify-end gap-3">
                    <div class="flex-1 bg-gray-100 rounded-full overflow-hidden" style="height:4px">
                        <div class="h-full rounded-full transition-all" :style="'width: ' + wordPercentage + '%; background-color: ' + (wordCount > 300 ? '#ef4444' : (wordCount > 250 ? '#f59e0b' : '#3C8B5E'))"></div>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">
                        <span class="font-semibold text-gray-600" x-text="wordCount"></span> / 300 kata
                    </span>
                </div>
                <p class="text-xs text-red-500 mt-1" x-show="wordCount > 300" style="display: none;">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>Melebihi batas 300 kata!
                </p>
            </div>

            <!-- Submit button -->
            <div class="flex justify-end gap-3 pt-5 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition active:scale-95 flex items-center gap-2 shadow-sm" style="background:#3C8B5E">
                    <i class="fa-solid fa-paper-plane"></i>Ajukan Lembur
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL RIWAYAT -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up" style="animation-delay:.10s">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-gray-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-[#3C8B5E]"></i>
                    Riwayat Pengajuan Lembur
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">Daftar seluruh pengajuan yang telah diajukan</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <select wire:model.live="filterValidasi" class="text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="semua">Semua Status</option>
                    <option value="pending">Belum Divalidasi (Pending)</option>
                    <option value="valid">Sudah Disetujui (Valid)</option>
                    <option value="invalid">Ditolak (Invalid)</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tgl Lembur</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Jam Lembur</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($riwayatLembur as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-5 py-3.5 text-gray-700">
                                {{ \Carbon\Carbon::parse($item->mulai_lembur)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ \Carbon\Carbon::parse($item->mulai_lembur)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->selesai_lembur)->format('H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 max-w-xs truncate" title="{{ $item->keterangan }}">
                                {{ $item->keterangan }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($item->status_validasi === \App\Enums\Validasi::Pending->value)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i>Belum Divalidasi
                                    </span>
                                @elseif ($item->status_validasi === \App\Enums\Validasi::Valid->value)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                        <i class="fa-solid fa-check text-[10px]"></i>Sudah Disetujui
                                    </span>
                                @elseif ($item->status_validasi === \App\Enums\Validasi::Invalid->value)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-100">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>Ditolak
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-2 block text-gray-300"></i>
                                <p class="text-sm">Belum ada riwayat pengajuan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($riwayatLembur->isNotEmpty())
            <div class="px-5 py-3 border-t border-gray-50">
                <p class="text-xs text-gray-400">Menampilkan {{ $riwayatLembur->count() }} data</p>
            </div>
        @endif
    </div>

    <!-- MODAL SUKSES -->
    <div x-show="showSuccessModal" style="display: none;" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center" @click.self="showSuccessModal = false">
        <div class="bg-white rounded-2xl shadow-2xl p-8 mx-4 max-w-sm w-full text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-green-50">
                <i class="fa-solid fa-check text-2xl text-[#3C8B5E]"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Pengajuan Terkirim!</h3>
            <p class="text-sm text-gray-500 mb-6">Pengajuan lembur kamu berhasil dikirim dan sedang menunggu validasi.</p>
            <button @click="showSuccessModal = false"
                class="w-full py-3 rounded-xl font-semibold text-white transition active:scale-95 bg-[#3C8B5E]">
                Oke, Mengerti
            </button>
        </div>
    </div>
</div>
