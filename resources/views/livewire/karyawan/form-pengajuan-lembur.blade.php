<div>
    {{-- FORM CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up"
        style="animation-delay:.05s">

        {{-- Card header --}}
        <div style="background: linear-gradient(to right, #2d6e4a, #3C8B5E);" class="px-5 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-white font-bold text-base">
                        <i class="fa-solid fa-file-circle-plus mr-2 opacity-75"></i>Form Pengajuan
                    </h2>
                    <p class="text-green-200 text-xs mt-0.5">Isi data pengajuan lembur</p>
                </div>
                <div class="flex rounded-xl overflow-hidden border border-white/40 self-start sm:self-auto">
                    <div class="px-5 py-2 text-sm font-semibold flex items-center gap-2 bg-white text-[#2d6e4a]">
                        <i class="fa-solid fa-stopwatch text-xs"></i>
                        Pengajuan Lembur
                    </div>
                </div>
            </div>
        </div>

        {{-- Form body --}}
        <form wire:submit.prevent="simpanPengajuan" class="px-6 py-6"
              x-data="{
                  init() {
                      if (typeof Livewire !== 'undefined') {
                          Livewire.hook('commit', ({ succeed }) => {
                              succeed(() => {
                                  setTimeout(() => {
                                      const errorEl = document.querySelector('.border-red-500');
                                      if (errorEl) {
                                          errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                          errorEl.focus();
                                      }
                                  }, 50);
                              });
                          });
                      }
                  }
              }">
              
            {{-- Success Message --}}
            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition.duration.500ms
                     class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold mb-5 shadow-sm">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Section title --}}
            <div class="flex items-center gap-2 mb-5">
                <div class="section-bar bg-[#3C8B5E]"></div>
                <h3 class="font-semibold text-gray-700 text-sm">Data Diri — Pengajuan Lembur</h3>
            </div>

            <div class="grid md:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <label class="inp-label">NIP</label>
                    <input type="text" value="{{ $user->nip }}" readonly class="inp bg-gray-50">
                </div>
                <div>
                    <label class="inp-label">Nama Lengkap</label>
                    <input type="text" value="{{ $user->nama_lengkap }}" readonly class="inp bg-gray-50">
                </div>
                <div>
                    <label class="inp-label">No. HP</label>
                    <input type="tel" value="{{ $user->nomor_tlp ?? '-' }}" readonly class="inp bg-gray-50">
                </div>
                <div>
                    <label class="inp-label">Email</label>
                    <input type="email" value="{{ $user->email }}" readonly class="inp bg-gray-50">
                </div>
                <div>
                    <label class="inp-label">Tanggal Lembur <span class="req">*</span></label>
                    <input wire:model="tanggal_lembur" type="date"
                        class="inp @error('tanggal_lembur') border-red-500 @enderror">
                    @error('tanggal_lembur')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="inp-label">Asal Departemen</label>
                    <input type="text" value="{{ $user->departemen->nama_departemen ?? '-' }}" readonly
                        class="inp bg-gray-50">
                </div>
                <div>
                    <label class="inp-label">Jam Mulai <span class="req">*</span></label>
                    <input wire:model="jam_mulai" type="time"
                        class="inp @error('jam_mulai') border-red-500 @enderror">
                    @error('jam_mulai')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="inp-label">Jam Selesai <span class="req">*</span></label>
                    <input wire:model="jam_selesai" type="time"
                        class="inp @error('jam_selesai') border-red-500 @enderror">
                    @error('jam_selesai')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="inp-label">Alamat</label>
                    <input type="text" value="{{ $user->alamat ?? '-' }}" readonly class="inp bg-gray-50">
                </div>

                {{-- Keterangan --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-3 mt-2">
                        <div class="section-bar bg-blue-400"></div>
                        <h3 class="font-semibold text-gray-700 text-sm">Keterangan</h3>
                    </div>
                    <label class="inp-label">Apa yang akan dikerjakan saat lembur? <span
                            class="req">*</span></label>
                    <textarea wire:model="keterangan" rows="5"
                        placeholder="Tuliskan rencana pekerjaan yang akan dilakukan saat lembur..."
                        class="inp resize-none leading-relaxed @error('keterangan') border-red-500 @enderror"
                        style="min-height:120px;"></textarea>
                    @error('keterangan')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition active:scale-95 flex items-center gap-2 shadow-sm"
                    style="background:#3C8B5E" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed">
                    <span wire:loading.remove wire:target="simpanPengajuan">
                        <i class="fa-solid fa-paper-plane"></i> Ajukan Lembur
                    </span>
                    <span wire:loading wire:target="simpanPengajuan">
                        <i class="fa-solid fa-spinner fa-spin"></i> Mengirim...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
