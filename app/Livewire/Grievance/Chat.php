<?php

namespace App\Livewire\Grievance;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Grievance;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chat extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use WithFileUploads;

    public Grievance $grievance;
    public $newMessage = '';
    public $currentUserId;
    public $isAuthorized = false;
    public $userRole;
    public array $messages = [];
    public ?array $data = [];
    public $messageLimit = 20;
    public $hasMore = true;
    public $loadingOlder = false;
    public array $files = [];

    public function mount(Grievance $grievance)
    {
        $this->currentUserId = Auth::id();
        $this->userRole = Auth::user()->role ?? 'guest';
        $this->grievance = $grievance->load('assignments');
        $this->isAuthorized = $this->checkAuthorization();

        if ($this->isAuthorized) {
            $this->loadMessages();
        }

        $this->form->fill();
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

    public function receiveMessage()
    {
        $this->loadMessages();

        $this->js('$nextTick(() => {
            const chatBox = document.getElementById("chat-box");
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        })');
    }

    private function checkAuthorization(): bool
    {
        return $this->currentUserId === $this->grievance->user_id ||
            $this->grievance->assignments->contains('hr_liaison_id', $this->currentUserId);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\FileUpload::make('files')
                ->label('Attachments')
                ->hiddenLabel(true)
                ->preserveFilenames()
                ->directory('chat_attachments')
                ->disk('public')
                ->maxSize(10240)
                ->multiple()
                ->imageEditor()
                ->panelLayout('grid')
                ->previewable(true)
                ->downloadable()
                ->openable()
                ->acceptedFileTypes([
                    'image/*',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->helperText('Accepted: Images, PDF, DOCX — Max 10MB each.'),
        ];
    }

    public function loadMessages()
    {
        $query = Message::where('grievance_id', $this->grievance->grievance_id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->take($this->messageLimit)
            ->get();

        $this->messages = $query->reverse()->values()->toArray();
        $this->hasMore = Message::where('grievance_id', $this->grievance->grievance_id)->count() > count($this->messages);
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $this->loadingOlder = true;
        $this->messageLimit += 20;
        $this->loadMessages();
        $this->loadingOlder = false;
    }

    public function sendMessage()
    {
        $state = $this->form->getState();

        if (!$this->newMessage && empty($state['files'])) {
            Notification::make()
                ->title('Nothing to send')
                ->warning()
                ->send();
            return;
        }

        $filePaths = [];
        $fileNames = [];

        if (!empty($state['files'])) {
            foreach ($state['files'] as $file) {
                $filePaths[] = $file;
                $fileNames[] = basename($file);
            }
        }

        $message = Message::create([
            'grievance_id' => $this->grievance->grievance_id,
            'sender_id'    => $this->currentUserId,
            'receiver_id'  => $this->getReceiverId(),
            'message'      => $this->newMessage,
            'file_path'    => $filePaths ? json_encode($filePaths) : null,
            'file_name'    => $fileNames ? json_encode($fileNames) : null,
        ])->load('sender');

        broadcast(new MessageSent($message));

        $receiver = \App\Models\User::find($message->receiver_id);
        if ($receiver) {
            Notification::make()
                ->title('New Message Received')
                ->body("{$message->sender->name} sent you a message in Grievance #{$this->grievance->grievance_id}.")
                ->icon('heroicon-o-chat-bubble-left-right')
                ->broadcast($receiver)
                ->sendToDatabase($receiver, isEventDispatched: true);
        }

        $this->reset(['newMessage']);
        $this->form->fill();
        $this->loadMessages();

        $this->js('$nextTick(() => {
            const chatBox = document.getElementById("chat-box");
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        })');
    }


    private function getReceiverId()
    {
        if ($this->currentUserId === $this->grievance->user_id) {
            return $this->grievance->assignments->first()->hr_liaison_id ?? null;
        }
        return $this->grievance->user_id;
    }

    public function render()
    {
        return view('livewire.grievance.chat');
    }
}
