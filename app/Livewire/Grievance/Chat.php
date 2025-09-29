<?php

namespace App\Livewire\Grievance;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Grievance;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public Grievance $grievance;
    public $newMessage = '';
    public $currentUserId;
    public array $messages = [];
    public $isAuthorized = false;
    public $userRole;

    protected $rules = [
        'newMessage' => 'required|string|max:1000',
    ];

    protected $listeners = ['refresh' => '$refresh'];

    public function mount(Grievance $grievance)
    {
        $this->currentUserId = Auth::id();
        $this->userRole = Auth::user()->role ?? 'guest';

        if (!$this->currentUserId) {
            abort(403, 'Unauthorized. Must be logged in.');
            return;
        }

        $this->grievance = $grievance->load('assignments');

        // Check authorization
        if ($this->currentUserId === $grievance->user_id) {
            $this->isAuthorized = true;
        } else {
            $assignment = $grievance->assignments->firstWhere('hr_liaison_id', $this->currentUserId);
            if ($assignment) {
                $this->isAuthorized = true;
            } else {
                abort(403, 'Unauthorized to view this chat.');
            }
        }

        if ($this->isAuthorized) {
            $this->loadMessages();
        }
    }

    public function getListeners()
    {
        if ($this->isAuthorized) {
            return [
                "echo-private:grievance.{$this->grievance->grievance_id},MessageSent" => 'receiveMessage',
            ];
        }
        return [];
    }

    public function receiveMessage($payload)
    {
        $this->loadMessages();

        $this->js('$nextTick(() => {
            const chatBox = document.getElementById("chat-box");
            if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        })');
    }

    public function sendMessage()
    {
        if (!$this->isAuthorized) {
            session()->flash('error', 'You are not authorized to send messages.');
            return;
        }

        $this->validate();

        if ($this->currentUserId === $this->grievance->user_id) {
            // Citizen → send to all HRs
            foreach ($this->grievance->assignments as $assignment) {
                $message = Message::create([
                    'grievance_id' => $this->grievance->grievance_id,
                    'sender_id'    => $this->currentUserId,
                    'receiver_id'  => $assignment->hr_liaison_id,
                    'message'      => $this->newMessage,
                ])->load('sender');

                broadcast(new MessageSent($message));
            }
        } else {
            $messageCitizen = Message::create([
                'grievance_id' => $this->grievance->grievance_id,
                'sender_id'    => $this->currentUserId,
                'receiver_id'  => $this->grievance->user_id,
                'message'      => $this->newMessage,
            ])->load('sender');

            broadcast(new MessageSent($messageCitizen));

            foreach ($this->grievance->assignments as $assignment) {
                if ($assignment->hr_liaison_id !== $this->currentUserId) {
                    $message = Message::create([
                        'grievance_id' => $this->grievance->grievance_id,
                        'sender_id'    => $this->currentUserId,
                        'receiver_id'  => $assignment->hr_liaison_id,
                        'message'      => $this->newMessage,
                    ])->load('sender');

                    broadcast(new MessageSent($message));
                }
            }
        }

        $this->newMessage = '';

        $this->loadMessages();

        $this->js('$nextTick(() => {
            const chatBox = document.getElementById("chat-box");
            if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        })');
    }


    protected function loadMessages()
    {
        $this->messages = Message::where('grievance_id', $this->grievance->grievance_id)
            ->with('sender')
            ->orderBy('created_at')
            ->get()
            ->unique(function ($msg) {
                return $msg->sender_id . '|' . $msg->message;
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.grievance.chat');
    }
}
