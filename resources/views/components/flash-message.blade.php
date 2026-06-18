@props([
    'on' => null,            // Nama event dispatch Livewire untuk memunculkan flash manual
    'sessionKey' => null,    // Nama key session()->has('...')
    'type' => 'success',     // success, error, warning, info
    'duration' => 3000,      // Waktu tampil (ms), ubah ke 0 jika tidak ingin hilang otomatis
    'icon' => null           // Slot opsional untuk custom SVG Icon
])

<div 
    x-data="{ 
        show: {{ ($sessionKey && session()->has($sessionKey)) ? 'true' : 'false' }}, 
        message: '{{ ($sessionKey && session()->has($sessionKey)) ? addslashes(session($sessionKey)) : '' }}',
        
        init() {
            if (this.show) {
                this.setTimer();
            }
        },
        
        setTimer() {
            if ({{ $duration }} > 0) {
                setTimeout(() => { this.show = false }, {{ $duration }});
            }
        }
    }"
    
    {{-- Jika dipicu menggunakan event Livewire --}}
    @if($on)
        x-on:{{ $on }}.window="
            show = true; 
            message = $event.detail.message || $event.detail[0] || 'Tindakan berhasil';
            setTimer();
        "
    @endif

    {{-- Animasi transisi menggunakan Alpine --}}
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
    
    class="fixed top-6 right-6 z-[9999] flex items-center justify-between gap-3 border rounded-xl px-4 py-4 min-w-[300px] max-w-md shadow-2xl 
        {{ $type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : '' }}
        {{ $type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : '' }}
        {{ $type === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-700' : '' }}
        {{ $type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-700' : '' }}"
    style="display: none; z-index: 99999;"
>
    <div class="flex items-center gap-2">
        <!-- Render Custom Icon -->
        @if($icon)
            {{ $icon }}
        <!-- Fallback Icon Bawaan -->
        @else
            @if($type === 'success')
                <!-- Icon Success Check -->
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @elseif($type === 'error')
                <!-- Icon Error Exclamation -->
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @elseif($type === 'warning')
                <!-- Icon Warning Triangle -->
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            @else
                <!-- Icon Info -->
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @endif
        @endif

        <!-- Pesan Alert -->
        <span x-text="message" class="font-medium text-sm leading-tight"></span>
    </div>

    <!-- Tombol Tutup (Silang) -->
    <button @click="show = false" type="button" class="flex-shrink-0 opacity-60 hover:opacity-100 transition focus:outline-none">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>
