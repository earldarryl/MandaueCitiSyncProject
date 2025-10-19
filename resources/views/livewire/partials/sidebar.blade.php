<aside
    x-cloak
    x-data="{
        isDesktop: window.matchMedia('(min-width: 1024px)').matches
    }"
    @resize.window="isDesktop = window.matchMedia('(min-width: 1024px)').matches"
    :class="{
        'w-[320px] sticky top-0 h-full z-[34] transition-all duration-300': $store.sidebar.open && isDesktop,
        'w-[80px] sticky top-0 h-full z-[34] transition-all duration-300': !$store.sidebar.open && isDesktop,
        'w-3/5 translate-x-0 fixed z-[36] transition-all duration-300': !isDesktop && $store.sidebar.open,
        'w-3/5 -translate-x-[100%] fixed z-[36] transition-all duration-300': !isDesktop && !$store.sidebar.open,
    }"
    class="flex flex-col justify-between bg-white dark:bg-black text-mc_primary_color dark:text-white font-bold text-lg shadow-md min-h-full overflow-hidden"
>
    <div class="relative flex flex-col">

    <div class="relative flex flex-col h-[500px] sm:h-[550px] md:h-[550px] lg:h-[550px] overflow-x-hidden overflow-y-auto">
         <!-- Navigation -->
        <div
            x-cloak
            x-data="{
                screen: window.innerWidth,
                visible: true,
                dropdowns: Array({{ count($menuItems) }}).fill(false) // initialize dropdowns array
            }"
            x-init="
                visible = !(screen < 1024 && !$store.sidebar.open);
                $watch('$store.sidebar.open', val => {
                    visible = !(screen < 1024 && !val);
                });
            "
            @resize.window="
                screen = window.innerWidth;
                visible = !(screen < 1024 && !$store.sidebar.open);
            "
            class="p-3 gap-2 text-center flex flex-col flex-shrink-0"
        >
            @foreach ($menuItems as $index => $item)
                @php
                    $hasChildren = !empty($item['children'] ?? []);

                    $isActive = false;

                    if (isset($item['route'])) {
                        $isActive = request()->routeIs($item['activePattern'] ?? $item['route']);
                    }

                    if ($hasChildren) {
                        $isActive = collect($item['children'])->contains(
                            fn($child) => request()->routeIs($child['route'])
                        );
                    }
                @endphp

                @if ($hasChildren)
                    <!-- Parent Dropdown Item -->
                    <div class="relative"
                        x-data="{ showTooltip: false }"
                        @mouseenter="if (!$store.sidebar.open) showTooltip = true"
                        @mouseleave="showTooltip = false">

                       <!-- Parent Dropdown Button -->
                        <div
                            @click="
                                if ($store.sidebar.open) {
                                    dropdowns[{{ $index }}] = !dropdowns[{{ $index }}];
                                } else {
                                    $store.sidebar.toggle();
                                }
                            "
                            class="relative flex items-center gap-2 px-4 py-2 rounded-lg w-full transition cursor-pointer overflow-hidden select-none
                                {{ $isActive ? 'bg-gray-200 dark:bg-zinc-800' : 'dark:hover:bg-zinc-800 hover:bg-gray-200' }}"
                            x-bind:class="'justify-' + ($store.sidebar.open ? 'start' : 'center')"
                            x-data="{ showTooltip: false }"
                            @mouseenter="if (!$store.sidebar.open) showTooltip = true"
                            @mouseleave="showTooltip = false"
                        >
                            <!-- Icon -->
                            <span class="inline-block text-center">
                                <i class="{{ $item['icon'] }}"></i>
                            </span>

                            <!-- Label (when sidebar is open) -->
                            <div
                                x-cloak
                                :class="{
                                    'left-14 opacity-100 transition-all duration-400': $store.sidebar.open,
                                    'opacity-0': !$store.sidebar.open
                                }"
                                class="absolute w-full text-left ease-in-out overflow-x-hidden"
                            >
                                <span>
                                    {{ $item['label'] }}
                                </span>
                            </div>

                            <!-- Dropdown Arrow (when sidebar is open) -->
                            <svg x-show="$store.sidebar.open"
                                :class="{ 'rotate-180': dropdowns[{{ $index }}] }"
                                class="w-4 h-4 transition-transform duration-200 ml-auto mr-2"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>

                            <!-- Tooltip (when sidebar is collapsed) -->
                            <span x-show="showTooltip && !$store.sidebar.open"
                                x-transition
                                x-cloak
                                class="fixed w-auto text-left left-14 px-4 py-2 transition-all
                                        dark:bg-zinc-800 bg-gray-200 rounded-tr-lg rounded-br-lg z-50 whitespace-nowrap">
                                {{ $item['label'] }}
                            </span>
                        </div>

                        <!-- Dropdown Children -->
                        <div
                            x-show="$store.sidebar.open"
                            x-ref="dropdown{{ $index }}"
                            x-data="{ open: false, height: 0 }"
                            x-init="$watch('dropdowns[{{ $index }}]', value => {
                                open = value;
                                height = value ? $refs.dropdown{{ $index }}.scrollHeight : 0;
                            })"
                            x-effect="$el.style.height = open ? height + 'px' : '0px'"
                            class="overflow-hidden transition-all duration-300 ease-in-out"
                            style="height: 0;"
                        >
                            <div class="relative flex flex-col items-start pl-6 mt-1 pb-1">
                                <!-- Line -->
                                <div class="absolute top-0 left-5 h-full w-0.5 bg-mc_primary_color dark:bg-white"></div>

                                @foreach ($item['children'] as $child)
                                    @php
                                        $isChildActive = request()->routeIs($child['route']);
                                    @endphp

                                    <x-responsive-nav-link
                                        href="{{ route($child['route']) }}"
                                        class="flex items-center gap-2 px-4 py-2 rounded-lg w-full transition select-none
                                            {{ $isChildActive
                                                ? 'bg-gray-200 dark:bg-zinc-800'
                                                : 'dark:hover:bg-zinc-800 hover:bg-gray-200' }}"
                                        wire:navigate
                                    >
                                        <i class="{{ $child['icon'] }}"></i>
                                        <span x-show="$store.sidebar.open" x-transition class="ml-2">
                                            {{ $child['label'] }}
                                        </span>
                                    </x-responsive-nav-link>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Single Link Item -->
                    <x-responsive-nav-link
                        href="{{ route($item['route']) }}"
                        class="relative flex items-center gap-2 px-4 py-2 rounded-lg w-full transition overflow-hidden select-none
                            {{ $isActive ? 'bg-gray-200 dark:bg-zinc-800' : 'dark:hover:bg-zinc-800 hover:bg-gray-200' }}"
                        x-bind:class="'justify-' + ($store.sidebar.open ? 'start' : 'center')"
                        x-data="{ showTooltip: false }"
                        wire:navigate
                        @mouseenter="if (!$store.sidebar.open) showTooltip = true"
                        @mouseleave="showTooltip = false">
                        <span class="inline-block text-center">
                            <i class="{{ $item['icon'] }}"></i>
                        </span>
                        <div
                            x-cloak
                            :class="{
                                'left-14 opacity-100 transition-all duration-400': $store.sidebar.open,
                                'opacity-0 ': !$store.sidebar.open,

                            }"
                            class="absolute w-full text-left ease-in-out overflow-x-hidden "
                        >
                        <span>
                            {{ $item['label'] }}
                        </span>
                        </div>

                        <!-- Tooltip (collapsed) -->
                        <span x-show="showTooltip && !$store.sidebar.open"
                            x-transition
                            x-cloak
                            class="fixed w-auto text-left left-14 px-4 py-2 transition-all
                                    dark:bg-zinc-800 bg-gray-200 rounded-tr-lg rounded-br-lg z-50 whitespace-nowrap select-none">
                            {{ $item['label'] }}
                        </span>
                    </x-responsive-nav-link>
                @endif
            @endforeach
        </div>
    </div>
    </div>

    <div class="relative flex flex-col"
        x-data="{ showTooltip: false, loadingLogoutModal: false }"
    >
        <div
            class="flex items-center px-4 py-3 w-full cursor-pointer text-white relative
                bg-red-600 hover:bg-red-500 shadow-md"
                @mouseenter="if (!$store.sidebar.open) showTooltip = true"
                @mouseleave="showTooltip = false"
                @click="$dispatch('logout-modal-started'); $dispatch('logout-modal')"
                x-bind:class="'justify-' + ($store.sidebar.open ? 'start' : 'center')"
            >
            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="size-6 mr-2"
                :class="{ 'mr-0': !$store.sidebar.open }">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
            </svg>

            <!-- Text -->
            <div
                x-cloak
                :class="{
                        'left-15 opacity-100 transition-all duration-200': $store.sidebar.open,
                        'opacity-0 ': !$store.sidebar.open,
                    }"
                class="absolute left-12"
            >
                <span class="flex items-center">
                   Log out
                </span>
            </div>

            <!-- Tooltip -->
            <span
                x-show="showTooltip && !$store.sidebar.open"
                x-cloak
            class="fixed w-auto text-left left-14 -bottom-1 p-3 transition-all text-left bg-red-500 duration-300 rounded-tr-lg rounded-br-lg z-100 whitespace-nowrap"
            >
                <span x-show="!loadingLogoutModal">Log out</span>
                <span
                    x-show="loadingLogoutModal"
                    class="animate-spin rounded-full h-3 w-3 border-2 border-white border-t-transparent inline-block"
                ></span>
            </span>
        </div>
    </div>
</aside>
