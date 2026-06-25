<div class="w-full space-y-6 pb-10" x-data="{ openPasswordModal: false, openFotoModal: false }">
    <!-- Layout Grid Side-by-Side -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kiri: Profil & Aksi (1 Kolom) -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            
            <!-- Card Profil Singkat -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
                
                @php
                    $profilPath = 'profiles/profil_' . Auth::id() . '.jpg';
                    $hasFoto = Storage::disk('public')->exists($profilPath);
                    $fotoUrl = $hasFoto ? asset('storage/' . $profilPath) . '?v=' . Storage::disk('public')->lastModified($profilPath) : null;
                @endphp

                <div class="relative w-32 h-32 rounded-full mb-4 group">
                    <div class="w-full h-full rounded-full bg-gray-100 overflow-hidden border-4 border-gray-50 shadow-inner">
                        <img src="{{ $fotoUrl ?? '/images/profile.jpg' }}" alt="Profile" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($nama_lengkap) }}&background=10b981&color=fff&size=128'" class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Tombol Ubah Foto -->
                    <button type="button" @click="openFotoModal = true"
                        class="absolute bottom-0 right-0 bg-emerald-600 text-white w-9 h-9 rounded-full flex items-center justify-center border-2 border-white shadow-md hover:bg-emerald-700 transition-colors opacity-80 group-hover:opacity-100">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </button>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900">{{ $nama_lengkap }}</h3>
                <p class="text-emerald-600 font-semibold text-xs mt-1 uppercase tracking-wider bg-emerald-50 px-3 py-1 rounded-full">
                    {{ str_replace('_', ' ', Auth::user()->role->value) }}
                </p>

                <div class="w-full h-px bg-gray-100 my-5"></div>

                <div class="w-full space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500"><i class="fa-solid fa-id-badge w-5 text-left"></i> NIP</span>
                        <span class="font-semibold text-gray-800">{{ $nip ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500"><i class="fa-solid fa-building w-5 text-left"></i> Dept.</span>
                        <span class="font-semibold text-gray-800">{{ Auth::user()->departemen->nama_departemen ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card Keamanan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-sm font-bold text-gray-900 mb-2 uppercase tracking-wide">Keamanan Akun</h4>
                <p class="text-xs text-gray-500 mb-4 leading-relaxed">Ganti password secara berkala untuk menjaga keamanan data Anda.</p>
                
                <button type="button" @click="openPasswordModal = true"
                    class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-gray-900 px-4 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-lock"></i> Ubah Password
                </button>
            </div>
            
        </div>

        <!-- Kanan: Form Biodata (2 Kolom) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 h-full">
                
                <div class="mb-6 pb-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Pengaturan Profil</h3>
                    <p class="text-sm text-gray-500 mt-1">Data ini digunakan untuk keperluan administrasi internal perusahaan.</p>
                </div>

                @if (session('success_profil'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                        {{ session('success_profil') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateProfil" class="space-y-6">
                    <div class="flex flex-col gap-5">
                        
                        <!-- Nama Lengkap (Disabled) -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                            <input type="text" wire:model="nama_lengkap" disabled
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-500 cursor-not-allowed shadow-sm">
                        </div>

                        <!-- Email (Disabled) -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-gray-700">Alamat Email</label>
                            <input type="email" wire:model="email" disabled
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-500 cursor-not-allowed">
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-gray-700">Nomor HP / WhatsApp</label>
                            <input type="text" wire:model="nomor_tlp" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm text-gray-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors shadow-sm @error('nomor_tlp') border-red-500 @enderror">
                            @error('nomor_tlp') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- NIP (Disabled) -->
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-gray-700">NIP Pegawai</label>
                            <input type="text" wire:model="nip" disabled
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                            class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-md active:scale-95 flex items-center gap-2">
                            <span wire:loading.remove wire:target="updateProfil">Simpan Perubahan</span>
                            <span wire:loading wire:target="updateProfil" class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Alpine.js untuk Ubah Password -->
    <div x-show="openPasswordModal" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/60" @click="openPasswordModal = false"></div>
        
        <!-- Modal Content -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Ubah Password</h3>
                <button @click="openPasswordModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Body Modal -->
            <div class="p-6 bg-gray-50/50">
                @if (session('success_password'))
                    <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3 text-sm font-semibold">
                        <i class="fa-solid fa-circle-check mt-0.5 text-lg"></i>
                        <div>{{ session('success_password') }}</div>
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    
                    <!-- Password Lama -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <label class="block text-sm font-semibold text-gray-700">Password Saat Ini</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" wire:model="password_lama" 
                                class="w-full px-4 pr-10 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors shadow-sm @error('password_lama') border-red-500 @enderror">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password_lama') <span class="text-xs text-red-500 font-medium block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password Baru -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <label class="block text-sm font-semibold text-gray-700">Password Baru</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" wire:model="password_baru" 
                                class="w-full px-4 pr-10 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors shadow-sm @error('password_baru') border-red-500 @enderror">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password_baru') <span class="text-xs text-red-500 font-medium block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="space-y-1.5" x-data="{ show: false }">
                        <label class="block text-sm font-semibold text-gray-700">Ulangi Password Baru</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" wire:model="password_baru_confirmation" 
                                class="w-full px-4 pr-10 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors shadow-sm">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-5">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-md active:scale-95 flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="updatePassword">Perbarui Password</span>
                            <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Alpine.js untuk Ubah Foto -->
    <div x-show="openFotoModal" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="absolute inset-0 bg-gray-900/60" @click="openFotoModal = false"></div>
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
             
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Ubah Foto Profil</h3>
                <button @click="openFotoModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 bg-gray-50/50 text-center">
                @if (session('success_foto'))
                    <div class="mb-4 bg-emerald-50 text-emerald-700 px-3 py-2 rounded-lg text-sm font-semibold">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success_foto') }}
                    </div>
                @endif

                <form wire:submit.prevent="updateFoto">
                    
                    <div class="mb-5 flex justify-center">
                        @if ($foto_baru)
                            <img src="{{ $foto_baru->temporaryUrl() }}" class="w-32 h-32 rounded-full object-cover border-4 border-emerald-100 shadow-md">
                        @else
                            <div class="w-32 h-32 rounded-full border-4 border-dashed border-gray-300 flex items-center justify-center text-gray-400 bg-gray-50">
                                <i class="fa-solid fa-image text-3xl"></i>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <input type="file" wire:model="foto_baru" accept="image/*" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-emerald-50 file:text-emerald-700
                            hover:file:bg-emerald-100 cursor-pointer
                        "/>
                        <div wire:loading wire:target="foto_baru" class="text-xs text-emerald-600 mt-2 font-medium">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Menyiapkan gambar...
                        </div>
                        @error('foto_baru') <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-md active:scale-95 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="updateFoto">Simpan Foto Baru</span>
                        <span wire:loading wire:target="updateFoto" class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
