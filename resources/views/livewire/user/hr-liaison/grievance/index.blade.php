<div class="p-6 space-y-6 flex-col w-full h-full"
     x-data
     data-component="hr-liaison-grievance-index"
     data-wire-id="{{ $this->id() }}"
     x-on:close-all-modals.window="
        document.querySelectorAll('[x-data][x-show]').forEach(el => {
            el.__x.$data.open = false
        })
     ">

    @php
        $highlight = fn($text, $search) => $search
            ? preg_replace(
                '/(' . preg_quote($search, '/') . ')/i',
                '<mark class="bg-blue-400 text-white dark:bg-blue-500 text-white">$1</mark>',
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
                >
                    <div class="w-full grid grid-cols-1 gap-4 mx-auto px-3 mb-6">
                        <div
                            class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2"
                        >
                            <div
                                class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"
                            ></div>

                            <div
                                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300"
                            >
                                <flux:icon.document-check class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Reports</p>
                            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                                {{ $totalGrievances }}
                            </p>
                        </div>
                    </div>

                    <div class="w-full grid grid-cols-2 md:grid-cols-4 gap-4 mx-auto px-3 mb-6">
                        <div
                            class="group relative bg-gradient-to-br from-red-50 to-red-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-red-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2"
                        >
                            <div
                                class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"
                            ></div>

                            <div
                                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-red-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300"
                            >
                                <flux:icon.exclamation-triangle class="h-8 w-8 text-red-600 dark:text-red-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Critical Priority</p>
                            <p class="relative text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">
                                {{ $criticalPriorityCount }}
                            </p>
                        </div>

                        <div
                            class="group relative bg-gradient-to-br from-red-50 to-red-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-red-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2"
                        >
                            <div
                                class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"
                            ></div>

                            <div
                                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-red-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300"
                            >
                                <flux:icon.exclamation-triangle class="h-8 w-8 text-red-600 dark:text-red-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">High Priority</p>
                            <p class="relative text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">
                                {{ $highPriorityCount }}
                            </p>
                        </div>

                        <div
                            class="group relative bg-gradient-to-br from-amber-50 to-yellow-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-amber-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2"
                        >
                            <div
                                class="absolute inset-0 rounded-2xl bg-gradient-to-br from-amber-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"
                            ></div>

                            <div
                                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-amber-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300"
                            >
                                <flux:icon.information-circle class="h-8 w-8 text-yellow-500 dark:text-yellow-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Normal Priority</p>
                            <p class="relative text-3xl font-bold text-yellow-500 dark:text-yellow-400 tracking-tight">
                                {{ $normalPriorityCount }}
                            </p>
                        </div>

                        <div
                            class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2"
                        >
                            <div
                                class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"
                            ></div>

                            <div
                                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300"
                            >
                                <flux:icon.arrow-down-circle class="h-8 w-8 text-green-600 dark:text-green-400" />
                            </div>

                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Low Priority</p>
                            <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">
                                {{ $lowPriorityCount }}
                            </p>
                        </div>
                    </div>

                    <div class="w-full grid grid-cols-4 md:grid-cols-8 gap-4 mx-auto px-3 mb-6">
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
                            class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-indigo-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-indigo-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.hand-raised class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Acknowledged</p>
                            <p class="relative text-3xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">{{ $acknowledgedCount }}</p>
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
                            class="group relative bg-gradient-to-br from-amber-50 to-orange-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-amber-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-amber-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-amber-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.arrow-up-on-square-stack class="h-8 w-8 text-amber-600 dark:text-amber-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Escalated</p>
                            <p class="relative text-3xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">{{ $escalatedCount }}</p>
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
                            class="group relative bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-gray-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-gray-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-gray-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.x-circle class="h-8 w-8 text-gray-500 dark:text-gray-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Unresolved</p>
                            <p class="relative text-3xl font-bold text-gray-500 dark:text-gray-400 tracking-tight">{{ $unresolvedCount }}</p>
                        </div>

                        <div
                            class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-purple-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-purple-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.lock-closed class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Closed</p>
                            <p class="relative text-3xl font-bold text-purple-600 dark:text-purple-400 tracking-tight">{{ $closedCount }}</p>
                        </div>

                        <div
                            class="group relative bg-gradient-to-br from-rose-50 to-rose-100 dark:from-zinc-800 dark:to-zinc-900
                                border border-rose-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                                transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-rose-200/20 to-transparent opacity-0
                                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
                            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-rose-200/50
                                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                                <flux:icon.x-circle class="h-8 w-8 text-rose-600 dark:text-rose-400" />
                            </div>
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Overdue</p>
                            <p class="relative text-3xl font-bold text-rose-600 dark:text-rose-400 tracking-tight">{{ $overdueCount }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <div
                x-data="{
                    filterType: '',
                }"
                class="grid grid-cols-2 lg:grid-cols-5 gap-3 w-full mx-auto px-3 my-2"
            >
                <x-filter-select
                    name="filterPriority"
                    placeholder="Priority"
                    :options="['Critical','High', 'Normal', 'Low']"
                />

                <x-filter-select
                    name="filterStatus"
                    placeholder="Status"
                    :options="['Show All', 'Pending', 'Acknowledged', 'In Progress', 'Escalated', 'Resolved', 'Unresolved', 'Closed', 'Overdue']"
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

                <x-date-picker
                    name="filterDate"
                    placeholder="Pick a date"
                    :model="'filterDate'"
                />
            </div>

            <div class="flex justify-center w-full px-3 mb-3">
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

    <div class="flex w-full flex-1 px-3 mb-3">

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
                    placeholder="Search reports..."
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

    <div
        x-data="{ openImportModal: false, openRerouteModal: false, openStatusModal: false, openPriorityModal: false }"
        x-on:reroute-success.window="openRerouteModal = false"
        x-on:status-update-success.window="openStatusModal = false"
        x-on:priority-update-success.window="openPriorityModal = false"
        class="flex flex-col w-full"
    >

        <div class="flex flex-wrap items-center justify-end gap-2 mb-2 px-3">
            <button
                wire:click="downloadReportsCsv"
                wire:loading.attr="disabled"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                    border border-blue-500 dark:border-blue-400
                    hover:bg-blue-200 dark:hover:bg-blue-800/50
                    focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                    transition-all duration-200">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                <span wire:loading.remove wire:target="downloadReportsCsv">Export All in CSV</span>
                <span wire:loading wire:target="downloadReportsCsv">Processing...</span>
            </button>

            <button
                wire:click="downloadAllGrievancesPdf"
                wire:loading.attr="disabled"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                    border border-red-500 dark:border-red-400
                    hover:bg-red-200 dark:hover:bg-red-800/50
                    focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                    transition-all duration-200">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                <span wire:loading.remove wire:target="downloadAllGrievancesPdf">Export All in PDF</span>
                <span wire:loading wire:target="downloadAllGrievancesPdf">Processing...</span>
            </button>

            <button
                wire:click="downloadReportsExcel"
                wire:loading.attr="disabled"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                    border border-green-500 dark:border-green-400
                    hover:bg-green-200 dark:hover:bg-green-800/50
                    focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                    transition-all duration-200">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                <span wire:loading.remove wire:target="downloadReportsExcel">Export All in Excel</span>
                <span wire:loading wire:target="downloadReportsExcel">Processing...</span>
            </button>

            <button
                @click="openImportModal = true; $wire.resetInputFieldsByCancelModal();"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                    border border-green-500 dark:border-green-400
                    hover:bg-green-200 dark:hover:bg-green-800/50
                    focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                    transition-all duration-200"
            >
                <x-heroicon-o-arrow-up-tray class="w-5 h-5" />
                <span wire:loading.remove wire:target="importReportsExcel">Import Reports</span>
                <span wire:loading wire:target="importReportsExcel">Processing...</span>
            </button>

            <button
                wire:click="printAllGrievances"
                wire:loading.attr="disabled"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-gray-100 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300
                    border border-gray-500 dark:border-gray-400
                    hover:bg-gray-200 dark:hover:bg-gray-800/50
                    focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-700
                    transition-all duration-200">
                <x-heroicon-o-printer class="w-4 h-4" />
                <span wire:loading.remove wire:target="printAllGrievances">Print All</span>
                <span wire:loading wire:target="printAllGrievances">Processing...</span>
            </button>

            <div
                x-data="{ progress: 0 }"
                x-show="openImportModal"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
                x-on:livewire-upload-start="progress = 5"
                x-on:livewire-upload-finish="progress = 100"
                x-on:livewire-upload-error="progress = 0"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
            >
                <div
                    @click.outside="openImportModal = false"
                    class="relative w-full max-w-md p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <x-heroicon-o-arrow-up-tray class="w-5 h-5 text-green-600 dark:text-green-400" />
                            Import Reports
                        </h2>
                        <button
                            @click="openImportModal = false"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
                            aria-label="Close"
                        >
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <div wire:loading wire:target="resetInputFieldsByCancelModal">
                            <div class="w-full flex items-center justify-center gap-2 py-6">
                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                                <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                            </div>
                        </div>

                        <div wire:loading.remove wire:target="resetInputFieldsByCancelModal">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                Upload an Excel file exported from the system. This will import reports into the database.
                            </p>

                            <flux:input
                                type="file"
                                wire:model="importFile"
                                label="Select Excel File"
                                accept=".xlsx,.xls"
                                required
                            />

                            <div class="space-y-1 mt-2">
                                <flux:error name="importFile" />
                            </div>

                            <div x-show="progress > 0 && progress < 100" class="mt-4">
                                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                                    <div
                                        class="h-2 bg-green-600 dark:bg-green-400 transition-all"
                                        x-bind:style="'width: ' + progress + '%'"
                                    ></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-300 mt-1" x-text="progress + '%'"></span>
                            </div>

                            <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

                            <div class="flex justify-end gap-3">
                                <button
                                    @click="openImportModal = false"
                                    wire:click="$set('importFile', null)"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-300 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition"
                                >
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                    Cancel
                                </button>

                                <button
                                    wire:click="importReportsExcel"
                                    @click="openImportModal = false"
                                    wire:loading.attr="disabled"
                                    wire:target="importFile"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 transition"
                                >
                                    <x-heroicon-o-check class="w-4 h-4" />
                                    <span wire:loading.remove wire:target="importFile">Import</span>
                                    <span wire:loading wire:target="importFile">Uploading...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="flex items-center justify-between gap-2 mb-4 px-3">

            @if(count($selected) > 0)
                <div class="flex flex-wrap gap-2">
                     <button wire:click="exportSelectedReportsCsv"
                        wire:loading.attr="disabled"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                            border border-blue-500 dark:border-blue-400
                            hover:bg-blue-200 dark:hover:bg-blue-800/50
                            focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                            transition-all duration-200">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        <span wire:loading.remove wire:target="exportSelectedReportsCsv">Export Selected in CSV</span>
                        <span wire:loading wire:target="exportSelectedReportsCsv">Processing...</span>
                    </button>

                    <button wire:click="downloadSelectedGrievancesPdf"
                        wire:loading.attr="disabled"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                            border border-red-500 dark:border-red-400
                            hover:bg-red-200 dark:hover:bg-red-800/50
                            focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                            transition-all duration-200">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        <span wire:loading.remove wire:target="downloadSelectedGrievancesPdf">Export Selected in PDF</span>
                        <span wire:loading wire:target="downloadSelectedGrievancesPdf">Processing...</span>
                    </button>

                    <button wire:click="exportSelectedReportsExcel"
                        wire:loading.attr="disabled"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                            border border-green-500 dark:border-green-400
                            hover:bg-green-200 dark:hover:bg-green-800/50
                            focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                            transition-all duration-200">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        <span wire:loading.remove wire:target="exportSelectedReportsExcel">Export Selected in Excel</span>
                        <span wire:loading wire:target="exportSelectedReportsExcel">Processing...</span>
                    </button>

                    <button wire:click="printSelectedGrievances"
                        wire:loading.attr="disabled"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-gray-100 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300
                            border border-gray-500 dark:border-gray-400
                            hover:bg-gray-200 dark:hover:bg-gray-800/50
                            focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-700
                            transition-all duration-200">
                        <x-heroicon-o-printer class="w-5 h-5" />
                        <span wire:loading.remove wire:target="printSelectedGrievances">Print Selected</span>
                        <span wire:loading wire:target="printSelectedGrievances">Processing...</span>
                    </button>

                    <button
                        @click="openRerouteModal = true; $wire.resetInputFieldsByCancelModal();"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                            border border-blue-500 dark:border-blue-400
                            hover:bg-blue-200 dark:hover:bg-blue-800/50
                            focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                            transition-all duration-200">
                        <x-heroicon-o-arrow-path class="w-5 h-5" />
                        <span>Reroute Selected</span>
                    </button>

                    <button
                        @click="openStatusModal = true; $wire.resetInputFieldsByCancelModal();"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300
                            border border-yellow-500 dark:border-yellow-400
                            hover:bg-yellow-200 dark:hover:bg-yellow-800/50
                            focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-700
                            transition-all duration-200">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                        <span>Update Selected Status</span>
                    </button>

                    <button
                        @click="openPriorityModal = true; $wire.resetInputFieldsByCancelModal();"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                            border border-red-500 dark:border-red-400
                            hover:bg-red-200 dark:hover:bg-red-800/50
                            focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                            transition-all duration-200">
                        <x-heroicon-o-exclamation-circle class="w-5 h-5" />
                        <span>Update Selected Status</span>
                    </button>

                </div>
            @endif
        </div>

        <div
            x-show="openRerouteModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
        >
            <div
                @click.outside="openRerouteModal = false"
                class="relative w-full max-w-md p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-heroicon-o-arrow-path class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        Reroute Selected Reports
                    </h2>
                    <button
                        @click="openRerouteModal = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
                        aria-label="Close"
                    >
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex flex-col items-center justify-center">
                    <div wire:loading wire:target="resetInputFieldsByCancelModal">
                        <div class="w-full flex items-center justify-center gap-2 py-6">
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="resetInputFieldsByCancelModal">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                            Please select a department and category to reroute all selected reports.
                        </p>

                        <div
                            x-data="{
                                department: @entangle('department'),
                                grievanceCategory: @entangle('grievance_category'),

                                categoriesMap: {
                                    'Business Permit and Licensing Office': [
                                        'Delayed Business Permit Processing',
                                        'Unclear Requirements or Procedures',
                                        'Unfair Treatment by Personnel',
                                        'Business Permit Requirements Inquiry',
                                        'Renewal Process Clarification',
                                        'Schedule or Fee Inquiry',
                                        'Document Correction or Update Request',
                                        'Business Record Verification Request',
                                        'Appointment or Processing Schedule Request'
                                    ],
                                    'Traffic Enforcement Agency of Mandaue': [
                                        'Traffic Enforcer Misconduct',
                                        'Unjust Ticketing or Penalty',
                                        'Inefficient Traffic Management',
                                        'Traffic Rules Clarification',
                                        'Citation or Violation Inquiry',
                                        'Inquiry About Traffic Assistance',
                                        'Request for Traffic Assistance',
                                        'Request for Event Traffic Coordination',
                                        'Request for Violation Review'
                                    ],
                                    'City Social Welfare Services': [
                                        'Discrimination or Neglect in Assistance',
                                        'Delayed Social Service Response',
                                        'Unprofessional Staff Behavior',
                                        'Assistance Program Inquiry',
                                        'Eligibility or Requirements Clarification',
                                        'Social Service Schedule Inquiry',
                                        'Request for Social Assistance',
                                        'Financial Aid or Program Enrollment Request',
                                        'Home Visit or Consultation Request'
                                    ]
                                },

                                get categoryOptions() {
                                    return this.department ? this.categoriesMap[this.department] || [] : [];
                                }
                            }"
                            class="flex flex-col gap-6"
                        >
                            <div class="flex flex-col gap-2">
                                <label class="font-medium text-gray-900 dark:text-gray-100">Department</label>
                                <x-searchable-select
                                    name="department"
                                    placeholder="Select department"
                                    :options="$departmentOptions"
                                    x-on:change="grievanceCategory = ''; $wire.set('category', '', true)"
                                />
                                <flux:error name="department" />
                            </div>

                            <div x-show="department" x-cloak>
                                <div class="flex flex-col gap-2">
                                    <label class="flex gap-2 items-center font-medium text-gray-900 dark:text-white">
                                        <flux:icon.list-bullet />
                                        <span>Category</span>
                                    </label>

                                    <h3 class="text-sm text-gray-700 dark:text-gray-300">
                                        Choose a category based on the selected department.
                                    </h3>

                                    <div class="relative !cursor-pointer" x-data="{ open: false, search: '' }">
                                        <flux:input
                                            readonly
                                            x-model="grievanceCategory"
                                            placeholder="Select grievance category"
                                            @click="open = !open"
                                            class:input="border rounded-lg w-full cursor-pointer select-none"
                                        />

                                        <div
                                            x-show="open"
                                            @click.outside="open = false"
                                            x-transition
                                            class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-900 ring-1 ring-gray-200 dark:ring-zinc-700 rounded-md shadow-md"
                                        >
                                            <div class="p-1 border-b border-gray-200 dark:border-zinc-700 flex items-center gap-2">
                                                <flux:icon.magnifying-glass class="text-gray-500 dark:text-zinc-400" />
                                                <input
                                                    type="text"
                                                    x-model="search"
                                                    placeholder="Search..."
                                                    class="w-full bg-transparent border-none focus:ring-0 focus:outline-none text-sm"
                                                />
                                            </div>

                                            <ul class="max-h-48 overflow-y-auto py-1">
                                                <template x-for="opt in categoryOptions.filter(o => o.toLowerCase().includes(search.toLowerCase()))" :key="opt">
                                                    <li>
                                                        <button
                                                            type="button"
                                                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-zinc-800"
                                                            @click="
                                                                grievanceCategory = opt;
                                                                $wire.set('category', opt, true);
                                                                open = false;
                                                                search = '';
                                                            "
                                                            x-text="opt"
                                                        ></button>
                                                    </li>
                                                </template>

                                                <li
                                                    x-show="categoryOptions.filter(o => o.toLowerCase().includes(search.toLowerCase())).length === 0"
                                                    class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    No results found
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <flux:error name="category" />
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

                        <div class="flex justify-end gap-3">
                            <button
                                @click="openRerouteModal = false"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-300 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition"
                            >
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                Cancel
                            </button>

                            <button
                                wire:click="rerouteSelectedGrievances"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
                            >
                                <x-heroicon-o-arrow-path class="w-4 h-4" />
                                <span wire:loading.remove wire:target="rerouteSelectedGrievances">Reroute</span>
                                <span wire:loading wire:target="rerouteSelectedGrievances">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div
            x-show="openStatusModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
        >
            <div
                @click.outside="openStatusModal = false"
                class="relative w-full max-w-md p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-heroicon-o-pencil-square class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                        Update Selected Report Status
                    </h2>
                    <button
                        @click="openStatusModal = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
                        aria-label="Close"
                    >
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex flex-col justify-center items-center">
                    <div wire:loading wire:target="resetInputFieldsByCancelModal">
                        <div class="w-full flex items-center justify-center gap-2 py-6">
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                        </div>
                    </div>
                    <div wire:loading.remove wire:target="resetInputFieldsByCancelModal">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                            Choose a new status to apply to all selected reports.
                        </p>

                        <div class="flex flex-col gap-2 mb-2">
                            <x-searchable-select
                                name="status"
                                placeholder="Select Status"
                                :options="[
                                    'pending' => 'Pending',
                                    'acknowledged' => 'Acknowledged',
                                    'in_progress' => 'In Progress',
                                    'escalated' => 'Escalated',
                                    'resolved' => 'Resolved',
                                    'unresolved' => 'Unresolved',
                                    'closed' => 'Closed',
                                ]"
                            />
                            <div class="space-y-1">
                                <flux:error name="status" />
                                <flux:error name="selected" />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

                        <div class="flex justify-end gap-3">
                            <button
                                @click="openStatusModal = false"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-300 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition"
                            >
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                Cancel
                            </button>

                            <button
                                wire:click="updateSelectedGrievanceStatus"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 disabled:opacity-50 transition"
                            >
                                <x-heroicon-o-check class="w-4 h-4" />
                                <span wire:loading.remove wire:target="updateSelectedGrievanceStatus">Update</span>
                                <span wire:loading wire:target="updateSelectedGrievanceStatus">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div
            x-show="openPriorityModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
        >
            <div
                @click.outside="openPriorityModal = false"
                class="relative w-full max-w-md p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400" />
                        Update Selected Report Priority
                    </h2>
                    <button
                        @click="openPriorityModal = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
                        aria-label="Close"
                    >
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex flex-col items-center justify-center">
                    <div wire:loading wire:target="resetInputFieldsByCancelModal">
                        <div class="w-full flex items-center justify-center gap-2 py-6">
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                            <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                        </div>
                    </div>

                    <div wire:loading.remove wire:target="resetInputFieldsByCancelModal">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                            Choose a new priority level to apply to all selected reports.
                        </p>

                        <div class="flex flex-col gap-2 mb-2">
                            <x-searchable-select
                                name="priorityUpdate"
                                placeholder="Select Priority"
                                :options="[
                                    'low' => 'Low',
                                    'normal' => 'Normal',
                                    'high' => 'High',
                                    'critical' => 'Critical',
                                ]"
                            />
                            <div class="space-y-1">
                                <flux:error name="priorityUpdate" />
                                <flux:error name="selected" />
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

                        <div class="flex justify-end gap-3">
                            <button
                                @click="openPriorityModal = false"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-300 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition"
                            >
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                Cancel
                            </button>

                            <button
                                wire:click="updateSelectedPriority"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 transition"
                            >
                                <x-heroicon-o-check class="w-4 h-4" />
                                <span wire:loading.remove wire:target="updateSelectedPriority">Update</span>
                                <span wire:loading wire:target="updateSelectedPriority">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="relative">
        <div class="w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">

            <div wire:loading.remove wire:target="applySearch, previousPage, nextPage, gotoPage, clearSearch">
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

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold">
                                            {{ $grievance->grievance_category ?? '—' }}
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
                                        @php
                                            $priorityClasses = match($grievance->priority_level) {
                                                'Critical' => 'bg-red-200 text-red-900 border-red-500 dark:bg-red-900/60 dark:text-red-300 dark:border-red-600',
                                                'High'     => 'bg-red-100 text-red-800 border-red-400 dark:bg-red-900/40 dark:text-red-300 dark:border-red-500',
                                                'Normal'   => 'bg-amber-100 text-amber-800 border-amber-400 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-500',
                                                default    => 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900/40 dark:text-green-300 dark:border-green-500',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded-full border shadow-sm {{ $priorityClasses }}">
                                            {!! $highlight($grievance->priority_level, $search) !!}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-800 dark:text-gray-100">
                                        {{ $grievance->created_at->format('M d, Y h:i A') }}
                                    </td>

                                    <td class="px-6 py-4 text-center space-x-1">
                                        <a href="{{ route('hr-liaison.grievance.view', $grievance) }}" wire:navigate
                                        class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 dark:bg-zinc-700 dark:text-gray-200">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-archive-box-x-mark class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                        No reports found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

            <div wire:loading wire:target="applySearch, previousPage, nextPage, gotoPage, clearSearch"
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

            <div class="p-4">
                {{ $grievances->links() }}
            </div>
        </div>
    </div>

</div>
