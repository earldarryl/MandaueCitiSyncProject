<div class="flex flex-col w-full space-y-6">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Reports Statistics
        </h2>
    </div>

    <div wire:loading.class="opacity-50"
        class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 gap-6">

        <div
            class="group relative bg-gradient-to-br from-teal-50 to-teal-100 dark:from-zinc-800 dark:to-zinc-900
                border border-teal-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-teal-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-teal-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.users class="h-8 w-8 text-teal-600 dark:text-teal-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Citizens Served</p>
            <p class="relative text-3xl font-bold text-teal-600 dark:text-teal-400 tracking-tight">
                {{ $citizenCount }}
            </p>
        </div>

        <div
            class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-zinc-800 dark:to-zinc-900
                border border-emerald-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-emerald-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-emerald-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.users class="h-8 w-8 text-emerald-600 dark:text-emerald-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Active Fellow HR Liaisons</p>
            <p class="relative text-3xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight">
                {{ $activeFellowHrLiaisons }}
            </p>
        </div>

        <div
            class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-zinc-800 dark:to-zinc-900
                border border-indigo-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-indigo-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-indigo-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.inbox class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Total Assignments Received</p>
            <p class="relative text-3xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">{{ $totalReceived }}</p>
        </div>

        <div
            class="group relative bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-zinc-800 dark:to-zinc-900
                border border-cyan-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
                transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">

            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-cyan-200/20 to-transparent opacity-0
                group-hover:opacity-100 blur-xl transition-all duration-500 pointer-events-none"></div>

            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-cyan-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.identification class="h-8 w-8 text-cyan-600 dark:text-cyan-400" />
            </div>

            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Latest Report Ticket ID</p>
            <p class="relative text-2xl font-bold text-cyan-600 dark:text-cyan-400 tracking-tight">
                {{ $latestGrievanceTicketId ?? '—' }}
            </p>

            @if($latestGrievanceTicketId)
                <a href="{{ route('hr-liaison.grievance.view', $latestGrievanceTicketId) }}" wire:navigate
                    class="mt-2 px-4 py-1 text-xs rounded-full font-medium
                        bg-gradient-to-br from-cyan-100 to-cyan-200 dark:from-zinc-700 dark:to-zinc-800
                        text-cyan-700 dark:text-cyan-400 hover:from-cyan-200 hover:to-cyan-300 dark:hover:from-zinc-600 dark:hover:to-zinc-700
                        transition-all duration-300 shadow-sm">
                    View
                </a>
            @endif

        </div>

    </div>

    <!-- Detailed Status Cards -->
    {{-- <div wire:loading.class="opacity-50"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-8 gap-6">

        <!-- Pending -->
        <div class="group relative bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-zinc-800 dark:to-zinc-900
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

        <!-- Acknowledged -->
        <div class="group relative bg-gradient-to-br from-amber-50 to-amber-100 dark:from-zinc-800 dark:to-zinc-900
            border border-amber-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-amber-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-amber-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.hand-raised class="h-8 w-8 text-amber-600 dark:text-amber-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Acknowledged</p>
            <p class="relative text-3xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">{{ $acknowledged }}</p>
        </div>

        <!-- In Progress -->
        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-zinc-800 dark:to-zinc-900
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

        <!-- Escalated -->
        <div class="group relative bg-gradient-to-br from-orange-50 to-orange-100 dark:from-zinc-800 dark:to-zinc-900
            border border-orange-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-orange-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-orange-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.arrow-up-circle class="h-8 w-8 text-orange-600 dark:text-orange-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Escalated</p>
            <p class="relative text-3xl font-bold text-orange-600 dark:text-orange-400 tracking-tight">{{ $escalated }}</p>
        </div>

        <!-- Resolved -->
        <div class="group relative bg-gradient-to-br from-green-50 to-green-100 dark:from-zinc-800 dark:to-zinc-900
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

        <!-- Unresolved -->
        <div class="group relative bg-gradient-to-br from-red-50 to-red-100 dark:from-zinc-800 dark:to-zinc-900
            border border-red-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-red-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-red-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.x-circle class="h-8 w-8 text-red-600 dark:text-red-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Unresolved</p>
            <p class="relative text-3xl font-bold text-red-600 dark:text-red-400 tracking-tight">{{ $unresolved }}</p>
        </div>

        <!-- Closed -->
        <div class="group relative bg-gradient-to-br from-slate-50 to-slate-100 dark:from-zinc-800 dark:to-zinc-900
            border border-slate-200/50 dark:border-zinc-700 rounded-2xl shadow-sm hover:shadow-lg
            transition-all duration-300 p-6 flex flex-col items-center justify-center gap-2">
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-slate-200/20 to-transparent opacity-0
                        group-hover:opacity-100 blur-xl transition-all duration-500"></div>
            <div class="relative bg-white dark:bg-zinc-800 p-3 rounded-full shadow-sm border border-slate-200/50
                        dark:border-zinc-700 group-hover:scale-105 transition-transform duration-300">
                <flux:icon.archive-box class="h-8 w-8 text-slate-600 dark:text-slate-400" />
            </div>
            <p class="relative text-base font-semibold text-gray-700 dark:text-gray-300 mt-2">Closed</p>
            <p class="relative text-3xl font-bold text-slate-600 dark:text-slate-400 tracking-tight">{{ $closed }}</p>
        </div>

        <!-- Overdue -->
        <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-zinc-800 dark:to-zinc-900
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

    </div> --}}

</div>
