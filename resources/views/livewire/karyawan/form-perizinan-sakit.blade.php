<div>
    <!-- Form Pengajuan -->
    <div id="form-pengajuan-container" class="bg-white rounded-xl shadow mb-6 border-l-4 border-emerald-500 overflow-hidden relative">
        <!-- Spinner Overlay while uploading/saving -->
        <div wire:loading.flex wire:target="submitForm, file_surat" class="absolute inset-0 z-50 bg-white/70 backdrop-blur-sm flex items-center justify-center">
            <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin text-3xl text-emerald-600"></i>
                <span class="text-sm font-semibold text-gray-700">Memproses...</span>
            </div>
        </div>

        <div class="px-6 pt-5 pb-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-file-medical text-emerald-600"></i>
                Form Pengajuan Izin Sakit
            </h2>
        </div>

        <form wire:submit.prevent="submitForm">
            <div class="p-6 space-y-4">

                @if(session('success'))
                    <div class="bg-emerald-50 text-emerald-600 px-4 py-3 rounded-lg text-sm font-semibold flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-700 block mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.live="tanggal_mulai" min="{{ date('Y-m-d', strtotime('-7 days')) }}"
                            class="w-full border @error('tanggal_mulai') border-red-500 @else border-gray-200 @enderror rounded-lg p-3">
                        @error('tanggal_mulai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700 block mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.live="tanggal_selesai" min="{{ $tanggal_mulai }}"
                            class="w-full border @error('tanggal_selesai') border-red-500 @else border-gray-200 @enderror rounded-lg p-3">
                        @error('tanggal_selesai') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-1">
                        Keterangan / Diagnosis <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="keterangan" rows="3"
                        class="w-full border @error('keterangan') border-red-500 @else border-gray-200 @enderror rounded-lg p-3"></textarea>
                    @error('keterangan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-2">
                        Upload Surat Sakit <span class="text-red-500">( oppsional )</span>
                    </label>

                    @if($file_surat)
                        <div class="flex items-center justify-between border border-gray-200 rounded-lg p-3 bg-gray-50 mb-2">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-circle-check text-2xl text-emerald-500"></i>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-700 truncate max-w-[200px] md:max-w-xs">{{ $file_surat->getClientOriginalName() }}</p>
                                    <p class="text-xs text-gray-500">File siap diunggah</p>
                                </div>
                            </div>
                            <button type="button" wire:click="$set('file_surat', null)" class="text-red-500 hover:text-red-700 text-sm p-2">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    @else
                        <label class="block border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:bg-gray-50 transition w-full">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-emerald-500"></i>
                                <p class="text-sm font-medium text-gray-600">Klik untuk upload surat (JPG, PNG, PDF)</p>
                                <p class="text-xs text-gray-400">Maksimal 5MB</p>
                            </div>
                            <input type="file" wire:model.live="file_surat" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                        </label>
                    @endif

                    @error('file_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                
                @if($perluAbsenKeluar)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-4">
                    <div class="flex items-start gap-3 mb-3">
                        <i class="fa-solid fa-info-circle text-blue-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Absen Keluar Otomatis</p>
                            <p class="text-xs text-blue-600">Anda saat ini sedang berstatus masuk kerja. Pengajuan izin akan otomatis melakukan absen keluar. Silakan ambil lokasi Anda saat ini.</p>
                        </div>
                    </div>
                    
                    <div class="relative z-10 space-y-2">
                        <div class="flex items-center gap-2 mb-2">
                            <button onclick="ambilLokasiForm()" type="button"
                                class="bg-blue-100 text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-200 transition flex items-center justify-center gap-2">
                                <i class="fas fa-location-arrow"></i>
                                Ambil Lokasi
                            </button>
                            <span id="infoLokasiForm" class="text-xs text-gray-500">Lokasi belum diambil</span>
                        </div>
                        
                        <!-- Input hidden untuk bind model -->
                        <input type="hidden" wire:model="latitude">
                        <input type="hidden" wire:model="longitude">
                        @error('latitude') <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

            </div>

            <!-- Divider -->
            <div class="border-t border-gray-100 mx-6"></div>

            <div class="p-6 pt-4 flex gap-3">
                <button type="submit"
                    class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white py-3 px-8 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim Pengajuan
                </button>
                <button type="button" wire:click="resetForm"
                    class="flex-1 md:flex-none bg-gray-100 hover:bg-gray-200 active:scale-95 text-gray-600 py-3 px-8 rounded-lg font-semibold transition-all duration-200">
                    Reset
                </button>
            </div>
        </form>
    </div>

    @script
    <script>
        $wire.on('perizinan-dikirim', () => {
            setTimeout(() => {
                const container = document.getElementById('form-pengajuan-container');
                if (container) {
                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 50);
        });

        $wire.on('form-reset', () => {
            const infoText = document.getElementById('infoLokasiForm');
            if (infoText) {
                infoText.innerHTML = 'Lokasi belum diambil';
                infoText.className = 'text-xs text-gray-500';
            }
        });
        
        window.ambilLokasiForm = function() {
            const infoText = document.getElementById('infoLokasiForm');
            
            if (!navigator.geolocation) {
                infoText.innerHTML = '<span class="text-red-500">Geolocation tidak didukung oleh browser Anda.</span>';
                return;
            }

            infoText.innerHTML = '<span class="text-blue-500"><i class="fas fa-spinner fa-spin mr-1"></i> Mengambil lokasi...</span>';
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = document.coords ? position.coords.longitude : position.coords.longitude; // typo fix
                    
                    @this.set('latitude', lat);
                    @this.set('longitude', lng);
                    
                    infoText.innerHTML = `<span class="text-emerald-600 font-medium"><i class="fas fa-check-circle mr-1"></i> Lokasi berhasil didapatkan.</span>`;
                },
                (error) => {
                    let errMsg = 'Gagal mendapatkan lokasi.';
                    if (error.code === error.PERMISSION_DENIED) errMsg = 'Izin akses lokasi ditolak.';
                    infoText.innerHTML = `<span class="text-red-500"><i class="fas fa-times-circle mr-1"></i> ${errMsg}</span>`;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }
    </script>
    @endscript
</div>
