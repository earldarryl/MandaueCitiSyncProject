<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 relative p-3">

    <div wire:loading wire:target="applyDates">
        <div class="absolute inset-0 flex items-center justify-center bg-white/90 dark:bg-gray-900/90 z-50">
            <div class="flex flex-col mt-6 items-center">
                <flux:icon.loading class="h-12 w-12 text-blue-600"/>
                <span class="mt-3 text-gray-700 dark:text-gray-300 text-sm">Refreshing dashboard…</span>
            </div>
        </div>
    </div>

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

    <div class="flex flex-col gap-4 border-box">
        <section class="w-full p-4">
            <div class="flex flex-col lg:flex-row gap-6">
                <livewire:user.admin.dashboard.custom-stats :start-date="$startDate" :end-date="$endDate"/>
            </div>
        </section>

        <section class="w-full flex flex-col md:flex-row gap-3 bg-white dark:bg-zinc-800 p-3">

            <div class="w-full">
                <livewire:admin-line-chart
                    :start-date="$startDate"
                    :end-date="$endDate"
                    wire:key="admin-line-chart-{{ $startDate }}-{{ $endDate }}" />
            </div>

        </section>

        <!-- Table Section -->
        <section class="w-full p-4 bg-white dark:bg-zinc-800 relative rounded-lg">
            @can('viewAny', $userModel)
                <!-- Table Selector -->
                <div class="flex w-full mb-4">
                    {{ $this->form }}
                </div>

                <div class="shadow-sm rounded-lg p-4 overflow-x-auto relative">

                    <!-- Loading Overlay for Table Switch -->
                    <div
                        wire:loading
                        wire:target="activeTab"
                        class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 z-50 rounded-lg"
                    >
                        <div class="flex flex-col items-center">
                            <flux:icon.loading class="h-12 w-12 text-blue-600"/>
                            <span class="mt-2 text-gray-700 dark:text-gray-300 text-sm">
                                Loading table…
                            </span>
                        </div>
                    </div>



                </div>
            @endcan
        </section>
    </div>

</div>
