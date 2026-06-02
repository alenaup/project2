{{-- bagian dari form login dengan livewire --}}
{{-- bagian ini akan diisi oleh livewire --}}
<form wire:submit.prevent="login">
    {{-- csrf berfungsi untuk melindungi form dari serangan csrf --}}
    @csrf

    {{-- memberikan pesan --}}
    @if (session('error'))
        <div class="mb-4 text-center text-red-500 font-semibold bg-red-100 p-2 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @error('email')
        <div class="mt-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400 backdrop-blur">
            {{ $message }}
        </div>
    @enderror

    @error('password')
        <div class="mt-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400 backdrop-blur">
            {{ $message }}
        </div>
    @enderror

    @error('login')
        <div class="mt-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400 backdrop-blur">
            {{ $message }}
        </div>
    @enderror

    <div class="mb-2 md:mb-4 mt-2">
        <input type="email" placeholder="Enter Email" wire:model.lazy="email"
            class="w-full pl-10 pr-4 py-2 mb-3 bg-white/80 text-black placeholder-black/70 rounded-lg
                                        outline-none transition
                                        focus:placeholder-black/90
                                        focus:ring-2 focus:ring-emerald-500
                                        focus:shadow-[0_0_12px_rgba(255,255,255,0.25)] @error('email') border-red-500 @enderror" />
    </div>

    <div x-data="{ show: false }" class="relative mb-6">

        <input :type="show ? 'text' : 'password'" wire:model.defer="password" placeholder="Enter password"
            class="w-full rounded-lg bg-white/80 py-2 pl-10 pr-10 text-black outline-none" />

        <!-- tombol eye -->
        <button type="button" @click="show = !show"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-emerald-600">

            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'">
            </i>

        </button>
    </div>
    <button type="submit" wire:loading.attr="disabled"
        class="w-full mb-3 bg-emerald-600 text-white/70 hover:text-white transition py-1 md:py-2 rounded-lg hover:bg-emerald-700 disabled:opacity-70 disabled:cursor-not-allowed">
        <span wire:loading.remove>Masuk</span>
        <span wire:loading>Memproses <i class="fas fa-spinner fa-spin"></i></span>
    </button>
    <button type="button"
        class="w-full bg-transparent border border-white/50 text-white/70 hover:text-white transition py-1 md:py-2 rounded-lg hover:bg-white/10">
        Lupa Password
    </button>

</form>
