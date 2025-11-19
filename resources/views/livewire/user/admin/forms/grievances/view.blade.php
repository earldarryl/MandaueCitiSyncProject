<div class="w-full px-2 bg-gray-100/20 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 flex flex-col gap-6"
     x-data="{ openRerouteModal: false, openStatusModal: false, openPriorityModal: false, showModal: false }"
     x-on:close-status-modal.window="openStatusModal = false"
     x-on:close-priority-modal.window="openPriorityModal = false"
     x-on:update-success-modal.window="showModal = true"
     x-on:close-success-modal.window="showModal = false"
     x-on:close-all-modals.window="showModal = false"
     >

    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto py-2">
        <x-responsive-nav-link
            href="{{ route('admin.forms.grievances.index') }}"
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
            @click="openRerouteModal = true"
            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                border border-blue-500 dark:border-blue-400
                hover:bg-blue-200 dark:hover:bg-blue-800/50
                focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                transition-all duration-200">
            <x-heroicon-o-arrow-path class="w-5 h-5" />
            <span>Reroute</span>
        </button>

        <button
            @click="openStatusModal = true"
            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300
                border border-yellow-500 dark:border-yellow-400
                hover:bg-yellow-200 dark:hover:bg-yellow-800/50
                focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-700
                transition-all duration-200">
            <x-heroicon-o-pencil-square class="w-5 h-5" />
            <span>Update Status</span>
        </button>

        <button
            @click="openPriorityModal = true"
            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                border border-red-500 dark:border-red-400
                hover:bg-red-200 dark:hover:bg-red-800/50
                focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                transition-all duration-200">
            <x-heroicon-o-exclamation-circle class="w-5 h-5" />
            <span>Update Priority</span>
        </button>

    </div>

    <header class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex flex-col gap-6 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex flex-col gap-2">
                <h2 class="flex items-center gap-2 flex-wrap text-md font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase">
                    <x-heroicon-o-identification class="w-4 h-4 inline mr-1 text-gray-500 dark:text-gray-400" />
                    TICKET ID
                </h2>
                <p class="text-3xl sm:text-4xl font-extrabold text-blue-700 dark:text-blue-400 leading-tight">
                    {{ $grievance->grievance_ticket_id }}
                </p>
            </div>

            <p class="hidden sm:flex text-sm text-gray-500 dark:text-gray-400 italic items-center gap-1 shrink-0">
                <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
                <span>Last updated {{ $grievance->updated_at->diffForHumans() }}</span>
            </p>
        </div>

        <div class="flex flex-col gap-2">
            <h2 class="flex items-center gap-2 flex-wrap text-md font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase">
                <x-heroicon-o-tag class="w-4 h-4 inline mr-1 text-gray-500 dark:text-gray-400" />
                TITLE
            </h2>
            <p
                class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 truncate overflow-hidden capitalize leading-tight"
                title="{{ $grievance->grievance_title }}"
            >
                {{ $grievance->grievance_title }}
            </p>
        </div>

        <div class="sm:hidden mt-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 italic flex items-center gap-1">
                <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
                <span>Last updated {{ $grievance->updated_at->diffForHumans() }}</span>
            </p>
        </div>
    </header>

    @if (!$grievance->is_anonymous && $grievance->user?->userInfo)
        @php
            $info = $grievance->user->userInfo;

            $citizenInfo = [
                [
                    'label' => 'Full Name',
                    'value' => trim("{$info->first_name} {$info->middle_name} {$info->last_name} {$info->suffix}") ?: 'N/A',
                    'icon'  => 'user',
                ],
                [
                    'label' => 'Barangay / Sitio',
                    'value' => trim(($info->barangay ?? 'N/A') . ($info->sitio ? ', ' . $info->sitio : '')),
                    'icon'  => 'map-pin',
                ],
            ];
        @endphp

        <div class="flex flex-col gap-2 font-sans p-3 rounded-sm">
            <h4 class="flex items-center gap-2 text-[17px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide uppercase">
                <x-heroicon-o-user-circle class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                Citizen Information
            </h4>

            <div class="flex flex-col divide-y divide-gray-200 dark:divide-zinc-700">
                @foreach ($citizenInfo as $item)
                    <div class="flex items-start justify-between py-2">
                        <div class="flex items-center gap-2 w-44">
                            <x-dynamic-component
                                :component="'heroicon-o-' . $item['icon']"
                                class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0"
                            />
                            <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300">
                                {{ $item['label'] }}
                            </span>
                        </div>
                        <span class="text-[15px] font-bold text-gray-900 dark:text-gray-100 flex-1 text-right break-words">
                            {{ $item['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    @else
        <div class="flex items-center gap-3 font-sans p-4 rounded-sm border border-gray-300 dark:border-zinc-700">
            <x-heroicon-o-user class="w-6 h-6 text-gray-500 dark:text-gray-400" />
            <div class="flex flex-col">
                <h4 class="text-[17px] font-semibold text-gray-600 dark:text-gray-400 tracking-wide uppercase">
                    Anonymous User
                </h4>
                <p class="text-[15px] text-gray-700 dark:text-gray-300">
                    This grievance was filed anonymously. No personal information is available.
                </p>
            </div>
        </div>
    @endif

    <div class="p-3 rounded-sm">
        <h4 class="flex items-center gap-2 text-[17px] font-semibold text-gray-600 dark:text-gray-400 mb-2 tracking-wide uppercase">
            <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            Grievance Information
        </h4>

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8">

            <div class="flex-1 flex flex-col gap-2">
                @php
                    use Carbon\Carbon;

                    $status = strtolower($grievance->grievance_status);

                    $isCompleted = in_array($status, ['resolved', 'unresolved', 'closed']);
                    $isEscalated = $status === 'escalated';

                    $endDate = $isCompleted ? $grievance->updated_at : now();
                    $created = Carbon::parse($grievance->created_at);
                    $elapsedDays = ceil($created->diffInHours($endDate) / 24);

                    $expectedDays = $grievance->processing_days ?? 7;

                    $isOverdue = !$isCompleted && $elapsedDays > $expectedDays;

                    $processingDisplay = match (true) {
                        $isCompleted => "{$elapsedDays} / {$expectedDays} days (Completed)",
                        $isEscalated => "{$elapsedDays} / {$expectedDays} days (Escalated — under review)",
                        $isOverdue   => "{$elapsedDays} / {$expectedDays} days (Overdue)",
                        default      => "{$elapsedDays} / {$expectedDays} days (Ongoing)",
                    };

                    $priorityClass = match (strtolower($grievance->priority_level)) {
                        'low'      => 'text-blue-600 font-semibold',
                        'normal'   => 'text-gray-600 font-semibold',
                        'medium'   => 'text-yellow-600 font-semibold',
                        'high'     => 'text-orange-600 font-semibold',
                        'critical' => 'text-red-700 font-extrabold',
                        default    => 'text-gray-600 font-semibold',
                    };

                    $class = match (true) {
                        $isCompleted => 'text-green-600 font-semibold',
                        $isEscalated => 'text-amber-600 font-semibold',
                        $isOverdue   => 'text-red-600 font-semibold',
                        default      => '',
                    };

                    $info = [
                        ['label' => 'Type', 'value' => $grievance->grievance_type, 'icon' => 'briefcase'],
                        ['label' => 'Category', 'value' => $grievance->grievance_category ?? 'N/A', 'icon' => 'tag'],
                        [
                            'label' => 'Priority Level',
                            'value' => ucfirst($grievance->priority_level),
                            'icon'  => 'exclamation-circle',
                            'class' => $priorityClass,
                        ],
                        ['label' => 'Anonymous', 'value' => $grievance->is_anonymous ? 'Yes' : 'No', 'icon' => 'user'],
                        ['label' => 'Filed On', 'value' => $grievance->created_at->format('M d, Y h:i A'), 'icon' => 'calendar-days'],
                        [
                            'label' => 'Processing Days',
                            'value' => $processingDisplay,
                            'icon'  => 'clock',
                            'class' => $class,
                        ],
                        ['label' => 'Status', 'value' => ucwords(str_replace('_', ' ', subject: $grievance->grievance_status)), 'icon' => 'chart-bar'],
                    ];
                    @endphp


                @foreach ($info as $item)
                    <div class="flex items-start justify-between border-b border-gray-300 dark:border-zinc-700 py-2">
                        <div class="flex items-center gap-2 w-40">
                            <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            <span class="text-[16px] font-semibold text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                        </div>
                        <span class="text-[15px] font-bold flex-1 text-right {{ $item['class'] ?? 'text-gray-900 dark:text-gray-100' }}">
                            {{ $item['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex-1 flex flex-col gap-4">
                <div class="border border-gray-300 dark:border-zinc-700 rounded-xl p-4">
                    <h4 class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-document-text class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300">Details</span>
                    </h4>
                    <div class="text-[15px] text-gray-900 dark:text-gray-200 leading-relaxed">
                        {!! $grievance->grievance_details !!}
                    </div>
                </div>

                <div class="border border-gray-300 dark:border-zinc-700 rounded-xl p-4">
                    <h4 class="flex items-center gap-2 mb-2">
                        <x-heroicon-o-building-office class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300">Departments</span>
                    </h4>
                    <div class="text-[15px] text-gray-900 dark:text-gray-200 leading-8">
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

    </div>

    <div class="flex flex-col gap-3 p-3 rounded-sm" x-data="{ showMore: false, zoomSrc: null }">
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

    <div class="flex flex-col gap-3 mt-8 border border-gray-300 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-900 p-5">
        <h4 class="flex items-center gap-2 text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            Conversation
        </h4>

        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            <p>Discuss updates or feedback regarding this grievance below.</p>
        </div>

        <livewire:grievance.chat :grievance="$grievance" />
    </div>


    <div
        x-show="openRerouteModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
    >
        <div
            @click.outside="openRerouteModal = false"
            class="relative w-full max-w-md p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700"
        >
            <div class="flex items-center justify-between mb-4">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <x-heroicon-o-arrow-path class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    Reroute Selected Grievances
                </h2>
                <button
                    @click="openRerouteModal = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
                    aria-label="Close"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Please select a department and category to reroute all selected grievances.
            </p>

            <div
                x-data="{
                    department: @entangle('department'),
                    grievanceCategory: @entangle('grievance_category'),

                    categoriesMap: {
                        'Business Permit and Licensing Office': [
                            'Delayed Business Permit Processing',
                            'Unclear Requirements or Procedures',
                            'Unfair Treatment by Personnel',
                            'Business Permit Requirements Inquiry',
                            'Renewal Process Clarification',
                            'Schedule or Fee Inquiry',
                            'Document Correction or Update Request',
                            'Business Record Verification Request',
                            'Appointment or Processing Schedule Request'
                        ],
                        'Traffic Enforcement Agency of Mandaue': [
                            'Traffic Enforcer Misconduct',
                            'Unjust Ticketing or Penalty',
                            'Inefficient Traffic Management',
                            'Traffic Rules Clarification',
                            'Citation or Violation Inquiry',
                            'Inquiry About Traffic Assistance',
                            'Request for Traffic Assistance',
                            'Request for Event Traffic Coordination',
                            'Request for Violation Review'
                        ],
                        'City Social Welfare Services': [
                            'Discrimination or Neglect in Assistance',
                            'Delayed Social Service Response',
                            'Unprofessional Staff Behavior',
                            'Assistance Program Inquiry',
                            'Eligibility or Requirements Clarification',
                            'Social Service Schedule Inquiry',
                            'Request for Social Assistance',
                            'Financial Aid or Program Enrollment Request',
                            'Home Visit or Consultation Request'
                        ]
                    },

                    get categoryOptions() {
                        return this.department ? this.categoriesMap[this.department] || [] : [];
                    }
                }"
                class="flex flex-col gap-6"
            >
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-gray-900 dark:text-gray-100">Department</label>
                    <x-searchable-select
                        name="department"
                        placeholder="Select department"
                        :options="$departmentOptions"
                        x-on:change="grievanceCategory = ''; $wire.set('category', '', true)"
                    />
                    <flux:error name="department" />
                </div>

                <div x-show="department" x-cloak>
                    <div class="flex flex-col gap-2">
                        <label class="flex gap-2 items-center font-medium text-gray-900 dark:text-white">
                            <flux:icon.list-bullet />
                            <span>Grievance Category</span>
                        </label>

                        <h3 class="text-sm text-gray-700 dark:text-gray-300">
                            Choose a category based on the selected department.
                        </h3>

                        <div class="relative !cursor-pointer" x-data="{ open: false, search: '' }">
                            <flux:input
                                readonly
                                x-model="grievanceCategory"
                                placeholder="Select grievance category"
                                @click="open = !open"
                                class:input="border rounded-lg w-full cursor-pointer select-none"
                            />

                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-900 ring-1 ring-gray-200 dark:ring-zinc-700 rounded-md shadow-md"
                            >
                                <div class="p-1 border-b border-gray-200 dark:border-zinc-700 flex items-center gap-2">
                                    <flux:icon.magnifying-glass class="text-gray-500 dark:text-zinc-400" />
                                    <input
                                        type="text"
                                        x-model="search"
                                        placeholder="Search..."
                                        class="w-full bg-transparent border-none focus:ring-0 focus:outline-none text-sm"
                                    />
                                </div>

                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <template x-for="opt in categoryOptions.filter(o => o.toLowerCase().includes(search.toLowerCase()))" :key="opt">
                                        <li>
                                            <button
                                                type="button"
                                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-zinc-800"
                                                @click="
                                                    grievanceCategory = opt;
                                                    $wire.set('category', opt, true);
                                                    open = false;
                                                    search = '';
                                                "
                                                x-text="opt"
                                            ></button>
                                        </li>
                                    </template>

                                    <li
                                        x-show="categoryOptions.filter(o => o.toLowerCase().includes(search.toLowerCase())).length === 0"
                                        class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        No results found
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <flux:error name="category" />
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

            <div class="flex justify-end gap-3">
                <button
                    @click="openRerouteModal = false"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-300 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition"
                >
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    Cancel
                </button>

                <button
                    wire:click="reroute"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition"
                >
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    <span wire:loading.remove wire:target="reroute">Reroute</span>
                    <span wire:loading wire:target="reroute">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="openStatusModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
    >
        <div
            @click.outside="openStatusModal = false"
            class="relative w-full max-w-md p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700"
        >
            <div class="flex items-center justify-between mb-4">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <x-heroicon-o-pencil-square class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                    Update Selected Grievance Status
                </h2>
                <button
                    @click="openStatusModal = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
                    aria-label="Close"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Choose a new status to update this particular grievance.
            </p>

            <div class="flex flex-col gap-2 mb-2">
                <x-searchable-select
                    name="statusUpdate"
                    placeholder="Select Status"
                    :options="[
                        'pending' => 'Pending',
                        'acknowledged' => 'Acknowledged',
                        'in_progress' => 'In Progress',
                        'escalated' => 'Escalated',
                        'resolved' => 'Resolved',
                        'unresolved' => 'Unresolved',
                        'closed' => 'Closed',
                    ]"
                />
                <div class="space-y-1">
                    <flux:error name="statusUpdate" />
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

            <div class="flex justify-end gap-3">
                <button
                    @click="openStatusModal = false"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-300 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition"
                >
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    Cancel
                </button>

                <button
                    wire:click="updateStatus"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 disabled:opacity-50 transition"
                >
                    <x-heroicon-o-check class="w-4 h-4" />
                    <span wire:loading.remove wire:target="updateStatus">Update</span>
                    <span wire:loading wire:target="updateStatus">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="openPriorityModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
    >
        <div
            @click.outside="openPriorityModal = false"
            class="relative w-full max-w-md p-6 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-gray-200 dark:border-zinc-700"
        >
            <div class="flex items-center justify-between mb-4">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-gray-100">
                    <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400" />
                    Update Grievance Priority
                </h2>
                <button
                    @click="openPriorityModal = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition"
                    aria-label="Close"
                >
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Choose a new priority level for this grievance.
            </p>

            <div class="flex flex-col gap-2 mb-2">
                <x-searchable-select
                    wire:model.defer="priorityUpdate"
                    name="priorityUpdate"
                    placeholder="Select Priority Level"
                    :options="[
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'critical' => 'Critical',
                    ]"
                />
                <div class="space-y-1">
                    <flux:error name="priorityUpdate" />
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-zinc-700 my-4"></div>

            <div class="flex justify-end gap-3">
                <button
                    @click="openPriorityModal = false"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg border border-gray-300 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-700 transition"
                >
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                    Cancel
                </button>

                <button
                    wire:click="updatePriority"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 transition"
                >
                    <x-heroicon-o-check class="w-4 h-4" />
                    <span wire:loading.remove wire:target="updatePriority">Update</span>
                    <span wire:loading wire:target="updatePriority">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 flex items-center justify-center z-50"
    >
        <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-black/50"></div>

        <div
            x-show="showModal"
            x-transition.scale
            class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg max-w-md w-full p-6 flex flex-col items-center gap-4 z-50"
        >
            <div class="relative">
                <img
                    src="{{ asset('/images/check.png') }}"
                    class="w-full h-48 sm:h-56 object-cover"
                    alt="Registration Success Background"
                >
            </div>

            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                Update Successful
            </h2>

            <p class="text-sm font-medium text-center text-gray-600 dark:text-gray-300 leading-relaxed">
                The grievance record has been successfully updated and saved.
            </p>

            <div class="flex justify-center mt-6">
                <button
                    type="button"
                    @click="showModal = false"
                    class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                >
                    <x-heroicon-o-check class="w-5 h-5" />
                    <span>Okay</span>
                </button>
            </div>
        </div>
    </div>

</div>
