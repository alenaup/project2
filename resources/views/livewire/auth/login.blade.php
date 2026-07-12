{{-- bagian dari form login dengan livewire --}}
{{-- bagian ini akan diisi oleh livewire --}}
<div>
    {{-- pada bagian ini jika tombol dengan type submit di tekan maka akan menjalankan function login --}}
    <form wire:submit.prevent="login">
        <div class="min-h-70px">
            {{-- untuk menampilkan pesan error --}}
            {{-- pada bagian ini terdapat syntax untuk mengecek apakah ada pesan error atau tidak jika ada maka akan menampilkan pesan error --}}
            <div x-data="{ showError: true }">

                @if ($errors->any())
                    <div x-show="showError" x-transition
                        class="rounded-xl mb-4 border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400 backdrop-blur">
                        @foreach ($errors->all() as $error)
                            <div x-text="'{{ $error }}'"></div>
                        @endforeach
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

            <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="Enter password"
                class="w-full rounded-lg bg-white/80 py-2 pl-10 pr-10 text-black outline-none" />

            <!-- tombol eye -->
            <button type="button" @click="show = !show" name="eye"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-emerald-600">
                <!-- Eye Slash Icon (hidden when show is false) -->
                <svg x-cloak x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
                <!-- Eye Icon (hidden when show is true) -->
                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </button>
        </div>
        <button type="submit" wire:loading.attr="disabled" name="login"
            class="w-full mb-3 bg-emerald-600 text-white hover:text-white transition py-1 md:py-2 rounded-lg hover:bg-emerald-700 disabled:opacity-70 disabled:cursor-not-allowed">
            <span wire:loading.remove>Masuk</span>
            <span wire:loading.flex class="items-center justify-center w-full">Memproses
                <svg class="animate-spin w-5 h-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>
    </form>
</div>
