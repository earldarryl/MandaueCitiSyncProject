<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 relative p-3" x-data>

    <div wire:loading wire:target="applyDates">
        <div class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
            <div class="flex flex-col mt-6 items-center">
                <flux:icon.loading class="h-12 w-12 text-blue-600"/>
                <span class="mt-3 text-gray-700 dark:text-gray-300 text-sm">Refreshing dashboard…</span>
            </div>
        </div>
    </div>

    <div class="w-full flex flex-col bg-gray-50 dark:bg-gray-900 relative p-3"
        x-data="{ start: '{{ $startDate }}', end: '{{ $endDate }}' }"
        x-init="$nextTick(() => {
            flatpickr($refs.startInput, {
                dateFormat: 'Y-m-d',
                defaultDate: start,
                onChange: (selectedDates, dateStr) => start = dateStr
            });
            flatpickr($refs.endInput, {
                dateFormat: 'Y-m-d',
                defaultDate: end,
                onChange: (selectedDates, dateStr) => end = dateStr
            });
        })">

        <!-- Loading overlay -->
        <div wire:loading wire:target="applyDates"
            class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
            <div class="flex flex-col mt-6 items-center">
                <flux:icon.loading class="h-12 w-12 text-blue-600"/>
                <span class="mt-3 text-gray-700 dark:text-gray-300 text-sm">Refreshing dashboard…</span>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 w-full px-4">

            <!-- Start Date -->
            <div class="flex flex-col gap-2 w-full md:w-1/2 cursor-pointer">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Start Date</span>
                </div>
                <flux:input
                    type="text"
                    x-ref="startInput"
                    readonly
                    x-model="start"
                    class:input="w-full cursor-pointer font-bold"
                ></flux:input>
            </div>

            <!-- End Date -->
            <div class="flex flex-col gap-2 w-full md:w-1/2 cursor-pointer">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>End Date</span>
                </div>
                <flux:input
                    type="text"
                    x-ref="endInput"
                    readonly
                    x-model="end"
                    class:input="w-full cursor-pointer font-bold"
                ></flux:input>
            </div>

        </div>

        <!-- Apply button -->
        <div class="px-4">
            <button
                type="button"
                class="flex gap-2 items-center justify-center font-bold w-full bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 transition duration-300 ease-in-out text-white p-3 rounded-lg"
                @click="$wire.applyDates(start, end)"
            >
                <x-heroicon-o-check class="w-5 h-5"/>
                <span>Apply</span>
            </button>
        </div>

    </div>



    <div class="flex flex-col gap-4 border-box">

        <section class="w-full p-4">
            <div class="flex flex-col lg:flex-row gap-6">
                <livewire:user.hr-liaison.dashboard.hr-liaison-stats
                    :start-date="$startDate"
                    :end-date="$endDate"
                    wire:key="hr-liaison-stats-{{ $startDate }}-{{ $endDate }}"/>
            </div>
        </section>

       <section class="w-full h-full p-4">
            <div class="flex flex-col lg:flex-row gap-6 w-full h-full">
                <div class="flex-1 w-full">
                    <livewire:grievance-line-chart
                        :start-date="$startDate"
                        :end-date="$endDate"
                        wire:key="grievance-bar-{{ $startDate }}-{{ $endDate }}"/>
                </div>

                <div class="flex-1 w-full">
                    <livewire:hr-liaison-user-grievance-chart
                        :start-date="$startDate"
                        :end-date="$endDate"
                        wire:key="user-grievance-chart-{{ $startDate }}-{{ $endDate }}"/>
                </div>
            </div>
        </section>

        <section class="w-full p-4 relative rounded-lg">
                <livewire:dashboard-grievance-table
                    :start-date="$startDate"
                    :end-date="$endDate"
                    wire:key="grievances-table-{{ $startDate }}-{{ $endDate }}" />
        </section>

    </div>
</div>
