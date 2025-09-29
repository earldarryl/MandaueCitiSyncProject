<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    use SerializesModels;

    public function __construct(public Message $message)
    {

    }

     public function broadcastOn()
    {
        return [
            new PrivateChannel('grievance.' . $this->message->grievance_id)
        ];
    }

    public function broadcastWith()
    {
        return [
            "id"          => $this->message->id,
            "sender_id"   => $this->message->sender_id,
            "receiver_id" => $this->message->receiver_id,
            "grievance_id"=> $this->message->grievance_id,
            "message"     => $this->message->message,
        ];
    }
}
