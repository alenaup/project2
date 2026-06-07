{{-- bagian dari form login dengan livewire --}}
{{-- bagian ini akan diisi oleh livewire --}}
<div>
    {{-- pada bagian ini jika tombol dengan type submit di tekan maka akan menjalankan function login --}}
    <form wire:submit.prevent="login">
        <div class="min-h-70px">
            {{-- untuk menampilkan pesan error --}}
            {{-- pada bagian ini terdapat syntax untuk mengecek apakah ada pesan error atau tidak jika ada maka akan menampilkan pesan error --}}
            <div x-data="{ showError: true }" x-init="$watch('$wire.errors', () => showError = true)">

                @if ($errors->any())
                    <div x-show="showError" x-transition
                        class="rounded-xl mb-4 border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400 backdrop-blur">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach

                        <button type="button" @click="showError = false"
                            class="mt-2 text-xs underline opacity-70 hover:opacity-100">
                            Tutup
                        </button>
                    </div>
                @endif

            </div>
        </div>
        <div class="mb-2 md:mb-4">
            <input type="email" placeholder="Enter Email" wire:model.lazy="email"
                class="w-full pl-10 pr-4 py-2 mb-3 bg-white/80 text-black placeholder-black/70 rounded-lg
            outline-none transition
            focus:placeholder-black/90
            focus:ring-2 focus:ring-emerald-500
            focus:shadow-[0_0_12px_rgba(255,255,255,0.25)]" />
        </div>

        <div x-data="{ show: false }" class="relative mb-6">

            <input :type="show ? 'text' : 'password'" wire:model.defer="password" placeholder="Enter password"
                class="w-full rounded-lg bg-white/80 py-2 pl-10 pr-10 text-black outline-none" />

            <!-- tombol eye -->
            <button type="button" @click="show = !show" name="eye"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-emerald-600">
                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'">
                </i>
            </button>
        </div>
        <button type="submit" wire:loading.attr="disabled" name="login"
            class="w-full mb-3 bg-emerald-600 text-white hover:text-white transition py-1 md:py-2 rounded-lg hover:bg-emerald-700 disabled:opacity-70 disabled:cursor-not-allowed">
            <span wire:loading.remove>Masuk</span>
            <span wire:loading>Memproses <i class="fas fa-spinner fa-spin"></i></span>
        </button>
        <button type="button" name="forgot-password"
            class="w-full bg-transparent border border-white/50 text-white hover:text-white transition py-1 md:py-2 rounded-lg hover:bg-white/10">
            Lupa Password
        </button>

    </form>
</div>
