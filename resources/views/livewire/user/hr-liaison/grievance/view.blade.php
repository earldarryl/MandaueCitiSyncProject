<div class="max-w-5xl mx-auto mt-4 p-6 flex flex-col gap-3 bg-white dark:bg-black rounded-xl shadow">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3">
        <h2 class="inline-flex gap-3 items-center">
            <span class="text-xl font-bold capitalize text-gray-800 dark:text-white">
                {{ $grievance->grievance_title }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400 italic">
                Last updated {{ $grievance->updated_at->diffForHumans() }}
            </span>
        </h2>



        <a href="{{ route('citizen.grievance.index') }}">
            <flux:button icon="arrow-left" variant="primary" color="zinc">
                Back
            </flux:button>
        </a>
    </div>

    <div class="grid sm:grid-cols-5 gap-4">
        <div class="flex flex-col gap-3 bg-gray-200 dark:bg-zinc-800 p-3 rounded-lg">
            <span class="font-semibold text-[11px] text-gray-600 dark:text-gray-400">TYPE:</span>
            <span class="font-bold text-2x1">{{ $grievance->grievance_type }}</span>
        </div>
        <div class="flex flex-col gap-3 bg-gray-200 dark:bg-zinc-800 p-3 rounded-lg">
            <span class="font-semibold text-[11px] text-gray-600 dark:text-gray-400">PRIORITY:</span>
            <span class="font-bold">{{ ucfirst($grievance->priority_level) }}</span>
        </div>
        <div class="flex flex-col gap-3 bg-gray-200 dark:bg-zinc-800 p-3 rounded-lg">
            <span class="font-semibold text-[11px] text-gray-600 dark:text-gray-400">ANONYMOUS:</span>
            <span class="font-bold text-2x1">{{ $grievance->is_anonymous ? 'Yes' : 'No' }}</span>
        </div>
        <div class="flex flex-col gap-3 bg-gray-200 dark:bg-zinc-800 p-3 rounded-lg">
            <span class="font-semibold text-[11px] text-gray-600 dark:text-gray-400">FILED ON:</span>
            <span class="font-bold text-2x1">{{ $grievance->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="flex flex-col gap-3 bg-gray-200 dark:bg-zinc-800 p-3 rounded-lg">
            <span class="font-semibold text-[11px] text-gray-600 dark:text-gray-400">STATUS:</span>
            <span class="font-bold text-2x1">{{ ucfirst($grievance->grievance_status) }}</span>
        </div>
    </div>

    <div class="flex flex-col gap-2 prose dark:prose-invert text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg shadow">
        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em]">Details</h3>
        {!! $grievance->grievance_details !!}
    </div>

    <div class="flex flex-col gap-2 bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg">
        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em] mb-2">Assigned Departments</h3>
        <ul class="list-disc list-inside text-sm p-3 rounded-lg text-gray-700 dark:text-gray-300">
            @forelse ($grievance->departments->unique('department_id') as $department)
                <li>{{ $department->department_name }}</li>
            @empty
                <li>No department assigned</li>
            @endforelse
        </ul>
    </div>

    <div>
        <h3 class="text-sm font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em] mb-2">Attachments</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @forelse ($grievance->attachments as $attachment)
                @php
                    $url = Storage::url($attachment->file_path);
                    $extension = pathinfo($attachment->file_name ?? $attachment->file_path, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                @endphp

                <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-3 text-center shadow hover:shadow-md transition">
                    @if ($isImage)
                        <a href="{{ $url }}" target="_blank">
                            <img src="{{ $url }}"
                                alt="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                class="h-32 w-full object-cover rounded hover:scale-105 transition">
                        </a>
                    @else
                        <a href="{{ $url }}" target="_blank"
                        class="flex flex-col items-center justify-center gap-2 bg-white dark:bg-zinc-900 rounded-lg p-4 shadow hover:shadow-md transition duration-200 ease-in-out border border-gray-200 dark:border-zinc-700">

                            <span class="flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-zinc-800 rounded-full">
                                <x-heroicon-o-document class="w-8 h-8 text-gray-500 dark:text-gray-300" />
                            </span>

                            <span class="w-full text-center text-sm font-medium text-gray-800 dark:text-white truncate max-w-[160px]">
                                {{ $attachment->file_name ?? basename($attachment->file_path) }}
                            </span>
                        </a>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">No attachments</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">
            Conversation
        </h3>
        <livewire:grievance.chat :grievance="$grievance" />
    </div>
</div>


