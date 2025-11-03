<div class="flex flex-col w-full space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-user class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Total Users</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                {{ $totalUsers }}
            </p>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 -mt-1">Registered in the system</p>

            <div class="grid grid-cols-2 gap-3 mt-3 w-full">
                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-user-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Citizens</span>
                    </div>
                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $citizenUsers }}
                    </span>
                </div>

                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-briefcase class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">HR Liaisons</span>
                    </div>
                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $hrLiaisonUsers }}
                    </span>
                </div>
            </div>
        </div>

        <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-zinc-800 dark:to-zinc-900
            border border-indigo-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-indigo-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none"
                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                </svg>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Assignments</p>
            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                {{ $totalAssignments }}
            </p>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">All assignments processed</p>

            <div x-data="{ open: false }" class="mt-3 w-full">
                <button @click="open = !open"
                        class="flex items-center justify-between w-full px-4 py-2 bg-indigo-100 dark:bg-zinc-700
                               rounded-lg text-sm font-medium text-indigo-700 dark:text-indigo-300
                               hover:bg-indigo-200 dark:hover:bg-zinc-600 transition focus:outline-none">
                    <span>Assignments by Department</span>
                    <svg :class="{ 'rotate-180': open }"
                         class="w-4 h-4 ml-2 transition-transform"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-collapse x-show="open"
                     class="mt-2 w-full bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 border border-indigo-200/40 dark:border-zinc-700">
                    <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach($assignmentsByDepartment as $dept)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $dept['department_name'] }}
                                </span>
                                <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                    {{ $dept['total'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
            border border-blue-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-4">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-blue-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <x-heroicon-o-document-text class="h-8 w-8 text-blue-600 dark:text-blue-400"/>
            </div>

            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Forms Collected</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                {{ $totalGrievances + $totalFeedbacks }}
            </p>
            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 -mt-1">Total grievances and feedback</p>

            <div class="grid grid-cols-2 gap-3 mt-3 w-full">
                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Grievances</span>
                    </div>
                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $totalGrievances }}
                    </span>
                </div>

                <div class="flex flex-col items-center justify-center bg-white/70 dark:bg-zinc-800/50
                            border border-blue-200/40 dark:border-zinc-700 rounded-xl p-3 shadow-sm
                            transition hover:shadow-md hover:bg-blue-50/70 dark:hover:bg-zinc-700/60">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-chat-bubble-oval-left class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Feedback</span>
                    </div>
                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400 mt-1">
                        {{ $totalFeedbacks }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
