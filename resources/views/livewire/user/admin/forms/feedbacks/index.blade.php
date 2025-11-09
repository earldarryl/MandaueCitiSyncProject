<div class="p-6 space-y-6 relative w-full">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 px-6">

        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-document-text class="h-8 w-8 text-blue-600 dark:text-blue-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Feedbacks</p>
            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                {{ $totalFeedbacks }}
            </p>
        </div>

        <div class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
            border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-clipboard-document-check class="h-8 w-8 text-green-600 dark:text-green-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">CC Summary</p>
            <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">
                {{ $mostCommonCC ?? 'N/A' }}
            </p>
        </div>

        <div class="group relative bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-zinc-800 dark:to-zinc-900
            border border-yellow-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-yellow-200/20 to-transparent opacity-0
                group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-yellow-200/50
                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-signal class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">SQD Summary</p>
            <p class="relative text-3xl font-bold text-yellow-600 dark:text-yellow-400 tracking-tight">
                {{ $mostCommonSQD ?? 'N/A' }}
            </p>
        </div>

    </div>


    <div class="flex flex-col items-center justify-center gap-4 mb-6">
        <div class="flex gap-3 w-full">
            <x-filter-select
                name="filterDate"
                wire:model="filterDate"
                placeholder="Date"
                :options="['Show All', 'Today', 'Yesterday', 'This Week', 'This Month', 'This Year']"
            />

            <x-filter-select
                name="filterSQD"
                wire:model="filterSQD"
                placeholder="Filter by SQD Summary"
                :options="['All', 'Most Agree', 'Most Disagree']"
            />

            <x-filter-select
                name="filterCC"
                wire:model="filterCC"
                placeholder="Filter by CC Summary"
                :options="['All', 'Strong Awareness', 'Moderate Awareness', 'Low Awareness', 'No Awareness']"
            />

        </div>
        <div class="flex justify-center w-full px-3 mb-3">
            <button
                wire:click="applyFilters"
                class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                <flux:icon.adjustments-horizontal class="w-4 h-4" />
                <span>Apply Filters</span>
            </button>
        </div>

    </div>

    <div class="flex w-full">
        <div class="relative w-full font-bold">
            <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>

            <div class="relative w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                </div>

                <input
                    type="text"
                    id="search"
                    wire:model.defer="searchInput"
                    wire:keydown.enter="applySearch"
                    placeholder="Search feedbacks..."
                    class="block w-full p-4 ps-10 pe-28 text-sm text-gray-900 border border-gray-300 rounded-lg
                        bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                        dark:bg-zinc-800 dark:border-gray-600 dark:text-white"
                />

                <button type="button" wire:click="clearSearch"
                    class="absolute inset-y-0 right-28 flex items-center justify-center text-gray-500 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">
                    <flux:icon.x-mark class="w-4 h-4" />
                </button>

                <button type="button" wire:click="applySearch"
                    class="absolute inset-y-0 right-0 my-auto inline-flex items-center justify-center gap-2
                        px-4 py-2 text-sm font-semibold rounded-r-xl
                        text-white bg-gradient-to-r from-blue-600 to-blue-700
                        hover:from-blue-700 hover:to-blue-800
                        shadow-sm hover:shadow-md transition-all duration-200">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    <span>Search</span>
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col w-full">
        <div class="flex items-center justify-end gap-2 mb-2 px-3">
            <button
                wire:click="downloadAllFeedbacksCsv"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                    border border-green-500 dark:border-green-400
                    hover:bg-green-200 dark:hover:bg-green-800/50
                    focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                    transition-all duration-200">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                <span>Export All in CSV</span>
            </button>

            <button
                wire:click="printAllFeedbacks"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-gray-100 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300
                    border border-gray-500 dark:border-gray-400
                    hover:bg-gray-200 dark:hover:bg-gray-800/50
                    focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-700
                    transition-all duration-200">
                <x-heroicon-o-printer class="w-4 h-4" />
                Print All
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between gap-2 mb-4 px-3">

        @if(count($selected) > 0)
            <div class="flex flex-wrap gap-2">
                <button wire:click="downloadAllFeedbacksCsv"
                    class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                        bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                        border border-green-500 dark:border-green-400
                        hover:bg-green-200 dark:hover:bg-green-800/50
                        focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                        transition-all duration-200">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                    <span>Export Selected</span>
                </button>

                <button wire:click="printSelectedFeedbacks"
                    class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                        bg-gray-100 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300
                        border border-gray-500 dark:border-gray-400
                        hover:bg-gray-200 dark:hover:bg-gray-800/50
                        focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-700
                        transition-all duration-200">
                    <x-heroicon-o-printer class="w-5 h-5" />
                    <span>Print Selected</span>
                </button>
            </div>
        @endif
    </div>

    <div class="relative">
        <div class="w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">

            <div wire:loading.remove wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch, applyFilters">
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3 text-center">
                                    <flux:checkbox wire:model.live="selectAll" id="select-all" />
                                </th>

                                <th wire:click="sortBy('date')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Date</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'date')
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

                                <th wire:click="sortBy('gender')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Gender</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'gender')
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

                                <th wire:click="sortBy('email')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Email</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'email')
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

                                <th wire:click="sortBy('cc_summary')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>CC Summary</span>
                                        <span>
                                            @if($sortField === 'cc_summary')
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

                                <th wire:click="sortBy('sqd_summary')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>SQD Summary</span>
                                        <span>
                                            @if($sortField === 'sqd_summary')
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

                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                            @forelse($feedbacks as $feedback)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                                    <td class="px-6 py-4 text-center">
                                        <flux:checkbox wire:model.live="selected" value="{{ $feedback->id }}" />
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $feedback->date->format('M d, Y h:i A') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-center">{{ $feedback->gender }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-center">{{ $feedback->email ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        {{ $this->summarizeCC($feedback) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        {{ $this->summarizeSQD($feedback->answers) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-center">
                                        <a href="{{ route('admin.forms.feedbacks.view', $feedback) }}" wire:navigate
                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 dark:bg-zinc-700 dark:text-gray-200">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-archive-box-x-mark class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                        No feedbacks found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div wire:loading wire:target="previousPage, nextPage, gotoPage, applySearch, clearSearch, applyFilters"
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
                                @for ($col = 0; $col < 6; $col++)
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
                {{ $feedbacks->links() }}
            </div>
        </div>
    </div>
</div>
