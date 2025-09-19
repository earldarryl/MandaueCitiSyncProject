<div class="w-full flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 relative">
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

    <!-- Stats Widgets -->
    <section class="w-full p-4">
        <div class="flex flex-col lg:flex-row gap-6">
            <livewire:user.admin.dashboard.custom-stats />
        </div>
    </section>

    <!-- Chart Widgets -->
    <section class="w-full grid grid-cols-1 gap-6 bg-white dark:bg-zinc-800 p-3">
            <livewire:bar-widget />
            <livewire:pie-widget />
    </section>

    <!-- Table -->
    <section class="w-full p-4 mt-4 bg-white dark:bg-zinc-800">
        @can('viewAny', $userModel)
            <div class="shadow-sm rounded-lg p-4 overflow-x-auto">
                <livewire:dashboard-table/>
            </div>
        @endcan
    </section>
</div>
