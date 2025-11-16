<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;


class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $body;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($title = null, $body = null, $type = 'info')
    {
        $this->title = $title;
        $this->body  = $body;
        $this->type  = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title ?? 'New Notification',
            'body'  => $this->body ?? 'You have a new notification',
            'type'  => $this->type,
        ];
    }

    /**
     * Get the broadcast representation.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->title ?? 'New Notification',
            'body'  => $this->body ?? 'You have a new notification',
            'type'  => $this->type,
        ]);
    }

}
