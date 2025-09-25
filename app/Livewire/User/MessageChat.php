<?php

namespace App\Livewire\User;

use App\Models\Message;
use App\Models\Grievance;
use Livewire\Component;

class MessageChat extends Component
{
    public Grievance $grievance;
    public $messageText;

    protected $rules = [
        'messageText' => 'required|string|max:1000',
    ];

    public function mount(Grievance $grievance)
    {
        $this->grievance = $grievance;
    }

    public function sendMessage()
    {
        $this->validate();

        Message::create([
            'grievance_id' => $this->grievance->grievance_id,
            'sender_id'    => auth()->id(),
            'receiver_id'  => $this->getReceiverId(),
            'message'      => $this->messageText,
        ]);

        $this->reset('messageText');
        $this->dispatch('$refresh'); // refresh messages
    }

    private function getReceiverId()
    {
        // Example logic: adjust to your system
        return auth()->user()->role === 'citizen'
            ? $this->grievance->assigned_hr_liaison_id
            : $this->grievance->user_id;
    }
    public function render()
    {
        return view('livewire.user.message-chat', [
            'messages' => $this->grievance->messages()->with('sender')->latest()->get(),
        ]);
    }
}
