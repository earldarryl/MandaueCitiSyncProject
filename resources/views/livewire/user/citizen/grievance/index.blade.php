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
                    wire:poll.10s="updateStats"
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
                            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Rejected</p>
                            <p class="relative text-3xl font-bold text-gray-500 dark:text-gray-400 tracking-tight">{{ $rejectedCount }}</p>
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
                                bg-gray-50 focus:ring-blue-500 focus:border-blue-500
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400
                                dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
                            class="absolute inset-y-0 right-0 my-auto inline-flex items-center gap-2
                                px-4 py-2 text-sm font-bold rounded-lg
                                bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                                border border-blue-400 dark:border-blue-600
                                hover:bg-blue-200 dark:hover:bg-blue-800/50
                                focus:outline-none focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-700
                                transition-all duration-200"
                        >
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            Search
                        </button>
                    </div>
                </div>

            </div>

            <x-responsive-nav-link
                href="{{ route('citizen.grievance.create') }}"
                wire:navigate
                class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                    border border-blue-500 dark:border-blue-400
                    hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-all duration-200 w-full sm:w-auto"
            >
                <flux:icon.document-plus class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                <span>File Grievance</span>
            </x-responsive-nav-link>

    </header>

    <div class="flex items-center justify-between gap-2 mb-4 px-3">
        <div class="flex items-center gap-2">
            <flux:checkbox wire:model.live="selectAll" id="select-all"/>
            <flux:label>Select All</flux:label>
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                wire:click="deleteSelected"
                class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                    border border-red-500 dark:border-red-400
                    hover:bg-red-200 dark:hover:bg-red-800/50 transition-all duration-200 w-full sm:w-auto"
            >
                <flux:icon.trash class="w-5 h-5 text-red-600 dark:text-red-400" />
                <span>Delete Selected</span>
            </button>

            <button
                wire:click="markSelectedHighPriority"
                class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300
                    border border-amber-500 dark:border-amber-400
                    hover:bg-amber-200 dark:hover:bg-amber-800/50 transition-all duration-200 w-full sm:w-auto"
            >
                <flux:icon.document-check class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                <span>Mark as High Priority</span>
            </button>
        </div>

    </div>

    <div class="relative">
        <!-- Grid -->
        <div class="flex w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-6 w-full"
                 wire:loading.remove wire:target="applySearch, previousPage, nextPage, gotoPage, filterPriority, filterStatus, filterType, filterDate, bulkDelete, bulkMarkHigh, clearSearch"
             >

                @forelse ($grievances as $grievance)
                    <div
                        wire:key="grievance-{{ $grievance->grievance_id }}"
                        x-data
                        class="cursor-pointer rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800 flex flex-col p-5 h-[350px]
                            transition-transform duration-300 ease-in-out hover:scale-[1.03] hover:shadow-lg active:scale-[0.98]">

                        <div class="flex flex-col flex-1 justify-between">

                            <!-- Header -->
                            <header class="flex justify-between items-start mb-3">
                                <div class="flex items-start gap-2">
                                    <div class="flex flex-col max-w-[250px]">
                                        <h2
                                            class="text-lg font-semibold text-gray-800 dark:text-gray-100 capitalize truncate"
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
                                    <!-- Checkbox beside Priority -->
                                    <flux:checkbox wire:model.live="selected" value="{{ $grievance->grievance_id }}" />

                                    <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border
                                        {{ $grievance->priority_level === 'High'
                                            ? 'bg-red-100 text-red-800 border-red-400 dark:bg-gray-700 dark:text-red-400'
                                            : ($grievance->priority_level === 'Normal'
                                                ? 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-gray-700 dark:text-yellow-300'
                                                : 'bg-green-100 text-green-800 border-green-400 dark:bg-gray-700 dark:text-green-400') }}">
                                        {!! $highlight($grievance->priority_level, $search) !!}
                                    </span>

                                    <flux:dropdown>
                                        <flux:button
                                            icon="ellipsis-horizontal"
                                            class="!p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition"
                                        />

                                        <flux:menu>

                                            <flux:menu.item>
                                                <button
                                                    wire:click="downloadPdf({{ $grievance->grievance_id }})"
                                                    class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                                                        bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                                                        hover:bg-green-200 dark:hover:bg-green-800/50
                                                        border border-green-300 dark:border-green-700
                                                        transition-all duration-200 w-full sm:w-52"
                                                >
                                                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-green-600 dark:text-green-400" />
                                                    <span class="hidden lg:inline">Download PDF</span>
                                                    <span class="lg:hidden">Download</span>
                                                </button>
                                            </flux:menu.item>

                                            <flux:menu.item>
                                                <button
                                                    wire:click="print({{ $grievance->grievance_id }})"
                                                    class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                                                        bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300
                                                        hover:bg-purple-200 dark:hover:bg-purple-800/50
                                                        border border-purple-300 dark:border-purple-700
                                                        transition-all duration-200 w-full sm:w-52"
                                                >
                                                    <x-heroicon-o-printer class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                                    <span class="hidden lg:inline">Print</span>
                                                    <span class="lg:hidden">Print</span>
                                                </button>
                                            </flux:menu.item>

                                        </flux:menu>
                                    </flux:dropdown>

                                </div>
                            </header>

                            <!-- Details -->
                            <div class="text-sm bg-gray-200 dark:bg-zinc-700 p-5 text-gray-600 rounded-xl dark:text-gray-300 prose dark:prose-invert overflow-y-auto flex-1">
                                {!! $highlight(Str::limit($grievance->grievance_details, 150), $search) !!}
                            </div>

                            <!-- Footer -->
                            <footer class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200 dark:border-zinc-700">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $grievance->created_at->format('M d, Y') }}
                                </div>

                               <div class="flex items-center gap-2">
                                    <!-- View -->
                                    <a href="{{ route('citizen.grievance.view', $grievance->grievance_id) }}"
                                        wire:navigate
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md border border-gray-300 text-gray-700 bg-gray-50
                                            hover:bg-gray-100 hover:border-gray-400
                                            dark:bg-zinc-700 dark:text-gray-200 dark:border-zinc-600 dark:hover:bg-zinc-600 dark:hover:border-zinc-500
                                            transition">
                                        View
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('citizen.grievance.edit', $grievance->grievance_id) }}"
                                        wire:navigate
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md border border-blue-300 text-blue-700 bg-blue-50
                                            hover:bg-blue-100 hover:border-blue-400
                                            dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-800 dark:hover:bg-blue-800/50 dark:hover:border-blue-700
                                            transition">
                                        Edit
                                    </a>

                                    <!-- Delete -->
                                    <button
                                        @click="$dispatch('open-delete-modal-{{ $grievance->grievance_id }}')"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md border border-red-300 text-red-700 bg-red-50
                                            hover:bg-red-100 hover:border-red-400
                                            dark:bg-red-900/40 dark:text-red-300 dark:border-red-800 dark:hover:bg-red-800/50 dark:hover:border-red-700
                                            transition">
                                        Delete
                                    </button>
                                </div>
                            </footer>
                        </div>
                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-500 dark:text-gray-400">No grievances found.</p>
                @endforelse

            </div>

            @foreach ($grievances as $grievance)
                <div
                    x-data="{ showModal: false }"
                    x-on:open-delete-modal-{{ $grievance->grievance_id }}.window="showModal = true"
                    x-on:close-delete-modal-{{ $grievance->grievance_id }}.window="showModal = false"
                    x-on:close-all-modals.window="showModal = false"
                    x-show="showModal"
                    x-cloak
                    class="fixed inset-0 flex items-center justify-center z-50"
                >
                    <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-black/50"></div>
                    <div x-show="showModal" x-transition.scale
                        class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 text-center space-y-5 z-50">

                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                            <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                        </div>

                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                            Confirm Deletion
                        </h2>

                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Are you sure you want to delete this grievance? This action cannot be undone.
                        </p>

                        <div wire:loading.remove wire:target="deleteGrievance({{ $grievance->grievance_id }})">
                            <div class="flex items-center justify-center w-full gap-3 mt-4">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                >
                                    Cancel
                                </button>
                                <flux:button
                                    variant="danger"
                                    icon="trash"
                                    wire:click="deleteGrievance({{ $grievance->grievance_id }})"
                                >
                                    Yes, Delete
                                </flux:button>
                            </div>
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
            @endforeach
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
