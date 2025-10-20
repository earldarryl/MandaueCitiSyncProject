<div class="w-full p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl shadow-sm">
    <div class="flex justify-end gap-2 mb-4">
        <button wire:click="clearHistory" class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-md border border-yellow-400 hover:bg-yellow-200">
            Clear History
        </button>

        <button wire:click="restoreHistory" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md border border-blue-400 hover:bg-blue-200">
            Restore History
        </button>
    </div>

    <ol class="relative border-s border-gray-200 dark:border-gray-700">
        @forelse ($grievances as $grievance)
            <li class="mb-10 ms-6 group">
                <span
                    class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-zinc-900 dark:bg-blue-900 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                    </svg>
                </span>

                <div
                    class="px-6 py-2 bg-gray-50 dark:bg-zinc-800/80 border-2 border-gray-200 dark:border-zinc-700 rounded-2xl hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-300">

                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                                {{ $grievance->grievance_title ?? 'Untitled Grievance' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                Submitted on <span class="font-medium text-gray-700 dark:text-gray-300">{{ $grievance->created_at->format('F j, Y - g:i A') }}</span>
                            </p>
                        </div>

                        @if ($loop->first)
                            <span
                                class="bg-blue-100 text-blue-800 text-lg font-semibold px-4 py-2 rounded-full border border-blue-300 dark:bg-blue-900 dark:text-blue-200 dark:border-blue-800 shadow-sm">
                                 Latest!
                            </span>
                        @endif
                    </div>

                    <div class="py-3 border-t border-gray-200 dark:border-zinc-700">
                        <h4 class="flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2 tracking-wide">
                            <x-heroicon-o-building-office class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            Departments
                        </h4>
                        @if ($grievance->departments->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($grievance->departments as $dept)
                                    <span
                                        class="text-xs font-medium px-2.5 py-0.5 rounded-md border border-blue-300 bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:border-blue-700 dark:text-blue-300">
                                        {{ $dept->department_name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 dark:text-gray-500">No departments assigned.</p>
                        @endif
                    </div>

                    <div class="py-3 border-t border-gray-200 dark:border-zinc-700">
                        <h4 class="flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2 tracking-wide">
                            <x-heroicon-o-document-text class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            Details
                        </h4>
                        <div
                            class="text-[15px] text-gray-800 dark:text-gray-100 leading-relaxed bg-gray-100 dark:bg-zinc-700/80 border border-gray-200 dark:border-zinc-600 rounded-lg p-4">
                            {!! $grievance->grievance_details ?? '<em class="text-gray-400 dark:text-gray-500">No description provided.</em>' !!}
                        </div>
                    </div>

                    <div class="py-3 border-t border-gray-200 dark:border-zinc-700">
                        <h4 class="flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2 tracking-wide">
                            <x-heroicon-o-exclamation-circle class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            Priority Level
                        </h4>
                        @if (!empty($grievance->priority_level))
                            <span
                                class="inline-flex items-center text-xs font-medium px-3 py-1 rounded-full border shadow-sm
                                    {{ $grievance->priority_level === 'High'
                                        ? 'bg-red-100 text-red-800 border-red-400 dark:bg-red-900 dark:text-red-300 dark:border-red-700'
                                        : ($grievance->priority_level === 'Normal'
                                            ? 'bg-yellow-100 text-yellow-800 border-yellow-400 dark:bg-yellow-900 dark:text-yellow-300 dark:border-yellow-700'
                                            : 'bg-green-100 text-green-800 border-green-400 dark:bg-green-900 dark:text-green-300 dark:border-green-700') }}">
                                {{ $grievance->priority_level }}
                            </span>
                        @else
                            <p class="text-xs text-gray-400 dark:text-gray-500">No priority level set.</p>
                        @endif
                    </div>

                    <div class="py-2 border-t border-gray-200 dark:border-zinc-700 flex justify-end gap-2">

                        @if ($grievance->grievance_id)
                            <a href="{{ route('citizen.grievance.view', $grievance->grievance_id) }}" wire:navigate
                                class="px-4 py-3.5 text-xs font-semibold rounded-md border border-gray-300 text-gray-700 bg-gray-100
                                            hover:bg-gray-100 hover:border-gray-400
                                            dark:bg-zinc-700 dark:text-gray-200 dark:border-zinc-600 dark:hover:bg-zinc-600 dark:hover:border-zinc-500
                                            transition">
                                View Details
                            </a>
                        @endif


                        <button wire:click="removeFromHistory({{ $grievance->grievance_id }})"
                            class="px-3 py-1 text-xs font-semibold rounded-md bg-yellow-100 text-yellow-700 border border-yellow-400 hover:bg-yellow-200 dark:bg-yellow-900/50 dark:text-yellow-300 dark:border-yellow-700 dark:hover:bg-yellow-800">
                            Remove from History
                        </button>
                    </div>
                </div>
            </li>
        @empty
            <li class="ms-6 text-gray-500 dark:text-gray-400">No grievances submitted yet.</li>
        @endforelse
    </ol>
</div>
