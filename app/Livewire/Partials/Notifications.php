<?php

namespace App\Livewire\Partials;

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
        $this->allNotifications = collect(); // ✅ Initialize it

        if ($this->user) {
            $this->loadNotifications();
        }
    }

    public function getListeners(): array
    {
        if (! $this->userId) {
            return [];
        }

        return [
            "echo-private:notifications.{$this->userId},notification.created" => 'handleRealtimeNotification',
        ];
    }

    public function handleRealtimeNotification($payload): void
    {
        try {
            \Log::info('📡 Reverb Notification received', ['payload' => $payload]);

            $notification = $payload['notification'] ?? null;
            if (! $notification) {
                \Log::warning('⚠️ Missing notification in payload', ['payload' => $payload]);
                return;
            }

            if (is_array($notification)) {
                $notification = (object) $notification;
            }

            if (isset($notification->notifiable_id) && $notification->notifiable_id !== $this->userId) {
                \Log::info('👤 Ignored: Notification is for another user', [
                    'current_user' => $this->userId,
                    'target_user' => $notification->notifiable_id,
                ]);
                return;
            }

            $this->prependNewNotification($notification);

            FilamentNotification::make()
                ->title($notification->data['title'] ?? 'New Notification')
                ->body($notification->data['body'] ?? '')
                ->success()
                ->send();

            \Log::info('✅ Filament notification shown successfully', [
                'user_id' => $this->userId,
                'notification_id' => $notification->id ?? null,
                'title' => $notification->data['title'] ?? '(none)',
            ]);
        } catch (\Throwable $e) {
            \Log::error('❌ Failed to handle Reverb notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

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

        // ✅ Merge both for a unified display list
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

        // ✅ Update collections properly
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

    public function render()
    {
        return view('livewire.partials.notifications');
    }
}
