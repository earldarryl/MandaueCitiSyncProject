<div class="flex flex-col w-full space-y-6">

    <!-- Top Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Users Card -->
        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

            <!-- Glow hover effect -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>

            <!-- Main icon -->
            <div
                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-user class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
            </div>

            <!-- Title -->
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300">Users</p>
            <p class="relative text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                {{ $totalUsers }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 -mt-2">Total registered users</p>

            <!-- Sub-box structure -->
            <div class="grid grid-cols-2 gap-3 mt-3 w-full">
                <!-- Citizens -->
                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-user-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Citizens</span>
                    </div>
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $citizenUsers }}
                    </span>
                </div>

                <!-- HR Liaisons -->
                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-briefcase class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">HR Liaisons</span>
                    </div>
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $hrLiaisonUsers }}
                    </span>
                </div>
            </div>
        </div>
        <!-- Assignments Card -->
        <div
            class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-zinc-800 dark:to-zinc-900
                border border-indigo-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div
                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-indigo-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                </svg>
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Assignments</p>
            <p class="relative text-3xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                {{ $totalAssignments }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total processed</p>
        </div>

        <!-- Grievances Card -->
        <div
            class="group relative bg-gradient-to-br from-zinc-100 to-zinc-200 dark:from-zinc-800 dark:to-zinc-900
                border border-zinc-300/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-zinc-300/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div
                class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-zinc-300/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-8 w-8 text-zinc-700 dark:text-zinc-300" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Grievances</p>
            <p class="relative text-3xl font-bold text-zinc-700 dark:text-zinc-200 tracking-tight">
                {{ $totalGrievances }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total submitted</p>
        </div>

    </div>
</div>
