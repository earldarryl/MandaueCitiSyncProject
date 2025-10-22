@props([
    'name',
    'placeholder' => 'Select an option',
    'options' => [],
])



<div
    x-data="{
        open: false,
        selected: @entangle($name),
        customValue: '',
        get value() {
            return this.selected === 'Other' ? this.customValue : this.selected;
        }
    }"
    x-init="
        $watch('value', (val) => {
            {{ $name }} = val;
            $wire.set('{{ $name }}', val, true);
        });
            window.addEventListener('clear', () => {
            selected = '';
            customValue = '';
            {{ $name }} = '';
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
            wire:model="{{ $name }}"
            x-model="{{ $name }}"
            name="{{ $name }}"
            class:input="border rounded-lg cursor-pointer"
            readonly
            placeholder="{{ $placeholder }}"
            x-bind:value="selected === ''
                ? ''
                : (selected === 'Other'
                    ? (customValue || 'Custom option')
                    : selected)"
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
        class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-black ring-opacity-5 rounded-md shadow-md"
    >
        <ul class="py-1" role="listbox">
            @foreach ($options as $option)
                <li>
                    <button
                        type="button"
                        @click="
                            selected = '{{ $option }}';
                            open = false;
                        "
                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                        :class="selected === '{{ $option }}'
                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                    >
                        {{ $option }}
                        <flux:icon.check x-show="selected === '{{ $option }}'" class="w-4 h-4" />
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>

