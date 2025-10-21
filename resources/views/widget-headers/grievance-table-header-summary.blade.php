<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-900 dark:to-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm">

    <div class="flex items-center justify-between p-3 bg-white/80 dark:bg-zinc-800/80 rounded-lg border border-gray-200 dark:border-zinc-700 hover:shadow-md transition-all duration-300">
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</span>
            <span class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $summary['total'] }}</span>
        </div>
        <div class="p-2 rounded-full bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-300">
            <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
        </div>
    </div>

    <div class="flex items-center justify-between p-3 bg-white/80 dark:bg-zinc-800/80 rounded-lg border border-gray-200 dark:border-zinc-700 hover:shadow-md transition-all duration-300">
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Open</span>
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['open'] }}</span>
        </div>
        <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">
            <x-heroicon-o-clock class="w-6 h-6" />
        </div>
    </div>

    <div class="flex items-center justify-between p-3 bg-white/80 dark:bg-zinc-800/80 rounded-lg border border-gray-200 dark:border-zinc-700 hover:shadow-md transition-all duration-300">
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Resolved</span>
            <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ $summary['resolved'] }}</span>
        </div>
        <div class="p-2 rounded-full bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400">
            <x-heroicon-o-check-circle class="w-6 h-6" />
        </div>
    </div>

    <div class="flex items-center justify-between p-3 bg-white/80 dark:bg-zinc-800/80 rounded-lg border border-gray-200 dark:border-zinc-700 hover:shadow-md transition-all duration-300">
        <div class="flex flex-col">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">High Priority</span>
            <span class="text-xl font-bold text-red-600 dark:text-red-400">{{ $summary['highPriority'] }}</span>
        </div>
        <div class="p-2 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 animate-pulse">
            <x-heroicon-o-fire class="w-6 h-6" />
        </div>
    </div>

</div>
