<a wire:click="logout"
    wire:loading.attr="disabled"
    @click="window.dispatchEvent(new CustomEvent('show-loading', { detail: { message: 'Sedang keluar dari sistem...' } }))"
    class="block px-4 py-2 text-sm text-red-500 cursor-pointer hover:bg-red-50">
    <span wire:loading.remove wire:target="logout">
        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9m9 0-3-3m3 3-3 3" />
        </svg> Logout
    </span>

    <span wire:loading wire:target="logout">
        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9m9 0-3-3m3 3-3 3" />
        </svg> Logging out...
    </span>
</a>
