@props(['title', 'value', 'subtext', 'icon', 'borderColor', 'textColor', 'hoverColor'])

<div class="animate-item bg-white rounded-xl p-2 shadow-sm border-2 {{ $borderColor }} flex items-center gap-4 relative transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl">

    <div
        class="w-14 h-16 bg-gray-100 rounded-xl shadow-inner border border-gray-200 flex items-center justify-center text-gray-700 text-2xl shrink-0">
        <i class="{{ $icon }}"></i>
    </div>

    <div class="flex-1 text-center pr-6">
        <p class="text-xs md:text-sm text-gray-600 font-medium"> {{ $title }}</p>
        <h3 class="text-3xl font-extrabold {{ $textColor }} my-1">{{ $value }}</h3>
        <p class="text-[10px] md:text-xs text-gray-400">{{ $subtext }}</p>
    </div>
</div>
