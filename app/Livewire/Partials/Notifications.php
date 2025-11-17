<?php

namespace App\Livewire\Partials;

use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Collection;

class Notifications extends Component
{
    public $user;
    public $userId;
    public $groupedNotifications = [];

    public $unreadCount = 0;
    public $readCount   = 0;

    public $limit = 20;
    public $totalNotifications = 0;

    protected $listeners = [
        'notification-created' => 'prependNewNotification',
        'refreshNotifications' => 'loadNotifications',
    ];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->userId = $this->user?->id;
        if ($this->user) {
            $this->loadNotifications();
        }
    }

    public function handleRealtimeNotification($payload): void
    {
        $this->prependNewNotification($payload['notification'] ?? null);
    }

    #[On('notificationUpdated')]
    public function loadNotifications(): void
    {
        if (! $this->user) return;

        $notifications = $this->user
            ->notifications()
            ->latest('created_at')
            ->take($this->limit)
            ->get();

        $this->totalNotifications = $this->user->notifications()->count();

        $this->groupedNotifications = $this->groupByTimeline($notifications);
        $this->updateCounts();
    }

    private function groupByTimeline(Collection $notifications): array
    {
        return $notifications->groupBy(function ($notification) {
            $date = $notification->created_at->startOfDay();
            $today = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();

            if ($date->equalTo($today)) {
                return 'Today';
            } elseif ($date->equalTo($yesterday)) {
                return 'Yesterday';
            } else {
                return $notification->created_at->format('F j, Y');
            }
        })->map(function ($group) {
            return $group->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '',
                    'body' => $n->data['body'] ?? '',
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at?->toDateTimeString(),
                    'diff' => $n->created_at?->diffForHumans(),
                    'actions' => $n->data['actions'] ?? [],
                ];

            })->values()->toArray();
        })->toArray();
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

        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        $this->user->unreadNotifications()->update(['read_at' => now()]);
        $this->refreshAfterBulk('All notifications marked as read');
    }

    public function markAllAsUnread(): void
    {
        $this->user->notifications()->whereNotNull('read_at')->update(['read_at' => null]);
        $this->refreshAfterBulk('All notifications marked as unread');
    }

    public function deleteAllNotifications(): void
    {
        $this->user->notifications()->delete();
        $this->refreshAfterBulk('All notifications deleted');
    }

    private function refreshAfterBulk(string $message): void
    {
        $this->loadNotifications();
        FilamentNotification::make()
            ->title($message)
            ->success()
            ->send();
    }

    public function markNotificationAsRead($notificationId): void
    {
        if ($n = $this->user->notifications()->find($notificationId)) {
            $n->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markNotificationAsUnread($notificationId): void
    {
        if ($n = $this->user->notifications()->find($notificationId)) {
            $n->update(['read_at' => null]);
            $this->loadNotifications();
        }
    }

    public function deleteNotification($notificationId): void
    {
        if ($n = $this->user->notifications()->find($notificationId)) {
            $n->delete();
            $this->loadNotifications();
        }
    }

    public function loadMore(): void
    {
        $this->limit += 20;
        $this->dispatch('notificationUpdated');
    }

    public function render()
    {
        return view('livewire.partials.notifications');
    }
}
