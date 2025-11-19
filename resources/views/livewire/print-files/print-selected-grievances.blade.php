<div class="max-w-5xl mx-auto bg-white p-10 rounded-lg shadow space-y-12">

    <div class="text-center border-b pb-6">
        <h1 class="text-3xl font-bold text-gray-800">Selected Grievance Reports</h1>

        @if(isset($hr_liaison))
            <p class="text-gray-600 mt-2">
                HR Liaison: <strong>{{ $hr_liaison->name }}</strong>
            </p>
        @elseif(isset($admin))
            <p class="text-gray-600 mt-2">
                Admin: <strong>{{ $admin->name }}</strong>
            </p>
        @endif

        <p class="text-gray-500 text-sm">{{ now()->format('F j, Y, g:i A') }}</p>

        <button onclick="window.print()" class="no-print mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            Print
        </button>
    </div>

    @forelse ($grievances as $grievance)
        <div class="border rounded-lg p-6 shadow-sm page-break">
            <h2 class="text-xl font-semibold mb-4 text-center text-blue-800">
                Grievance Report #{{ $grievance->grievance_ticket_id }}
            </h2>

            <div class="grid grid-cols-2 gap-6 mb-4">
                <div>
                    <p>
                        <strong>Citizen:</strong>
                        @if ($grievance->is_anonymous)
                            <span class="italic text-gray-500">Anonymous</span>
                        @else
                            {{ $grievance->user->name ?? 'Unknown' }}
                        @endif
                    </p>

                    <p><strong>Departments:</strong>
                        @if(isset($admin))
                            {{ $grievance->departments->pluck('department_name')->join(', ') ?? 'N/A' }}
                        @else
                            {{ $grievance->assignments->first()?->department->department_name ?? 'N/A' }}
                        @endif
                    </p>
                </div>
                <div>
                    <p><strong>Date Filed:</strong> {{ $grievance->created_at->format('F j, Y') }}</p>
                    <p><strong>Priority Level:</strong> {{ ucfirst($grievance->priority_level) }}</p>
                    <p><strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $grievance->grievance_status ?? 'N/A')) }}</p>
                </div>
            </div>

            <div class="border-t pt-4">
                <p class="text-gray-700 whitespace-pre-line">{!! $grievance->grievance_details !!}</p>
            </div>

            @if ($grievance->attachments && $grievance->attachments->count() > 0)
                <div class="mt-6">
                    <h3 class="font-medium mb-2">Attachments:</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($grievance->attachments as $attachment)
                            @php
                                $url = Storage::url($attachment->file_path);
                                $extension = pathinfo($attachment->file_name ?? $attachment->file_path, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp

                            <div class="bg-gray-100 rounded-xl border border-gray-300 overflow-hidden transition group relative">
                                @if ($isImage)
                                    <img src="{{ $url }}" alt="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                        class="w-full h-36 object-cover cursor-pointer hover:opacity-80 transition-opacity" />
                                @else
                                    <a href="{{ $url }}" target="_blank"
                                        class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center">
                                        <x-heroicon-o-document class="w-10 h-10 text-gray-500" />
                                        <span class="text-sm font-semibold truncate w-full text-gray-800">
                                            {{ $attachment->file_name ?? basename($attachment->file_path) }}
                                        </span>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @if (!$loop->last)
            <hr class="my-10 border-gray-300">
        @endif
    @empty
        <p class="text-center text-gray-600 italic">No selected grievances available for printing.</p>
    @endforelse
</div>
