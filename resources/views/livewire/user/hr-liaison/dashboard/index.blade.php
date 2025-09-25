<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 relative p-3">

    <!-- Global Loader -->
    <div wire:loading wire:target="startDate,endDate"
         class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-900 z-50">
        <div class="flex flex-col mt-6 items-center">
            <flux:icon.loading class="h-12 w-12 text-blue-600"/>
            <span class="mt-3 text-gray-700 dark:text-gray-300 text-sm">Refreshing dashboard…</span>
        </div>
    </div>

    <!-- Date Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4 w-full px-4">
        <div class="w-full md:w-1/2">
            <flux:input label="Start Date" type="date" wire:model.live="startDate" class:input="w-full"/>
        </div>
        <div class="w-full md:w-1/2">
            <flux:input label="End Date" type="date" wire:model.live="endDate" class:input="w-full"/>
        </div>
    </div>

    <div class="flex flex-col gap-4 border-box">

        <!-- HR Liaison Stats -->
        <section class="w-full p-4">
            <div class="flex flex-col lg:flex-row gap-6">
                <livewire:user.hr-liaison.dashboard.hr-liaison-stats
                    :start-date="$startDate"
                    :end-date="$endDate"/>
            </div>
        </section>

        <!-- Charts (Bar / Pie) -->
        <section class="w-full flex flex-col md:flex-row gap-3 bg-white dark:bg-zinc-800 p-3 rounded-lg">
            <div class="w-full">
                <livewire:bar-widget
                    :start-date="$startDate"
                    :end-date="$endDate"/>
            </div>
            <div class="w-full">
                <livewire:pie-widget
                    :start-date="$startDate"
                    :end-date="$endDate"/>
            </div>
        </section>

        <!-- Grievance Table -->
        <section class="w-full p-4 bg-white dark:bg-zinc-800 relative rounded-lg">
            <div class="shadow-sm rounded-lg p-4 overflow-x-auto relative">
                <div
                    wire:loading
                    wire:target="activeTab"
                    class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70 z-50 rounded-lg"
                >
                    <div class="flex flex-col items-center">
                        <flux:icon.loading class="h-12 w-12 text-blue-600"/>
                        <span class="mt-2 text-gray-700 dark:text-gray-300 text-sm">
                            Loading grievances…
                        </span>
                    </div>
                </div>

                <livewire:dashboard-grievance-table
                    :start-date="$startDate"
                    :end-date="$endDate"
                    :wire:key="'grievances-table-'.$startDate.'-'.$endDate"/>
                <livewire:dashboard-user-table/>
            </div>
        </section>

    </div>
</div>
