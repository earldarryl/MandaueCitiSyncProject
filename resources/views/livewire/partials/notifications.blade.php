            <!-- Drawer Panel -->
            <div
                class="fixed z-[40] right-0 w-96 bg-white dark:bg-zinc-800 shadow-xl h-screen ml-auto flex flex-col transform transition-transform duration-300"
                x-show="$store.notifications.open"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
            >
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-zinc-700 flex-shrink-0">
                    <span class="font-semibold">Notifications</span>
                    <flux:button type="button" icon="x-mark" variant="subtle" @click="$store.notifications.close()" ></flux:button>
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
