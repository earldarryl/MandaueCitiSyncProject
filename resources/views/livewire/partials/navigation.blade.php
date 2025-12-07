<div class="sticky top-0 z-[32] h-[60px] border border-gray-300 dark:border-zinc-800 dark:bg-black w-full bg-white">
<nav x-data="{ open: false }" class="w-full">
    <div class="w-full px-2">
        <div class="flex justify-between items-center h-16">
            <div class="flex gap-3">
                <div class="flex items-center">

                    <button
                        x-data
                        @click="$store.sidebar.toggle()"
                        class="dark:hover:bg-zinc-800 hover:bg-gray-200 p-2 text-mc_primary_color dark:text-white rounded-full cursor-pointer transition-transform"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor"
                            class="size-6 transform transition-transform duration-300"
                            :class="{ 'rotate-180': $store.sidebar.open }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>

                </div>
                <div class="flex items-center justify-start gap-3 text-2xl sm:text-3xl md:text-4xl font-bold text-mc_primary_color dark:text-white">
                    <i class="{{ $this->getHeaderIconClass() }}"></i>
                    <h1>
                        {{ $header }}
                    </h1>
                </div>
            </div>



        <div class="relative flex w-auto items-center justify-center gap-4 mr-5">
            <div class="relative flex flex-col items-center">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div x-data="{ openProfile: false }" class="relative flex items-center">
                            <div class="relative w-10 h-10 rounded-full cursor-pointer shrink-0 dark:bg-white overflow-visible"
                                @click="openProfile = !openProfile">

                                @php
                                    $palette = [
                                        '0D8ABC','10B981','EF4444','F59E0B','8B5CF6','EC4899',
                                        '14B8A6','6366F1','F97316','84CC16',
                                    ];
                                    $index = crc32($user->name) % count($palette);
                                    $bgColor = $palette[$index];

                                    $statusDotColor = match($status['text'] ?? 'Offline') {
                                        'Online' => 'bg-green-400',
                                        'Away' => 'bg-yellow-400',
                                        default => 'bg-gray-400',
                                    };

                                @endphp

                                <img
                                    src="{{ $user->profile_pic && !str_starts_with($user->profile_pic, '/storage/')
                                        ? Storage::url($user->profile_pic)
                                        : $user->profile_pic ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=' . $bgColor . '&color=fff&size=128' }}"
                                    alt="profile-pic"
                                    class="rounded-full w-full h-full object-cover"
                                />


                                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full ring-1 ring-white {{ $statusDotColor }}"></span>

                            </div>

                            <div x-show="openProfile"
                                x-transition
                                x-cloak
                                @click.outside="openProfile = false"
                                class="absolute top-14 right-0 dark:bg-zinc-800 bg-white rounded-lg z-40 shadow-md
                                        w-[300px] sm:w-[350px] md:w-[400px] lg:w-[450px] p-3 sm:p-4 space-y-3">

                                <div class="relative border-box dark:bg-zinc-700/50 bg-gray-100 p-3 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="rounded-full w-16 h-16 sm:w-20 sm:h-20 shrink-0">
                                            <img
                                                src="{{ $user->profile_pic
                                                    ? Storage::url($user->profile_pic)
                                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) .
                                                    '&background=' . $bgColor .
                                                    '&color=fff&size=128' }}"
                                                alt="profile-pic"
                                                class="rounded-full w-full h-full object-cover"
                                            />
                                        </div>

                                        <div class="flex flex-col gap-1 min-w-0">
                                            <span class="font-bold text-sm truncate">{{ $userName }}</span>
                                            <span class="text-xs font-bold sm:text-sm text-gray-600 dark:text-gray-300 truncate">{{ $userEmail }}</span>

                                            <div wire:poll.10s="updateStatus" class="flex items-center gap-2">
                                                <span class="{{ $status['color'] }} text-xs">
                                                    <i class="bi bi-circle-fill"></i>
                                                </span>
                                                <span class="text-xs sm:text-sm font-bold">{{ $status['text'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance"
                                                class="flex justify-center sm:justify-start">
                                    <flux:radio value="light" icon="sun" />
                                    <flux:radio value="dark" icon="moon" />
                                    <flux:radio value="system" icon="computer-desktop" />
                                </flux:radio.group>

                                <x-responsive-nav-link href="{{ route('settings') }}"
                                                    class="flex items-center font-bold gap-2 text-sm dark:text-white dark:hover:bg-zinc-700 hover:bg-gray-200 w-full px-2 py-1 rounded-md"
                                                    wire:navigate>
                                    <flux:icon.cog-6-tooth/>
                                    <span>Settings</span>
                                </x-responsive-nav-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="relative" x-data="{ modalOpen: null }">
                    <div @click="$store.notifications.toggle();" class="relative cursor-pointer">
                        @if($unreadCount > 0)
                            <span
                                wire:poll.10s="loadCounts"
                                wire:loading.remove
                                wire:target="markNotificationAsRead,markNotificationAsUnread,loadMore,deleteNotification,markAllAsRead,deleteAllUnread,markAllAsUnread,deleteAllRead"
                                class="absolute -top-2 -right-2 flex items-center bg-red-600 text-white rounded-full text-xs font-semibold
                                    {{ $unreadCount > 9 ? 'p-1' : 'px-2 py-1' }}"
                            >
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                        <div class="p-2 rounded-full dark:bg-zinc-800 bg-gray-200 text-mc_primary_color dark:text-white">
                            <flux:icon.bell class="w-6 h-6"/>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>
</div>
