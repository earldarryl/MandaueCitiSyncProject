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
                <div class="flex items-center justify-between w-full ">
                    <!-- Avatar and Profile Info -->
                    <div class="flex items-center gap-4">
                        <div x-data="{ openProfile: false }" class="relative flex items-center">

                            <!-- Avatar Button -->
                            <div class="dark:bg-white w-10 h-10 rounded-full cursor-pointer"
                                @click="openProfile = !openProfile">
                                <img  src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('images/avatar.png') }}" alt="profile-pic" class="rounded-full w-full h-full">
                            </div>

                            <!-- Modal Profile Info -->
                            <div x-show="openProfile" x-transition x-cloak
                                @click.outside="openProfile = false"
                                class="absolute top-14 right-0 dark:bg-zinc-800 bg-white rounded-lg z-40 shadow-md min-w-[350px] p-4 space-y-3">

                                <div class="relative border-box dark:bg-zinc-700 bg-gray-200 p-3 rounded-lg">
                                    <!-- Avatar and Email -->
                                    <div class="flex items-center gap-3">
                                        <div class="rounded-full w-20 h-20">
                                            <img
                                                src="{{ $user->profile_pic ? Storage::url($user->profile_pic) : asset('images/avatar.png') }}"
                                                alt="profile-pic"
                                                class="rounded-full w-full h-full object-cover"
                                            />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <span class="font-semibold text-sm">{{ $userName }}</span>
                                            <span class="text-sm">{{ $userEmail }}</span>
                                        </div>
                                    </div>

                                    <!-- Manage Account -->
                                    <!-- Logout Button -->
                                    {{-- <div class="cursor-pointer flex items-center gap-2 text-sm dark:hover:bg-zinc-900 hover:bg-gray-200 w-full px-2 py-1 rounded-md"
                                        x-data
                                        @click="Livewire.dispatch('openModal', {
                                            action: 'log-out',
                                            title: 'Log Out',
                                            subtitle: 'Are you sure you want to log out?'
                                        })">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                        </svg>
                                        <span>Log out</span>
                                    </div> --}}
                                </div>

                                <x-responsive-nav-link href="{{ route('settings') }}"
                                    class="flex items-center gap-2 text-sm dark:text-white dark:hover:bg-zinc-700 hover:bg-gray-200 w-full px-2 py-1 rounded-md"
                                    wire:navigate>
                                    <svg class="w-[1em] h-[1em]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span>Settings</span>
                                </x-responsive-nav-link>

                            </div> <!-- End Tooltip -->
                        </div> <!-- End Avatar & Info -->
                    </div>
                </div>
            </div> <!-- End Profile Section -->

                <!-- Notification Section -->
            <div x-data="{ open: false }" class="relative">

                <!-- Notification Icon & Badge -->
                <div @click="open = !open" class="relative cursor-pointer">
                    @if ($unreadCount > 0)
                        <span
                            class="absolute -top-2 -right-5 bg-red-600 text-white rounded-full p-2 w-8 z-20 text-sm inline-flex justify-center">
                            {{ $unreadCount }}
                        </span>
                    @endif

                    <div class="p-2 relative dark:hover:bg-zinc-800/50 hover:bg-gray-200/20 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                </div>

                <!-- Sidebar (Drawer) -->
                <div x-show="open" x-cloak class="fixed inset-0 z-40 flex">

                    <!-- Overlay -->
                    <div class="fixed inset-0 bg-black/40" @click="open = false"></div>

                    <!-- Sidebar Panel -->
                    <div
                        x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="relative w-80 bg-white dark:bg-zinc-800 shadow-xl h-full ml-auto">

                        <!-- Header -->
                        <div class="flex items-center justify-between p-4 border-b dark:border-zinc-700">
                            <span class="font-semibold">Notifications</span>
                            <button @click="open = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                ✕
                            </button>
                        </div>

                        <!-- Notification List -->
                        <div class="p-4 max-h-[calc(100vh-4rem)] overflow-y-auto">
                            @forelse ($notifications as $notif)
                                <div class="mt-3 p-2 rounded-md dark:hover:bg-zinc-700 hover:bg-gray-100 cursor-pointer flex justify-between items-center">
                                    <div>
                                        <p class="text-sm">{{ $notif['data']['title'] ?? 'New Notification' }}</p>
                                        <span class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}
                                        </span>
                                    </div>
                                    @if(!$notif['read_at'])
                                        <button wire:click.stop="markAsRead('{{ $notif['id'] }}')"
                                            class="text-xs text-blue-500">
                                            Mark read
                                        </button>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm mt-2 text-gray-500">No notifications yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
                <!-- End Notification -->
            </div>
        </div>
    </nav>
</div>
