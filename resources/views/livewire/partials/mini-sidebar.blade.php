<div
    x-cloak
    x-data="{ open: true }"
    class="relative lg:sticky lg:top-[60px] lg:h-[calc(100vh-60px)] w-full lg:w-[280px]
           z-20 shadow-md overflow-y-auto
           flex flex-col items-center gap-3 p-4">
    @foreach ($menuItems as $index => $item)
        @php
            $itemRoute = $item['route'] ?? null;
            $isActive = $itemRoute && request()->routeIs($itemRoute)
                        || collect($item['children'] ?? [])->pluck('route')->contains(fn($r) => request()->routeIs($r));
        @endphp
        <div :class="open ? 'h-auto w-full' : 'h-0 hidden transition-all duration-300 lg:flex lg:flex-col lg:h-auto lg:w-full'">
            @if ($itemRoute)
                <div
                    class="flex justify-start w-full text-sm p-2 rounded-lg cursor-pointer hover:bg-gray-200 font-bold dark:hover:bg-zinc-800 select-none"
                    :class="activePage === '{{ $item['route'] }}' ? 'bg-gray-200 dark:bg-zinc-800' : ''"
                    @click="
                        $dispatch('set-page', '{{ $item['route'] }}');
                        Livewire.dispatch('reset-form');
                    "
                >
                    <i class="{{ $item['icon'] ?? '' }}"></i>
                    <span class="ml-2">{{ $item['label'] ?? 'Untitled' }}</span>
                </div>
            @endif
        </div>
    @endforeach

    <!-- Toggle Sidebar for mobile -->
    <div
        x-show="$store.sidebar.screen < 1024"
        @click="open = !open"
        class="w-full flex justify-center items-center rounded-lg dark:hover:bg-zinc-800 hover:bg-gray-200 p-2 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </div>
</div>
