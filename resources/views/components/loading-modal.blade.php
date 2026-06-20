@props([
    'target' => null,
    'message' => 'Sedang memproses...',
    'keepAlive' => false,
])

{{--
  Komponen Loading Modal (Body-Level Safe)
  Dipicu via window event: show-loading / hide-loading
  Tetap aktif saat Livewire redirect (keepAlive=true)
--}}
<div
    x-data="{
        show: false,
        msg: '{{ $message }}',
        isRedirecting: false
    }"
    x-on:show-loading.window="show = true; msg = $event.detail?.message || msg"
    x-on:hide-loading.window="if (!isRedirecting) { show = false }"
    x-on:livewire-redirecting.window="{{ $keepAlive ? 'isRedirecting = true' : '' }}"
    x-show="show || isRedirecting"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    style="display: none;">

    <!-- Modal Box -->
    <div class="bg-white px-10 py-8 rounded-2xl shadow-2xl flex flex-col items-center gap-5">
        <!-- Spinner -->
        <svg class="animate-spin w-14 h-14 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-80" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>

        <!-- Pesan -->
        <p class="text-base font-semibold text-gray-800 animate-pulse" x-text="msg"></p>
    </div>
</div>

{{-- Script global — hanya dieksekusi sekali --}}
@once
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('request', ({ succeed, fail }) => {
                    succeed(({ json }) => {
                        if (json && json.effects && json.effects.redirect) {
                            window.dispatchEvent(new CustomEvent('livewire-redirecting'));
                        } else {
                            // Sembunyikan modal jika sukses dan tidak ada redirect
                            window.dispatchEvent(new CustomEvent('hide-loading'));
                        }
                    });

                    fail(() => {
                        // Sembunyikan modal jika request gagal
                        window.dispatchEvent(new CustomEvent('hide-loading'));
                    });
                });
            });
        </script>
    @endpush
@endonce
