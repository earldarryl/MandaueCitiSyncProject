<div class="relative w-full bg-white dark:bg-gray-900 rounded-lg shadow-md p-6" wire:poll.visible.30s>
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
                        <span>Model Type</span>
                    </div>
                    <x-filter-select
                        name="filterType"
                        placeholder="Type"
                        :options="['Grievances', 'Feedbacks', 'Users', 'Activity Logs']"
                        x-model="filterType"
                    />
                </div>

                @if($filterType === 'Grievances')
                    <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-rectangle-stack class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Grievance Type</span>
                        </div>
                        <x-filter-select
                            name="grievanceType"
                            placeholder="Select Grievance Type"
                            :options="['Complaint', 'Request', 'Inquiry']"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-flag class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Priority</span>
                        </div>
                        <x-filter-select
                            name="grievancePriority"
                            placeholder="Select Priority"
                            :options="['Low', 'Normal', 'High']"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-flag class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Status</span>
                        </div>
                        <x-filter-select
                            name="grievanceStatus"
                            placeholder="Select Status"
                            :options="['Pending','Acknowledged','In Progress','Escalated','Resolved','Unresolved','Closed']"
                        />
                    </div>

                @endif

            </div>

           <div class="px-4 mb-4">
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

            <div class="flex flex-wrap justify-end gap-3 px-4 mb-4">

            </div>
        </div>
    </div>


    <div class="relative w-full bg-white dark:bg-gray-900 rounded-lg shadow-md p-6">

        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm bg-white dark:bg-zinc-800">
            <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200 font-sans">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 uppercase text-xs">
                    <tr>
                        @if($filterType === 'Grievances')
                            <th class="px-6 py-3 text-center">TICKET ID</th>
                            <th class="px-6 py-3">TITLE</th>
                            <th class="px-6 py-3 text-center">TYPE</th>
                            <th class="px-6 py-3 text-center">CATEGORY</th>
                            <th class="px-6 py-3 text-center">PRIORITY LEVEL</th>
                            <th class="px-6 py-3 text-center">STATUS</th>
                            <th class="px-6 py-3 text-center">PROCESSING DAYS</th>
                            <th class="px-6 py-3 text-center">DATE</th>
                        @elseif($filterType === 'Feedbacks')
                            <th class="px-6 py-3 text-center">USER</th>
                            <th class="px-6 py-3">SERVICE</th>
                            <th class="px-6 py-3 text-center">GENDER</th>
                            <th class="px-6 py-3 text-center">REGION</th>
                            <th class="px-6 py-3">SUGGESTIONS</th>
                            <th class="px-6 py-3 text-center">DATE</th>
                        @elseif($filterType === 'Users')
                            <th class="px-6 py-3 text-center">NAME</th>
                            <th class="px-6 py-3 text-center">EMAIL</th>
                            <th class="px-6 py-3 text-center">ROLES</th>
                            <th class="px-6 py-3 text-center">STATUS</th>
                            <th class="px-6 py-3 text-center">CREATED AT</th>
                        @elseif($filterType === 'Activity Logs')
                            <th class="px-6 py-3 text-center">USER</th>
                            <th class="px-6 py-3">ACTION</th>
                            <th class="px-6 py-3">MODULE</th>
                            <th class="px-6 py-3 text-center">ROLE</th>
                            <th class="px-6 py-3 text-center">DATE</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900">
                    @forelse($data as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition">
                            @if($filterType === 'Grievances')
                                <td class="px-6 py-3 text-center">{{ $item->grievance_ticket_id }}</td>
                                <td class="px-6 py-3">{{ $item->grievance_title }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->grievance_type ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->grievance_category ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->priority_level ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->grievance_status }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->processing_days ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->created_at->format('Y-m-d h:i A') }}</td>
                            @elseif($filterType === 'Feedbacks')
                                <td class="px-6 py-3 text-center">{{ $item->user->name ?? 'Anonymous' }}</td>
                                <td class="px-6 py-3">{{ $item->service }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->gender }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->region }}</td>
                                <td class="px-6 py-3">{{ $item->suggestions }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->date->format('Y-m-d') }}</td>
                            @elseif($filterType === 'Users')
                                <td class="px-6 py-3 text-center">{{ $item->name }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->email }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->roles->pluck('name')->join(', ') }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->status }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->created_at->format('Y-m-d') }}</td>
                            @elseif($filterType === 'Activity Logs')
                                <td class="px-6 py-3 text-center">{{ $item->user->name ?? 'System' }}</td>
                                <td class="px-6 py-3">{{ $item->action }}</td>
                                <td class="px-6 py-3">{{ $item->module }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->role->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->timestamp->format('Y-m-d H:i') }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500 dark:text-gray-400 italic">
                                No data available for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
