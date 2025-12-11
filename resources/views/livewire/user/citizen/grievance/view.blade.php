<div class="w-full px-2 bg-gray-100/20 dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 flex flex-col gap-6"
     data-component="citizen-grievance-view"
     x-data="{
        showDeleteModal: false,
        notyf: null,

        handleDelayedRedirect() {
            setTimeout(() => {
                $wire.handleDelayedRedirect();
            }, 1500);
        },

    }"
     @close-modal-delete.window="showDeleteModal = false"
     @delayed-redirect.window="handleDelayedRedirect()"
     data-wire-id="{{ $this->id() }}">

    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto py-2">

        <x-responsive-nav-link
            href="{{ route('citizen.grievance.index') }}"
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
            wire:click="refreshGrievance"
            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                border border-blue-500 dark:border-blue-400
                hover:bg-blue-200 dark:hover:bg-blue-800/50
                focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                transition-all duration-200">
            <x-heroicon-o-arrow-path class="w-5 h-5" />
            <span wire:loading.remove wire:target="refreshGrievance">Refresh</span>
            <span wire:loading wire:target="refreshGrievance">Processing...</span>
        </button>

        <button
            @click="showDeleteModal = true"
            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                border border-red-500 dark:border-red-400
                hover:bg-red-200 dark:hover:bg-red-800/50
                focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                transition-all duration-200">
            <x-heroicon-o-trash class="w-5 h-5" />
            <span wire:loading.remove wire:target="deleteReport">Delete Report</span>
            <span wire:loading wire:target="deleteReport">Processing...</span>
        </button>

        @php
            $hasPermission = $grievance->editRequests()
                ->where('user_id', auth()->id())
                ->where('status', 'approved')
                ->exists();

            $pending = $grievance->editRequests()
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->exists();
        @endphp

        @if($hasPermission)
            <a href="{{ route('citizen.grievance.edit', $grievance) }}" wire:navigate
            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                    border border-green-500 dark:border-green-400
                    hover:bg-green-200 dark:hover:bg-green-800/50
                    focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                    transition-all duration-200">
                <x-heroicon-o-pencil class="w-5 h-5 text-green-500" />
                <span>Edit Report</span>
            </a>
        @elseif($pending)
            <button
                wire:click="editRequest"
                wire:loading.attr="disabled"
                wire:target="editRequest"
                disabled
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                    border border-green-500 dark:border-green-400
                    cursor-not-allowed
                    transition-all duration-200"
            >
                <x-heroicon-o-lock-open class="w-5 h-5" />
                <span class="flex items-center gap-2">
                    <span>Request Pending</span>
                    <span class="flex space-x-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-bounce delay-0"></span>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-bounce delay-150"></span>
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-bounce delay-300"></span>
                    </span>
                </span>
            </button>
        @else
            <button
                wire:click="editRequest"
                wire:loading.attr="disabled"
                wire:target="editRequest"
                class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold rounded-lg
                    bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300
                    border border-green-500 dark:border-green-400
                    hover:bg-green-200 dark:hover:bg-green-800/50
                    focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-700
                    transition-all duration-200"
            >
                <x-heroicon-o-lock-open class="w-5 h-5" />
                <span wire:loading.remove wire:target="editRequest">Send Edit Request</span>
                <span wire:loading wire:target="editRequest">Processing...</span>
            </button>
        @endif

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

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-8 font-sans">

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
                    'low'      => 'text-blue-600 dark:text-blue-400 font-semibold',
                    'normal'   => 'text-gray-600 dark:text-gray-400 font-semibold',
                    'medium'   => 'text-yellow-600 dark:text-yellow-400 font-semibold',
                    'high'     => 'text-red-600 dark:text-red-400 font-semibold',
                    'critical' => 'text-red-700 dark:text-red-600 font-extrabold',
                    default    => 'text-gray-600 dark:text-gray-400 font-semibold',
                };

                $class = match (true) {
                    $isCompleted => 'text-green-600 dark:text-green-400 font-semibold',
                    $isEscalated => 'text-amber-600 dark:text-amber-400 font-semibold',
                    $isOverdue   => 'text-red-600 dark:text-red-400 font-semibold',
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
                    @php
                        $cleanDetails = trim(strip_tags($grievance->grievance_details ?? ''));
                    @endphp

                    @if ($cleanDetails !== '')
                        {!! $grievance->grievance_details !!}
                    @else
                        <span class="text-gray-600 dark:text-gray-400 italic">No details provided</span>
                    @endif
                </div>
            </div>

            <div class="border border-gray-300 dark:border-zinc-700 rounded-xl p-4 w-full">
                <h4 class="flex items-center gap-2 mb-2">
                    <x-heroicon-o-building-office class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                    <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300">Departments</span>
                </h4>

                <ul class="mt-3 divide-y divide-gray-200 dark:divide-zinc-700 w-full">
                    @forelse ($grievance->departments->unique('department_id') as $department)
                        @php
                            $palette = ['0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899','14B8A6','6366F1','F97316','84CC16'];
                            $index = crc32($department->department_name) % count($palette);
                            $bgColor = $palette[$index];

                            $hasBg = !empty($department->department_bg);

                            $bgUrl = $hasBg
                                ? Storage::url($department->department_bg)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) .
                                '&background=' . $bgColor . '&color=fff&size=512';

                            $profileUrl = $department->department_profile
                                ? Storage::url($department->department_profile)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) .
                                '&background=' . $bgColor . '&color=fff&size=128';

                            $textColor = $hasBg
                                ? 'text-gray-900 dark:text-gray-200'
                                : 'text-[#' . $bgColor . '] dark:text-[#' . $bgColor . ']';
                        @endphp

                        <li class="py-4 w-full relative rounded-xl overflow-hidden shadow-lg">
                            <div class="absolute inset-0">
                                <img src="{{ $bgUrl }}" alt="Department BG"
                                    class="w-full h-full object-cover opacity-80">
                                <div class="absolute inset-0 bg-black/20"></div>
                            </div>

                            <div class="relative flex items-center space-x-4 rtl:space-x-reverse w-full px-4">
                                <div class="shrink-0">
                                    <img class="w-16 h-16 rounded-full border-2 border-white dark:border-zinc-700 object-cover"
                                        src="{{ $profileUrl }}" alt="{{ $department->department_name }}">
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold truncate {{ $textColor }}">
                                        {{ $department->department_name }}
                                    </p>

                                    <p class="text-sm font-semibold truncate {{ $textColor }} opacity-90">
                                        HR Liaisons: {{ $department->hr_liaisons_status }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-4 w-full">
                            <span class="text-gray-600 dark:text-gray-400 italic">No department assigned</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="border border-gray-300 dark:border-zinc-700 rounded-xl p-3 mt-6">
        <h4 class="flex items-center gap-2 mb-3">
            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-300">
                Add Progress Log
            </span>
        </h4>

        <div
            x-data="progressLogs(@js($this->canLoadMore))"
            x-init="scrollToBottom()"
            x-on:new-log.window="scrollToBottom()"
            class="flex flex-col gap-4 max-h-80 overflow-y-auto px-6 py-4 mt-3 border border-gray-300 dark:border-zinc-800 scroll-smooth"
            x-ref="logContainer"
            @scroll.passive="checkScroll()"
        >

            @if(count($this->remarks) === 0)
                <div class="text-center text-xs text-gray-500 dark:text-gray-400 py-2 italic">
                    No progress logs yet. Logs will appear here when HR Liaison or Admin adds updates.
                </div>
            @else

            <template x-if="loadingMore">
                <div class="text-center text-xs text-gray-500 dark:text-gray-400 py-2">
                    Loading older logs, please wait...
                </div>
            </template>

            @if(count($this->remarks) > 10)
                <template x-if="!canLoadMore && !loadingMore">
                    <div class="text-center text-xs text-gray-400 dark:text-gray-500 py-2 italic">
                        — No older remarks to load —
                    </div>
                </template>
            @endif

            <ul class="max-w-full divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($this->remarks as $remark)
                    <li class="@if($remark['type'] === 'update') bg-blue-50 dark:bg-blue-900/30
                            @elseif($remark['type'] === 'note') bg-gray-50 dark:bg-gray-800/30
                            @elseif($remark['type'] === 'escalation') bg-red-50 dark:bg-red-900/30
                            @else bg-gray-50 dark:bg-gray-800/30 @endif">
                        <div class="flex items-start space-x-4 rtl:space-x-reverse p-3
                        ">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate
                                    @if($remark['type'] === 'update') text-blue-700 dark:text-blue-300
                                    @elseif($remark['type'] === 'note') text-gray-600 dark:text-gray-400
                                    @elseif($remark['type'] === 'escalation') text-red-600 dark:text-red-400
                                    @else text-gray-700 dark:text-gray-300 @endif
                                ">
                                    {{ $remark['user_name'] }}
                                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">({{ $remark['role'] }})</span>
                                </p>

                                <p class="text-xs truncate mt-0.5 font-semibold
                                    @if($remark['type'] === 'update') text-blue-500 dark:text-blue-400
                                    @elseif($remark['type'] === 'note') text-gray-500 dark:text-gray-400
                                    @elseif($remark['type'] === 'escalation') text-red-500 dark:text-red-400
                                    @else text-gray-500 dark:text-gray-400 @endif
                                ">
                                    {{ strtoupper(str_replace('_', ' ', $remark['type'])) }} - {{ strtoupper(str_replace('_', ' ', $remark['status'])) }}
                                </p>

                                <p class="text-sm mt-1 leading-relaxed font-medium
                                    @if($remark['type'] === 'update') text-blue-800 dark:text-blue-200
                                    @elseif($remark['type'] === 'note') text-gray-800 dark:text-gray-200
                                    @elseif($remark['type'] === 'escalation') text-red-700 dark:text-red-300
                                    @else text-gray-800 dark:text-gray-200 @endif
                                ">
                                    {{ $remark['message'] }}
                                </p>
                            </div>

                            <div class="inline-flex items-start text-xs font-semibold text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ Carbon::parse($remark['timestamp'])->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>

   <div class="flex flex-col gap-3 p-3 border border-gray-300 dark:border-zinc-700 rounded-xl" x-data="{ showMore: false, zoomSrc: null }">
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
                            $file = $attachment->file_path;
                            $size = Storage::disk('public')->exists($file)
                                ? $this->readableSize(Storage::disk('public')->size($file))
                                : 'Unavailable';
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
                                    <a
                                        href="{{ $url }}"
                                        download="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                        class="absolute top-2 right-2 z-20 bg-white/90 dark:bg-zinc-900/90 border border-gray-300 dark:border-zinc-700
                                            text-[11px] font-semibold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-all duration-200
                                            hover:bg-gray-200 dark:hover:bg-zinc-700"
                                    >
                                        Download
                                    </a>
                                    <a
                                        href="{{ $url }}"
                                        target="_blank"
                                        class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center transition-all duration-200 hover:bg-gray-200/60 dark:hover:bg-gray-700/60"
                                    >
                                        <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />

                                        <span class="flex flex-col gap-1">
                                            <span class="text-sm font-medium truncate w-full text-gray-800 dark:text-gray-200">
                                                {{ $attachment->file_name ?? basename($attachment->file_path) }}
                                            </span>

                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                ({{ $size }})
                                            </span>
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
                                    <a
                                        href="{{ $url }}"
                                        download="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                        class="absolute top-2 right-2 z-20 bg-white/90 dark:bg-zinc-900/90 border border-gray-300 dark:border-zinc-700
                                            text-[11px] font-semibold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-all duration-200
                                            hover:bg-gray-200 dark:hover:bg-zinc-700"
                                    >
                                        Download
                                    </a>
                                    <a
                                        href="{{ $url }}"
                                        target="_blank"
                                        class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center transition-all duration-200 hover:bg-gray-200/60 dark:hover:bg-gray-700/60"
                                    >
                                        <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />

                                        <span class="flex flex-col gap-1">
                                            <span class="text-sm font-medium truncate w-full text-gray-800 dark:text-gray-200">
                                                {{ $attachment->file_name ?? basename($attachment->file_path) }}
                                            </span>

                                            <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                ({{ $size }})
                                            </span>
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
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
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
                                            $file = $attachment->file_path;
                                            $size = Storage::disk('public')->exists($file)
                                                ? $this->readableSize(Storage::disk('public')->size($file))
                                                : 'Unavailable';
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
                                                    download="{{ $attachment->file_name ?? basename($attachment->file_path) }}"
                                                    class="absolute top-2 right-2 z-20 bg-white/90 dark:bg-zinc-900/90 border border-gray-300 dark:border-zinc-700
                                                        text-[11px] font-semibold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-all duration-200
                                                        hover:bg-gray-200 dark:hover:bg-zinc-700"
                                                >
                                                    Download
                                                </a>
                                                <a
                                                    href="{{ $url }}"
                                                    target="_blank"
                                                    class="flex flex-col items-center justify-center gap-2 py-6 px-3 text-center transition-all duration-200 hover:bg-gray-200/60 dark:hover:bg-gray-700/60"
                                                >
                                                    <x-heroicon-o-document class="w-10 h-10 text-gray-500 dark:text-gray-300" />

                                                    <span class="flex flex-col gap-1">
                                                        <span class="text-sm font-medium truncate w-full text-gray-800 dark:text-gray-200">
                                                            {{ $attachment->file_name ?? basename($attachment->file_path) }}
                                                        </span>

                                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                            ({{ $size }})
                                                        </span>
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
                    class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60"
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
            <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6" />
            Conversation
        </h4>

        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            <p>Discuss updates or feedback regarding this report below.</p>
        </div>

        <livewire:partials.chat :grievance="$grievance"/>
    </div>

    <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-50"></div>

    <div x-show="showDeleteModal" x-transition.scale
        class="fixed inset-0 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg w-full max-w-md p-6 text-center space-y-5">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 mx-auto">
                <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-500" />
            </div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">Confirm Deletion</h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Are you sure you want to delete this report?</p>

            <div wire:loading.remove wire:target="deleteReport" class="flex justify-center gap-3 mt-4">
                <button type="button" @click="showDeleteModal = false"
                    class="px-4 py-2 border border-gray-200 dark:border-zinc-800 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                    Cancel
                </button>
                <flux:button variant="danger" icon="trash" wire:click="deleteReport">
                    Yes, Delete
                </flux:button>
            </div>

            <div wire:loading wire:target="deleteReport">
                <div class="flex items-center justify-center gap-2 w-full">
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:0.5s]"></div>
                    <div class="dot w-2 h-2 bg-black dark:bg-zinc-300 rounded-full [animation-delay:1s]"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('progressLogs', (initialCanLoadMore) => ({
        loadingMore: false,
        canLoadMore: initialCanLoadMore,

    emitToLivewire(eventName, payload = null) {
      if (window.Livewire && typeof window.Livewire.emit === 'function') {
        return window.Livewire.emit(eventName, payload);
      }
      if (window.livewire && typeof window.livewire.emit === 'function') {
        return window.livewire.emit(eventName, payload);
      }

      return new Promise(resolve => {
        const onLoad = () => {
          if (window.Livewire && typeof window.Livewire.emit === 'function') {
            window.Livewire.emit(eventName, payload);
          } else if (window.livewire && typeof window.livewire.emit === 'function') {
            window.livewire.emit(eventName, payload);
          } else {
            window.dispatchEvent(new CustomEvent(eventName, { detail: payload }));
          }
          resolve();
        };

        window.addEventListener('livewire:load', onLoad, { once: true });

        setTimeout(() => {
          if (window.Livewire || window.livewire) onLoad();
        }, 2000);
      });
    },

    scrollToBottom() {
      this.$nextTick(() => {
        const el = this.$refs.logContainer;
        if (!el) return;
        el.scrollTop = el.scrollHeight;
      });
    },

    checkScroll() {
            const el = this.$refs.logContainer;
            if (!el) return;

            if (el.scrollTop <= 5 && !this.loadingMore && this.canLoadMore) {
                this.loadingMore = true;

                const prevScrollTop = el.scrollTop;
                const prevScrollHeight = el.scrollHeight;

                const onUpdated = (event) => {
                    this.$nextTick(() => {
                        const newScrollHeight = el.scrollHeight;
                        el.scrollTop = prevScrollTop + (newScrollHeight - prevScrollHeight);
                        this.loadingMore = false;

                        const newCanLoadMore = event.detail.canLoadMore !== undefined
                            ? event.detail.canLoadMore
                            : (event.detail[0] && event.detail[0].canLoadMore);

                        if (newCanLoadMore !== undefined) {
                            this.canLoadMore = newCanLoadMore;
                        }

                    });
                    window.removeEventListener('remarks-updated', onUpdated);
                };

                window.addEventListener('remarks-updated', onUpdated);

                this.emitToLivewire('loadMore').catch(() => {
                    this.loadingMore = false;
                    window.removeEventListener('remarks-updated', onUpdated);
                });
            }
        }
  }));
});
</script>

