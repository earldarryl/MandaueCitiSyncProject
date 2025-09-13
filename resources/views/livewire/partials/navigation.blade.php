<div class="sticky top-0 z-[32] h-[60px] shadow-md dark:bg-black bg-white">
<nav x-data="{ open: false }" class="w-full">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-2">
        <div class="flex justify-between items-center h-16">
            <div class="flex">
                <div class="flex items-center">

                    <!-- Toggle Button -->
                    <button
                        x-data
                        @click="$store.sidebar.toggle()"
                        :class="{
                            'hover:bg-none': !$store.sidebar.open && $store.sidebar.screen < 600,
                        }"
                        class="dark:hover:bg-zinc-800/50 hover:bg-gray-200/20 p-2 rounded-full cursor-pointer"

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
                            <div x-show="openProfile" x-transition x-cloak
                                @click.outside="openProfile = false"
                                class="absolute top-14 right-0 dark:bg-zinc-800 bg-white rounded-lg z-40 shadow-md
                                        w-[300px] sm:w-[350px] md:w-[400px] lg:w-[450px] p-3 sm:p-4 space-y-3">

                                <div class="relative border-box dark:bg-zinc-700 bg-gray-200 p-3 rounded-lg">
                                    <!-- Avatar and Email -->
                                    <div class="flex items-center gap-3">
                                        <div class="rounded-full w-16 h-16 sm:w-20 sm:h-20 shrink-0">
                                            <img src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('images/avatar.png') }}"
                                                alt="profile-pic"
                                                class="rounded-full w-full h-full object-cover">
                                        </div>
                                        <div class="flex flex-col gap-1 min-w-0">
                                            <span class="font-semibold text-sm truncate">{{ $userName }}</span>
                                            <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 truncate">{{ $userEmail }}</span>
                                            <div wire:poll.10s="updateStatus" class="flex items-center gap-2">
                                                <span class="{{ $status['color'] }} text-xs">
                                                    <i class="bi bi-circle-fill"></i>
                                                </span>
                                                <span>{{ $status['text'] }}</span>
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
                                <x-responsive-nav-link href="{{ route('settings') }}" class="flex items-center gap-2 text-sm dark:text-white dark:hover:bg-zinc-700 hover:bg-gray-200 w-full px-2 py-1 rounded-md" wire:navigate>
                                    <svg class="w-[1em] h-[1em]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" /> <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>Settings</span>
                                </x-responsive-nav-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Profile Section -->

               <!-- Notification Section -->
<div class="relative">
    <!-- Notification Icon & Badge -->
    <div
        @click="$store.notifications.toggle(); $store.sidebar.open = false;"
        class="relative cursor-pointer"
    >
        @if($unreadCount > 0)
            <span
                wire:loading.remove
                wire:target="markNotificationAsRead,deleteNotification"
                class="absolute -top-2 -right-5 bg-red-600 text-white rounded-full p-2 w-8 z-20 text-sm flex justify-center"
            >
                {{ $unreadCount }}
            </span>
            <span
                wire:loading
                wire:target="markNotificationAsRead,deleteNotification"
                class="absolute -top-2 -right-5 bg-red-600 text-white rounded-full p-2 w-8 z-20 text-sm flex justify-center"
            >
                {{ max(0, $unreadCount - 1) }}
            </span>
        @endif

        <div class="p-2 rounded-full dark:hover:bg-zinc-800/50 hover:bg-gray-200/20">
            <!-- bell icon -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M14.857 17.082a23.848..."/>
            </svg>
        </div>
    </div>

    <div
        x-data
        x-init="
            Livewire.on('keep-active-tab', e => {
                @this.set('activeTab', e.tab);
            })
        "
    >
     <!-- Sidebar (Drawer) -->
    <div
        x-show="$store.notifications.open"
        x-cloak
        class="fixed inset-0 z-40 flex"
    >
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/40" @click="$store.notifications.close()"></div>

        <!-- Sidebar Panel -->
        <div
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="relative w-80 bg-white dark:bg-zinc-800 shadow-xl h-screen ml-auto flex flex-col"
        >

            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b dark:border-zinc-700 flex-shrink-0">
                <span class="font-semibold">Notifications</span>
                <flux:button icon="x-mark" @click="$store.notifications.close()"></flux:button>
            </div>
