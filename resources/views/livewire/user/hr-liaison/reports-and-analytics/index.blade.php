<div class="relative w-full bg-white dark:bg-gray-900 rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">
        Reports & Analytics
    </h2>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="w-full flex flex-col bg-gray-50 dark:bg-gray-900 relative p-3 rounded-lg shadow-sm"
             x-data="{ start: @entangle('startDate'), end: @entangle('endDate') }"
             x-init="$nextTick(() => {
                 flatpickr($refs.startInput, {
                     dateFormat: 'Y-m-d',
                     defaultDate: start,
                     onChange: (selectedDates, dateStr) => $wire.set('startDate', dateStr)
                 });
                 flatpickr($refs.endInput, {
                     dateFormat: 'Y-m-d',
                     defaultDate: end,
                     onChange: (selectedDates, dateStr) => $wire.set('endDate', dateStr)
                 });
             })"
        >
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 w-full px-4">
                <!-- Start Date -->
                <div class="flex flex-col gap-2 w-full md:w-1/3 cursor-pointer">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-calendar class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                        <span>Start Date</span>
                    </div>
                    <flux:input type="text" x-ref="startInput" readonly x-model="start" class="w-full cursor-pointer font-bold" />
                </div>

                <!-- End Date -->
                <div class="flex flex-col gap-2 w-full md:w-1/3 cursor-pointer">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-calendar class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                        <span>End Date</span>
                    </div>
                    <flux:input type="text" x-ref="endInput" readonly x-model="end" class="w-full cursor-pointer font-bold" />
                </div>

                <!-- Category -->
                <div class="flex flex-col gap-2 w-full md:w-1/3 cursor-pointer">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                        <span>Category</span>
                    </div>
                    <x-filter-select
                        name="category"
                        placeholder="Select Category"
                        :options="['all' => 'All Categories'] + collect($categories)->mapWithKeys(fn($c) => [$c => ucfirst(str_replace('_',' ',$c))])->toArray()"
                        wire:model="category"
                    />
                </div>
            </div>

            <div class="px-4">
                <button
                    type="button"
                    class="flex gap-2 items-center justify-center font-bold w-full bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 transition duration-300 ease-in-out text-white p-3 rounded-lg"
                    wire:click="applyFilters"
                    wire:loading.attr="disabled"
                    wire:target="applyFilters"
                >
                    <x-heroicon-o-check class="w-5 h-5"/>
                    <span>Apply</span>

                    <svg wire:loading wire:target="applyFilters" class="animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto relative rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
        <!-- Loading skeleton -->
        <div wire:loading.flex wire:target="applyFilters" class="absolute inset-0 items-center justify-center bg-white/70 dark:bg-gray-900/70 z-10">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        @for ($i = 0; $i < 5; $i++)
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <div class="h-3 bg-gray-300 dark:bg-zinc-700 rounded w-3/4"></div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @for ($row = 0; $row < 5; $row++)
                        <tr>
                            @for ($col = 0; $col < 5; $col++)
                                <td class="px-4 py-3 align-middle">
                                    <div class="h-3 bg-gray-200 dark:bg-zinc-700 rounded w-full"></div>
                                </td>
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Actual table -->
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 relative">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <!-- Ticket ID -->
                    <th wire:click="sortBy('grievance_ticket_id')"
                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-hashtag class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <span>Ticket ID</span>
                            <span class="ml-auto">
                                @if($sortField === 'grievance_ticket_id')
                                    @if($sortDirection === 'asc')
                                        <x-heroicon-s-chevron-up class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @else
                                        <x-heroicon-s-chevron-down class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </th>

                    <!-- Title -->
                    <th wire:click="sortBy('grievance_title')"
                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <span>Title</span>
                            <span class="ml-auto">
                                @if($sortField === 'grievance_title')
                                    @if($sortDirection === 'asc')
                                        <x-heroicon-s-chevron-up class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @else
                                        <x-heroicon-s-chevron-down class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </th>

                    <!-- Category -->
                    <th wire:click="sortBy('grievance_category')"
                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-list-bullet class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <span>Category</span>
                            <span class="ml-auto">
                                @if($sortField === 'grievance_category')
                                    @if($sortDirection === 'asc')
                                        <x-heroicon-s-chevron-up class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @else
                                        <x-heroicon-s-chevron-down class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </th>

                    <!-- Status -->
                    <th wire:click="sortBy('grievance_status')"
                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-flag class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <span>Status</span>
                            <span class="ml-auto">
                                @if($sortField === 'grievance_status')
                                    @if($sortDirection === 'asc')
                                        <x-heroicon-s-chevron-up class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @else
                                        <x-heroicon-s-chevron-down class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </th>

                    <!-- Date -->
                    <th wire:click="sortBy('created_at')"
                        class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <span>Date</span>
                            <span class="ml-auto">
                                @if($sortField === 'created_at')
                                    @if($sortDirection === 'asc')
                                        <x-heroicon-s-chevron-up class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @else
                                        <x-heroicon-s-chevron-down class="w-4 h-4 text-blue-500 dark:text-blue-400"/>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($data as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-2">{{ $item->grievance_ticket_id }}</td>
                        <td class="px-4 py-2">{{ $item->grievance_title }}</td>
                        <td class="px-4 py-2 capitalize">{{ $item->grievance_category }}</td>
                        <td class="px-4 py-2">{{ $item->grievance_status }}</td>
                        <td class="px-4 py-2">{{ $item->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">
                            No data available for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
