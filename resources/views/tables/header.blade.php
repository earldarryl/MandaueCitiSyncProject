<div class="flex flex-col gap-4">
    <!-- Heading -->
    <h2 class="text-2xl font-bold tracking-tight p-3 text-sky-900 dark:text-blue-500">
        {{ $heading }}
    </h2>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Users -->
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md">
            <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V9H2v11h5m10 0V9m0 11H7m10-11V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v5" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
            </div>
        </div>

        <!-- Active Users -->
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md">
            <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Users</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $activeUsers }}</p>
            </div>
        </div>

        <!-- Registered Today -->
        <div class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md">
            <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M4 19h16" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Registered Today</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $todayUsers }}</p>
            </div>
        </div>
    </div>
</div>
