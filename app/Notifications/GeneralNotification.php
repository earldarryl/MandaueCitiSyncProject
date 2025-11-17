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

    public $extraData;
    public $metadata;
    public $saveToDatabase;
    public $actions;
    /**
     * Create a new notification instance.
     */
        public function __construct(
        $title = null,
        $body = null,
        $type = 'info',
        $extraData = [],
        $metadata = [],
        $saveToDatabase = true,
        $actions = []
    ) {
        $this->title          = $title;
        $this->body           = $body;
        $this->type           = $type;
        $this->extraData      = $extraData;
        $this->metadata       = $metadata;
        $this->saveToDatabase = $saveToDatabase;

        $this->actions = $actions;
    }



    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

    public function via($notifiable)
    {
        $channels = ['broadcast'];

        if ($this->saveToDatabase) {
            $channels[] = 'database';
        }

        return $channels;
    }

    public function toArray($notifiable)
    {
        return [
            'title'   => $this->title,
            'body'    => $this->body,
            'type'    => $this->type,
            'extra'   => $this->extraData,
            'actions' => $this->actions,
        ];
    }

    /**
     * Get the broadcast representation.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title'    => $this->title,
            'body'     => $this->body,
            'type'     => $this->type,
            'metadata' => $this->metadata,
            'actions'  => $this->actions,
        ]);
    }

}
