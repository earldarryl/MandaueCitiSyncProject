<div class="w-full p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm">

    <div class="flex flex-col sm:flex-row justify-between gap-3 mb-5 items-start sm:items-center">

        <div class="w-full sm:w-64">
            <x-filter-select
                name="filter"
                placeholder="Filter by type"
                :options="['Grievances', 'Feedbacks']"
                wire:model="filter"
            />
        </div>

        <div class="flex flex-col sm:flex-row gap-3 mt-2 sm:mt-0 w-full sm:w-auto">
            <button wire:click="$refresh"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    w-full sm:w-auto bg-blue-100 text-blue-800 border-blue-300 hover:bg-blue-200
                    dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800/60
                    transition-all duration-200">
                Refresh
            </button>
        </div>

    </div>

    @forelse ($logs as $log)
        @php
            $user = $log->user;
            $isHr = $user->hasRole('hr_liaison');
            $badgeText = $isHr ? 'HR Action' : 'Citizen Action';
            $bgColor = $isHr ? 'bg-green-400 dark:bg-green-700' : 'bg-purple-500 dark:bg-purple-700';
            $timestamp = \Carbon\Carbon::parse($log->timestamp);
        @endphp

        <div class="mb-4 p-4 border border-gray-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-900 flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->name }} ({{ $user->email }})</p>
                <h3 class="text-md font-semibold text-gray-900 dark:text-white">{{ $log->action }}</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $timestamp->diffForHumans() }}</p>
            </div>

            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $bgColor }} text-white">
                {{ $badgeText }}
            </span>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
            <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
            <p class="text-sm font-medium">No activity logs available</p>
        </div>
    @endforelse

    <div class="mt-4">
        {{ $logs->links() }}
    </div>

</div>
