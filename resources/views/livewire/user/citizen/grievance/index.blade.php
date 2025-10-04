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

    <header class="relative w-full flex flex-col md:flex-row md:items-center md:justify-end gap-3 p-2">
        <!-- Right Side: File grievance button -->
            <x-responsive-nav-link
                href="{{ route('citizen.grievance.create') }}"
                class="flex gap-2 items-center justify-center border border-gray-200 dark:border-zinc-700 px-3 py-2 bg-mc_primary_color/20 text-mc_primary_color dark:bg-black dark:text-white rounded-lg w-full md:w-auto"
            >
                <flux:icon.document-plus />
                <span>File Grievance</span>
            </x-responsive-nav-link>
    </header>

    <header class="relative w-full flex flex-col items-center justify-center">
        <!-- Left Side: Search + Filters -->
        <div class="flex flex-col flex-1 gap-2 w-full">
            <!-- Search -->
            <div class="flex w-full flex-1 px-4">
                <input
                    type="text"
                    wire:model.defer="searchInput"
                    wire:keydown.enter="applySearch"
                    placeholder="Search grievances..."
                    class="border border-gray-200 dark:border-zinc-700 p-2 w-full bg-gray-200/60 rounded-l-md dark:bg-zinc-900"
                />
                <button
                    wire:click="applySearch"
                    class="py-2 px-4 bg-mc_primary_color/20 text-mc_primary_color dark:bg-black dark:text-white border border-gray-200 dark:border-zinc-700 rounded-r-md"
                >
                    <flux:icon.magnifying-glass />
                </button>
            </div>

            <!-- Filters -->
            <div class="flex flex-col justify-center items-center sm:flex-row sm:flex-wrap lg:flex-nowrap gap-2 w-full px-2">
                <x-filter-select
                    name="filterPriority"
                    placeholder="Priority"
                    :options="['High', 'Normal', 'Low']"
                />

                <x-filter-select
                    name="filterStatus"
                    placeholder="Status"
                    :options="['Pending', 'Resolved', 'Rejected']"
                />

                <x-filter-select
                    name="filterType"
                    placeholder="Type"
                    :options="['Complaint', 'Request', 'Inquiry']"
                />

                <x-filter-select
                    name="filterDate"
                    placeholder="Date"
                    :options="['Today', 'Yesterday', 'This Week', 'This Month', 'This Year']"
                />
            </div>

        </div>

    </header>


    <div
        x-data="{
            selected: @entangle('selectedGrievances').live,
            toggleAll(e) {
                if (e.target.checked) {
                    this.selected = @js($grievances->pluck('grievance_id'));
                } else {
                    this.selected = [];
                }
            }
        }"
        class="w-full"
    >
        <div class="flex items-center justify-between p-3 mb-4">
           <label class="flex items-center gap-2 cursor-pointer">
                <input
                    type="checkbox"
                    @change="toggleAll($event)"
                    class="peer hidden"
                >
                <span class="w-5 h-5 flex items-center justify-center border-2 border-gray-300 rounded-md
                            peer-checked:border-mc_primary_color peer-checked:bg-mc_primary_color
                            transition duration-200">
                    <!-- Checkmark Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="3"
                        stroke="currentColor"
                        class="w-3 h-3 text-white hidden peer-checked:block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>

                <span class="text-sm font-medium">Select All</span>
            </label>

        </div>

        <template x-if="selected.length > 0">
            <div class="flex justify-between items-center p-3 mb-4 rounded-md bg-gray-100 dark:bg-zinc-800">
                <span class="text-sm font-medium" x-text="`${selected.length} selected`"></span>

                <div class="flex gap-2">
                    <button
                        wire:click="bulkDelete"
                        class="flex items-center justify-center gap-2 px-3 py-2 bg-red-500/20 text-red-500 text-sm font-bold rounded-md hover:text-red-600">
                        <span><flux:icon.trash/></span>
                        <span>Delete Selected</span>
                    </button>
                    <button
                        wire:click="bulkMarkHigh"
                        class="flex items-center justify-center gap-2 px-3 py-2 bg-amber-500/20 text-amber-500 text-sm font-bold rounded-md hover:text-amber-600">
                        <span><flux:icon.document-check/></span>
                        <span>Mark as High Priority</span>
                    </button>
                </div>
            </div>
        </template>
    </div>



    <div class="relative">
        <!-- Grid -->
        <div class="flex w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-6 w-full">

                @forelse ($grievances as $grievance)
                    <div
                        class="cursor-pointer rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 flex flex-col p-5 h-[350px]
                            transition-transform duration-300 ease-in-out hover:scale-[1.03] hover:shadow-lg active:scale-[0.98]">

                        <div class="flex flex-col flex-1 justify-between">
                            <header class="flex justify-between items-start mb-3">
                                <div class="flex items-start gap-2">

                                    <div class="flex flex-col">
                                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 truncate">
                                            {!! $highlight($grievance->grievance_title, $search) !!}
                                        </h2>
                                        <span class="text-xs italic text-gray-500 dark:text-gray-400">
                                            {{ $grievance->is_anonymous ? 'Submitted Anonymously' : 'Submitted by ' . $grievance->user->name }}
                                        </span>
                                    </div>

                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full
                                        {{ $grievance->priority_level === 'High'
                                            ? 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300'
                                            : ($grievance->priority_level === 'Normal'
                                                ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300'
                                                : 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300') }}">
                                        {!! $highlight($grievance->priority_level, $search) !!}
                                    </span>

                                    <!-- View -->
                                    <a href="{{ route('citizen.grievance.view', $grievance->grievance_id) }}"
                                        class="bg-mc_primary_color/20 text-mc_primary_color dark:bg-blue-500/20 dark:text-blue-500 font-bold rounded-full p-2 transition-transform duration-300 ease-in-out hover:scale-[1.1] active:scale-[0.98]">
                                        <flux:icon.eye />
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('citizen.grievance.edit', $grievance->grievance_id) }}"
                                        class="bg-amber-500/20 text-amber-500 font-bold rounded-full p-2 transition-transform duration-300 ease-in-out hover:scale-[1.1] active:scale-[0.98]">
                                        <flux:icon.pencil-square />
                                    </a>

                                    <!-- Delete -->
                                    <button
                                        x-on:click.stop="$dispatch('open-modal', 'delete-{{ $grievance->grievance_id }}')"
                                        class="text-red-500 hover:text-red-700 font-bold rounded-full p-2 bg-red-500/20 transition-transform duration-300 ease-in-out hover:scale-[1.1] active:scale-[0.98]">
                                        <flux:icon.trash />
                                    </button>
                                </div>
                            </header>

                            <div class="text-sm bg-gray-200 dark:bg-zinc-700 p-5 text-gray-600 rounded-xl dark:text-gray-300 prose dark:prose-invert overflow-y-auto flex-1">
                                {!! $highlight(Str::limit($grievance->grievance_details, 150), $search) !!}
                            </div>

                            <footer class="flex justify-between w-full items-center mt-2 pt-3">
                                <div class="text-xs">{{ $grievance->created_at->format('M d, Y') }}</div>
                                <div class="flex gap-2">

                                    <!-- Bulk Select Checkbox -->
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedGrievances"
                                            value="{{ $grievance->grievance_id }}"
                                            class="peer hidden"
                                        >
                                        <span class="w-5 h-5 flex items-center justify-center border-2 border-gray-300 rounded-md
                                                    peer-checked:border-mc_primary_color peer-checked:bg-mc_primary_color
                                                    transition duration-200">

                                            <!-- Checkmark Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="3"
                                                stroke="currentColor"
                                                class="w-3 h-3 text-white hidden peer-checked:block">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>

                                        </span>
                                    </label>

                                </div>
                            </footer>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <x-modal name="delete-{{ $grievance->grievance_id }}">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-red-600">Confirm Delete</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                Are you sure you want to delete this grievance?
                            </p>
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    variant="primary"
                                    color="zinc"
                                    x-on:click="$dispatch('close-modal', 'delete-{{ $grievance->grievance_id }}')"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                    Cancel
                                </flux:button>
                                <flux:button
                                    variant="danger"
                                    wire:click="deleteGrievance({{ $grievance->grievance_id }})"
                                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                                    Delete
                                </flux:button>
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

        <!-- Overlay loader -->
        <div wire:loading.delay wire:target="applySearch, previousPage, nextPage, gotoPage, filterPriority, filterStatus, filterType, filterDate">
            <div class="absolute inset-0 bg-gray-100 dark:bg-black flex items-center justify-center z-50 rounded-lg">
                <div class="w-full flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    </div>
</div>
