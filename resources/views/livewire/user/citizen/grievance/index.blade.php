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

    <header class="relative w-full flex flex-col items-center justify-center">

        <div class="flex flex-col flex-1 gap-2 w-full">

            <div
                x-data="{
                    openStats: $store.sidebar.screen >= 768,
                    updateStatsVisibility() {
                        this.openStats = $store.sidebar.screen >= 768;
                    }
                }"
                x-init="updateStatsVisibility(); window.addEventListener('resize', () => updateStatsVisibility())"
                class="relative w-full h-auto flex flex-col"
            >

                <!-- Toggle Button -->
                <div class="flex justify-center items-center mb-4">
                    <button
                        @click="openStats = !openStats"
                        class="flex items-center justify-center gap-2 px-4 py-2 rounded-full border border-gray-300 dark:border-zinc-600
                            bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-all duration-300"
                    >
                        <flux:icon.chevron-down
                            :class="openStats ? 'rotate-180 text-blue-500' : 'text-gray-600'"
                            class="h-5 w-5 transition-transform duration-300"
                        />
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                            <template x-if="!openStats"><span>Show Statistics</span></template>
                            <template x-if="openStats"><span>Hide Statistics</span></template>
                        </span>
                    </button>
                </div>

                <!-- Slide Transition -->
                <div
                    x-show="openStats"
                    x-collapse
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-400"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full flex flex-col h-auto"
                >

                    <div class="w-full grid grid-cols-2 md:grid-cols-4 gap-4 mx-auto px-3 mb-6">

                        <!-- Total Grievances -->
                        <div
                            class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.document-check class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Grievances</p>
                            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight" wire:poll.visible>
                                {{ $totalGrievances }}
                            </p>
                        </div>

                        <!-- High Priority -->
                        <div
                            class="group relative bg-gradient-to-br from-red-50 to-red-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-red-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-red-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.exclamation-triangle class="h-8 w-8 text-red-600 dark:text-red-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">High Priority</p>
                            <p class="relative text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">{{ $highPriorityCount }}</p>
                        </div>

                        <!-- Normal Priority -->
                        <div
                            class="group relative bg-gradient-to-br from-amber-50 to-yellow-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-amber-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-amber-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-amber-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.information-circle class="h-8 w-8 text-yellow-500 dark:text-yellow-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Normal Priority</p>
                            <p class="relative text-3xl font-bold text-yellow-500 dark:text-yellow-400 tracking-tight">{{ $normalPriorityCount }}</p>
                        </div>

                        <!-- Low Priority -->
                        <div
                            class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.arrow-down-circle class="h-8 w-8 text-green-600 dark:text-green-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Low Priority</p>
                            <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $lowPriorityCount }}</p>
                        </div>
                    </div>

                    <div class="w-full grid grid-cols-2 md:grid-cols-4 gap-4 mx-auto px-3 mb-6">

                        <!-- Pending -->
                        <div
                            class="group relative bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-yellow-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-yellow-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-yellow-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.clock class="h-8 w-8 text-yellow-500 dark:text-yellow-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Pending</p>
                            <p class="relative text-3xl font-bold text-yellow-500 dark:text-yellow-400 tracking-tight">{{ $pendingCount }}</p>
                        </div>

                        <!-- In Progress -->
                        <div
                            class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.arrow-trending-up class="h-8 w-8 text-blue-600 dark:text-blue-400 animate-spin-slow" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">In Progress</p>
                            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $inProgressCount }}</p>
                        </div>

                        <!-- Resolved -->
                        <div
                            class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.check-circle class="h-8 w-8 text-green-600 dark:text-green-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Resolved</p>
                            <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $resolvedCount }}</p>
                        </div>

                        <!-- Closed -->
                        <div
                            class="group relative bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-gray-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-gray-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-gray-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.x-circle class="h-8 w-8 text-gray-500 dark:text-gray-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Closed</p>
                            <p class="relative text-3xl font-bold text-gray-500 dark:text-gray-400 tracking-tight">{{ $closedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="grid grid-cols-2 lg:grid-cols-4 gap-3 w-full mx-auto px-3 mb-2"
                >
                    <x-filter-select
                        name="filterPriority"
                        placeholder="Priority"
                        :options="['High', 'Normal', 'Low']"
                    />

                    <x-filter-select
                        name="filterStatus"
                        placeholder="Status"
                        :options="['Pending', 'In Progress', 'Resolved', 'Closed']"
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

    <header class="relative w-full flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-2">

            <div class="flex w-full flex-1">

                <div class="relative w-full font-bold">
                    <input
                        type="text"
                        wire:model.defer="searchInput"
                        wire:keydown.enter="applySearch"
                        placeholder="Search grievances..."
                        class="relative border border-gray-200 dark:border-zinc-700 p-2 pr-10 w-full bg-gray-100 rounded-l-md dark:bg-zinc-900"
                    />

                    <button
                        type="button"
                        wire:click="clearSearch"
                        class="absolute p-3 rounded-lg inset-y-0 right-1 flex items-center justify-center text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white hover:bg-gray-300 dark:hover:bg-zinc-700"
                    >
                        <flux:icon.x-mark class="w-4 h-4"/>
                    </button>
                </div>

                <button
                    wire:click="applySearch"
                    class="py-2 px-4 font-bold bg-mc_primary_color/20 text-mc_primary_color dark:bg-black dark:text-white border border-gray-200 dark:border-zinc-700 rounded-r-md"
                >
                    <flux:icon.magnifying-glass />
                </button>

            </div>

            <x-responsive-nav-link
                href="{{ route('citizen.grievance.create') }}"
                class="flex gap-2 font-bold items-center justify-center border border-gray-200 dark:border-zinc-700 px-3 py-2 bg-mc_primary_color/20 text-mc_primary_color dark:bg-black dark:text-white rounded-lg w-full md:w-auto"
            >
                <flux:icon.document-plus />
                <span>File Grievance</span>
            </x-responsive-nav-link>
    </header>

    <div wire:loading.remove wire:target="applySearch, previousPage, nextPage, gotoPage, filterPriority, filterStatus, filterType, filterDate">
        @if ($grievances->count() > 0)
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
                <!-- Select All -->
                <div class="flex items-center justify-between p-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            @change="toggleAll($event)"
                            class="peer hidden"
                        >
                        <span class="w-5 h-5 flex items-center justify-center border-2 border-gray-300 rounded-md
                                    peer-checked:border-mc_primary_color peer-checked:bg-mc_primary_color
                                    transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-3 h-3 text-white hidden peer-checked:block">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </span>
                        <span class="text-md font-bold">Select All</span>
                    </label>
                </div>

                <!-- Bulk Action Buttons -->
                <template x-if="selected.length > 0">
                    <div class="flex justify-between items-center p-3 rounded-md bg-gray-100 dark:bg-zinc-800">
                        <span class="text-sm font-medium" x-text="`${selected.length} selected`"></span>

                        <div class="flex gap-2">
                            <button
                                wire:click="bulkDelete"
                                class="flex items-center justify-center gap-2 px-3 py-2 bg-red-500/20 text-red-500 text-sm font-bold rounded-md hover:text-red-600">
                                <flux:icon.trash />
                                <span>Delete Selected</span>
                            </button>
                            <button
                                wire:click="bulkMarkHigh"
                                class="flex items-center justify-center gap-2 px-3 py-2 bg-amber-500/20 text-amber-500 text-sm font-bold rounded-md hover:text-amber-600">
                                <flux:icon.document-check />
                                <span>Mark as High Priority</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>


    <div class="relative">
        <!-- Grid -->
        <div class="flex w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-6 w-full"
                 wire:loading.remove wire:target="applySearch, previousPage, nextPage, gotoPage, filterPriority, filterStatus, filterType, filterDate, bulkDelete, bulkMarkHigh, clearSearch"
            >

                @forelse ($grievances as $grievance)
                    <div
                        class="cursor-pointer rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 flex flex-col p-5 h-[350px]
                            transition-transform duration-300 ease-in-out hover:scale-[1.03] hover:shadow-lg active:scale-[0.98]">

                        <div class="flex flex-col flex-1 justify-between">
                            <header class="flex justify-between items-start mb-3">
                                <div class="flex items-start gap-2">
                                    <div class="flex flex-col max-w-[250px]">
                                        <h2
                                            class="text-lg font-semibold text-gray-800 dark:text-gray-100 truncate"
                                            title="{{ strip_tags($grievance->grievance_title) }}"
                                        >
                                            {!! $highlight(Str::limit($grievance->grievance_title, 60), $search) !!}
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

                                    <flux:dropdown>
                                        <flux:button
                                            icon="ellipsis-horizontal"
                                            class="!p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition"
                                        />

                                       <flux:menu>

                                            <!-- View -->
                                            <flux:menu.item
                                                icon="eye"
                                                x-on:click="window.location.href='{{ route('citizen.grievance.view', $grievance->grievance_id) }}'"
                                            >
                                                <span class="font-bold text-lg">View</span>
                                            </flux:menu.item>

                                            <!-- Edit -->
                                            <flux:menu.item
                                                icon="pencil-square"
                                                x-on:click="window.location.href='{{ route('citizen.grievance.edit', $grievance->grievance_id) }}'"
                                            >
                                                <span class="font-bold text-lg">Edit</span>
                                            </flux:menu.item>

                                            <!-- Delete -->
                                            <flux:modal.trigger name="delete-{{ $grievance->grievance_id }}">
                                                <flux:menu.item
                                                    icon="trash"
                                                    variant="danger"
                                                >
                                                    <span class="font-bold text-lg">Delete</span>
                                                </flux:menu.item>
                                            </flux:modal.trigger>
                                        </flux:menu>

                                    </flux:dropdown>

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

                         <flux:modal name="delete-{{ $grievance->grievance_id }}" class="md:w-1/3">
                                <div class="flex flex-col items-center text-center p-6 space-y-4">
                                    <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20">
                                        <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                    </div>
                                    <flux:heading size="lg" class="font-bold text-gray-800 dark:text-gray-100">Confirm Deletion</flux:heading>
                                    <flux:text class="text-sm leading-relaxed">
                                        Are you sure you want to delete this grievance?
                                    </flux:text>
                                </div>
                                <div class="flex items-center justify-center w-full">
                                    <div
                                        wire:loading.remove
                                        wire:target="deleteGrievance({{ $grievance->grievance_id }})"
                                        class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 rounded-b-2xl">
                                        <flux:modal.close>
                                            <flux:button variant="subtle" class="border border-gray-200 dark:border-zinc-800">Cancel</flux:button>
                                        </flux:modal.close>
                                        <flux:button
                                            variant="danger"
                                            icon="trash"
                                            wire:click="deleteGrievance({{ $grievance->grievance_id }})"
                                        >
                                            Yes, Delete
                                        </flux:button>
                                    </div>
                                    <div wire:loading wire:target="deleteGrievance({{ $grievance->grievance_id }})">
                                        <div class="flex items-center justify-center gap-2 w-full">
                                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                                        </div>
                                    </div>
                                </div>
                        </flux:modal>
                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-500">No grievances found.</p>
                @endforelse

            </div>
        </div>

        <!-- Skeleton Grid -->
        <div
            wire:loading
            wire:target="applySearch, previousPage, nextPage, gotoPage, filterPriority, filterStatus, filterType, filterDate, bulkDelete, bulkMarkHigh, clearSearch"
            class="w-full bg-gray-50 dark:bg-zinc-900 px-6 py-8"
        >
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-6 w-full animate-pulse">
                @for ($i = 0; $i < 4; $i++)
                    <div class="rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 p-6 h-[350px] flex flex-col justify-between">

                        <!-- Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex flex-col gap-2 w-3/4">
                                <div class="h-5 bg-gray-300 dark:bg-zinc-600 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 dark:bg-zinc-700 rounded w-1/2"></div>
                            </div>
                            <div class="flex gap-2">
                                <div class="h-6 w-12 bg-gray-200 dark:bg-zinc-700 rounded-full"></div>
                                <div class="h-8 w-8 bg-gray-200 dark:bg-zinc-700 rounded-full"></div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 space-y-3">
                            <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded w-5/6"></div>
                            <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded w-4/6"></div>
                            <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 dark:bg-zinc-700 rounded w-2/3"></div>
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-between items-center mt-5">
                            <div class="h-3 w-20 bg-gray-200 dark:bg-zinc-700 rounded"></div>
                            <div class="h-5 w-5 bg-gray-200 dark:bg-zinc-700 rounded-md"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $grievances->links() }}
        </div>

    </div>
</div>
