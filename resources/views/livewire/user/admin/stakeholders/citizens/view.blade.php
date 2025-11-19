<div class="p-3 w-full font-sans">
    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto py-2">

        <x-responsive-nav-link
            href="{{ route('admin.stakeholders.citizens.index') }}"
            wire:navigate
            class="flex items-center justify-center sm:justify-start gap-2 px-4 py-2 text-sm font-bold rounded-lg
                bg-gray-100 dark:bg-zinc-800 text-gray-800 dark:text-gray-200
                border border-gray-500 dark:border-gray-200
                hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 w-full sm:w-52"
        >
            <x-heroicon-o-user-group class="w-5 h-5 text-gray-700 dark:text-gray-300" />
            <span class="hidden lg:inline">Return to Citizens</span>
            <span class="lg:hidden">Citizens</span>
        </x-responsive-nav-link>

    </div>
    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">

        <div class="flex flex-col items-center mb-8" x-data="{ zoomSrc: null }">
            @php
                $palette = [
                    '0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899',
                    '14B8A6','6366F1','F97316','84CC16',
                ];

                $name = trim($userInfo->first_name . ' ' . $userInfo->last_name);
                $index = crc32($name) % count($palette);
                $bgColor = $palette[$index];

                $profilePic = $user->profile_pic
                    ? Storage::url($user->profile_pic)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($name) .
                        '&background=' . $bgColor .
                        '&color=fff&size=128';
            @endphp

            <div class="relative w-32 h-32 mb-4">
                <img
                    src="{{ $profilePic }}"
                    alt="Profile Picture"
                    class="rounded-full w-32 h-32 object-cover border-4 border-blue-500 shadow-md cursor-pointer transition-transform hover:scale-105"
                    @click="zoomSrc = '{{ $profilePic }}'"
                />
            </div>

            <div
                x-show="zoomSrc"
                x-transition.opacity
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm"
                @click.self="zoomSrc = null"
            >
                <div class="relative max-w-2xl flex items-center justify-center">
                    <img :src="zoomSrc" class="w-full max-h-[85vh] object-contain rounded-xl shadow-2xl border border-gray-600" />

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

            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-2">
                {{ $userInfo->first_name }} {{ $userInfo->middle_name ? $userInfo->middle_name[0] . '.' : '' }} {{ $userInfo->last_name }}
                {{ $userInfo->suffix ? ', ' . $userInfo->suffix : '' }}
            </h2>
        </div>


        <hr class="border-gray-300 dark:border-zinc-700 mb-6">

        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
            Personal Information
        </h3>

        @php
            $info = [
                ['label' => 'First Name', 'value' => $userInfo->first_name, 'icon' => 'user'],
                ['label' => 'Middle Name', 'value' => $userInfo->middle_name ?? 'N/A', 'icon' => 'user'],
                ['label' => 'Last Name', 'value' => $userInfo->last_name, 'icon' => 'user'],
                ['label' => 'Suffix', 'value' => $userInfo->suffix ?? 'N/A', 'icon' => 'user-plus'],

                ['label' => 'Gender', 'value' => ucfirst($userInfo->gender ?? 'N/A'), 'icon' => 'heart'],
                ['label' => 'Civil Status', 'value' => ucfirst($userInfo->civil_status ?? 'N/A'), 'icon' => 'user-circle'],

                ['label' => 'Barangay', 'value' => $userInfo->barangay ?? 'N/A', 'icon' => 'map-pin'],
                ['label' => 'Sitio', 'value' => $userInfo->sitio ?? 'N/A', 'icon' => 'map'],

                ['label' => 'Birthdate', 'value' => optional($userInfo->birthdate)->format('F d, Y') ?? 'N/A', 'icon' => 'calendar-days'],
                ['label' => 'Age', 'value' => $userInfo->age ?? 'N/A', 'icon' => 'cake'],

                ['label' => 'Phone Number', 'value' => $userInfo->phone_number ?? 'N/A', 'icon' => 'phone'],

                ['label' => 'Emergency Contact', 'value' => $userInfo->emergency_contact_name ?? 'N/A', 'icon' => 'user-group'],
                ['label' => 'Emergency Contact No.', 'value' => $userInfo->emergency_contact_number ?? 'N/A', 'icon' => 'phone'],
                ['label' => 'Relationship', 'value' => $userInfo->emergency_relationship ?? 'N/A', 'icon' => 'link'],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($info as $item)
                <div class="flex flex-col border-b border-gray-300 dark:border-zinc-700 pb-3">
                    <div class="flex items-center gap-2 mb-1">
                        <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="font-semibold text-gray-700 dark:text-gray-300 text-[15px]">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-gray-900 dark:text-gray-100 font-medium text-[15px]">
                        {{ $item['value'] }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="mt-10 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
            <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
            <p>Citizen Record — Mandaue CitiSync</p>
        </div>
    </div>
</div>
