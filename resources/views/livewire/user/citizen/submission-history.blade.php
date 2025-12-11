<div class="w-full p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm">

    <div class="flex flex-col justify-between items-start sm:items-center gap-3 mb-5">

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full">
            <x-filter-select
                name="filter"
                placeholder="Filter by Type"
                :options="['Reports', 'Feedbacks']"
            />

            <x-filter-select
                name="filter"
                placeholder="Filter by Action Type"
                :options="$actionTypeOptions"
            />

            <x-date-picker
                name="selectedDate"
                placeholder="Pick a date"
                :model="'selectedDate'"
            />

        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full">
            <button
                wire:click="applyFilter"
                wire:loading.attr="disabled"
                wire:target="applyFilter"
                class="flex justify-center items-center gap-2 px-4 py-2 bg-blue-600 text-white w-full font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                <flux:icon.adjustments-horizontal class="w-4 h-4" />
                <span wire:loading.remove wire:target="applyFilter">Apply Filter</span>
                <span wire:loading wire:target="applyFilter">Processing...</span>
            </button>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 mt-2 sm:mt-0 w-full justify-end">

            <div x-data="{ showClearModal: false }" class="relative">
                <button @click="showClearModal = true"
                    wire:loading.attr="disabled"
                    wire:target="clearHistory"
                    class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                        bg-red-100 text-red-800 border-red-300
                        hover:bg-red-200 hover:border-red-400
                        dark:bg-red-900/40 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800/50
                        transition-all duration-200
                        disabled:opacity-50 disabled:cursor-not-allowed">
                    <flux:icon.trash class="w-4 h-4" />
                    <span wire:loading.remove wire:target="clearHistory">Clear History</span>
                    <span wire:loading wire:target="clearHistory">Processing...</span>
                </button>

                <div x-show="showClearModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-50"></div>

                <div x-show="showClearModal" x-transition.scale
                    class="fixed inset-0 flex items-center justify-center z-50 p-4">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 text-center space-y-5">
                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                            <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Clear History</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Are you sure you want to clear all your submission history? This action cannot be undone.</p>

                        <div class="flex justify-center gap-3 mt-4">
                            <button type="button" @click="showClearModal = false"
                                class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                Cancel
                            </button>
                            <flux:button variant="danger" icon="trash" wire:click="clearHistory" @click="showClearModal = false">
                                Yes, Clear
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ showRestoreModal: false }" class="relative">
                <button @click="showRestoreModal = true"
                    wire:loading.attr="disabled"
                    wire:target="restoreHistory"
                    class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-semibold rounded-lg border
                        bg-blue-100 text-blue-800 border-blue-300
                        hover:bg-blue-200 hover:border-blue-400
                        dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800/60
                        transition-all duration-200
                        disabled:opacity-50 disabled:cursor-not-allowed">
                    <flux:icon.arrow-path class="w-4 h-4" />
                    <span wire:loading.remove wire:target="restoreHistory">Restore History</span>
                    <span wire:loading wire:target="restoreHistory">Processing...</span>
                </button>

                <div x-show="showRestoreModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-50"></div>

                <div x-show="showRestoreModal" x-transition.scale
                    class="fixed inset-0 flex items-center justify-center z-50 p-4">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 text-center space-y-5">
                        <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-500/20 mx-auto">
                            <x-heroicon-o-arrow-path class="w-10 h-10 text-blue-500" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Restore History</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Are you sure you want to restore all hidden records back to your submission history?</p>

                        <div class="flex justify-center gap-3 mt-4">
                            <button type="button" @click="showRestoreModal = false"
                                class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                Cancel
                            </button>
                            <flux:button variant="primary" color="blue" icon="arrow-path" wire:click="restoreHistory" @click="showRestoreModal = false">
                                Yes, Restore
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @forelse ($groupedLogs as $dateLabel => $logs)
        <div wire:key="group-{{ md5($dateLabel) }}">
            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ $dateLabel }}</h2>

            <ol class="relative border-s border-gray-200 dark:border-gray-700 mb-8">
                @foreach ($logs as $log)
                    @php
                        $grievance = $log->reference_table === 'grievances'
                            ? \App\Models\Grievance::find($log->reference_id)
                            : null;

                        $feedback = $log->reference_table === 'feedback'
                            ? \App\Models\Feedback::find($log->reference_id)
                            : null;

                        if (!$grievance && !$feedback) {
                            continue;
                        }

                        $bgColor = $log->reference_table === 'grievances'
                            ? 'bg-blue-500 dark:bg-blue-700'
                            : 'bg-purple-500 dark:bg-purple-700';

                        $svgColor = 'text-white';
                    @endphp

                    <li class="mb-10 ms-5 group w-full" wire:key="log-{{ $log->id }}">
                        <span
                            class="absolute flex items-center justify-center w-6 h-6 {{ $bgColor }} rounded-full -start-3 ring-8 ring-white dark:ring-zinc-900 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-2.5 h-2.5 {{ $svgColor }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </span>

                        <div
                            class="px-6 py-5 w-full bg-white dark:bg-zinc-900/60 border border-gray-200 dark:border-zinc-700/80 rounded-2xl
                                hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out backdrop-blur-sm">

                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm hover:shadow-md transition p-4 mb-4 border border-gray-200 dark:border-zinc-700">

                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full p-2">
                                            <x-heroicon-o-clipboard-document-check class="h-6 w-6" />
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col gap-3">

                                        <div>
                                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                Submission
                                            </span>

                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug tracking-tight">
                                                {{ ucwords(str_replace('_', ' ', $log->action_type)) }}
                                            </h3>

                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">
                                                    {{ strtolower($log->action_type) === 'update' ? 'Updated:' : 'Submitted:' }}
                                                </span>
                                                <span class="text-xs text-gray-700 dark:text-gray-300">
                                                    {{ \Carbon\Carbon::parse(
                                                        strtolower($log->action_type) === 'update' ? $log->updated_at : $log->created_at
                                                    )->format('F j, Y — g:i A') }}
                                                </span>
                                            </div>
                                        </div>

                                        @if ($grievance)
                                        <div class="mt-2">
                                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Report Details</h4>

                                            <div class="mt-2 space-y-2">

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Ticket ID:</span>
                                                    <span class="bg-blue-100 dark:bg-blue-800/40 text-blue-700 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded-full">
                                                        {{ $grievance->grievance_ticket_id }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Title:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $grievance->grievance_title }}</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Category:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $grievance->grievance_category }}</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Priority:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $grievance->priority_level }}</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Type:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $grievance->grievance_type }}</span>
                                                </div>
                                            </div>

                                            <div class="mt-3 flex gap-2 justify-end">
                                                <a href="{{ route('citizen.grievance.view', $grievance) }}"
                                                    wire:navigate
                                                    class="px-3 py-1 text-xs rounded-md border
                                                        bg-gray-100 text-gray-800 border-gray-300
                                                        hover:bg-gray-200 hover:border-gray-400
                                                        dark:bg-gray-900/40 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-800/50
                                                        transition-colors duration-200">
                                                    View
                                                </a>

                                                <button type="button"
                                                    wire:click="removeFromHistory({{ $log->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="removeFromHistory({{ $log->id }})"
                                                    class="px-3 py-1 text-xs rounded-md border
                                                        bg-red-100 text-red-800 border-red-300
                                                        hover:bg-red-200 hover:border-red-400
                                                        dark:bg-red-900/40 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800/50
                                                        transition-colors duration-200">
                                                    <span wire:loading.remove wire:target="removeFromHistory({{ $log->id }})" >
                                                        Remove
                                                    </span>
                                                    <span wire:loading wire:target="removeFromHistory({{ $log->id }})">
                                                        Processing...
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        @endif

                                        @if ($feedback)
                                        <div class="mt-2">
                                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Feedback Details</h4>

                                            <div class="mt-2 space-y-2">

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Service:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $feedback->service }}</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Region:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $feedback->region }}</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Gender:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ ucfirst($feedback->gender) }}</span>
                                                </div>

                                                <div class="flex items-start gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Suggestions:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $feedback->suggestions ?? 'None' }}</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">Email:</span>
                                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $feedback->email ?? 'Not Provided' }}</span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">CC Summary:</span>
                                                    <span class="bg-blue-100 dark:bg-blue-800/40 text-blue-700 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded-full">
                                                        {{ $feedback->cc_summary }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">SQD Summary:</span>
                                                    <span class="bg-gray-100 dark:bg-zinc-700/40 text-gray-700 dark:text-gray-300 text-xs font-medium px-2 py-1 rounded-full">
                                                        {{ $feedback->sqd_summary }}
                                                    </span>
                                                </div>

                                            </div>
                                        </div>

                                         <div class="mt-3 flex gap-2 justify-end">
                                            <button type="button"
                                                wire:click="removeFromHistory({{ $log->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="removeFromHistory({{ $log->id }})"
                                                class="px-3 py-1 text-xs rounded-md border
                                                    bg-red-100 text-red-800 border-red-300
                                                    hover:bg-red-200 hover:border-red-400
                                                    dark:bg-red-900/40 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800/50
                                                    transition-colors duration-200">
                                                <span wire:loading.remove wire:target="removeFromHistory({{ $log->id }})">
                                                    Remove
                                                </span>
                                                <span wire:loading wire:target="removeFromHistory({{ $log->id }})">
                                                    Processing...
                                                </span>
                                            </button>
                                        </div>
                                        @endif

                                    </div>
                                </div>

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
                <flux:button wire:click="loadMore" wire:loading.attr="disabled" variant="ghost">
                    <span wire:loading.remove wire:target="loadMore">Load More</span>
                    <span wire:loading wire:target="loadMore">Loading...</span>
                </flux:button>
            </div>

            <div wire:target="loadMore" wire:loading>
                <div class="w-full flex items-center justify-center gap-2">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    @elseif (!$hasMore && $hasAny)
        <div class="flex justify-center items-center mt-6">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                No more records to load.
            </span>
        </div>
    @endif

</div>
