@props([
    'name',
    'placeholder' => 'Select options',
    'options' => [],
])

<div
    x-data="{
        open: false,
        selected: @entangle($name).live || [],
        customValue: '',
        get value() {
            return this.selected.map(s => {
                let opt = {{ Js::from($options) }}[s];
                return opt ? opt : s;
            });
        },
        remove(item) {
            this.selected = this.selected.filter(v => v !== item);
            $wire.set('{{ $name }}', this.selected, true);
        }
    }"
    x-init="window.addEventListener('clear', () => {
        selected = [];
        customValue = '';
    })"
    class="relative w-full"
>
    <div
        @click="open = !open"
        tabindex="0"
        role="button"
        class="flex flex-wrap items-center gap-1 px-3 py-2 border rounded-lg cursor-pointer min-h-[42px]
            {{ $errors->has($name) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-200/50 dark:border-zinc-600' }}
            bg-white dark:bg-zinc-800"
    >
        <template x-for="(label, index) in value" :key="index">
            <span class="inline-flex gap-2 items-center bg-mc_primary_color/10 dark:bg-mc_primary_color/20 text-mc_primary_color dark:text-mc_primary_color text-sm font-medium px-2 py-1 rounded-full">
                <span x-text="label"></span>
                <button
                    type="button"
                    class="ml-1 text-mc_primary_color dark:text-mc_primary_color hover:text-red-500 dark:hover:text-red-400 focus:outline-none"
                    @click.stop="remove(selected[index])"
                >
                    ✕
                </button>
            </span>
        </template>

        <span x-show="selected.length === 0" class="text-gray-400 dark:text-gray-400 text-sm">{{ $placeholder }}</span>

        <input type="hidden" :value="selected">
    </div>

    <div class="absolute right-3 top-2 flex items-center gap-2">
        <flux:button
            x-show="selected.length > 0"
            size="sm"
            variant="subtle"
            icon="x-mark"
            class="h-5 w-5"
            @click.stop="selected = []; customValue = ''; $wire.set('{{ $name }}', [], true)"
        />
    </div>

    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white border-gray-200/50 dark:border-zinc-600 rounded-md shadow-md"
    >
        <ul class="py-1 max-h-60 overflow-y-auto" role="listbox">
            @foreach ($options as $id => $label)
                <li>
                    <button
                        type="button"
                        @click="
                            if (selected.includes({{ $id }})) {
                                selected = selected.filter(v => v !== {{ $id }});
                            } else {
                                selected.push({{ $id }});
                            }
                            $wire.set('{{ $name }}', selected, true);
                        "
                        class="w-full flex items-center justify-between text-left px-4 py-2 text-sm rounded-md"
                        :class="selected.includes({{ $id }})
                            ? 'bg-mc_primary_color/20 dark:bg-zinc-800 font-medium'
                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'">
                        {{ $label }}
                        <flux:icon.check x-show="selected.includes({{ $id }})" class="w-4 h-4 text-mc_primary_color" />
                    </button>
                </li>
            @endforeach
        </ul>

    </div>
</div>
