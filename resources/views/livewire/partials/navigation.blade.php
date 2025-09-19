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
                        :class="{
                            'hover:bg-none': !$store.sidebar.open && $store.sidebar.screen < 600,
                        }"
                        class="dark:hover:bg-zinc-800 hover:bg-gray-200 p-2 text-sky-900 dark:text-blue-500 rounded-full cursor-pointer"

                    >
                        <!-- Show CLOSE icon (X) when sidebar is open -->
                        <template x-if="$store.sidebar.open && $store.sidebar.screen >= 600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>

                        <!-- Show HAMBURGER icon when sidebar is closed -->
                        <template x-if="!$store.sidebar.open">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </template>
                    </button>
                </div>
                <div class="flex items-center justify-start gap-3 text-2xl sm:text-3xl md:text-4xl font-bold text-sky-900 dark:text-blue-500">
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
                <div @click="$store.notifications.toggle(); $store.sidebar.open = false;" class="relative cursor-pointer">
                    @if($unreadCount > 0)
                        <span
                            wire:loading.remove
                            wire:target="markNotificationAsRead,markNotificationAsUnread,loadMore,deleteNotification,markAllAsRead,deleteAllUnread,markAllAsUnread,deleteAllRead"
                            class="absolute -top-2 -right-5 bg-red-600 text-white rounded-full px-2 py-1 text-xs font-semibold"
                        >
                            {{ $unreadCount }}
                        </span>
                    @endif
                    <div class="p-2 rounded-full dark:hover:bg-zinc-800/50 hover:bg-gray-200/20 text-sky-900 dark:text-blue-500">
                        <flux:icon.bell class="w-6 h-6"/>
                    </div>
                </div>

                <!-- Drawer -->
                <div x-show="$store.notifications.open" x-cloak class="fixed inset-0 z-40 flex">
                    <!-- Overlay -->
                    <div class="fixed inset-0 bg-black/40 transition-opacity duration-300"
                        @click="$store.notifications.close()"
                        x-show="$store.notifications.open"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"></div>

                        <!-- Drawer Panel -->
                        <div
                            class="relative w-96 bg-white dark:bg-zinc-800 shadow-xl h-screen ml-auto flex flex-col transform transition-transform duration-300"
                            x-show="$store.notifications.open"
                            x-transition:enter="transform transition ease-in-out duration-300"
                            x-transition:enter-start="translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in-out duration-300"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                        >
                            <!-- Header -->
                            <div class="flex items-center justify-between p-4 border-b dark:border-zinc-700 flex-shrink-0">
                                <span class="font-semibold">Notifications</span>
                                <flux:button type="button" icon="x-mark" @click="$store.notifications.close()" ></flux:button>
                            </div>

                            <!-- Scrollable Content -->
                            <div class="relative flex-1 overflow-y-auto px-4 py-3">

                            <!-- Unified Notifications List -->
                            @php
                                $allNotifications = $unreadNotifications->concat($readNotifications)->sortByDesc('created_at');
                            @endphp

                            @forelse ($allNotifications as $notification)
                <div wire:key="notification-{{ $notification->id }}"
                    class="p-3 rounded-lg shadow-sm flex justify-between items-start mb-2
                            {{ is_null($notification->read_at) ? 'bg-gray-100' : 'bg-white dark:bg-zinc-700' }}">

                    <!-- Notification Message -->
                    <div class="flex-1 flex-col pr-4 break-words text-sm">
                        <span>{{ $notification->data['message'] ?? 'No message' }}</span>
                        @if($notification->created_at)
                            <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>

                    <!-- Dropdown per notification -->
                    <div class="relative flex-shrink-0" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="px-3 py-1 bg-gray-200 dark:bg-zinc-700 rounded hover:bg-gray-300 dark:hover:bg-zinc-600 text-sm whitespace-nowrap">
                            Actions ▾
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false"
                            class="absolute right-0 mt-1 w-40 bg-white dark:bg-zinc-800 border dark:border-zinc-700 rounded shadow-lg z-50">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    @if(is_null($notification->read_at))
                                        <button
                                            wire:click="markNotificationAsRead('{{ $notification->id }}')"
                                            class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center space-x-2"
                                        >
                                            <span wire:loading wire:target="markNotificationAsRead">
                                                <flux:icon.loading class="w-4 h-4"/>
                                            </span>
                                            <span wire:loading.remove wire:target="markNotificationAsRead">Mark as read</span>
                                        </button>
                                    @else
                                        <button
                                            wire:click="markNotificationAsUnread('{{ $notification->id }}')"
                                            class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center space-x-2"
                                        >
                                            <span wire:loading wire:target="markNotificationAsUnread">
                                                <flux:icon.loading class="w-4 h-4"/>
                                            </span>
                                            <span wire:loading.remove wire:target="markNotificationAsUnread">Mark as unread</span>
                                        </button>
                                    @endif
                                </li>
                                <li>
                                    <button
                                        wire:click="deleteNotification('{{ $notification->id }}')"
                                        class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-700 text-red-500"
                                    >
                                        Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No notifications.</p>
            @endforelse


                            <!-- Load More -->
                            @if ($unreadCount + $readCount > $allNotifications->count())
                                <div class="mt-4 flex justify-center">
                                    <button
                                        wire:click="loadMore('all')"
                                        wire:loading.attr="disabled"
                                        class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 hover:bg-gray-300 dark:hover:bg-zinc-600 rounded-lg"
                                    >
                                        <span wire:loading.remove wire:target="loadMore">Load More</span>
                                        <span wire:loading wire:target="loadMore">Loading...</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>
    </nav>
</div>
