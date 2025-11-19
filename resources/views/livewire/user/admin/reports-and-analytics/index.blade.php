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
                        :options="['Grievances', 'Departments','Feedbacks', 'Users']"
                        x-model="filterType"
                    />
                </div>

            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-center gap-2 mb-4 w-full px-4">
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

                    <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Dynamic Filter</span>
                        </div>
                        <x-filter-select
                            name="dynamicGrievanceFilter"
                            placeholder="Select Filter"
                            :options="$dynamicGrievanceOptions"
                            wire:model="dynamicGrievanceFilter"
                        />
                    </div>

                @endif

                @if($filterType === 'Departments')
                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Service Status</span>
                        </div>
                        <x-filter-select
                            name="filterServiceStatus"
                            placeholder="Select Options"
                            :options="['Active', 'Inactive']"
                            wire:model.live="filterServiceStatus"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Service Availability</span>
                        </div>
                        <x-filter-select
                            name="filterServiceAvailability"
                            placeholder="Select Options"
                            :options="['Available', 'Not Available']"
                            wire:model.live="filterServiceStatus"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Dynamic Filter</span>
                        </div>
                        <x-filter-select
                            name="dynamicDepartmentFilter"
                            placeholder="Select Filter"
                            :options="$dynamicDepartmentFilterOptions"
                            wire:model="dynamicDepartmentFilter"
                        />
                    </div>
                @endif

                @if($filterType === 'Feedbacks')

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Gender</span>
                        </div>
                        <x-filter-select
                            name="filterGender"
                            placeholder="Select Gender"
                            :options="['Male', 'Female', 'Other']"
                            wire:model.live="filterGender"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>CC Summary</span>
                        </div>
                        <x-filter-select
                            name="filterCCSummary"
                            placeholder="Select CC Summary"
                            :options="['High Awareness', 'Medium Awareness', 'Low Awareness', 'No Awareness', 'N/A']"
                            wire:model.live="filterCCSummary"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-chart-pie class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>SQD Summary</span>
                        </div>
                        <x-filter-select
                            name="filterSQDSummary"
                            placeholder="Select SQD Summary"
                            :options="['Most Agree','Most Disagree','Neutral','N/A']"
                            wire:model.live="filterSQDSummary"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Dynamic Filter</span>
                        </div>
                        <x-filter-select
                            name="dynamicFeedbackFilter"
                            placeholder="Select Filter"
                            :options="$dynamicFeedbackFilterOptions"
                            wire:model="dynamicFeedbackFilter"
                        />
                    </div>

                @endif

                @if($filterType === 'Users')
                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-user class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>User Type</span>
                        </div>

                        <x-filter-select
                            name="filterUserType"
                            placeholder="Select User Type"
                            :options="['Citizen', 'HR Liaison']"
                            wire:model.live="filterUserType"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="font-bold mb-1">Gender</div>
                        <x-filter-select
                            name="filterGender"
                            placeholder="Select Gender"
                            :options="['Male', 'Female', 'Other']"
                            wire:model.live="filterGender"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="font-bold mb-1">Barangay</div>
                        <x-filter-select
                            name="filterBarangay"
                            placeholder="Select Barangay"
                            :options="$barangayOptions"
                            wire:model.live="filterBarangay"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="font-bold mb-1">Civil Status</div>
                        <x-filter-select
                            name="filterCivilStatus"
                            placeholder="Select Civil Status"
                            :options="['Single', 'Married', 'Separated', 'Widowed']"
                            wire:model.live="filterCivilStatus"
                        />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/4">
                        <div class="font-bold mb-1">Dynamic User Data</div>
                        <x-filter-select
                            name="dynamicUserFilter"
                            placeholder="Select Analytics"
                            :options="$dynamicUserFilterOptions"
                            wire:model.live="dynamicUserFilter"
                        />
                    </div>
                @endif

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
        </div>
    </div>

    @if(($dynamicGrievanceFilter || $dynamicDepartmentFilter || $dynamicFeedbackFilter || $dynamicUserFilter) && $filtersApplied)

        <div class="flex flex-wrap justify-end gap-3 px-4 mb-4">

            <button
                wire:click="printReport"
                wire:loading.attr="disabled"
                wire:target="printReport"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    bg-gray-100 text-gray-800 border-gray-300
                    hover:bg-gray-200 hover:border-gray-400
                    dark:bg-zinc-800 dark:text-gray-200 dark:border-zinc-700
                    dark:hover:bg-zinc-700
                    whitespace-nowrap
                    transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
            >
                <flux:icon.printer class="w-4 h-4" />
                <span wire:loading.remove wire:target="printReport">Print</span>
                <span wire:loading wire:target="printReport">Processing...</span>
            </button>

            <button
                wire:click="exportPDF"
                wire:loading.attr="disabled"
                wire:target="exportPDF"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    bg-red-100 text-red-800 border-red-300
                    hover:bg-red-200 hover:border-red-400
                    dark:bg-red-800 dark:text-red-200 dark:border-red-700
                    dark:hover:bg-red-700
                    whitespace-nowrap
                    transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
            >
                <flux:icon.document-text class="w-4 h-4" />
                <span wire:loading.remove wire:target="exportPDF">Export PDF</span>
                <span wire:loading wire:target="exportPDF">Processing...</span>
            </button>

            <button
                wire:click="exportCSV"
                wire:loading.attr="disabled"
                wire:target="exportCSV"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    bg-blue-100 text-blue-800 border-blue-300
                    hover:bg-blue-200 hover:border-blue-400
                    dark:bg-blue-800 dark:text-blue-200 dark:border-blue-700
                    dark:hover:bg-blue-700
                    whitespace-nowrap
                    transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
            >
                <flux:icon.arrow-down-tray class="w-4 h-4" />
                <span wire:loading.remove wire:target="exportCSV">Export CSV</span>
                <span wire:loading wire:target="exportCSV">Processing...</span>
            </button>

            <button
                wire:click="exportExcel"
                wire:loading.attr="disabled"
                wire:target="exportExcel"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    bg-green-100 text-green-800 border-green-300
                    hover:bg-green-200 hover:border-green-400
                    dark:bg-green-800 dark:text-green-200 dark:border-green-700
                    dark:hover:bg-green-700
                    whitespace-nowrap
                    transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed"
            >
                <flux:icon.arrow-down-tray class="w-4 h-4" />
                <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
                <span wire:loading wire:target="exportExcel">Processing...</span>
            </button>
        </div>

    @endif

    @if($filterType === 'Grievances' && $dynamicGrievanceFilter)
        <div class="grid gap-6 mt-6 justify-center"
            style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

            @foreach ($stats as $stat)
                @php
                    $key = $dynamicGrievanceFilter === 'High → Low Priority'
                            ? $stat->priority_level
                            : ($dynamicGrievanceFilter === 'Most Submitted Grievance Type'
                                ? $stat->grievance_type
                                : $stat->grievance_status);

                    $bgClass = $stat->bg ?? 'from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700';
                    $textClass = $stat->text ?? 'text-gray-600 dark:text-gray-400';
                @endphp

                <div class="group relative bg-gradient-to-br {{ $bgClass }}
                            border border-gray-200/50 dark:border-zinc-700 rounded-2xl shadow-sm
                            hover:shadow-lg transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">

                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/20 to-transparent
                                opacity-0 group-hover:opacity-100 blur-xl transition-all duration-500"></div>

                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border
                                border-gray-200/50 dark:border-zinc-700 group-hover:scale-105
                                transition-transform duration-300">
                        <x-heroicon-o-chart-pie class="h-8 w-8 {{ $textClass }}" />
                    </div>

                    <p class="relative text-base font-semibold {{ $textClass }} mt-2">
                        {{ ucwords(str_replace(['_', '-'], ' ', $key)) }}
                    </p>

                    <p class="relative text-3xl font-bold {{ $textClass }} tracking-tight">
                        {{ $stat->total }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    @if($filterType === 'Departments' && $dynamicDepartmentFilter)
        <div class="grid gap-6 mt-6 justify-center"
            style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

            @foreach ($stats as $stat)
                <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-gray-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">

                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/20 to-transparent opacity-0
                                group-hover:opacity-100 blur-xl transition-all duration-500"></div>

                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-gray-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                        <x-dynamic-component :component="$stat->icon" class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    </div>

                    <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">
                        {{ $stat->department_name }}
                    </p>

                    <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-500 tracking-tight text-center">
                        {{ $stat->total }}
                        @if(isset($stat->total_online_time))
                            <span class="block text-base font-medium text-gray-500 dark:text-gray-400 mt-1">
                                Total Online: {{ $stat->total_online_time }}
                            </span>
                        @endif
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    @if($filterType === 'Feedbacks' && $dynamicFeedbackFilter)
        <div class="grid gap-6 mt-6 justify-center"
            style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

            @foreach ($stats as $stat)
                <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                            border border-gray-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">

                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-gray-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                        <x-dynamic-component :component="$stat->icon" class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                    </div>

                    <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">
                        {{ $stat->label }}
                    </p>

                    <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-500 tracking-tight">
                        {{ $stat->total }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    @if($filterType === 'Users' && $dynamicUserFilter)
        <div class="grid gap-6 mt-6 justify-center"
            style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

            @foreach ($stats as $stat)
            @php
                $color = $this->dynamicColorMap[$this->dynamicUserFilter] ?? null;

                $bgClass = $color['bg'] ?? 'from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900';
                $textClass = $color['text'] ?? 'text-green-600 dark:text-green-400';
            @endphp

                <div class="group relative bg-gradient-to-br {{ $bgClass }}
                            border border-gray-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                            transition-all duration-300 p-5 flex flex-col items-center justify-center gap-2">

                    <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-gray-200/50
                                dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                        <x-dynamic-component :component="$stat->icon" class="h-8 w-8 {{ $textClass }}" />
                    </div>

                    <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">
                        {{ $stat->label }}
                    </p>

                    <p class="relative text-3xl font-bold {{ $textClass }} tracking-tight">
                        {{ $stat->total }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

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
                            <th class="px-6 py-3 text-center">DEPARTMENT</th>
                            <th class="px-6 py-3 text-center">PRIORITY LEVEL</th>
                            <th class="px-6 py-3 text-center">STATUS</th>
                            <th class="px-6 py-3 text-center">PROCESSING DAYS</th>
                            <th class="px-6 py-3 text-center">DATE</th>

                        @elseif($filterType === 'Departments')
                            <th class="px-6 py-3 text-center">DEPARTMENT NAME</th>
                            <th class="px-6 py-3 text-center">CODE</th>
                            <th class="px-6 py-3 text-center">TOTAL ASSIGNMENTS</th>
                            <th class="px-6 py-3 text-center">HR LIAISONS ONLINE</th>
                            <th class="px-6 py-3 text-center">CREATED AT</th>

                        @elseif($filterType === 'Feedbacks')
                            <th class="px-6 py-3 text-center">EMAIL</th>
                            <th class="px-6 py-3">SERVICE</th>
                            <th class="px-6 py-3 text-center">GENDER</th>
                            <th class="px-6 py-3 text-center">REGION</th>
                            <th class="px-6 py-3 text-center">CC SUMMARY</th>
                            <th class="px-6 py-3 text-center">SQD SUMMARY</th>
                            <th class="px-6 py-3">SUGGESTIONS</th>
                            <th class="px-6 py-3 text-center">DATE</th>

                        @elseif($filterType === 'Users' && $filterUserType === 'Citizen')
                            <th class="px-6 py-3 text-center">FIRST NAME</th>
                            <th class="px-6 py-3 text-center">MIDDLE NAME</th>
                            <th class="px-6 py-3 text-center">LAST NAME</th>
                            <th class="px-6 py-3 text-center">SUFFIX</th>
                            <th class="px-6 py-3 text-center">GENDER</th>
                            <th class="px-6 py-3 text-center">CIVIL STATUS</th>
                            <th class="px-6 py-3 text-center">BARANGAY</th>
                            <th class="px-6 py-3 text-center">SITIO</th>
                            <th class="px-6 py-3 text-center">BIRTHDATE</th>
                            <th class="px-6 py-3 text-center">AGE</th>
                            <th class="px-6 py-3 text-center">PHONE NUMBER</th>
                            <th class="px-6 py-3 text-center">EMERGENCY CONTACT NAME</th>
                            <th class="px-6 py-3 text-center">EMERGENCY CONTACT NUMBER</th>
                            <th class="px-6 py-3 text-center">EMERGENCY RELATIONSHIP</th>
                            <th class="px-6 py-3 text-center">EMAIL</th>
                            <th class="px-6 py-3 text-center">CREATED AT</th>

                        @elseif($filterType === 'Users' && $filterUserType === 'HR Liaison')
                            <th class="px-6 py-3 text-center">NAME</th>
                            <th class="px-6 py-3 text-center">EMAIL</th>
                            <th class="px-6 py-3 text-center">DEPARTMENT</th>
                            <th class="px-6 py-3 text-center">STATUS</th>
                            <th class="px-6 py-3 text-center">CREATED AT</th>
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
                                <td class="px-6 py-3 text-center">
                                    {{ $item->departments->pluck('department_name')->join(', ') ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-center">{{ $item->priority_level ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->grievance_status }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->processing_days ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->created_at->format('Y-m-d h:i A') }}</td>

                            @elseif($filterType === 'Departments')
                                <td class="px-6 py-3 text-center">{{ $item->department_name }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->department_code }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->assignments_count ?? 0 }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->hrLiaisonsStatus }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->created_at->format('Y-m-d') }}</td>

                            @elseif($filterType === 'Feedbacks')
                                <td class="px-6 py-3 text-center">{{ $item->email ?? 'N/A' }}</td>
                                <td class="px-6 py-3">{{ $item->service }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->gender }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->region }}</td>
                                <td class="px-6 py-3 text-center font-bold">{{ $item->cc_summary }}</td>
                                <td class="px-6 py-3 text-center font-bold">{{ $item->sqd_summary }}</td>
                                <td class="px-6 py-3">{{ $item->suggestions }}</td>
                                <td class="px-6 py-3 text-center">{{ $item->date->format('Y-m-d') }}</td>

                            @elseif($filterType === 'Users' && $filterUserType === 'Citizen')
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->first_name ?? '' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->middle_name ?? '' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->last_name ?? '' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->suffix ?? '' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->gender ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->civil_status ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->barangay ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->sitio ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ optional($item['userInfo']->birthdate)->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->age ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->phone_number ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->emergency_contact_name ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->emergency_contact_number ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['userInfo']->emergency_relationship ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['email'] }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['created_at']->format('Y-m-d') }}</td>

                            @elseif($filterType === 'Users' && $filterUserType === 'HR Liaison')
                                <td class="px-6 py-3 text-center">{{ $item['name'] }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['email'] }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['departments'] ?? '—' }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['status'] }}</td>
                                <td class="px-6 py-3 text-center">{{ $item['created_at']->format('Y-m-d') }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center py-6 text-gray-500 dark:text-gray-400 italic">
                                No data available for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
