<div class="flex-col w-full h-full"
     data-component="citizen-grievance-index"
     data-wire-id="{{ $this->id() }}"
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

            {{-- <div
                x-data="{
                    openStats: $store.sidebar.screen >= 768,
                    updateStatsVisibility() {
                        this.openStats = $store.sidebar.screen >= 768;
                    }
                }"
                x-init="updateStatsVisibility(); window.addEventListener('resize', () => updateStatsVisibility())"
                class="relative w-full h-auto flex flex-col"
            >

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
                    wire:poll.10s="updateStats"
                >

                    <div class="w-full grid grid-cols-2 md:grid-cols-4 gap-4 mx-auto px-3 mb-6">

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

                        <div
                            class="group relative bg-gradient-to-br from-red-50 to-red-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-red-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-red-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.x-circle class="h-8 w-8 text-red-500 dark:text-red-400" />
                            </div>
                            <p class="relative text-base font-semibold text-red-700 dark:text-red-300 mt-2">Unresolved</p>
                            <p class="relative text-3xl font-bold text-red-500 dark:text-red-400 tracking-tight">{{ $unresolvedCount }}</p>
                        </div>

                        <div
                            class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-purple-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-purple-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.x-circle class="h-8 w-8 text-purple-500 dark:text-purple-400" />
                            </div>
                            <p class="relative text-base font-semibold text-purple-700 dark:text-purple-300 mt-2">Closed</p>
                            <p class="relative text-3xl font-bold text-purple-500 dark:text-purple-400 tracking-tight">{{ $closedCount }}</p>
                        </div>

                        <div
                            class="group relative bg-gradient-to-br from-rose-50 to-rose-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-rose-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-rose-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-rose-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.x-circle class="h-8 w-8 text-rose-500 dark:text-rose-400" />
                            </div>
                            <p class="relative text-base font-semibold text-rose-700 dark:text-rose-300 mt-2">Overdue</p>
                            <p class="relative text-3xl font-bold text-rose-500 dark:text-rose-400 tracking-tight">{{ $overdueCount }}</p>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div
                 x-data="{
                    filterDepartment: '',
                    filterType: '',
                    categoryOptions: @js($categoryOptions),
                }"
                class="grid grid-cols-2 lg:grid-cols-7 gap-3 w-full mx-auto px-3 my-2"
            >
                <x-filter-select
                    name="filterPriority"
                    placeholder="Priority"
                    :options="['High', 'Normal', 'Low']"
                />

                <x-filter-select
                    name="filterStatus"
                    placeholder="Status"
                    :options="['Show All', 'Pending', 'Acknowledged', 'In Progress', 'Escalated', 'Resolved', 'Unresolved', 'Closed', 'Overdue']"
                />

                <x-filter-select
                    name="filterDate"
                    placeholder="Date"
                    :options="['Today', 'Yesterday', 'This Week', 'This Month', 'This Year']"
                />

                <x-filter-select
                    name="filterDepartment"
                    placeholder="Department"
                    :options="$departmentOptions"
                    x-model="filterDepartment"
                    x-on:change="filterType = ''"
                />

                <x-filter-select
                    name="filterType"
                    placeholder="Type"
                    :options="['Complaint', 'Inquiry', 'Request']"
                    x-model="filterType"
                />

                <x-filter-select
                    name="filterCategory"
                    placeholder="Category"
                    :options="$categoryOptions"
                />

                <x-filter-select
                    name="filterEditable"
                    placeholder="Editable/Not Editable"
                    :options="['Editable', 'Not Editable']"
                    x-model="filterEditable"
                />
            </div>

            <div class="flex justify-center w-full px-3">
                <button
                    wire:click="applyFilters"
                    class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                    <flux:icon.adjustments-horizontal class="w-4 h-4" />
                    <span wire:loading.remove wire:target="applyFilters">Apply Filters</span>
                    <span wire:loading wire:target="applyFilters">Processing...</span>
                </button>
            </div>
        </div>

    </header>

    <header class="relative w-full flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-2">

            <div class="flex w-full flex-1">

                <div class="relative w-full font-bold">
                    <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>

                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>

                        <input
                            type="text"
                            id="search"
                            wire:model.defer="searchInput"
                            wire:keydown.enter="applySearch"
                            placeholder="Search grievances..."
                            class="block w-full p-4 ps-10 pe-28 text-sm text-gray-900 border border-gray-300 rounded-lg
                                bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                dark:bg-zinc-800 dark:border-gray-600 dark:placeholder-gray-400
                                dark:text-white dark:focus:outline-none dark:focus:ring-2 dark:focus:ring-blue-400 dark:focus:border-blue-400"
                        />

                        <button
                            type="button"
                            wire:click="clearSearch"
                            class="absolute inset-y-0 right-28 flex items-center justify-center text-gray-500
                                hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors"
                        >
                            <flux:icon.x-mark class="w-4 h-4" />
                        </button>

                       <button
                            type="button"
                            wire:click="applySearch"
                            class="absolute inset-y-0 right-0 my-auto inline-flex items-center justify-center gap-2
                                px-4 py-2 text-sm font-semibold rounded-r-xl
                                text-white bg-gradient-to-r from-blue-600 to-blue-700
                                hover:from-blue-700 hover:to-blue-800
                                focus:outline-none focus:ring-0
                                shadow-sm hover:shadow-md transition-all duration-200"
                        >
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            <span>Search</span>
                        </button>
                    </div>
                </div>

            </div>

            <x-responsive-nav-link
                href="{{ route('citizen.grievance.create') }}"
                wire:navigate
                class="flex items-center justify-center sm:justify-start gap-2 p-4 text-sm font-bold rounded-lg
                    bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                    border border-blue-500 dark:border-blue-400
                    hover:bg-blue-200 dark:hover:bg-blue-800/50
                    focus:outline-none
                    focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                    transition-all duration-200 w-full sm:w-auto"
            >
                <flux:icon.document-plus class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                <span>File Grievance</span>
            </x-responsive-nav-link>

    </header>

    <div class="flex items-center justify-between gap-2 mb-4 px-3">

        @if(count($selected) > 0)
            <div class="flex flex-wrap gap-2">
                <button
                    wire:click="deleteSelected"
                    class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                        bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                        border border-red-500 dark:border-red-400
                        hover:bg-red-200 dark:hover:bg-red-800/50
                        focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                        transition-all duration-200 w-full sm:w-auto"
                >
                    <flux:icon.trash class="w-5 h-5 text-red-600 dark:text-red-400" />
                    <span>Delete Selected</span>
                </button>

                <button
                    wire:click="markSelectedHighPriority"
                    class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                        bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300
                        border border-amber-500 dark:border-amber-400
                        hover:bg-amber-200 dark:hover:bg-amber-800/50
                        focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-700
                        transition-all duration-200 w-full sm:w-auto"
                >
                    <flux:icon.document-check class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                    <span>Mark as High Priority</span>
                </button>
            </div>
        @endif
    </div>

   <div class="relative">
        <div class="w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">

            <div wire:loading.remove wire:target="applySearch, previousPage, nextPage, gotoPage, filterPriority, filterStatus, filterType, filterDate, filterCategory, filterDepartment, deleteSelected, markSelectedHighPriority, clearSearch">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center">
                                    <flux:checkbox wire:model.live="selectAll" id="select-all" />
                                </th>

                                <th wire:click="sortBy('grievance_id')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Ticket ID</span>

                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'grievance_id')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th wire:click="sortBy('grievance_title')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Title</span>

                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'grievance_title')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th wire:click="sortBy('grievance_type')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Type</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'grievance_type')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th wire:click="sortBy('grievance_category')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Category</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'grievance_category')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th wire:click="sortBy('departments.department_name')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Department</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'departments.department_name')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th scope="col" class="px-6 py-3">
                                    Identity Type
                                </th>

                                <th wire:click="sortBy('grievance_status')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Status</span>

                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'grievance_status')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th wire:click="sortBy('priority_level')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Priority</span>

                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'priority_level')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th wire:click="sortBy('created_at')" scope="col" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Date</span>

                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'created_at')
                                                @if($sortDirection === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-3 h-3 text-blue-500 dark:text-blue-400" />
                                                @endif
                                            @else
                                                <x-heroicon-s-chevron-up class="w-3 h-3 text-gray-400 dark:text-gray-500" />
                                                <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 dark:text-gray-500 -mt-0.5" />
                                            @endif
                                        </span>
                                    </div>
                                </th>

                                <th scope="col" class="px-6 py-3 text-center">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @forelse($grievances as $grievance)
                                <tr wire:key="grievance-{{ $grievance->grievance_id }}" class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">

                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <flux:checkbox wire:model.live="selected" value="{{ $grievance->grievance_id }}" />
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {!! $highlight($grievance->grievance_ticket_id, $search) !!}
                                    </td>

                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        {!! $highlight(Str::limit($grievance->grievance_title, 60), $search) !!}
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full border shadow-sm
                                            {{ match($grievance->grievance_type) {
                                                'Complaint' => 'bg-red-100 text-red-800 border-red-400 dark:bg-red-900/40 dark:text-red-300 dark:border-red-500',
                                                'Inquiry' => 'bg-blue-100 text-blue-800 border-blue-400 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-500',
                                                'Request' => 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500',
                                                default => 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600',
                                            } }}">
                                            {{ $grievance->grievance_type ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800 dark:text-gray-100 text-center">
                                        {{ $grievance->grievance_category ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800 dark:text-gray-100 text-center">
                                        @forelse ($grievance->departments->unique('department_id') as $department)
                                                {{ $department->department_name }}
                                        @empty
                                            <span class="text-gray-500 dark:text-gray-400 text-sm">—</span>
                                        @endforelse
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full border shadow-sm
                                            {{ $grievance->is_anonymous
                                                ? 'bg-yellow-100 text-yellow-800 border-yellow-400 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-500'
                                                : 'bg-emerald-100 text-emerald-800 border-emerald-400 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-500' }}">
                                            {{ $grievance->is_anonymous ? 'Anonymous' : 'Identified' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full border shadow-sm
                                            {{ match($grievance->grievance_status) {
                                                'pending' => 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600',
                                                'acknowledged' => 'bg-indigo-100 text-indigo-800 border-indigo-400 dark:bg-indigo-900/40 dark:text-indigo-300 dark:border-indigo-500',
                                                'in_progress' => 'bg-blue-100 text-blue-800 border-blue-400 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-500',
                                                'escalated' => 'bg-amber-100 text-amber-800 border-amber-400 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-500',
                                                'resolved' => 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500',
                                                'unresolved' => 'bg-red-100 text-red-800 border-red-400 dark:bg-red-900/40 dark:text-red-300 dark:border-red-500',
                                                'closed' => 'bg-purple-100 text-purple-800 border-purple-400 dark:bg-purple-900/40 dark:text-purple-300 dark:border-purple-500',
                                                'overdue' => 'bg-rose-100 text-rose-800 border-rose-400 dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-500',
                                                default => 'bg-gray-100 text-gray-800 border-gray-400 dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-600',
                                            } }}"
                                            >
                                                {!! $highlight(ucwords(str_replace('_', ' ', $grievance->grievance_status)), $search) !!}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full border shadow-sm
                                            {{ $grievance->priority_level === 'High'
                                                ? 'bg-red-100 text-red-800 border-red-400 dark:bg-red-900/40 dark:text-red-300 dark:border-red-500'
                                                : ($grievance->priority_level === 'Normal'
                                                    ? 'bg-amber-100 text-amber-800 border-amber-400 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-500'
                                                    : 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500') }}">
                                            {!! $highlight($grievance->priority_level, $search) !!}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $grievance->created_at->format('M d, Y h:i A') }}
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                                                <x-heroicon-o-ellipsis-horizontal class="w-6 h-6 text-black dark:text-white"/>
                                            </button>

                                            <div x-show="open" @click.away="open = false" x-transition
                                                class="absolute right-10 mt-2 w-44 bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 z-50">
                                                <div class="flex flex-col divide-y divide-gray-200 dark:divide-zinc-700">

                                                    <a
                                                        href="{{ route('citizen.grievance.view', $grievance) }}"
                                                        wire:navigate
                                                        class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium"
                                                    >
                                                        <x-heroicon-o-eye class="w-4 h-4 text-blue-500"/>
                                                        View
                                                    </a>

                                                    @php
                                                        $hasPermission = $grievance->editRequests()
                                                            ->where('user_id', auth()->id())
                                                            ->where('status', 'approved')
                                                            ->exists();

                                                        $pending = $grievance->editRequests()
                                                            ->where('user_id', auth()->id())
                                                            ->where('status', 'pending')
                                                            ->exists();
                                                    @endphp

                                                    @if ($hasPermission)
                                                        <a href="{{ route('citizen.grievance.edit', $grievance) }}" wire:navigate
                                                        class="px-4 py-2 flex hover:bg-gray-100 dark:hover:bg-zinc-800 items-center gap-2 text-sm font-medium">
                                                            <x-heroicon-o-pencil class="w-4 h-4 text-green-500"/> Edit
                                                        </a>
                                                    @elseif ($pending)
                                                        <span class="px-4 py-2 text-green-500 text-[12px] flex items-center gap-2 select-none">
                                                            Request Pending...
                                                            <span class="flex space-x-1">
                                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-bounce delay-0"></span>
                                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-bounce delay-150"></span>
                                                                <span class="w-2 h-2 bg-green-500 rounded-full animate-bounce delay-300"></span>
                                                            </span>
                                                        </span>
                                                    @else
                                                        <button wire:click="requestEditPermission({{ $grievance->grievance_id }})"
                                                            class="px-4 py-2 w-full hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium text-[10px]">
                                                            <x-heroicon-o-lock-open class="w-4 h-4 text-green-500"/> Request Edit Permission
                                                        </button>
                                                    @endif

                                                    <div x-data="{ showDeleteModal: false }">
                                                        <button @click="showDeleteModal = true"
                                                            class="px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm font-medium text-red-500">
                                                            <x-heroicon-o-trash class="w-4 h-4"/>
                                                            Delete
                                                        </button>

                                                        <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-50"></div>

                                                        <div x-show="showDeleteModal" x-transition.scale
                                                            class="fixed inset-0 flex items-center justify-center z-50 p-4">
                                                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 text-center space-y-5">
                                                                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                                                                    <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                                                                </div>
                                                                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Deletion</h2>
                                                                <p class="text-sm text-gray-600 dark:text-gray-300">Are you sure you want to delete this grievance? This action cannot be undone.</p>

                                                                <div wire:loading.remove wire:target="deleteGrievance({{ $grievance->grievance_id }})" class="flex justify-center gap-3 mt-4">
                                                                    <button type="button" @click="showDeleteModal = false"
                                                                        class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                                                        Cancel
                                                                    </button>
                                                                    <flux:button variant="danger" icon="trash" wire:click="deleteGrievance({{ $grievance->grievance_id }})">
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-archive-box-x-mark class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                        No grievances found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>


            <div wire:loading wire:target="applySearch, previousPage, nextPage, gotoPage, filterPriority, filterStatus, filterType, filterDate, filterCategory, filterDepartment, deleteSelected, markSelectedHighPriority, clearSearch"
                 class="overflow-x-auto w-full rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 animate-pulse">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-100 dark:bg-zinc-900">
                        <tr>
                            @for ($i = 0; $i < 8; $i++)
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <div class="h-3 bg-gray-300 dark:bg-zinc-700 rounded w-3/4"></div>
                                </th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @for ($row = 0; $row < 5; $row++)
                            <tr>
                                @for ($col = 0; $col < 8; $col++)
                                    <td class="px-4 py-3 align-middle">
                                        @if($col === 0)
                                            <div class="h-4 w-4 rounded bg-gray-200 dark:bg-zinc-700"></div>
                                        @else
                                            <div class="h-3 bg-gray-200 dark:bg-zinc-700 rounded w-full"></div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div x-data="{ showFeedbackModal: @entangle('showFeedbackModal') }">
                <div
                    x-show="showFeedbackModal"
                    x-transition.opacity
                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60]"
                >
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl w-[90%] sm:w-[32rem] overflow-hidden border border-gray-100 dark:border-zinc-700"
                    >
                        <div class="relative">
                            <img
                                src="{{ asset('/images/feedback-img.jpg') }}"
                                class="w-full h-48 sm:h-56 object-cover"
                                alt="Feedback Background"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        </div>

                        <div class="p-6 space-y-6 text-center">
                            <p class="text-base text-gray-800 dark:text-gray-200 font-medium">
                                Would you like to share your experience about the grievance process?
                            </p>

                            <div class="flex items-center justify-center gap-2">
                                <flux:checkbox wire:model="dontShowAgain" />
                                <flux:label class="text-sm text-gray-700 dark:text-gray-300">
                                    Don’t show this message again
                                </flux:label>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a
                                    href="{{ route('citizen.feedback-form') }}"
                                    wire:navigate
                                    class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                >
                                    Give Feedback
                                </a>
                                <button
                                    wire:click="closeFeedbackModal"
                                    class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-all duration-200"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4">
                {{ $grievances->links() }}
            </div>
        </div>
    </div>

</div>
