<div class="w-full p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full">
            <x-filter-select
                name="filter"
                placeholder="Filter by type"
                :options="['Grievances', 'Feedbacks']"
            />

            <button
                wire:click="applyFilter"
                wire:loading.attr="disabled"
                wire:target="applyFilter"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    bg-gray-100 text-gray-800 border-gray-300
                    hover:bg-gray-200 hover:border-gray-400
                    dark:bg-zinc-800 dark:text-gray-200 dark:border-zinc-700
                    dark:hover:bg-zinc-700
                    whitespace-nowrap
                    transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                <flux:icon.adjustments-horizontal class="w-4 h-4" />
                <span wire:loading.remove wire:target="applyFilter">Apply Filter</span>
                <span wire:loading wire:target="applyFilter">Processing...</span>
            </button>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 mt-2 sm:mt-0 w-full justify-end">
            <button
                wire:click="clearHistory"
                wire:loading.attr="disabled"
                wire:target="clearHistory"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    bg-red-100 text-red-800 border-red-300
                    hover:bg-red-200 hover:border-red-400
                    dark:bg-red-900/40 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800/50
                    transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                <flux:icon.trash class="w-4 h-4" />
                <span wire:loading.remove wire:target="clearHistory">Clear History</span>
                <span wire:loading wire:target="clearHistory">Processing...</span>
            </button>

            <button
                wire:click="restoreHistory"
                wire:loading.attr="disabled"
                wire:target="restoreHistory"
                class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                    bg-blue-100 text-blue-800 border-blue-300
                    hover:bg-blue-200 hover:border-blue-400
                    dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800/60
                    transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                <flux:icon.arrow-path class="w-4 h-4" />
                <span wire:loading.remove wire:target="restoreHistory">Restore History</span>
                <span wire:loading wire:target="restoreHistory">Processing...</span>
            </button>
        </div>

    </div>

    @forelse ($groupedLogs as $dateLabel => $logs)
        <div wire:key="group-{{ md5($dateLabel) }}">
            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ $dateLabel }}</h2>

            <ol class="relative border-s border-gray-200 dark:border-gray-700 mb-8">
                @foreach ($logs as $log)
                    @php
                        if ($log->reference_table === 'grievances') {
                            $bgColor  = 'bg-green-400 dark:bg-green-700';
                            $svgColor = 'text-white';
                        } else {
                            $bgColor  = 'bg-purple-500 dark:bg-purple-700';
                            $svgColor = 'text-white';
                        }
                    @endphp

                    <li class="mb-10 ms-5 group w-full" wire:key="log-{{ $log->id }}">
                        <span
                            class="absolute flex items-center justify-center w-6 h-6 {{ $bgColor }} rounded-full -start-3 ring-8 ring-white dark:ring-zinc-900 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-2.5 h-2.5 {{ $svgColor }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </span>

                        <div
                            class="px-6 py-5 w-full bg-white dark:bg-zinc-900/60 border border-gray-200 dark:border-zinc-700/80 rounded-2xl
                                hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out backdrop-blur-sm">

                            <div class="flex justify-between items-start mb-3">
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        {{ ucwords(str_replace('_', ' ', $log->action_type)) }}
                                    </span>

                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug tracking-tight">
                                        {{ $log->description ?? 'No description provided' }}
                                    </h3>

                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">
                                            {{ ucwords(str_replace('_', ' ', $log->reference_table)) }}
                                        </span>
                                        •
                                        <span>{{ $log->created_at->format('F j, Y - g:i A') }}</span>
                                    </p>
                                </div>

                                @if ($loop->first && $loop->parent->first)
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full border
                                            border-blue-300 dark:bg-blue-900/50 dark:text-blue-200 dark:border-blue-800 shadow-sm">
                                        Latest
                                    </span>
                                @endif
                            </div>

                            <div class="border-t border-gray-200 dark:border-zinc-700/70 my-3"></div>

                            <div class="flex justify-end items-center gap-2">
                                @if($log->reference_table === 'grievances')
                                    @php
                                        $grievance = \App\Models\Grievance::find($log->reference_id);
                                    @endphp

                                    @if($grievance)
                                        <a href="{{ route('citizen.grievance.view', $grievance) }}" wire:navigate
                                            class="flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-md
                                                bg-blue-100 text-blue-800 border border-blue-300 hover:bg-blue-200
                                                dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700
                                                dark:hover:bg-blue-800/60 transition-all duration-200">
                                            <flux:icon.eye class="w-4 h-4" />
                                            View
                                        </a>
                                    @endif
                                @endif
                                <button wire:click="removeFromHistory({{ $log->id }})"
                                    class="flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-md
                                        bg-red-100 text-red-800 border border-red-300 hover:bg-red-200
                                        dark:bg-red-900/40 dark:text-red-300 dark:border-red-700
                                        dark:hover:bg-red-800/60 transition-all duration-200">
                                    <flux:icon.trash class="w-4 h-4" />
                                    Remove
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
            <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
            <p class="text-sm font-medium">No submission history available.</p>
        </div>
    @endforelse

    @if ($hasMore)
        <div class="flex justify-center items-center mt-6">
            <div wire:target="loadMore" wire:loading.remove>
                <button wire:click="loadMore"
                    class="px-6 py-2.5 text-sm font-semibold rounded-md bg-blue-100 text-blue-800 border border-blue-300
                        hover:bg-blue-200 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700
                        dark:hover:bg-blue-800/60 transition-all duration-200">
                    Load More
                </button>
            </div>

            <div wire:target="loadMore" wire:loading>
                <div class="w-full flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    @endif
</div>
