<!-- Drawer Panel -->
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
        // Force-close all Flux/Dropdown overlays after Livewire update
        document.querySelectorAll('[role=menu]').forEach(el => el.remove());
    "
>
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-zinc-700 flex-shrink-0 bg-gray-50 dark:bg-zinc-800">
        <span class="flex gap-2 items-center text-xl font-semibold text-gray-800 dark:text-gray-100">
            <flux:icon.bell class="w-6 h-6" />
            <span>Notifications</span>
        </span>
    </div>

    <!-- Bulk Actions -->
    <div class="flex justify-between items-center p-3 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
        <div class="flex gap-2">
            <flux:button wire:click="markAllAsRead" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="markAllAsRead">Mark All Read</span>
                <span wire:loading wire:target="markAllAsRead">...</span>
            </flux:button>
            <flux:button wire:click="markAllAsUnread" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="markAllAsUnread">Mark All Unread</span>
                <span wire:loading wire:target="markAllAsUnread">...</span>
            </flux:button>
        </div>

        <flux:button variant="danger" wire:click="deleteAllNotifications" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="deleteAllNotifications">Delete All</span>
            <span wire:loading wire:target="deleteAllNotifications">...</span>
        </flux:button>
    </div>

    <!-- Scrollable Content -->
    <div
        class="relative flex-1 overflow-y-auto px-4 py-3 space-y-3"
        wire:poll.5s="loadNotifications"
    >
        @forelse ($this->allNotifications as $notification)
            <div
                wire:key="notification-{{ $notification->id }}"
                class="p-4 rounded-xl border border-gray-200 dark:border-zinc-700 flex justify-between items-start transition-colors
                    {{ is_null($notification->read_at) ? 'bg-gray-100 dark:bg-zinc-800' : 'bg-white dark:bg-zinc-900' }}"
            >
                <div class="flex-1 pr-3 break-words text-sm text-gray-800 dark:text-gray-200">
                    <div class="flex flex-col gap-2 justify-start">
                        <span class="font-bold">{{ $notification->data['title'] ?? '' }}</span>
                        <span>{{ $notification->data['body'] ?? 'No message' }}</span>
                    </div>

                    @if($notification->created_at)
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex-shrink-0">
                    <flux:dropdown align="end">
                        <flux:button variant="subtle" icon:trailing="chevron-down" size="sm">Actions</flux:button>
                        <flux:menu>
                            @if(is_null($notification->read_at))
                                <flux:menu.item icon="check-circle" wire:click="markNotificationAsRead('{{ $notification->id }}')">
                                    Mark as Read
                                </flux:menu.item>
                            @else
                                <flux:menu.item icon="arrow-uturn-left" wire:click="markNotificationAsUnread('{{ $notification->id }}')">
                                    Mark as Unread
                                </flux:menu.item>
                            @endif

                            <flux:menu.item icon="trash" variant="danger" wire:click="deleteNotification('{{ $notification->id }}')">
                                Delete
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-6">No notifications.</p>
        @endforelse

        <!-- Load More -->
        @if ($unreadCount + $readCount > $this->allNotifications->count())
            <div class="mt-4 flex justify-center">
                <flux:button wire:click="loadMore('all')" wire:loading.attr="disabled" variant="secondary">
                    <span wire:loading.remove wire:target="loadMore">Load More</span>
                    <span wire:loading wire:target="loadMore">Loading...</span>
                </flux:button>
            </div>
        @endif
    </div>
</div>
