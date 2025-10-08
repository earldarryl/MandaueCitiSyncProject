<div class="w-full mx-auto my-6 p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-lg flex flex-col lg:flex-row gap-8">
    <!-- LEFT PANEL: Grievance Details -->
    <div class="flex-1 flex flex-col gap-6">
        <!-- Header -->
        <header class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pb-4 border-b border-gray-200 dark:border-zinc-800">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $grievance->grievance_title }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                    Last updated {{ $grievance->updated_at->diffForHumans() }}
                </p>
            </div>

            <!-- Dropdown -->
            <flux:dropdown position="left" align="start">
                <flux:button icon="ellipsis-horizontal" color="gray" />
                <flux:menu>
                    <flux:menu.item
                        x-on:click="window.location.href='{{ route('citizen.grievance.index') }}'"
                        icon="home"
                    >
                        <span>Return to Home</span>
                    </flux:menu.item>

                    <flux:menu.item
                        x-on:click="$dispatch('open-modal', 'chat')"
                        icon="chat-bubble-left-ellipsis"
                    >
                        <span>Chat with HR Liaison</span>
                    </flux:menu.item>

                </flux:menu>
            </flux:dropdown>
        </header>

        <!-- Info Grid -->
        <div class="grid sm:grid-cols-5 gap-4">
            @foreach ([
                'TYPE' => $grievance->grievance_type,
                'PRIORITY' => ucfirst($grievance->priority_level),
                'ANONYMOUS' => $grievance->is_anonymous ? 'Yes' : 'No',
                'FILED ON' => $grievance->created_at->format('M d, Y h:i A'),
                'STATUS' => ucfirst($grievance->grievance_status),
            ] as $label => $value)
                <div class="flex flex-col bg-gray-100 dark:bg-zinc-800 p-3 rounded-lg text-center shadow-sm">
                    <span class="text-[11px] uppercase font-semibold text-gray-600 dark:text-gray-400 tracking-wide">{{ $label }}</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $value }}</span>
                </div>
            @endforeach
        </div>

        <!-- Details -->
        <div class="flex flex-col gap-2 bg-gray-50 dark:bg-zinc-800 p-5 rounded-lg shadow-sm">
            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Details</h4>
            <div class="prose prose-sm dark:prose-invert max-w-none">
                {!! $grievance->grievance_details !!}
            </div>
        </div>

        <!-- Departments -->
        <div class="bg-gray-50 dark:bg-zinc-800 p-5 rounded-lg shadow-sm">
            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">
                Assigned Departments
            </h4>
            <ul class="list-disc list-inside text-sm text-gray-800 dark:text-gray-200">
                @forelse ($grievance->departments->unique('department_id') as $department)
                    <li>{{ $department->department_name }}</li>
                @empty
                    <li>No department assigned</li>
                @endforelse
            </ul>
        </div>

        <!-- Attachments -->
        <div class="flex flex-col gap-3">
            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                Attachments
            </h4>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse ($grievance->attachments as $attachment)
                    @php
                        $url = Storage::url($attachment->file_path);
                        $extension = pathinfo($attachment->file_name ?? $attachment->file_path, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                    @endphp

                    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-sm hover:shadow-md transition group relative">
                        @if ($isImage)
                            <div x-data="{ show: false }" @keydown.window.escape="show = false">
                                <img
                                    src="{{ $url }}"
                                    alt="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                    class="w-full h-36 object-cover cursor-pointer hover:scale-105 transition-transform"
                                    @click="show = true"
                                />
                                <!-- Zoom Modal -->
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
                                            class="rounded-lg shadow-lg max-w-full max-h-[80vh] border border-gray-700"
                                        />
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ $url }}" target="_blank"
                               class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center">
                                <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />
                                <span class="text-sm font-semibold truncate w-full text-gray-700 dark:text-gray-200">
                                    {{ $attachment->file_name ?? basename($attachment->file_path) }}
                                </span>
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No attachments available</p>
                @endforelse
            </div>
        </div>
    </div>

    <x-modal name="chat" maxWidth="7xl"  maxHeight="full">
        <div class="w-full h-full flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-zinc-800">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5" />
                    Conversation
                </h3>

                <flux:button
                    icon="x-mark"
                    @click="$dispatch('close-modal', 'chat')"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white"
                />

            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <livewire:grievance.chat :grievance="$grievance" />
            </div>
        </div>
    </x-modal>

</div>
