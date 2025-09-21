<div class="flex-col w-full h-full"
     x-data
     x-on:close-all-modals.window="
        document.querySelectorAll('[x-data][x-show]').forEach(el => {
            el.__x.$data.open = false
        })
     ">
    <header class="relative border-box w-full flex justify-end p-2 items-end">
        <flux:button
            icon="plus-circle"
            variant="primary"
            color="blue"
            wire:click="goToGrievanceCreate"
            >
            Add Grievance
        </flux:button>
    </header>
    <div class="flex w-full h-full p-6 bg-gray-50 dark:bg-zinc-900">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 w-full">

            @forelse ($grievances as $grievance)
                <div class="rounded-xl border border-gray-200 dark:border-zinc-700 shadow-sm hover:shadow-lg transition-all duration-300
                    bg-white dark:bg-zinc-800 flex flex-col p-5 h-[350px]">

                    <div class="flex flex-col flex-1 justify-between">
                        <header class="flex justify-between items-start mb-3">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 truncate">
                                {{ ucwords(strtolower($grievance->grievance_title)) }}
                            </h2>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full
                                {{ $grievance->priority_level === 'High' ? 'bg-red-100 text-red-600' :
                                ($grievance->priority_level === 'Normal' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600') }}">
                                {{ $grievance->priority_level }}
                            </span>
                        </header>

                        <div class="text-sm bg-gray-200 dark:bg-zinc-700 p-5 text-gray-600 rounded-xl dark:text-gray-300 mb-4 prose dark:prose-invert overflow-y-auto flex-1">
                            {!! $grievance->grievance_details !!}
                        </div>

                        <footer class="flex justify-between w-full items-center mt-2 pt-3">
                            <div class="text-xs">{{ $grievance->created_at->format('M d, Y') }}</div>
                            <div class="flex gap-2">
                                <flux:button
                                    icon="eye"
                                    variant="primary"
                                    color="zinc"
                                    x-on:click="$dispatch('open-modal', 'view-{{ $grievance->grievance_id }}')">
                                    View
                                </flux:button>
                                <flux:button
                                    icon="pencil-square"
                                    variant="primary"
                                    color="blue"
                                    wire:click="goToGrievanceEdit({{ $grievance->grievance_id }})">
                                    Edit
                                </flux:button>
                                <flux:button
                                    icon="trash"
                                    variant="danger"
                                    x-on:click="$dispatch('open-modal', 'delete-{{ $grievance->grievance_id }}')"
                                    >
                                    Delete
                                </flux:button>
                            </div>
                        </footer>
                    </div>
                </div>

                <!-- View Modal -->
                <x-modal name="view-{{ $grievance->grievance_id }}">
                    <div class="p-6 space-y-6 bg-white dark:bg-black h-1/2">

                        <!-- Header -->
                        <div class="flex items-center justify-between pb-3">
                            <h2 class="text-xl font-bold">
                                {{ $grievance->grievance_title }}
                            </h2>
                            <flux:button
                                icon="x-mark"
                                x-on:click="$dispatch('close-modal', 'view-{{ $grievance->grievance_id }}')"
                            />
                        </div>

                        <!-- Meta Info -->
                        <div class="grid sm:grid-cols-4 gap-4 text-sm">
                            <div class="flex flex-col gap-2 bg-gray-50 dark:bg-zinc-800 p-3 rounded-lg">
                                <span class="font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em]">Type:</span>
                                <p class="text-gray-600 dark:text-gray-400">{{ $grievance->grievance_type }}</p>
                            </div>

                            <div class="flex flex-col gap-2 bg-gray-50 dark:bg-zinc-800 p-3 rounded-lg">
                                <span class="font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em]">Priority:</span>
                                <p>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $grievance->priority_level === 'High' ? 'bg-red-100 text-red-600' :
                                        ($grievance->priority_level === 'Normal' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600') }}">
                                        {{ $grievance->priority_level }}
                                    </span>
                                </p>
                            </div>

                            <div class="flex flex-col gap-2 bg-gray-50 dark:bg-zinc-800 p-3 rounded-lg">
                                <span class="font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em]">Filed On:</span>
                                <p class="text-gray-600 dark:text-gray-400">{{ $grievance->created_at->format('M d, Y h:i A') }}</p>
                            </div>

                            <!-- New Status Field -->
                            <div class="flex flex-col gap-2 bg-gray-50 dark:bg-zinc-800 p-3 rounded-lg">
                                <span class="font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em]">Status:</span>
                                <p>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $grievance->grievance_status === 'Pending' ? 'bg-yellow-100 text-yellow-600' :
                                        ($grievance->grievance_status === 'Resolved' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $grievance->grievance_status }}
                                    </span>
                                </p>
                            </div>
                        </div>


                        <!-- Grievance Details -->
                        <div>
                            <div class="flex flex-col gap-2 prose dark:prose-invert text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg shadow">
                                <h3 class="text-sm font-semibold text-gray-500 dark:text-white/70 uppercase tracking-[.25em]">Details</h3>
                                {!! $grievance->grievance_details !!}
                            </div>
                        </div>

                        <!-- Departments -->
                        <div>
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
                        </div>

                        <!-- Attachments -->
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
                                            <a href="{{ $url }}" target="_blank" class="flex flex-col items-center gap-2">
                                                <x-heroicon-o-document class="w-12 h-12 text-gray-500" />
                                                <span class="text-xs text-black dark:text-white truncate">
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

                        <!-- Footer -->
                        <div class="flex justify-end pt-4">
                            <flux:button
                                variant="primary"
                                color="zinc"
                                x-on:click="$dispatch('close-modal', 'view-{{ $grievance->grievance_id }}')">
                                Close
                            </flux:button>
                        </div>
                    </div>
                </x-modal>


                <!-- Delete Modal -->
                <x-modal name="delete-{{ $grievance->grievance_id }}">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-red-600">Confirm Delete</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to delete this grievance?</p>
                        <div class="flex justify-end gap-2">
                            <flux:button
                                    variant="primary"
                                    color="zinc"
                                    x-on:click="$dispatch('close-modal', 'delete-{{ $grievance->grievance_id }}')"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Cancel</flux:button>
                            <flux:button
                                    variant="danger"
                                    wire:click="deleteGrievance({{ $grievance->grievance_id }})"
                                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Delete</flux:button>
                        </div>
                    </div>
                </x-modal>
            @empty
                <p class="col-span-3 text-center text-gray-500">No grievances found.</p>
            @endforelse
        </div>
    </div>
</div>
