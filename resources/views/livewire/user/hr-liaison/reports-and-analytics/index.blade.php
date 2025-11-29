<div class="relative w-full bg-white dark:bg-gray-900 rounded-lg shadow-md p-6">

    <div class="flex flex-col md:flex-row gap-4 mb-6">
        <div class="w-full flex flex-col bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-zinc-700 relative p-3 rounded-lg shadow-sm"
            x-data="{
                start: @entangle('startDate'),
                end: @entangle('endDate'),
                filterType: @entangle('filterType'),
                filterCategory: @entangle('filterCategory'),
            }"
            x-init="$nextTick(() => {
                flatpickr($refs.startInput, {
                    dateFormat: 'Y-m-d',
                    defaultDate: start,
                    onChange: (selectedDates, dateStr) => {
                        start = dateStr
                        $wire.set('startDate', dateStr)
                    }
                });
                flatpickr($refs.endInput, {
                    dateFormat: 'Y-m-d',
                    defaultDate: end,
                    onChange: (selectedDates, dateStr) => {
                        end = dateStr
                        $wire.set('endDate', dateStr)
                    }
                });
            })"
        >
            <div class="flex flex-col md:flex-row md:items-center md:justify-center gap-2 mb-4 w-full px-4">

                <div class="flex flex-col gap-2 w-full md:w-1/4">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-calendar class="w-5 h-5 text-gray-500 dark:text-gray-300" />
                        <span>Start Date</span>
                    </div>
                    <div class="relative w-full">
                        <div
                            class="flex items-center justify-between px-3 py-2 border border-gray-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-900 cursor-pointer transition"
                            @click="$refs.startInput._flatpickr.open()"
                        >
                            <input
                                type="text"
                                x-ref="startInput"
                                x-model="start"
                                readonly
                                class="w-full bg-transparent text-[12px] font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer"
                                placeholder="Select start date"
                            />
                            <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-500 transition-transform duration-200" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 w-full md:w-1/4">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-calendar class="w-5 h-5 text-gray-500 dark:text-gray-300" />
                        <span>End Date</span>
                    </div>
                    <div class="relative w-full">
                        <div
                            class="flex items-center justify-between px-3 py-2 border border-gray-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-900 cursor-pointer transition"
                            @click="$refs.endInput._flatpickr.open()"
                        >
                            <input
                                type="text"
                                x-ref="endInput"
                                x-model="end"
                                readonly
                                class="w-full bg-transparent text-[12px] font-bold text-gray-700 dark:text-gray-200 focus:outline-none cursor-pointer"
                                placeholder="Select end date"
                            />
                            <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-500 transition-transform duration-200" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                        <span>Sort By</span>
                    </div>

                    <x-filter-select
                        name="sortOption"
                        placeholder="Sort By"
                        :options="[
                            'Priority: Low → Critical',
                            'Priority: Critical → Low',
                            'Type: Complaint → Request',
                            'Type: Request → Complaint',
                            'Status: Ascending',
                            'Status: Descending'
                        ]"
                        wire:model="sortOption"
                    />
                </div>

                <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                        <span>Type</span>
                    </div>
                    <x-filter-select
                        name="filterType"
                        placeholder="Type"
                        :options="['Complaint', 'Inquiry', 'Request']"
                        x-model="filterType"
                    />
                </div>

                <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                        <span>Category</span>
                    </div>
                    <div class="relative">
                        <x-filter-select
                            name="filterCategory"
                            placeholder="Category"
                            :options="$categoryOptions"
                        />
                    </div>
                </div>
            </div>

           <div class="px-4 mb-4">
                <button
                    wire:click="applyFilters"
                    wire:loading.attr="disabled"
                    wire:target="applyFilters"
                    class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                    <flux:icon.adjustments-horizontal class="w-4 h-4" />
                    <span wire:loading.remove wire:target="applyFilters">Apply Filter</span>
                    <span wire:loading wire:target="applyFilters">Processing...</span>
                </button>
            </div>

            @if($filtersApplied)
                <div class="flex lg:flex-wrap lg:flex-row flex-col justify-end gap-3 px-4 mb-4">
                    <button
                        wire:click="printReport"
                        wire:loading.attr="disabled"
                        wire:target="printReport"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-gray-100 dark:bg-gray-900/40 text-gray-700 dark:text-gray-300
                            border border-gray-500 dark:border-gray-400
                            hover:bg-gray-200 dark:hover:bg-gray-800/50
                            focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-700
                            transition-all duration-200 disabled:cursor-not-allowed"
                    >
                        <flux:icon.printer class="w-4 h-4" />
                        <span wire:loading.remove wire:target="printReport">Print</span>
                        <span wire:loading wire:target="printReport">Processing...</span>
                    </button>

                    <button
                        wire:click="exportPDF"
                        wire:loading.attr="disabled"
                        wire:target="exportPDF"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                            border border-red-500 dark:border-red-400
                            hover:bg-red-200 dark:hover:bg-red-800/50
                            focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                            transition-all duration-200 disabled:cursor-not-allowed"
                    >
                        <flux:icon.document-text class="w-4 h-4" />
                        <span wire:loading.remove wire:target="exportPDF">Export PDF</span>
                        <span wire:loading wire:target="exportPDF">Processing...</span>
                    </button>

                    <button
                        wire:click="exportCSV"
                        wire:loading.attr="disabled"
                        wire:target="exportCSV"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                            border border-blue-500 dark:border-blue-400
                            hover:bg-blue-200 dark:hover:bg-blue-800/50
                            focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                            transition-all duration-200 disabled:cursor-not-allowed"
                    >
                        <flux:icon.arrow-down-tray class="w-4 h-4" />
                        <span wire:loading.remove wire:target="exportCSV">Export CSV</span>
                        <span wire:loading wire:target="exportCSV">Processing...</span>
                    </button>

                    <button
                        wire:click="exportExcel"
                        wire:loading.attr="disabled"
                        wire:target="exportExcel"
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                            bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                            border border-green-500 dark:border-green-400
                            hover:bg-green-200 dark:hover:bg-green-800/50
                            focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                            transition-all duration-200 disabled:cursor-not-allowed"
                    >
                        <flux:icon.arrow-down-tray class="w-4 h-4" />
                        <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                        <span wire:loading wire:target="exportExcel">Processing...</span>
                    </button>

                </div>

            @endif
        </div>
    </div>

    @if($filtersApplied)
        <div class="bg-gradient-to-br from-white via-gray-50 to-gray-100 dark:from-zinc-900 dark:via-zinc-800 dark:to-zinc-900
                rounded-2xl p-6 mb-6 border border-gray-200 dark:border-zinc-700
                transition-all duration-300">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="rounded-xl p-5 border border-gray-200 dark:border-zinc-700">
                    @php
                        $liaisonInfo = [
                            ['label' => 'Name', 'value' => Auth::user()->name, 'icon' => 'user-circle'],
                            [
                                'label' => 'Department',
                                'value' => Auth::user()->hasRole('hr_liaison') && Auth::user()->departments->isNotEmpty()
                                    ? Auth::user()->departments->pluck('department_name')->join(', ')
                                    : 'N/A',
                                'icon' => 'building-office'
                            ],
                            [
                                'label' => 'Role',
                                'value' => str_replace('Hr', 'HR', ucwords(str_replace('_', ' ', Auth::user()->getRoleNames()->first() ?? 'N/A'))),
                                'icon'  => 'identification',
                            ],
                            ['label' => 'Date & Time', 'value' => now()->format('F d, Y — h:i A'), 'icon' => 'calendar-days'],
                        ];
                    @endphp

                    <div class="flex flex-col divide-y divide-gray-300 dark:divide-zinc-700">
                        @foreach ($liaisonInfo as $item)
                            <div class="flex items-start justify-between py-2">
                                <div class="flex items-center gap-2 w-44">
                                    <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                    <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                                </div>
                                <span class="text-[15px] font-bold flex-1 text-right text-gray-900 dark:text-gray-100">
                                    {{ $item['value'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <livewire:grievances-report-pie-chart
                        :start-date="$startDate"
                        :end-date="$endDate"
                        :filter-type="$filterType"
                        :filter-category="$filterCategory"
                        wire:key="grievance-categories-pie-{{ $startDate }}-{{ $endDate }}-{{ $filterType }}-{{ $filterCategory }}" />
                </div>
            </div>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200 font-sans">
                <thead class="text-xs uppercase tracking-wide bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center font-semibold">TICKET ID</th>
                        <th scope="col" class="px-6 py-3 font-semibold">TITLE</th>
                        <th scope="col" class="px-6 py-3 text-center font-semibold">TYPE</th>
                        <th scope="col" class="px-6 py-3 text-center font-semibold">CATEGORY</th>
                        <th scope="col" class="px-6 py-3 text-center font-semibold">STATUS</th>
                        <th scope="col" class="px-6 py-3 text-center font-semibold">PRIORITY LEVEL</th>
                        <th scope="col" class="px-6 py-3 text-center font-semibold">PROCESSING DAYS</th>
                        <th scope="col" class="px-6 py-3 text-center font-semibold">DATE</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900">
                    @forelse($data as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors duration-200">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white text-center">
                                {{ $item->grievance_ticket_id }}
                            </th>
                            <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">
                                {{ $item->grievance_title }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300 text-center capitalize">
                                {{ $item->grievance_type ?? '—' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300 text-center capitalize">
                                {{ $item->grievance_category ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold border border-gray-300 dark:border-zinc-800 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ strtoupper($item->grievance_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold border border-gray-300 dark:border-zinc-800 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ strtoupper($item->priority_level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-gray-800 dark:text-gray-200">
                                {{ $item->processing_days ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-gray-700 dark:text-gray-300">
                                {{ $item->created_at->format('Y-m-d h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-400 italic">
                                No data available for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    @else
        <div class="text-center py-10 text-gray-500 dark:text-gray-400 italic">
            Please apply filters to view the report.
        </div>
    @endif
</div>
