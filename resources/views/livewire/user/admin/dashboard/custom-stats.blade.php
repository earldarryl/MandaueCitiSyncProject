<div class="p-4 rounded-lg w-full shadow-sm bg-white dark:bg-zinc-800">
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Users Card -->
        <div class="relative bg-blue-500 rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-shadow duration-300 min-h-[250px] flex flex-col justify-between lg:col-span-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute -top-4 -right-4 w-32 h-32 opacity-20 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            <div class="z-10 p-6 flex flex-col justify-center items-center">
                <h2 class="text-xl font-bold text-white mb-4">Users</h2>
                <span class="text-4xl font-extrabold text-white">{{ $totalUsers }}</span>
                <p class="text-sm text-white/80 mt-1 text-center">Total of registered users</p>
            </div>
        </div>

        <!-- Assignment Card -->
        <div class="relative bg-indigo-700 rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-shadow duration-300 min-h-[250px] flex flex-col justify-between lg:col-span-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute -top-4 -right-4 w-32 h-32 opacity-20 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
            </svg>
            <div class="z-10 p-6 flex flex-col justify-center items-center">
                <h2 class="text-xl font-bold text-white mb-4">Assignments</h2>
                <span class="text-4xl font-extrabold text-white">{{ $totalAssignments }}</span>
                <p class="text-sm text-white/80 mt-1">Total processed</p>
            </div>
        </div>

        <!-- Grievances Card (bigger) -->
        <div class="relative bg-zinc-900 dark:bg-zinc-700/50 rounded-xl shadow-lg overflow-hidden group hover:shadow-2xl transition-shadow duration-300 min-h-[300px] flex flex-col justify-between lg:col-span-2">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="absolute -top-6 -right-6 w-36 h-36 opacity-20 text-white"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <div class="z-10 p-6 text-center">
                <h2 class="text-xl font-bold text-white mb-4">Grievances</h2>
                <span class="text-4xl font-extrabold text-white">{{ $totalGrievances }}</span>
                <p class="text-sm text-white/80 mt-1">Total submitted</p>
            </div>
            <!-- Status Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 z-10 p-4">
                <div class="p-3 bg-yellow-300/80 text-yellow-900 text-sm font-semibold text-center shadow">
                    Pending
                    <div class="text-xl font-bold">{{ $pendingGrievances }}</div>
                </div>
                <div class="p-3 bg-red-500/80 text-white text-sm font-semibold text-center shadow">
                    Unresolved
                    <div class="text-xl font-bold">{{ $unresolvedGrievances }}</div>
                </div>
                <div class="p-3 bg-blue-500/80 text-white text-sm font-semibold text-center shadow">
                    In Progress
                    <div class="text-xl font-bold">{{ $inProgressGrievances }}</div>
                </div>
                <div class="p-3 bg-green-500/80 text-white text-sm font-semibold text-center shadow">
                    Resolved
                    <div class="text-xl font-bold">{{ $resolvedGrievances }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