<!-- Loader -->
<div wire:loading.flex wire:target="switchTab" class="justify-center items-center py-6">
    <div class="flex space-x-1">
        <div class="w-2 h-2 bg-zinc-500 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
        <div class="w-2 h-2 bg-zinc-500 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
        <div class="w-2 h-2 bg-zinc-500 rounded-full animate-bounce"></div>
    </div>
</div>
           <!-- Tabs -->
<div wire:loading.remove wire:target="switchTab" class="flex-shrink-0 px-3 py-2 flex gap-4 border-b dark:border-zinc-700">
    <button
        wire:click="switchTab('unread')"
        :class="{'bg-gray-200 dark:bg-zinc-700 font-semibold': '{{ $activeTab }}' === 'unread'}"
        class="px-3 py-2 rounded-full focus:outline-none hover:bg-gray-200 dark:hover:bg-zinc-700"
    >
        Unread ({{ $unreadCount }})
    </button>
    <button
        wire:click="switchTab('read')"
        :class="{'bg-gray-200 dark:bg-zinc-700 font-semibold': '{{ $activeTab }}' === 'read'}"
        class="px-3 py-2 rounded-full focus:outline-none hover:bg-gray-200 dark:hover:bg-zinc-700"
    >
        Read ({{ $readCount }})
    </button>
</div>



<!-- Notifications List -->
<div wire:loading.remove wire:target="switchTab" class="flex-1 overflow-y-auto p-4 space-y-2">
    @if($activeTab === 'unread')
        @if($unreadNotifications->isEmpty())
            <div class="text-lg text-gray-300 dark:text-white">No unread notifications</div>
        @else
            @foreach($unreadNotifications as $notif)
                <div wire:key="unread-{{ $notif['id'] }}" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-zinc-700 flex justify-between items-center">
                    <div>
                        <p class="text-sm">{{ $notif['data']['title'] ?? 'New Notification' }}</p>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}</span>
                    </div>
                    <flux:modal.trigger name="openModalNotif-{{ $notif['id'] }}">
                        <flux:button icon="ellipsis-horizontal"></flux:button>
                    </flux:modal.trigger>
                </div>
            @endforeach

            @if($unreadCount > $unreadLimit)
                <flux:button
                    wire:click="loadMore('unread')"
                    class="mt-3 w-full px-3 py-2 bg-gray-200 dark:bg-zinc-700 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600"
                >
                    Load More
                </flux:button>
            @endif
        @endif
    @elseif($activeTab === 'read')
        @if($readNotifications->isEmpty())
            <div class="text-lg text-gray-300 dark:text-white">No read notifications</div>
        @else
            @foreach($readNotifications as $notif)
                <div wire:key="read-{{ $notif['id'] }}" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-zinc-700 flex justify-between items-center">
                    <div>
                        <p class="text-sm">{{ $notif['data']['title'] ?? 'Notification' }}</p>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}</span>
                    </div>
                    <flux:modal.trigger name="openModalNotif-{{ $notif['id'] }}">
                        <flux:button icon="ellipsis-horizontal"></flux:button>
                    </flux:modal.trigger>
                </div>
            @endforeach

            @if($readCount > $readLimit)
                <flux:button
                    wire:click="loadMore('read')"
                    class="mt-3 w-full px-3 py-2 bg-gray-200 dark:bg-zinc-700 rounded-md hover:bg-gray-300 dark:hover:bg-zinc-600"
                >
                    Load More
                </flux:button>
            @endif
        @endif
    @endif
</div>


            @foreach ($unreadNotifications as $notif)
                    <flux:modal name="openModalNotif-{{ $notif['id'] }}" wire:key="modal-{{ $notif['id'] }}" class="w-96">
                        <flux:heading size="lg">{{ $notif['data']['title'] ?? 'Notification' }}</flux:heading>

                        <flux:text class="flex flex-col gap-3 p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $notif['data']['message'] ?? 'No details available.' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                Received {{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}
                            </p>
                        </flux:text>

                        <div class="flex justify-between w-full">
                            <flux:button size="sm" variant="primary" color="zinc"
                                        wire:click="markNotificationAsRead('{{ $notif['id'] }}')">
                                Mark as Read
                            </flux:button>

                            <flux:button size="sm" variant="danger"
                                        wire:click="deleteNotification('{{ $notif['id'] }}')">
                                Delete
                            </flux:button>
                        </div>
                    </flux:modal>
            @endforeach
                    </div>
                </div>
    </div>

            </div>
            </div>
        </div>
    </nav>
</div>
