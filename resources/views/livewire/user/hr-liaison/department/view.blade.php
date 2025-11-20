<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm w-full"
     data-component="hr-liaison-department-view"
     data-wire-id="{{ $this->id() }}"
>
    <div class="flex flex-col gap-6">
       <div class="flex-1 relative">
            @php
                $palette = ['0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899','14B8A6','6366F1','F97316','84CC16'];
                $index = crc32($department->department_name) % count($palette);
                $bgColor = $palette[$index];
            @endphp
            <img
                src="{{ $department->department_bg
                    ? Storage::url($department->department_bg)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) .
                    '&background=' . $bgColor . '&color=fff&size=512' }}"
                alt="Department BG"
                class="w-full h-52 object-cover rounded-b-xl mb-4"
            >

            <a href="{{ route('hr-liaison.department.index') }}" wire:navigate
            class="absolute top-4 left-4 flex items-center gap-2 px-3 py-2 bg-gray-600/20 text-white text-sm font-semibold rounded-lg shadow hover:bg-gray-700/50 transition-all duration-200">
                <flux:icon.arrow-long-left class="w-4 h-4" />
            </a>

            <div class="flex items-center gap-4 px-4">
                @php
                    $indexProfile = crc32($department->department_name) % count($palette);
                    $profileBg = $palette[$indexProfile];
                @endphp
                <img
                    src="{{ $department->department_profile
                        ? Storage::url($department->department_profile)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($department->department_name) .
                        '&background=' . $profileBg . '&color=fff&size=128' }}"
                    alt="Profile"
                    class="w-16 h-16 rounded-full border border-gray-200 dark:border-zinc-700"
                >
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $department->department_name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $department->department_description }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 px-4 w-full" wire:poll.10s>
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-3">HR Liaisons</h3>
            @if($hrLiaisons->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No HR Liaisons assigned.</p>
            @else
                <ul class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($hrLiaisons as $liaison)
                        @php
                            $indexLiaison = crc32($liaison->name) % count($palette);
                            $liaisonBg = $palette[$indexLiaison];

                            $isActive = $liaison->isOnline();
                            $lastSeen = $liaison->last_seen_at;
                            $activeText = $isActive
                                ? 'Active now'
                                : ($lastSeen ? 'Active ' . $lastSeen->diffForHumans() : 'Offline');
                        @endphp
                        <li class="py-3 sm:py-4 w-full">
                            <div class="flex items-center justify-between space-x-4 rtl:space-x-reverse w-full">
                                <div class="flex items-center space-x-4 rtl:space-x-reverse w-full">
                                    <div class="relative shrink-0 w-10 h-10">
                                        <img
                                            class="w-10 h-10 rounded-full object-cover"
                                            src="{{ $liaison->profile_pic
                                                ? Storage::url($liaison->profile_pic)
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($liaison->name) .
                                                '&background=' . $liaisonBg . '&color=fff&size=128' }}"
                                            alt="{{ $liaison->name }}"
                                        >
                                        @if($isActive)
                                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-green-400 ring-1 ring-white"></span>
                                        @else
                                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-gray-400 ring-1 ring-white"></span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            {{ $liaison->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            {{ $liaison->email }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex-shrink-0 text-sm font-medium {{ $isActive ? 'text-green-500' : 'text-gray-400 dark:text-gray-400' }}">
                                    {{ $activeText }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </div>
</div>
