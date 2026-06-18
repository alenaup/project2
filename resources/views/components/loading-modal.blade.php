@props([
    'target' => null,
    'message' => 'Sedang memproses...',
    'keepAlive' => false, // Set 'true' jika ingin modal ditahan sampai render halaman baru selesai (mencegah kedipan putih)
])

<!--
  Komponen Loading Modal
  Dapat dipicu otomatis oleh Livewire (wire:loading)
  ATAU manual oleh AlpineJS ($dispatch('show-loading'))
-->
<div {{-- Atribut Livewire: Target aksi yang memicu loading --}} @if ($target) wire:target="{{ $target }}" @endif
    {{-- Livewire akan mengubah display menjadi 'flex' saat loading aktif --}} wire:loading.flex {{-- Atribut AlpineJS: State lokal untuk menampung visibilitas dan pesan dinamis --}} x-data="{ show: false, msg: '{{ $message }}', isRedirecting: false }" {{-- Event listener untuk membuka/menutup modal secara manual dari Alpine/JavaScript --}}
    x-on:show-loading.window="show = true; msg = $event.detail.message || msg" x-on:hide-loading.window="show = false"
    {{-- Event khusus jika ada request Livewire yang menghasilkan redirect --}}
    x-on:livewire-redirecting.window="if('{{ $keepAlive }}' == '1' || '{{ $keepAlive }}' == 'true') isRedirecting = true"
    {{-- Mengubah display menjadi flex jika dipicu manual oleh Alpine ATAU sedang dalam status redirect --}} x-bind:style="(show || isRedirecting) ? 'display: flex !important;' : ''"
    {{-- Styling CSS bawaan --}}
    class="fixed inset-0 z-[9999] items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity duration-300"
    style="display: none;">
    <!-- Modal Box -->
    <div class="bg-white px-8 py-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4 animate-bounce-in">
        <!-- SVG Spinner Element -->
        <svg class="animate-spin w-12 h-12 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-80" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>

        <!-- Loading Message -->
        <p class="text-base font-semibold text-gray-800 animate-pulse" x-text="msg"></p>
    </div>
</div>

{{-- Global Hook diletakkan di luar agar hanya dieksekusi sekali meski dipanggil berkali-kali --}}
@once
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('request', ({
                    respond,
                    succeed
                }) => {
                    succeed(({
                        status,
                        json
                    }) => {
                        // Cek jika response Livewire mengandung directive redirect
                        if (json && json.effects && json.effects.redirect) {
                            window.dispatchEvent(new CustomEvent('livewire-redirecting'));
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
