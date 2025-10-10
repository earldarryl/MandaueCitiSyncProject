<div class="flex flex-col w-full space-y-6">

    <!-- Section Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Grievance Statistics
        </h2>
    </div>

    <!-- Stats Cards -->
    <div wire:loading.class="opacity-50"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">

        <!-- Resolved -->
        <div
            class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
                border border-green-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-green-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-green-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.check-circle class="h-8 w-8 text-green-600 dark:text-green-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Resolved</p>
            <p class="relative text-3xl font-bold text-green-600 dark:text-green-400 tracking-tight">{{ $resolved }}</p>
        </div>

        <!-- Pending -->
        <div
            class="group relative bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-zinc-800 dark:to-zinc-900
                border border-yellow-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-yellow-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-yellow-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.clock class="h-8 w-8 text-yellow-500 dark:text-yellow-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Pending</p>
            <p class="relative text-3xl font-bold text-yellow-500 dark:text-yellow-400 tracking-tight">{{ $pending }}</p>
        </div>

        <!-- In Progress -->
        <div
            class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
                border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.arrow-trending-up class="h-8 w-8 text-blue-600 dark:text-blue-400 animate-spin-slow" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">In Progress</p>
            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">{{ $inProgress }}</p>
        </div>

        <!-- Closed / Rejected -->
        <div
            class="group relative bg-gradient-to-br from-red-50 to-red-100 dark:from-zinc-800 dark:to-zinc-900
                border border-red-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-red-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.x-circle class="h-8 w-8 text-red-600 dark:text-red-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Closed / Rejected</p>
            <p class="relative text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">{{ $closed }}</p>
        </div>

        <!-- Overdue -->
        <div
            class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-zinc-800 dark:to-zinc-900
                border border-purple-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-purple-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-purple-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.exclamation-triangle class="h-8 w-8 text-purple-600 dark:text-purple-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Overdue</p>
            <p class="relative text-3xl font-bold text-purple-600 dark:text-purple-400 tracking-tight">{{ $overdue }}</p>
        </div>

    </div>
</div>
