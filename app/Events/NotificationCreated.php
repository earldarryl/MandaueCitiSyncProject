<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
     public $notification;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($notification, $userId)
    {
        $this->notification = $notification;
        $this->userId = $userId;

        Log::info('📤 NotificationCreated event initialized', [
            'user_id' => $this->userId,
            'notification_id' => $notification->id ?? null,
            'notification_title' => $notification->data['title'] ?? null,
            'notification_body' => $notification->data['body'] ?? null,
        ]);
    }

    /**
     * The channel to broadcast on.
     */
    public function broadcastOn(): array
    {
        $channel = new PrivateChannel("notifications.{$this->userId}");

        // 🧩 Log the broadcast channel name
        Log::info('📡 Notification will broadcast on channel', [
            'channel' => "notifications.{$this->userId}",
        ]);

        return [$channel];
    }

    /**
     * Event alias for frontend listeners.
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    /**
     * The data that will be sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        // 🪄 Include the notification payload for Livewire / JS
        $payload = [
            'notification' => $this->notification,
            'user_id' => $this->userId,
        ];

        Log::info('✅ Notification broadcast payload ready', [
            'user_id' => $this->userId,
            'payload_summary' => [
                'title' => $this->notification->data['title'] ?? null,
                'body' => $this->notification->data['body'] ?? null,
            ],
        ]);

        return $payload;
    }
}
