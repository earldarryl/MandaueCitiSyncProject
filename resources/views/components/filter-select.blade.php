@props([
    'name',
    'placeholder' => 'Select an option',
    'options' => [],
])

<div
    x-data="{
        open: false,
        selected: '',
        search: '',
        get filteredOptions() {
            if (this.search.trim() === '') return this.options;
            return this.options.filter(opt =>
                opt.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        options: @js(array_values($options)),
        toggle(option) {
            this.selected = option;
            $refs.hidden.value = option;
            $refs.hidden.dispatchEvent(new Event('input'));
            this.open = false;
        },
        clear() {
            this.selected = '';
            $refs.hidden.value = '';
            $refs.hidden.dispatchEvent(new Event('input'));
        }
    }"
    x-init="selected = $refs.hidden.value || ''"
    class="relative w-full"
>
    <input x-ref="hidden" type="hidden" x-model="{{ $name }}" wire:model="{{ $name }}" />

    <!-- Trigger -->
    <div
        @click="open = !open"
        @keydown.enter.prevent="open = !open"
        @keydown.space.prevent="open = !open"
        tabindex="0"
        role="button"
        :aria-expanded="open"
        aria-haspopup="listbox"
        class="flex items-center justify-between px-3 py-2 border border-gray-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-900 cursor-pointer transition"
    >
        <span
            x-text="selected || '{{ $placeholder }}'"
            class="text-[12px]"
            :class="!selected ? 'text-gray-500' : 'text-gray-700 dark:text-gray-200'">
        </span>

        <!-- Right controls -->
        <div class="flex items-center gap-2">
            <!-- Clear button -->
            <button
                x-show="selected"
                @click.stop="clear()"
                type="button"
                class="h-6 w-6 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-zinc-800"
                aria-label="Clear selection"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Chevron -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor"
                 class="h-4 w-4 text-gray-500 transition-transform duration-200"
                 :class="open ? 'rotate-180' : ''">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </div>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="absolute z-[60] mt-1 w-full bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-md shadow-lg overflow-hidden"
    >
        <!-- Search bar (only if >5 options) -->
        <template x-if="options.length > 5">
            <div class="p-2 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                <input
                    type="text"
                    x-model="search"
                    placeholder="Search..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-zinc-700 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-zinc-900 dark:text-gray-100"
                />
            </div>
        </template>

        <!-- Scrollable list -->
        <ul
            class="py-1 text-sm text-gray-700 dark:text-gray-200 max-h-56 overflow-y-auto"
            role="listbox"
        >
            <template x-for="option in filteredOptions" :key="option">
                <li>
                    <button
                        type="button"
                        @click="toggle(option)"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-md text-left"
                        :class="selected === option
                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                    >
                        <span x-text="option"></span>
                        <svg x-show="selected === option" xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                             class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </li>
            </template>

            <template x-if="filteredOptions.length === 0">
                <li class="px-4 py-2 text-gray-500 dark:text-gray-400 italic text-center">
                    No results found.
                </li>
            </template>
        </ul>
    </div>
</div>
