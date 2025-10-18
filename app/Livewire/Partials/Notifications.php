<?php

namespace App\Livewire\Partials;

use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification as FilamentNotification;
class Notifications extends Component
{
    public $user;
    public $userId;
    public $unreadNotifications;
    public $readNotifications;
    public $allNotifications;

    public $unreadCount = 0;
    public $readCount   = 0;

    public $unreadLimit = 20;
    public $readLimit   = 20;

    protected $listeners = [
        'notification-created' => 'prependNewNotification',
        'refreshNotifications' => 'loadNotifications',
    ];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->userId = $this->user?->id;

        $this->unreadNotifications = collect();
        $this->readNotifications = collect();
        $this->allNotifications = collect();

        if ($this->user) {
            $this->loadNotifications();
        }
    }

    public function getListeners(): array
    {
        return [
            "echo-private:notifications.{$this->userId},NotificationCreated" => 'handleRealtimeNotification',
        ];
    }

    public function handleRealtimeNotification($payload): void
    {
        \Log::info('📡 Reverb Notification received', ['payload' => $payload]);

        $notification = $payload['notification'] ?? null;

        $this->prependNewNotification($notification);

        FilamentNotification::make()
            ->title($notification['data']['title'] ?? 'New Notification')
            ->body($notification['data']['body'] ?? '')
            ->success()
            ->send();
    }

    #[On('notificationUpdated')]
    public function loadNotifications(): void
    {
        if (! $this->user) return;

        $this->unreadNotifications = $this->user
            ->unreadNotifications()
            ->latest('created_at')
            ->take($this->unreadLimit)
            ->get();

        $this->readNotifications = $this->user
            ->readNotifications()
            ->latest('created_at')
            ->take($this->readLimit)
            ->get();

        $this->allNotifications = $this->unreadNotifications
            ->merge($this->readNotifications)
            ->sortByDesc('created_at')
            ->values();

        $this->updateCounts();
    }

    private function updateCounts(): void
    {
        $this->unreadCount = $this->user->unreadNotifications()->count();
        $this->readCount   = $this->user->readNotifications()->count();
        $this->dispatch('updateUnreadCount');
    }

    public function prependNewNotification($notification): void
    {
        if (!$this->user || !$notification) return;

        if (is_array($notification)) {
            $notification = (object) $notification;
        }

        if (isset($notification->notifiable_id) && $notification->notifiable_id !== $this->user->id) {
            return;
        }

        $this->unreadNotifications = $this->unreadNotifications->prepend($notification)->values();
        $this->allNotifications = $this->allNotifications->prepend($notification)->values();

        $this->unreadCount = $this->unreadNotifications->count();
        $this->dispatch('updateUnreadCount');

        FilamentNotification::make()
            ->title($notification->data['title'] ?? 'New Notification')
            ->body($notification->data['body'] ?? '')
            ->success()
            ->send();
    }

    // 🔹 MARK AS READ
    public function markNotificationAsRead($notificationId): void
    {
        $notification = $this->user->notifications()->find($notificationId);

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
            FilamentNotification::make()
                ->title('Notification marked as read')
                ->success()
                ->send();
        }

    }

    public function markNotificationAsUnread($notificationId): void
    {
        $notification = $this->user->notifications()->find($notificationId);

        if ($notification && $notification->read_at) {
            $notification->update(['read_at' => null]);
            FilamentNotification::make()
                ->title('Notification marked as unread')
                ->success()
                ->send();
        }
    }

    public function deleteNotification($notificationId): void
    {
        $notification = $this->user->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
            FilamentNotification::make()
                ->title('Notification deleted')
                ->success()
                ->send();
        }
    }

    public function markAllAsRead(): void
    {
        $this->user->unreadNotifications->markAsRead();

        FilamentNotification::make()
            ->title('All notifications marked as read')
            ->success()
            ->send();
    }

    public function markAllAsUnread(): void
    {
        $this->user->notifications()
            ->whereNotNull('read_at')
            ->update(['read_at' => null]);

        FilamentNotification::make()
            ->title('All notifications marked as unread')
            ->success()
            ->send();

    }

    public function deleteAllNotifications(): void
    {
        $this->user->notifications()->delete();

        FilamentNotification::make()
            ->title('All notifications deleted')
            ->success()
            ->send();

    }

    public function loadMore($type = 'all'): void
    {
        $this->unreadLimit += 20;
        $this->readLimit += 20;
        $this->dispatch('notificationUpdated');
    }

    public function render()
    {
        return view('livewire.partials.notifications');
    }
}
