<div class="sticky top-0 z-[32] h-[60px] shadow-md dark:bg-black bg-white">
<nav x-data="{ open: false }" class="w-full">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-2">
        <div class="flex justify-between items-center h-16">
            <div class="flex gap-3">
                <div class="flex items-center">

                    <!-- Toggle Button -->
                    <button
                        x-data
                        @click="$store.sidebar.toggle()"
                        class="dark:hover:bg-zinc-800 hover:bg-gray-200 p-2 text-mc_primary_color dark:text-white rounded-full cursor-pointer transition-transform"
                    >
                        <!-- Single Chevron that rotates -->
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
            <!-- Profile Section -->
            <div class="relative flex flex-col items-center">
                <!-- Profile Wrapper -->
                <div class="flex items-center justify-between w-full">
                    <!-- Avatar and Profile Info -->
                    <div class="flex items-center gap-4">
                        <div x-data="{ openProfile: false }" class="relative flex items-center">
                            <!-- Avatar Button -->
                            <div class="dark:bg-white w-10 h-10 rounded-full cursor-pointer shrink-0"
                                @click="openProfile = !openProfile">
                                <img src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('images/avatar.png') }}"
                                    alt="profile-pic"
                                    class="rounded-full w-full h-full object-cover">
                            </div>

                            <!-- Profile Dropdown -->
                            <div x-show="openProfile"
                                x-transition
                                x-cloak
                                @click.outside="openProfile = false"
                                class="absolute top-14 right-0 dark:bg-zinc-800 bg-white rounded-lg z-40 shadow-md
                                        w-[300px] sm:w-[350px] md:w-[400px] lg:w-[450px] p-3 sm:p-4 space-y-3">

                                <!-- Profile -->
                                <div class="relative border-box dark:bg-zinc-700 bg-gray-200 p-3 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar -->
                                        <div class="rounded-full w-16 h-16 sm:w-20 sm:h-20 shrink-0">
                                            <img src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('images/avatar.png') }}"
                                                alt="profile-pic"
                                                class="rounded-full w-full h-full object-cover">
                                        </div>

                                        <!-- Info -->
                                        <div class="flex flex-col gap-1 min-w-0">
                                            <span class="font-semibold text-sm truncate">{{ $userName }}</span>
                                            <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 truncate">{{ $userEmail }}</span>

                                            <!-- Status (poll every 10s) -->
                                            <div wire:poll.10s="updateStatus" class="flex items-center gap-2">
                                                <span class="{{ $status['color'] }} text-xs">
                                                    <i class="bi bi-circle-fill"></i>
                                                </span>
                                                <span class="text-xs sm:text-sm">{{ $status['text'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Theme Switch -->
                                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance"
                                                class="flex justify-center sm:justify-start">
                                    <flux:radio value="light" icon="sun" />
                                    <flux:radio value="dark" icon="moon" />
                                    <flux:radio value="system" icon="computer-desktop" />
                                </flux:radio.group>

                                <!-- Settings Link -->
                                <x-responsive-nav-link href="{{ route('settings') }}"
                                                    class="flex items-center gap-2 text-sm dark:text-white dark:hover:bg-zinc-700 hover:bg-gray-200 w-full px-2 py-1 rounded-md"
                                                    wire:navigate>
                                    <flux:icon.cog-6-tooth/>
                                    <span>Settings</span>
                                </x-responsive-nav-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Profile Section -->

                <!-- Notification Section -->
                <div class="relative" x-data="{ modalOpen: null }">
                    <!-- Bell Icon -->
                    <div @click="$store.notifications.toggle();" class="relative cursor-pointer">
                        @if($unreadCount > 0)
                            <span
                                wire:loading.remove
                                wire:target="markNotificationAsRead,markNotificationAsUnread,loadMore,deleteNotification,markAllAsRead,deleteAllUnread,markAllAsUnread,deleteAllRead"
                                class="absolute -top-2 -right-5 bg-red-600 text-white rounded-full px-2 py-1 text-xs font-semibold"
                            >
                                {{ $unreadCount }}
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
