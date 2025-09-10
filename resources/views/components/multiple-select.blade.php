@props([
    'name',
    'placeholder' => 'Select options',
    'options' => [], // expects [id => name, id => name, ...]
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
        }
    }"
    x-init="window.addEventListener('clear', () => {
        selected = [];
        customValue = '';
    })"
    class="relative w-full"
>
    <!-- Trigger -->
    <div @click="open = !open" tabindex="0" role="button">

        <flux:input
            readonly
            placeholder="{{ $placeholder }}"
            class:input="cursor-pointer"
            class="rounded-lg {{ $errors->has($name)
                ? 'border border-red-500 focus:border-red-500 focus:ring-red-500'
                : '' }}"

            x-bind:value="value.length === 0 ? '' : value.join(' • ')"
        />

        <!-- Right controls -->
        <div class="absolute right-3 inset-y-0 flex items-center gap-2">
            <flux:button
                x-show="selected.length > 0"
                size="sm"
                variant="subtle"
                icon="x-mark"
                class="h-5 w-5"
                @click.stop="selected = []; customValue = ''; $wire.set('{{ $name }}', [], true)"
            />
        </div>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute z-50 mt-1 w-full dark:bg-zinc-900 bg-white ring-1 ring-black ring-opacity-5 rounded-md shadow-md"
    >
        <ul class="py-1" role="listbox">
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
                            ? 'bg-zinc-100 dark:bg-zinc-800 font-medium'
                            : 'hover:bg-zinc-100 dark:hover:bg-zinc-800'">
                        {{ $label }}
                        <flux:icon.check x-show="selected.includes({{ $id }})" class="w-4 h-4" />
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Custom input for "Other" -->
        <template x-if="selected.includes(999)">
            <div class="px-4 pb-3">
                <flux:input
                    type="text"
                    x-model="customValue"
                    placeholder="Please specify"
                    @click.stop
                    @input="$wire.set('{{ $name }}', [...selected.filter(v => v !== 999), customValue], true)"
                />
            </div>
        </template>
    </div>
</div>
