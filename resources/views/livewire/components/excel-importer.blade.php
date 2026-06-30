<div class="animate-bitem bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full shadow-sm mb-6">
    <div class="flex flex-col gap-1">
        <span class="text-sm font-semibold text-gray-700">Impor Data via Excel / CSV</span>
        <p class="text-xs text-gray-500">Unduh berkas template terlebih dahulu untuk menginput data secara masal.</p>

        @if($templatePath)
            <a href="{{ url($templatePath) }}" download class="text-xs text-green-700 hover:text-green-800 font-semibold flex items-center gap-1.5 mt-1.5 transition">
                <i class="fas fa-download text-green-600"></i> Unduh Template Excel (.xlsx)
            </a>
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
