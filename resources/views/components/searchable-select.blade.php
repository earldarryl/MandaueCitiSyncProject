@props([
    'name',
    'placeholder' => 'Select an option',
    'options' => [],
    'selected' => null,
])

@php
    $normalized = collect($options)->mapWithKeys(function ($label, $key) {
        if (is_int($key)) {
            return [$label => $label];
        }
        return [$key => $label];
    })->toArray();
@endphp

<div
    x-data="{
        open: false,
        selected: @entangle($name),
        search: '',
        optionsMap: @js($normalized),
        highlightedIndex: -1, // Track highlighted option

        get optionsList() {
            return Object.entries(this.optionsMap);
        },

        get filteredOptions() {
            if (!this.search) return this.optionsList;
            const q = this.search.toLowerCase();
            return this.optionsList.filter(([k, v]) => v.toLowerCase().includes(q));
        },

        selectOption(key) {
            this.selected = key;
            $wire.set('{{ $name }}', key, true);
            this.open = false;
            this.search = '';
            this.highlightedIndex = -1;
        },

        get displayValue() {
            return this.optionsMap[this.selected] ?? this.selected ?? '';
        },
    }"
    x-init="
        const initialSelected = @js($selected);
        if (!selected && initialSelected) {
            selected = initialSelected;
            $wire.set('{{ $name }}', selected, true);
        }

        $watch('displayValue', (val) => {
            {{ $name }} = val;
            $wire.set('{{ $name }}', val, true);
        });

        window.addEventListener('clear', () => {
            selected = '';
            search = '';
            highlightedIndex = -1;
            $wire.set('{{ $name }}', selected, true);
        });
    "
    class="relative w-full !cursor-pointer"
>
    <!-- Input -->
    <div
        @click="open = !open; if(open) highlightedIndex=0"
        @keydown.enter.prevent="open = true; highlightedIndex=0"
        tabindex="0"
        class="relative !cursor-pointer"
    >
        <flux:input
            readonly
            placeholder="{{ $placeholder }}"
            class:input="border rounded-lg w-full cursor-pointer select-none !cursor-pointer"
            x-bind:value="displayValue"
        />

        <div class="absolute right-3 inset-y-0 flex items-center gap-2">
            <flux:button
                x-show="!!selected"
                size="sm"
                variant="subtle"
                icon="x-mark"
                class="h-5 w-5"
                @click.stop="selected = ''; search = ''; highlightedIndex=-1; $wire.set('{{ $name }}', selected, true);"
            />

            <!-- Dropdown arrow -->
            <div class="h-5 w-5 flex items-center justify-center">
                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'
                    stroke-width='2' stroke='currentColor'
                    class='h-5 w-5 text-gray-500 transition-transform duration-200'
                    :class='open ? `rotate-180` : ``'>
                    <path stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' />
                </svg>
            </div>
        </div>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open = false; highlightedIndex=-1"
        x-transition
        class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-gray-200 dark:ring-zinc-700 rounded-md shadow-md"
    >
        <!-- Search -->
        <div class="w-full flex items-center border-b border-gray-300 dark:border-zinc-700 p-1">
            <flux:icon.magnifying-glass class="px-1 text-gray-500 dark:text-zinc-700"/>
            <input
                type="text"
                x-model="search"
                placeholder="Search..."
                class="w-full border-none focus:outline-none focus:ring-0 bg-transparent placeholder-gray-400 py-1 text-sm font-medium"
                @keydown.arrow-down.prevent="if (highlightedIndex < filteredOptions.length-1) highlightedIndex++"
                @keydown.arrow-up.prevent="if (highlightedIndex > 0) highlightedIndex--"
                @keydown.enter.prevent="if(filteredOptions[highlightedIndex]) selectOption(filteredOptions[highlightedIndex][0])"
            />
        </div>

        <!-- Options -->
        <div class="max-h-48 overflow-y-auto">
            <ul class="py-1" role="listbox">
                <template x-for="[key, label], index in filteredOptions" :key="key">
                    <li>
                        <button
                            type="button"
                            @click="selectOption(key)"
                            class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                            :class="selected === key
                                ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                : index === highlightedIndex
                                    ? 'bg-zinc-100 dark:bg-zinc-800'
                                    : 'hover:bg-zinc-100 dark:hover:bg-zinc-800' "
                        >
                            <span x-text="label"></span>
                            <flux:icon.check x-show="selected === key" class="w-4 h-4" />
                        </button>
                    </li>
                </template>

                <li
                    x-show="filteredOptions.length === 0"
                    class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
                >
                    No results found
                </li>
            </ul>
        </div>
    </div>
</div>
