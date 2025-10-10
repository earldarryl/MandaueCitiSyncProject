<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4 text-center">Grievance Report</h1>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <p><strong>Complainant:</strong> {{ $grievance->user->name }}</p>
            <p><strong>Department:</strong> {{ $grievance->assignments->first()?->department->name ?? 'N/A' }}</p>
        </div>
        <div>
            <p><strong>Date Filed:</strong> {{ $grievance->created_at->format('F j, Y') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($grievance->status) }}</p>
        </div>
    </div>

    <div class="border-t pt-4">
        <p class="text-gray-700 whitespace-pre-line">{!! $grievance->grievance_details !!}</p>
    </div>

    @if ($grievance->attachments)
        <div class="mt-6">
            <h3 class="font-medium mb-2">Attachments:</h3>
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
    @endif
</div>
