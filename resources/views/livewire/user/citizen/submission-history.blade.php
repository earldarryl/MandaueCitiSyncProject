<div class="w-full p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm">

    <div class="flex justify-end gap-3 mb-5">
        <button
            wire:click="clearHistory"
            class="flex gap-2 justify-start px-5 py-2.5 text-sm font-semibold rounded-lg border
                bg-red-100 text-red-800 border-red-300
                hover:bg-red-200 hover:border-red-400
                dark:bg-red-900/40 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800/50
                transition-all duration-200">
            <flux:icon.trash />
            <span>Clear History</span>
        </button>

        <button
            wire:click="restoreHistory"
            @disabled(!$canRestore)
            class="flex gap-2 justify-start px-5 py-2.5 text-sm font-semibold rounded-lg border
                bg-blue-100 text-blue-800 border-blue-300
                hover:bg-blue-200 hover:border-blue-400
                dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800/60
                transition-all duration-200
                disabled:opacity-50 disabled:cursor-not-allowed">
            <flux:icon.arrow-path />
            <span>Restore History</span>
        </button>
    </div>

    @forelse ($groupedGrievances as $dateLabel => $grievances)
        <div wire:key="group-{{ md5($dateLabel) }}">
            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">{{ $dateLabel }}</h2>

            <ol class="relative border-s border-gray-200 dark:border-gray-700 mb-8">
                @foreach ($grievances as $grievance)
                    <li class="mb-10 ms-5 group w-full" wire:key="grievance-{{ $grievance->grievance_id }}">
                        <span
                            class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-zinc-900 dark:bg-blue-900 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </span>

                        <div wire:target="removeFromHistory({{ $grievance->grievance_id }})" wire:loading.remove>
                            <div
                                class="px-6 py-5 w-full bg-white dark:bg-zinc-900/60 border border-gray-200 dark:border-zinc-700/80 rounded-2xl
                                    hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 ease-in-out backdrop-blur-sm">

                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex flex-col gap-1.5">
                                        <span class="self-start text-xs font-semibold px-2.5 py-0.5 rounded-md border
                                            shadow-sm backdrop-blur-sm transition-all duration-200
                                            {{ match($grievance->grievance_status) {
                                                'pending' => 'bg-gray-100 text-gray-800 border-gray-400
                                                            dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600',

                                                'in_progress' => 'bg-blue-100 text-blue-800 border-blue-400
                                                                dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-600',

                                                'resolved' => 'bg-green-100 text-green-800 border-green-400
                                                            dark:bg-green-900/40 dark:text-green-300 dark:border-green-600',

                                                'rejected' => 'bg-red-100 text-red-800 border-red-400
                                                            dark:bg-red-900/40 dark:text-red-300 dark:border-red-600',

                                                default => 'bg-gray-100 text-gray-800 border-gray-400
                                                            dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600',
                                            } }}">
                                            {{ ucfirst(str_replace('_', ' ', $grievance->grievance_status)) }}
                                        </span>

                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug tracking-tight">
                                            {{ $grievance->grievance_title ?? 'Untitled Grievance' }}
                                        </h3>

                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Submitted on
                                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                                {{ $grievance->created_at->format('F j, Y - g:i A') }}
                                            </span>
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
                                    <a href="{{ route('citizen.grievance.view', $grievance->grievance_id) }}" wire:navigate
                                        class="flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-md border
                                            border-gray-300 bg-gray-100 text-gray-700 hover:bg-gray-200
                                            dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600
                                            dark:hover:bg-gray-600 dark:hover:border-gray-500 transition">
                                        <flux:icon.eye class="w-4 h-4" />
                                        View Details
                                    </a>

                                    <button wire:click="removeFromHistory({{ $grievance->grievance_id }})"
                                        class="flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-md
                                            bg-red-100 text-red-800 border border-red-300 hover:bg-red-200
                                            dark:bg-red-900/40 dark:text-red-300 dark:border-red-700
                                            dark:hover:bg-red-800/60 transition-all duration-200">
                                        <flux:icon.trash class="w-4 h-4" />
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div wire:target="removeFromHistory({{ $grievance->grievance_id }})" wire:loading>
                            <div
                                class="px-6 py-5 w-full bg-white dark:bg-zinc-900/60 border border-gray-200 dark:border-zinc-700/80 rounded-2xl">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="h-4 w-24 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                        <div class="h-5 w-48 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                        <div class="h-3 w-32 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                    </div>
                                    <div class="h-5 w-14 rounded-full bg-gray-200 dark:bg-zinc-700"></div>
                                </div>

                                <div class="border-t border-gray-200 dark:border-zinc-700/70 my-3"></div>

                                <div class="flex justify-end items-center gap-2">
                                    <div class="h-8 w-24 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-8 w-20 rounded-md bg-gray-200 dark:bg-zinc-700"></div>
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
            <p class="text-sm font-medium">No grievances submitted yet</p>
        </div>
    @endforelse

    @if ($hasMore)
    <div class="flex justify-center items-center">
        <div wire:target="loadMore" wire:loading.remove>
            <div class="flex justify-center mt-6">
                <button wire:click="loadMore"
                    class="px-6 py-2.5 text-sm font-semibold rounded-md bg-blue-100 text-blue-800 border border-blue-300
                        hover:bg-blue-200 dark:bg-blue-900/40 dark:text-blue-300 dark:border-blue-700
                        dark:hover:bg-blue-800/60 transition-all duration-200">
                    Load More
                </button>
            </div>
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
