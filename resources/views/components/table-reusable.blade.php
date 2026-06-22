<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto w-full">
    <table class="w-full text-sm border-separate border-spacing-y-2 whitespace-nowrap min-w-max">

        {{-- HEADER --}}
        <thead class="bg-green-100 text-gray-600 uppercase text-xs tracking-wide">
            <tr class="shadow-sm border border-gray-100">
                @foreach ($headers as $header)
                    <th class="px-6 py-4 text-left">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>

        {{-- BODY --}}
        <tbody class="divide-y">
            {{ $slot }}
        </tbody>

    </table>
</div>
