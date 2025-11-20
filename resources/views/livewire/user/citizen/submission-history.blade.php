<div class="w-full p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm">

    <div class="flex flex-col justify-between items-start sm:items-center gap-3 mb-5">

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full">
            <x-filter-select
                name="filter"
                placeholder="Filter by type"
                :options="['Grievances', 'Feedbacks']"
            />

            <div class="flex flex-col gap-1 w-full"
                x-data="{ selected: @entangle('selectedDate') }"
                x-init="$nextTick(() => {
                    flatpickr($refs.dateInput, {
                        dateFormat: 'Y-m-d',
                        defaultDate: selected,
                        onChange: (selectedDates, dateStr) => {
                            selected = dateStr
                        }
                    });
                })"
            >
                <div class="relative w-full">
                    <div
                        class="flex items-center justify-between px-3 py-2 border border-gray-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-900 cursor-pointer"
                        @click="$refs.dateInput._flatpickr.open()"
                    >
                        <input
                            type="text"
                            x-ref="dateInput"
                            x-model="selected"
                            readonly
                            placeholder="Select date"
                            class="w-full bg-transparent text-[12px] focus:outline-none cursor-pointer"
                        />
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                x-show="selected"
                                @click.stop="selected = null; $wire.set('selectedDate', null); $refs.dateInput._flatpickr.clear()"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition p-1 rounded"
                            >
                                <x-heroicon-o-x-mark class="w-4 h-4"/>
                            </button>
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-500" />
                        </div>
                    </div>
                </div>

            </div>

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
                        $bgColor = $log->reference_table === 'grievances'
                            ? 'bg-green-500 dark:bg-green-700'
                            : 'bg-purple-500 dark:bg-purple-700';
                        $svgColor = 'text-white';
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

                            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm hover:shadow-md transition p-4 mb-4 border border-gray-200 dark:border-zinc-700">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full p-2">
                                            <x-heroicon-o-clipboard-document-check class="h-6 w-6"/>
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col gap-1">
                                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            Submission
                                        </span>

                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug tracking-tight">
                                            {{ ucwords(str_replace('_', ' ', $log->action_type)) }}
                                        </h3>

                                        <div class="flex flex-col gap-2 mt-2">

                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">CC Summary:</span>
                                                <span class="bg-blue-100 dark:bg-blue-800/40 text-blue-700 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded-full">
                                                    {{ $log->cc_summary ?? 'N/A' }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">SQD Summary:</span>
                                                <span class="bg-gray-100 dark:bg-zinc-700/40 text-gray-700 dark:text-gray-300 text-xs font-medium px-2 py-1 rounded-full">
                                                    {{ $log->sqd_summary ?? 'N/A' }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase">When:</span>
                                                <span class="text-xs text-gray-700 dark:text-gray-300">
                                                    {{ \Carbon\Carbon::parse($log->date_submitted)->format('F j, Y – g:i A') ?? 'N/A' }}
                                                </span>
                                            </div>

                                        </div>
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
