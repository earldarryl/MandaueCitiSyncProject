<div class="flex-col w-full h-full"
     x-data
     x-on:close-all-modals.window="
        document.querySelectorAll('[x-data][x-show]').forEach(el => {
            el.__x.$data.open = false
        })
     ">

    @php
        $highlight = fn($text, $search) => $search
            ? preg_replace(
                '/(' . preg_quote($search, '/') . ')/i',
                '<mark class="bg-yellow-200 text-black dark:bg-yellow-500 dark:text-black">$1</mark>',
                $text
            )
            : $text;
    @endphp

    <!-- Header -->
    <header class="relative border-box w-full flex justify-between p-2 items-center gap-2">
        <div class="relative w-1/2">
            <flux:input
                icon="magnifying-glass"
                type="text"
                wire:model.live="search"
                placeholder="Search grievances..."
                clearable
            />
        </div>

        <flux:button
            icon="plus-circle"
            variant="primary"
            color="blue"
            wire:click="goToGrievanceCreate"
        >
            Add Grievance
        </flux:button>
    </header>

    <!-- Grid -->
    <div class="flex w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 w-full">

            @forelse ($grievances as $grievance)
                <div class="rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm hover:shadow-lg transition-all duration-300
                    bg-white dark:bg-zinc-800 flex flex-col p-5 h-[350px]">

                    <div class="flex flex-col flex-1 justify-between">
                        <header class="flex justify-between items-start mb-3">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 truncate">
                                    {!! $highlight($grievance->grievance_title, $search) !!}
                                </h2>
                                <span class="text-xs italic text-gray-500 dark:text-gray-400">
                                    {{ $grievance->is_anonymous ? 'Submitted Anonymously' : 'Submitted by ' . $grievance->user->name }}
                                </span>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full
                                {{ $grievance->priority_level === 'High' ? 'bg-red-100 text-red-600' :
                                ($grievance->priority_level === 'Normal' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600') }}">
                                {!! $highlight($grievance->priority_level, $search) !!}
                            </span>
                        </header>

                        <div class="text-sm bg-gray-200 dark:bg-zinc-700 p-5 text-gray-600 rounded-xl dark:text-gray-300 prose dark:prose-invert overflow-y-auto flex-1">
                            {!! $highlight(Str::limit($grievance->grievance_details, 150), $search) !!}
                        </div>

                        <footer class="flex justify-between w-full items-center mt-2 pt-3">
                            <div class="text-xs">{{ $grievance->created_at->format('M d, Y') }}</div>
                            <div class="flex gap-2">
                                <flux:button
                                    icon="eye"
                                    variant="primary"
                                    color="zinc"
                                    wire:click="goToGrievanceView({{ $grievance->grievance_id }})">
                                    View
                                </flux:button>

                                <flux:button
                                    icon="pencil-square"
                                    variant="primary"
                                    color="blue"
                                    wire:click="goToGrievanceEdit({{ $grievance->grievance_id }})">
                                    Edit
                                </flux:button>

                                <flux:button
                                    icon="trash"
                                    variant="danger"
                                    x-on:click="$dispatch('open-modal', 'delete-{{ $grievance->grievance_id }}')">
                                    Delete
                                </flux:button>
                            </div>
                        </footer>
                    </div>
                </div>

                <!-- Delete Modal -->
                <x-modal name="delete-{{ $grievance->grievance_id }}">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-red-600">Confirm Delete</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to delete this grievance?</p>
                        <div class="flex justify-end gap-2">
                            <flux:button
                                    variant="primary"
                                    color="zinc"
                                    x-on:click="$dispatch('close-modal', 'delete-{{ $grievance->grievance_id }}')"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</flux:button>
                            <flux:button
                                    variant="danger"
                                    wire:click="deleteGrievance({{ $grievance->grievance_id }})"
                                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Delete</flux:button>
                        </div>
                    </div>
                </x-modal>

            @empty
                <p class="col-span-3 text-center text-gray-500">No grievances found.</p>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="p-4">
        {{ $grievances->links() }}
    </div>
</div>
