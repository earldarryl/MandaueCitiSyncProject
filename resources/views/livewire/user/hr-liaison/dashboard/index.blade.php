<div class="flex-1 flex flex-col w-full min-h-screen bg-gray-50 dark:bg-zinc-900 relative p-4 md:p-6 lg:p-8"
     x-data>

    <div wire:loading wire:target="applyDates">
        <div class="relative flex items-center justify-center bg-gray-50 dark:bg-gray-900/90">
            <div class="flex flex-col items-center">
                <flux:icon.loading class="h-12 w-12 text-blue-600"/>
                <span class="mt-3 text-gray-700 dark:text-gray-300 text-sm">Refreshing dashboard…</span>
            </div>
        </div>
    </div>

    <div wire:loading.remove wire:target="applyDates">
         <div class="w-full max-w-7xl mx-auto flex flex-col gap-6">

            <div class="w-full flex flex-col bg-gray-50 dark:bg-gray-900 relative p-3 rounded-lg shadow-sm"
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

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 w-full px-4">
                    <div class="flex flex-col gap-2 w-full md:w-1/2 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-calendar class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>Start Date</span>
                        </div>
                        <flux:input type="text" x-ref="startInput" readonly x-model="start" class="w-full cursor-pointer font-bold" />
                    </div>

                    <div class="flex flex-col gap-2 w-full md:w-1/2 cursor-pointer">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <x-heroicon-o-calendar class="w-5 h-5 text-gray-500 dark:text-gray-300"/>
                            <span>End Date</span>
                        </div>
                        <flux:input type="text" x-ref="endInput" readonly x-model="end" class="w-full cursor-pointer font-bold" />
                    </div>
                </div>

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

            <section class="w-full p-4 bg-white/70 dark:bg-zinc-800/40 rounded-xl shadow-sm">
                <div x-data="{ autoRefresh: true, start: '{{ $startDate }}', end: '{{ $endDate }}' }"
                        x-init="
                            setInterval(() => {
                                if(autoRefresh) $wire.call('applyDates', start, end)
                            }, 300000);
                        ">
                        <livewire:user.hr-liaison.dashboard.hr-liaison-stats
                            :start-date="$startDate"
                            :end-date="$endDate"
                            wire:key="hr-liaison-stats-{{ $startDate }}-{{ $endDate }}"/>
                    </div>
            </section>

            <section class="w-full h-full p-4 flex justify-center items-center gap-6">
                <div class="flex gap-6 justify-center w-full flex-col lg:flex-row items-stretch">
                    <div class="bg-white/70 dark:bg-zinc-800/40 rounded-xl shadow-sm p-4 w-full lg:w-2/4 max-w-full">
                        <livewire:grievance-statuses-polar-chart
                            :start-date="$startDate"
                            :end-date="$endDate"
                            wire:key="grievance-statuses-polar-{{ $startDate }}-{{ $endDate }}" />
                    </div>

                    <div class="bg-white/70 dark:bg-zinc-800/40 rounded-xl shadow-sm p-4 w-full lg:w-2/4 max-w-full">
                        <livewire:grievance-categories-pie-chart
                            :start-date="$startDate"
                            :end-date="$endDate"
                            wire:key="grievance-categories-pie-{{ $startDate }}-{{ $endDate }}" />
                    </div>
                </div>
            </section>

            <section class="w-full h-full p-4">
                <div class="flex flex-col lg:flex-row gap-6 w-full h-full">
                    <div class="flex-1 w-full bg-white/70 dark:bg-zinc-800/40 rounded-xl shadow-sm p-4">
                        <livewire:grievance-line-chart
                            :start-date="$startDate"
                            :end-date="$endDate"
                            wire:key="grievance-line-{{ $startDate }}-{{ $endDate }}" />
                    </div>

                    <div class="flex-1 w-full bg-white/70 dark:bg-zinc-800/40 rounded-xl shadow-sm p-4">
                        <livewire:hr-liaison-user-grievance-chart
                            :start-date="$startDate"
                            :end-date="$endDate"
                            wire:key="user-grievance-chart-{{ $startDate }}-{{ $endDate }}" />
                    </div>
                </div>
            </section>

            <section class="w-full p-4 relative rounded-lg bg-white/70 dark:bg-zinc-800/40 shadow-sm">
                <livewire:dashboard-grievance-table
                    :start-date="$startDate"
                    :end-date="$endDate"
                    wire:key="grievances-table-{{ $startDate }}-{{ $endDate }}" />
            </section>
        </div>
    </div>

</div>
