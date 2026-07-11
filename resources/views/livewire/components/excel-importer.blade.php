<div class="animate-bitem bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full shadow-sm mb-6">
    <div class="flex flex-col gap-1">
        <span class="text-sm font-semibold text-gray-700">Impor Data via Excel / CSV</span>
        <p class="text-xs text-gray-500">Unduh berkas template terlebih dahulu untuk menginput data secara masal.</p>

        @if($templatePath)
            <div x-data="{
                isDownloading: false,
                startDownload(e) {
                    if (this.isDownloading) {
                        e.preventDefault();
                        return;
                    }
                    this.isDownloading = true;
                    let token = new Date().getTime().toString();
                    let url = new URL(e.currentTarget.href, window.location.origin);
                    url.searchParams.set('download_token', token);
                    
                    window.dispatchEvent(new CustomEvent('show-loading', { detail: { message: 'Mengunduh template...' } }));
                    
                    window.location.href = url.toString();
                    
                    let checkCookie = setInterval(() => {
                        const match = document.cookie.match(new RegExp('(^| )download_token=([^;]+)'));
                        if (match && match[2] == token) {
                            clearInterval(checkCookie);
                            this.isDownloading = false;
                            document.cookie = 'download_token=; Max-Age=-99999999; path=/;';
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                        }
                    }, 500);
                    e.preventDefault();
                }
            }">
                <a href="{{ url($templatePath) }}" 
                   @click="startDownload($event)"
                   class="text-xs font-semibold flex items-center gap-1.5 mt-1.5 transition"
                   :class="isDownloading ? 'text-gray-400 cursor-not-allowed pointer-events-none' : 'text-green-700 hover:text-green-800'">
                    <i class="fas" :class="isDownloading ? 'fa-spinner fa-spin' : 'fa-download text-green-600'"></i> 
                    <span x-text="isDownloading ? 'Mengunduh...' : 'Unduh Template Excel (.xlsx)'"></span>
                </a>
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-2 w-full sm:w-auto">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="file" wire:model="fileExcel" accept=".xlsx, .xls, .csv"
                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded-lg p-1 bg-white cursor-pointer transition">
            </div>

            <button wire:click="import" wire:loading.attr="disabled"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-semibold whitespace-nowrap shadow-sm transition flex items-center justify-center gap-1.5">
                <span wire:loading.remove wire:target="import">
                    <i class="fas fa-file-import"></i> {{ $buttonLabel }}
                </span>
                <span wire:loading.flex wire:target="import" class="items-center gap-1.5">
                    <i class="fas fa-circle-notch fa-spin"></i> Memproses...
                </span>
            </button>
        </div>
        @error('fileExcel')
            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
        @enderror
    </div>
</div>
