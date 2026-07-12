<div class="animate-bitem bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-start sm:items-center justify-between gap-4 w-full shadow-sm mb-6 relative">
    <div x-data="{
            showRoleModal: false,
            modalType: 'download', // 'download' atau 'import'
            role: @entangle('selectedRole'),
            departemen: @entangle('selectedDepartemen'),
            needsRoleSelection: {{ $needsRoleSelection ? 'true' : 'false' }},
            
            openDownloadModal() {
                this.modalType = 'download';
                this.showRoleModal = true;
            },
            
            openImportModal() {
                // Pastikan file sudah dipilih sebelum buka modal impor
                const fileInput = document.querySelector('input[type=file][wire\\:model=\'fileExcel\']');
                if (!fileInput || !fileInput.files.length) {
                    alert('Silakan pilih file Excel/CSV terlebih dahulu sebelum mengimpor.');
                    return;
                }
                
                this.modalType = 'import';
                this.showRoleModal = true;
            },
            
            proceedAction() {
                if (!this.role) {
                    alert('Silakan pilih role terlebih dahulu.');
                    return;
                }
                if (this.role === 'kepala_departemen' && !this.departemen) {
                    alert('Silakan pilih departemen terlebih dahulu.');
                    return;
                }
                
                this.showRoleModal = false;
                
                if (this.modalType === 'download') {
                    $wire.downloadTemplate(this.role, this.departemen);
                } else if (this.modalType === 'import') {
                    $wire.import();
                }
            }
        }" class="flex flex-col sm:flex-row sm:items-start sm:items-center justify-between gap-4 w-full">
        
        <div class="flex flex-col gap-1 w-full sm:w-auto">
            <span class="text-sm font-semibold text-gray-700">Impor Data via Excel / CSV</span>
            <p class="text-xs text-gray-500">Unduh berkas template terlebih dahulu untuk menginput data secara masal.</p>

            @if($templatePath)
                <div class="flex flex-col mt-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                           @click="needsRoleSelection ? openDownloadModal() : $wire.downloadTemplate()"
                           class="text-xs font-semibold flex items-center gap-1.5 transition whitespace-nowrap text-green-700 hover:text-green-800"
                           wire:loading.attr="disabled" wire:target="downloadTemplate">
                            <i class="fas fa-download text-green-600" wire:loading.remove wire:target="downloadTemplate"></i>
                            <i class="fas fa-spinner fa-spin text-green-600" wire:loading wire:target="downloadTemplate"></i>
                            <span wire:loading.remove wire:target="downloadTemplate">Unduh Template Excel (.xlsx)</span>
                            <span wire:loading wire:target="downloadTemplate">Mengunduh...</span>
                        </button>
                    </div>

                    <!-- Modal Alpine untuk Pemilihan Role -->
                    <div x-show="showRoleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center transition-opacity" style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
                        <div @click.away="showRoleModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all p-6 text-left">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800" x-text="modalType === 'download' ? 'Pilih Role untuk Template' : 'Konfirmasi Role Impor'"></h3>
                                <button @click="showRoleModal = false" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <p class="text-xs text-gray-500 mb-4" x-show="modalType === 'download'">
                                Template yang diunduh akan disesuaikan untuk Role yang Anda pilih di bawah ini (contoh: otomatis mengisi kolom Departemen).
                            </p>
                            <p class="text-xs text-gray-500 mb-4" x-show="modalType === 'import'" x-cloak>
                                Pastikan Role yang Anda pilih sesuai dengan isi file Excel yang akan diunggah ke sistem.
                            </p>

                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Role Pengguna <span class="text-red-500">*</span></label>
                                <select x-model="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin_outsourcing">Admin Vendor</option>
                                    <option value="hr">HR</option>
                                    <option value="kepala_departemen">Kepala Departemen</option>
                                </select>
                            </div>

                            <div x-show="role === 'kepala_departemen'" x-cloak class="mb-5">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Departemen <span class="text-red-500">*</span></label>
                                <select x-model="departemen" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach($departemensList as $dept)
                                        <option value="{{ $dept['id_departemen'] }}">{{ $dept['nama_departemen'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end gap-2 mt-2">
                                <button type="button" @click="showRoleModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Batal</button>
                                <button type="button" @click="proceedAction()" class="px-4 py-2 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition shadow-sm flex items-center gap-1.5">
                                    <i class="fas" :class="modalType === 'download' ? 'fa-download' : 'fa-file-import'"></i> 
                                    <span x-text="modalType === 'download' ? 'Lanjutkan Unduh' : 'Lanjutkan Impor'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-2 w-full sm:w-auto mt-4 sm:mt-0">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="file" wire:model="fileExcel" accept=".xlsx, .xls, .csv"
                        class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded-lg p-1 bg-white cursor-pointer transition">
                </div>

                <button type="button"
                    @click="needsRoleSelection ? openImportModal() : $wire.import()"
                    wire:loading.attr="disabled" wire:target="import, fileExcel"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-semibold whitespace-nowrap shadow-sm transition flex items-center justify-center gap-1.5">
                    <span wire:loading.remove wire:target="import, fileExcel">
                        <i class="fas fa-file-import"></i> {{ $buttonLabel }}
                    </span>
                    <span wire:loading.flex wire:target="import" class="items-center gap-1.5">
                        <i class="fas fa-circle-notch fa-spin"></i> Memproses...
                    </span>
                    <span wire:loading.flex wire:target="fileExcel" class="items-center gap-1.5">
                        <i class="fas fa-spinner fa-spin"></i> Mengunggah...
                    </span>
                </button>
            </div>
            
            <!-- Validation errors from Livewire component -->
            @error('selectedRole')
                <span class="text-red-500 text-[11px] block flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
            @error('selectedDepartemen')
                <span class="text-red-500 text-[11px] block flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
            @error('fileExcel')
                <span class="text-red-500 text-[11px] block flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
