@props([
    'name',
    'placeholder' => 'Select an option',
    'options' => [],
    'selected' => '',
])

<div
    x-data="{
        open: false,
        selected: @js($selected) || '',    // initialize from prop
        customValue: '',
        search: '',
        get filteredOptions() {
            if (this.search === '') return @js($options);
            return @js($options).filter(opt =>
                opt.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        get value() {
            return this.selected === 'Other' ? this.customValue : this.selected;
        }
    }"
    x-init="
        // initialize Livewire with selected value
        if (selected) {
            $wire.set('{{ $name }}', selected, true);
        }
        $watch('value', (val) => {
            $wire.set('{{ $name }}', val, true);
        });
        window.addEventListener('clear', () => {
            selected = '';
            customValue = '';
            search = '';
            $wire.set('{{ $name }}', '', true);
        })
    "
    class="relative w-full"
>
    <div
        @click="open = !open"
        @keydown.enter.prevent="open = !open"
        @keydown.space.prevent="open = !open"
        tabindex="0"
        role="button"
        :aria-expanded="open"
        aria-haspopup="listbox"
    >
        <flux:input
            readonly
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            x-bind:value="selected === ''
                ? ''
                : (selected === 'Other'
                    ? (customValue || 'Custom option')
                    : selected)"
            class:input="border rounded-lg cursor-pointer"
        />

        <div class="absolute right-3 inset-y-0 flex items-center gap-2">
            <flux:button
                x-show="!!selected"
                size="sm"
                variant="subtle"
                icon="x-mark"
                class="h-5 w-5"
                @click.stop="
                    selected = '';
                    customValue = '';
                    search = '';
                    $wire.set('{{ $name }}', '', true);
                "
            />

            <div class="h-5 w-5 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor"
                    class="h-5 w-5 text-gray-500 transition-transform duration-200"
                    :class="open ? 'rotate-180' : ''">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>
    </div>

    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-gray-200 dark:ring-zinc-700 ring-opacity-5 rounded-md shadow-md"
    >
        <div class="w-full flex items-center border-b border-gray-300 dark:border-zinc-700 p-1">
            <flux:icon.magnifying-glass class="px-1 text-gray-500 dark:text-zinc-700"/>
            <input
                type="text"
                x-model="search"
                placeholder="Search..."
                class="w-full border-none focus:outline-none focus:ring-0 bg-transparent placeholder-gray-400 py-1 text-sm font-medium"
            />
        </div>

        <div class="max-h-48 overflow-y-auto">
            <ul class="py-1" role="listbox">
                <template x-for="option in filteredOptions" :key="option">
                    <li>
                        <button
                            type="button"
                            @click="
                                selected = option;
                                open = false;
                                search = '';
                            "
                            class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                            :class="selected === option
                                ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                                : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                        >
                            <span x-text="option"></span>
                            <flux:icon.check x-show="selected === option" class="w-4 h-4" />
                        </button>
                    </li>
                </template>

                <li x-show="filteredOptions.length === 0" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                    No results found
                </li>
            </ul>
        </div>
    </div>
</div>
