<div class="w-full m-4 px-2 bg-gray-100/20 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 flex flex-col gap-6">

    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto py-2">
        <button
            x-on:click="window.location.href='{{ route('citizen.grievance.index') }}'"
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-home class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            <span class="hidden lg:inline">Return to Home</span>
            <span class="lg:hidden">Home</span>
        </button>

        <button
            x-on:click="$dispatch('open-modal', 'chat')"
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            <span class="hidden lg:inline">Chat with HR Liaison</span>
            <span class="lg:hidden">Chat</span>
        </button>

        <button
            wire:click="downloadPdf"
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800/50 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-green-600 dark:text-green-400" />
            <span class="hidden lg:inline">Download PDF</span>
            <span class="lg:hidden">Download</span>
        </button>

        <button
            wire:click="print({{ $grievance->grievance_id }})"
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-800/50 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-printer class="w-5 h-5 text-purple-600 dark:text-purple-400" />
            <span class="hidden lg:inline">Print</span>
            <span class="lg:hidden">Print</span>
        </button>

    </div>

    <header class="border border-gray-300 dark:border-gray-700 rounded-xl p-4 sm:p-6 flex flex-col gap-5 transition">

    <div class="flex flex-wrap justify-end items-center gap-3 w-full">

        <p class="hidden sm:flex text-sm text-gray-600 dark:text-gray-400 italic items-center gap-1">
            <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
            <span>Last updated {{ $grievance->updated_at->diffForHumans() }}</span>
        </p>

    </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-col">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <x-heroicon-o-identification class="w-6 sm:w-8 h-6 sm:h-8 text-gray-500 dark:text-gray-400" />
                    Grievance ID:
                    <span class="text-blue-600 dark:text-blue-400 font-extrabold text-2xl sm:text-3xl">
                        #{{ $grievance->grievance_id }}
                    </span>
                </h2>
            </div>
        </div>

        <div class="flex flex-col">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2 flex-wrap">
                <x-heroicon-o-tag class="w-6 sm:w-8 h-6 sm:h-8 text-gray-500 dark:text-gray-400" />
                Title:
                <span class="text-xl sm:text-3xl text-blue-600 dark:text-blue-400 font-extrabold break-words">
                    {{ $grievance->grievance_title }}
                </span>
            </h2>
        </div>

        <div class="sm:hidden mt-2">
            <p class="text-xs text-gray-600 dark:text-gray-400 italic flex items-center gap-1">
                <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
                <span>Last updated {{ $grievance->updated_at->diffForHumans() }}</span>
            </p>
        </div>

    </header>

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8 font-sans">

        <div class="flex-1 flex flex-col gap-2">
            @php
                $info = [
                    ['label' => 'Type', 'value' => $grievance->grievance_type, 'icon' => 'briefcase'],
                    ['label' => 'Priority', 'value' => ucfirst($grievance->priority_level), 'icon' => 'exclamation-circle'],
                    ['label' => 'Anonymous', 'value' => $grievance->is_anonymous ? 'Yes' : 'No', 'icon' => 'user'],
                    ['label' => 'Filed On', 'value' => $grievance->created_at->format('M d, Y h:i A'), 'icon' => 'calendar-days'],
                    ['label' => 'Status', 'value' => ucfirst($grievance->grievance_status), 'icon' => 'chart-bar'],
                ];
            @endphp

            @foreach ($info as $item)
                <div class="flex items-start justify-between border-b border-gray-300 dark:border-zinc-700 py-2">
                    <div class="flex items-center gap-2 w-40">
                        <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="text-[16px] font-semibold text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-[15px] font-bold text-gray-900 dark:text-gray-100 flex-1 text-right">
                        {{ $item['value'] }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="flex-1 flex flex-col gap-4">

            <div class="border border-gray-300 dark:border-zinc-700 rounded-xl p-4">
                <h4 class="flex items-center gap-2 text-[14px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide">
                    <x-heroicon-o-document-text class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                    Details
                </h4>
                <div class="text-[15px] text-gray-900 dark:text-gray-200 leading-relaxed">
                    {!! $grievance->grievance_details !!}
                </div>
            </div>

            <div class="border border-gray-300 dark:border-zinc-700 rounded-xl p-4">
                <h4 class="flex items-center gap-2 text-[14px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide">
                    <x-heroicon-o-building-office class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                    Departments
                </h4>
                <div class="text-[15px] text-gray-900 dark:text-gray-200 leading-relaxed">
                    @forelse ($grievance->departments->unique('department_id') as $department)
                        <span class="inline-block bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-lg text-sm font-medium mr-1 mb-1">
                            {{ $department->department_name }}
                        </span>
                    @empty
                        <span class="text-gray-600 dark:text-gray-400 italic">No department assigned</span>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <div class="flex flex-col gap-3">
        <h4 class="flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-400">
            <x-heroicon-o-paper-clip class="w-4 h-4" /> Attachments
        </h4>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @forelse ($grievance->attachments as $attachment)
                @php
                    $url = Storage::url($attachment->file_path);
                    $extension = pathinfo($attachment->file_name ?? $attachment->file_path, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                @endphp

                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-300 dark:border-zinc-700 overflow-hidden transition group relative">
                    @if ($isImage)
                        <div x-data="{ show: false }" @keydown.window.escape="show = false">
                            <img
                                src="{{ $url }}"
                                alt="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                class="w-full h-36 object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                @click="show = true"
                            />
                            <div
                                x-show="show"
                                x-transition.opacity
                                x-cloak
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
                                @click.self="show = false"
                            >
                                <div x-transition.scale class="relative max-w-[90vw] max-h-[85vh]">
                                    <button
                                        @click="show = false"
                                        class="absolute top-3 right-3 text-white bg-black/50 rounded-full p-1 hover:bg-black"
                                    >
                                        <x-heroicon-o-x-mark class="w-5 h-5" />
                                    </button>
                                    <img
                                        src="{{ $url }}"
                                        class="rounded-lg border border-gray-300 dark:border-zinc-700 max-w-full max-h-[80vh]"
                                    />
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ $url }}" target="_blank"
                           class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center">
                            <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />
                            <span class="text-sm font-semibold truncate w-full text-gray-800 dark:text-gray-200">
                                {{ $attachment->file_name ?? basename($attachment->file_path) }}
                            </span>
                        </a>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-600 dark:text-gray-400">No attachments available</p>
            @endforelse
        </div>
    </div>

    <div
        x-data="{ open: false }"
        x-on:open-modal.window="if ($event.detail === 'chat') open = true"
        x-on:close-modal.window="if ($event.detail === 'chat') open = false"
        x-cloak
    >
        <div
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 bg-black/60 z-40"
            @click.self="open = false"
        ></div>

        <div
            x-show="open"
            x-transition
            x-transition.scale
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <div
                class="w-full max-w-7xl h-full bg-white dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 rounded-2xl shadow-2xl flex flex-col overflow-hidden"
            >
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5" />
                        Conversation
                    </h3>
                    <flux:button
                        @click="$dispatch('close-modal', 'chat')"
                        icon="x-mark"
                        variant="subtle"
                    />

                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <livewire:grievance.chat :grievance="$grievance" />
                </div>

            </div>
        </div>
    </div>

</div>

