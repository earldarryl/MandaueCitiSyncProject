<div
    class="fixed z-[40] right-0 w-96 bg-white dark:bg-zinc-900 shadow-2xl h-screen ml-auto flex flex-col transform transition-transform duration-300 border-l border-gray-200 dark:border-zinc-700"
    x-data
    x-show="$store.notifications.open"
    x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    x-on:notifications-updated.window="
        document.querySelectorAll('[role=menu]').forEach(el => el.remove());
    "
>
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-zinc-700 flex-shrink-0 bg-gray-50 dark:bg-zinc-800">
        <span class="flex gap-2 items-center text-xl font-semibold text-gray-800 dark:text-gray-100">
            <flux:icon.bell class="w-6 h-6" />
            <span>Notifications</span>
        </span>
    </div>

    <div class="flex flex-col justify-center p-2 gap-2 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 w-full">

        <div class="flex gap-2 w-full">
            <button
                type="button"
                wire:click="markAllAsRead"
                class="inline-flex items-center justify-center gap-2 w-full
                    px-4 py-2 text-sm font-bold rounded-lg
                    bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                    border border-blue-500 dark:border-blue-400
                    hover:bg-blue-200 dark:hover:bg-blue-800/50
                    focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                    transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
                @disabled($totalNotifications === 0)
            >
                <x-heroicon-o-check class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                <span wire:loading.remove wire:target="markAllAsRead,markAllAsUnread,deleteAllNotifications">Mark All Read</span>
                <span wire:loading wire:target="markAllAsRead,markAllAsUnread,deleteAllNotifications">Processing...</span>
            </button>

            <button
                type="button"
                wire:click="markAllAsUnread"
                class="inline-flex items-center justify-center gap-2 w-full
                    px-4 py-2 text-sm font-bold rounded-lg
                    bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                    border border-blue-500 dark:border-blue-400
                    hover:bg-blue-200 dark:hover:bg-blue-800/50
                    focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-700
                    transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
                @disabled($totalNotifications === 0)
            >
                <x-heroicon-o-x-mark class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                <span wire:loading.remove wire:target="markAllAsRead,markAllAsUnread,deleteAllNotifications">Mark All Unread</span>
                <span wire:loading wire:target="markAllAsRead,markAllAsUnread,deleteAllNotifications">Processing...</span>
            </button>
        </div>

        <div class="flex justify-between items-center w-full">
            <button
                type="button"
                wire:click="deleteAllNotifications"
                class="inline-flex items-center justify-center gap-2 w-full
                    px-4 py-2 text-sm font-bold rounded-lg
                    bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300
                    border border-red-500 dark:border-red-400
                    hover:bg-red-200 dark:hover:bg-red-800/50
                    focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-700
                    transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
                @disabled($totalNotifications === 0)
            >
                <x-heroicon-o-trash class="w-4 h-4 text-red-600 dark:text-red-400" />
                <span wire:loading.remove wire:target="markAllAsRead,markAllAsUnread,deleteAllNotifications">Delete All</span>
                <span wire:loading wire:target="markAllAsRead,markAllAsUnread,deleteAllNotifications">Processing...</span>
            </button>
        </div>
    </div>

    <div
        class="relative flex-1 overflow-y-auto px-4 py-3 space-y-6"
        wire:poll.5s="loadNotifications"
    >
        @forelse ($groupedNotifications as $dateLabel => $notifications)
            <div>
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 border-b border-gray-200 dark:border-zinc-700 pb-1">
                    {{ $dateLabel }}
                </h3>

                <div class="space-y-3">
                    @foreach ($notifications as $notification)
                        <div
                            wire:key="notification-{{ $notification['id'] }}"
                            class="relative p-4 rounded-xl border border-gray-200 dark:border-zinc-700 flex items-center justify-center transition-colors
                                {{ is_null($notification['read_at']) ? 'bg-gray-200 dark:bg-zinc-800' : 'bg-white dark:bg-zinc-900' }}"
                            style="min-height: 90px;"
                        >
                            <div class="flex w-full justify-between items-start">
                                <div class="flex-1 pr-3 break-words text-sm text-gray-800 dark:text-gray-200">
                                    <div class="flex flex-col gap-2 justify-start">
                                        <span class="font-bold">{{ $notification['title'] }}</span>
                                        <span>{{ $notification['body'] ?: 'No message' }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                                        {{ $notification['diff'] }}
                                    </span>
                                </div>

                                <div class="flex-shrink-0">
                                    <div class="relative flex-shrink-0" x-data="{ open: false, showActions: false }">
                                        <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-800 transition">
                                            <x-heroicon-o-ellipsis-horizontal class="w-8 h-8 text-black dark:text-white"/>
                                        </button>

                                        <div x-show="open" @click.away="open = false" x-transition
                                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-700 z-50">
                                            <div class="flex flex-col divide-y divide-gray-200 dark:divide-zinc-700">

                                                @if(is_null($notification['read_at']))
                                                    <button wire:click="markNotificationAsRead('{{ $notification['id'] }}')" class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm">
                                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500" /> Mark as Read
                                                    </button>
                                                @else
                                                    <button wire:click="markNotificationAsUnread('{{ $notification['id'] }}')" class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm">
                                                        <x-heroicon-o-arrow-uturn-left class="w-5 h-5 text-yellow-500" /> Mark as Unread
                                                    </button>
                                                @endif

                                                @if(!empty($notification['actions']))
                                                    <div class="px-2 py-1">
                                                        <button @click="showActions = !showActions" class="w-full text-left text-sm font-medium px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-zinc-800 flex justify-between items-center">
                                                            Actions
                                                            <svg :class="{ 'rotate-180': showActions }" class="h-4 w-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </button>

                                                        <div x-show="showActions" x-transition class="mt-1 space-y-1">
                                                            @foreach($notification['actions'] as $action)
                                                                <button
                                                                    class="w-full text-left px-4 py-2 text-sm rounded hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2"
                                                                    @if(!empty($action['wireClick'])) wire:click="{{ $action['wireClick'] }}" @endif
                                                                    @if(!empty($action['url'])) onclick="window.open('{{ $action['url'] }}')" @endif
                                                                    style="color: {{ $action['color'] ?? 'inherit' }}"
                                                                >
                                                                    <x-dynamic-component :component="$action['icon'] ?? 'heroicon-o-link'" class="w-4 h-4" />
                                                                    {{ $action['label'] }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <button wire:click="deleteNotification('{{ $notification['id'] }}')" class="px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-zinc-800 flex items-center gap-2 text-sm text-red-500">
                                                    <x-heroicon-o-trash class="w-5 h-5" /> Delete
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-500 dark:text-gray-400 w-full">
                <x-heroicon-o-archive-box-x-mark class="w-10 h-10 mb-2 text-gray-400 dark:text-gray-500" />
                <p class="text-sm font-medium">No notifications</p>
            </div>
        @endforelse

       @if ($totalNotifications > collect($groupedNotifications)->flatten(1)->count())
            <div class="mt-4 flex justify-center">
                <flux:button wire:click="loadMore" wire:loading.attr="disabled" variant="subtle">
                    <span wire:loading.remove wire:target="loadMore">Load More</span>
                    <span wire:loading wire:target="loadMore">Loading...</span>
                </flux:button>
            </div>
        @endif
    </div>

</div>

