<a wire:click="logout" wire:loading.attr="disabled" class="block px-4 py-2 text-sm text-red-500 pointer hover:bg-red-50">
    <span wire:loading.remove>
        Logout
    </span>

    <span wire:loading>
        Logging out...
    </span>
</a>
