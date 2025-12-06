@props([
    'name',
    'placeholder' => 'Select date',
    'model',
])

<div {{ $attributes->merge(['class' => 'relative w-full']) }}
     x-data="{
        open: false,
        selected: @entangle($model),
        get value() { return selected; },
        toggle() {
            open = !open;
            if (open) $refs.dateInput._flatpickr.open();
        },
        clear() {
            selected = null;
            $refs.dateInput._flatpickr.clear();
        }
     }"
     x-init="$nextTick(() => {
        flatpickr($refs.dateInput, {
            dateFormat: 'Y-m-d',
            defaultDate: selected,
            onChange: (selectedDates, dateStr) => { selected = dateStr; }
        });
     })"
>
    <div
        @click="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.space.prevent="toggle()"
        tabindex="0"
        role="button"
        :aria-expanded="open"
        aria-haspopup="dialog"
        class="flex items-center justify-between px-3 py-2 border border-gray-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-900 cursor-pointer"
    >
        <input
            type="text"
            x-ref="dateInput"
            x-model="selected"
            readonly
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            class="w-full bg-transparent text-[12px] focus:outline-none cursor-pointer"
        />

        <div class="flex items-center gap-2">
            <button
                type="button"
                x-show="selected"
                @click.stop="clear()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1 rounded"
            >
                <x-heroicon-o-x-mark class="w-4 h-4"/>
            </button>

            <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
        </div>
    </div>
</div>
