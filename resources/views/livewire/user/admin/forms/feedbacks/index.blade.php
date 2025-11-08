<div class="p-6 space-y-6 relative w-full">

    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">

        <div class="flex w-full md:w-1/2">
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

        <div class="flex gap-3 w-full md:w-auto">
            <x-filter-select
                name="filterDate"
                wire:model="filterDate"
                placeholder="Date"
                :options="['Show All', 'Today', 'Yesterday', 'This Week', 'This Month', 'This Year']"
            />

        </div>
    </div>

    <div class="relative">
        <div class="w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">

            <div wire:loading.remove wire:target="sortBy, previousPage, nextPage, gotoPage">
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

                                <th wire:click="sortBy('region')" class="px-6 py-3 cursor-pointer">
                                    <div class="flex items-center justify-between">
                                        <span>Region</span>
                                        <span class="w-2.5 h-full font-bold text-black dark:text-white">
                                            @if($sortField === 'region')
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
                                    <td class="px-6 py-4 text-sm font-medium text-center">{{ $feedback->region }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-center">{{ $feedback->email ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-center">
                                        <a href="{{ route('admin.forms.feedbacks.view', $feedback) }}" wire:navigate
                                            class="px-3 py-1 text-xs rounded-md border border-gray-300 text-gray-700 bg-gray-50 dark:bg-zinc-700 dark:text-gray-200">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-archive-box-x-mark class="w-6 h-6 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                        No feedbacks found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4">
                {{ $feedbacks->links() }}
            </div>
        </div>
    </div>
</div>
