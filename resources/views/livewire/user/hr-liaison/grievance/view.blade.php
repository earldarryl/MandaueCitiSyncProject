<div class="w-full m-4 px-2 bg-gray-100/20 dark:bg-zinc-800 border border-gray-300 dark:border-zinc-700 flex flex-col gap-6">

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto py-2">
        <x-responsive-nav-link
            href="{{ route('hr-liaison.grievance.index') }}"
            wire:navigate
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-gray-200
                border border-gray-500 dark:border-gray-200
                hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-home class="w-5 h-5 text-gray-700 dark:text-gray-300" />
            <span class="hidden lg:inline">Return to Home</span>
            <span class="lg:hidden">Home</span>
        </x-responsive-nav-link>

        <button
            x-on:click="$dispatch('open-modal', 'chat')"
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                border border-blue-400 dark:border-blue-600
                hover:bg-blue-200 dark:hover:bg-blue-800/50 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            <span class="hidden lg:inline">Chat with Citizen</span>
            <span class="lg:hidden">Chat</span>
        </button>

    </div>

    <!-- Grievance Header & Info -->
    <header class="border border-gray-300 dark:border-gray-700 rounded-xl p-4 flex flex-col gap-5 transition">
        <!-- ID & Last Updated -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2 flex-wrap">
                <x-heroicon-o-identification class="w-6 sm:w-7 h-6 sm:h-7 text-gray-500 dark:text-gray-400" />
                Grievance ID:
                <span class="text-blue-600 dark:text-blue-400 font-extrabold text-2xl sm:text-3xl">
                    #{{ $grievance->grievance_id }}
                </span>
            </h2>

            <p class="hidden sm:flex text-sm text-gray-600 dark:text-gray-400 italic items-center gap-1 shrink-0">
                <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
                <span>Last updated {{ $grievance->updated_at->diffForHumans() }}</span>
            </p>
        </div>

        <!-- Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2 flex-wrap">
                <x-heroicon-o-tag class="w-6 sm:w-7 h-6 sm:h-7 text-gray-500 dark:text-gray-400" />
                Title:
                <span
                    class="text-xl sm:text-3xl text-blue-600 dark:text-blue-400 font-extrabold truncate overflow-hidden capitalize text-ellipsis max-w-full sm:max-w-[600px]"
                    title="{{ $grievance->grievance_title }}"
                >
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

    <!-- Grievance Info & Departments -->
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8 font-sans">
        <div class="flex-1 flex flex-col gap-2">
            @php
                $info = [
                    ['label' => 'Type', 'value' => $grievance->grievance_type, 'icon' => 'briefcase'],
                    ['label' => 'Priority Level', 'value' => ucfirst($grievance->priority_level), 'icon' => 'exclamation-circle'],
                    [
                        'label' => 'Submitted By',
                        'value' => $grievance->is_anonymous
                            ? 'Anonymous User'
                            : ($grievance->user->name ?? 'Unknown'),
                        'icon' => 'user',
                    ],
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
                <div class="text-[15px] text-gray-900 dark:text-gray-200 leading-8 ">
                    @forelse ($grievance->departments->unique('department_id') as $department)
                        <span
                            class="inline-block bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950/30 dark:to-blue-900/30
                                border border-blue-400 dark:border-blue-600
                                text-blue-700 dark:text-blue-300 font-medium text-sm
                                px-3 py-1.5 rounded-full shadow-sm
                                hover:shadow-md hover:brightness-105 transition-all duration-200 ease-in-out mr-1 mb-1"
                        >
                            {{ $department->department_name }}
                        </span>
                    @empty
                        <span class="text-gray-600 dark:text-gray-400 italic">No department assigned</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-3" x-data="{ showMore: false, zoomSrc: null }">
        <h4 class="flex items-center gap-2 text-[14px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide">
            <x-heroicon-o-paper-clip class="w-5 h-5 text-gray-500 dark:text-gray-400" /> Attachments
        </h4>

        @if ($grievance->attachments->isNotEmpty())
            @php
                $visibleAttachments = $grievance->attachments->take(4);
                $extraAttachments = $grievance->attachments->slice(3);
            @endphp

            <div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($visibleAttachments as $index => $attachment)
                        @php
                            $url = Storage::url($attachment->file_path);
                            $extension = pathinfo($attachment->file_name ?? $attachment->file_path, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                        @endphp

                        @if ($loop->iteration < 4 && $grievance->attachments->count() > 4)
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-300 dark:border-zinc-700 overflow-hidden relative group transition">
                                @if ($isImage)
                                    <img
                                        src="{{ $url }}"
                                        alt="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                        class="w-full h-36 object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                        @click="zoomSrc = '{{ $url }}'"
                                    />
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

                        @elseif ($loop->iteration === 4 && $grievance->attachments->count() > 4)
                            <div
                                class="relative bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-300 dark:border-zinc-700 overflow-hidden cursor-pointer group"
                                @click="showMore = true"
                            >
                                @if ($isImage)
                                    <img src="{{ $url }}" class="w-full h-36 object-cover opacity-60" />
                                @else
                                    <div class="flex items-center justify-center w-full h-36 bg-gray-200 dark:bg-gray-700">
                                        <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />
                                    </div>
                                @endif

                                <div class="absolute inset-0 flex items-center justify-center bg-black/60 text-white font-semibold text-lg">
                                    +{{ $grievance->attachments->count() - 3 }} more
                                </div>
                            </div>

                        @else
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-300 dark:border-zinc-700 overflow-hidden relative group transition">
                                @if ($isImage)
                                    <img
                                        src="{{ $url }}"
                                        alt="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                        class="w-full h-36 object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                        @click="zoomSrc = '{{ $url }}'"
                                    />
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
                        @endif
                    @endforeach
                </div>

                <div
                    x-show="showMore"
                    x-transition.opacity
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
                    @click.self="showMore = false"
                >
                    <div
                        x-transition.scale
                        class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-5xl w-[90%] max-h-[85vh] overflow-hidden"
                    >
                        <!-- Header -->
                        <header class="sticky top-0 bg-white dark:bg-gray-900 z-10 px-6 py-4 border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between">
                            <h2 class="flex items-center gap-2 text-lg sm:text-xl font-semibold text-gray-700 dark:text-gray-300 tracking-wide">
                                <x-heroicon-o-folder-plus class="w-6 h-6 sm:w-7 sm:h-7 text-gray-500 dark:text-gray-400" />
                                More Attachments
                            </h2>

                            <button
                                @click="showMore = false"
                                class="text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 border border-gray-300 dark:border-zinc-700 rounded-full p-2 transition-all duration-200"
                                aria-label="Close"
                            >
                                <x-heroicon-o-x-mark class="w-5 h-5" />
                            </button>
                        </header>

                        <!-- Content -->
                        <div class="p-6 overflow-y-auto max-h-[70vh]">
                            @if($extraAttachments->isEmpty())
                                <div class="text-center text-gray-500 dark:text-gray-400 py-12">
                                    <x-heroicon-o-inbox class="w-12 h-12 mx-auto mb-3 opacity-70" />
                                    <p>No extra attachments found.</p>
                                </div>
                            @else
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
                                    @foreach ($extraAttachments as $attachment)
                                        @php
                                            $url = Storage::url($attachment->file_path);
                                            $extension = pathinfo($attachment->file_name ?? $attachment->file_path, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                                        @endphp

                                        <div class="group relative bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden transition-all duration-200 hover:shadow-md">
                                            @if ($isImage)
                                                <img
                                                    src="{{ $url }}"
                                                    alt="Attachment"
                                                    class="w-full h-40 object-cover cursor-pointer transition-all duration-200 group-hover:opacity-85"
                                                    @click="zoomSrc = '{{ $url }}'"
                                                />
                                            @else
                                                <a
                                                    href="{{ $url }}"
                                                    target="_blank"
                                                    class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center transition-all duration-200 hover:bg-gray-200/60 dark:hover:bg-gray-700/60"
                                                >
                                                    <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />
                                                    <span class="text-sm font-medium truncate w-full text-gray-800 dark:text-gray-200">
                                                        {{ $attachment->file_name ?? basename($attachment->file_path) }}
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    x-show="zoomSrc"
                    x-cloak
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90"
                    @click.self="zoomSrc = null"
                >
                    <div class="relative max-w-5xl w-[90%] flex items-center justify-center">
                        <img :src="zoomSrc" class="w-full max-h-[85vh] object-contain rounded-lg shadow-lg" />

                        <div class="absolute top-4 right-4 flex items-center gap-2">
                            <a
                                :href="zoomSrc"
                                download
                                class="bg-black/60 hover:bg-black/80 text-white rounded-full p-2 transition"
                                title="Download Image"
                            >
                                <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                            </a>

                            <button
                                @click="zoomSrc = null"
                                class="bg-black/60 hover:bg-black/80 text-white rounded-full p-2 transition"
                                title="Close"
                            >
                                <x-heroicon-o-x-mark class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
                <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
                <p class="text-sm font-medium">No attachments available</p>
            </div>
        @endif
    </div>

    <!-- Chat with Citizen Modal -->
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
            class="fixed inset-0 z-[45] flex items-center justify-center"
        >
            <div
                class="w-full max-w-7xl h-full bg-white dark:bg-black border border-gray-300 dark:border-zinc-700 rounded-2xl shadow-2xl flex flex-col overflow-hidden"
            >
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300 dark:border-zinc-700 bg-white dark:bg-black">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5" />
                        Conversation with Citizen
                    </h3>
                    <flux:button
                        @click="$dispatch('close-modal', 'chat')"
                        icon="x-mark"
                        variant="subtle"
                    />
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <livewire:grievance.chat :grievance="$grievance" />
                </div>

            </div>
        </div>
    </div>

</div>
