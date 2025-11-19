<?php

namespace App\Livewire\Grievance;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Grievance;
use App\Notifications\GeneralNotification;
use Filament\Forms;
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
        $this->userRole = Auth::user()->roles->first()?->name ?? 'guest';
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

    public function receiveMessage(array $message)
    {
        $this->loadMessages();

        $this->js('$nextTick(() => {
            const chatBox = document.getElementById("chat-box");
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        })');
    }

    private function checkAuthorization(): bool
    {
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

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
            ->take($this->messageLimit);

        $this->messages = $query->get()->reverse()->values()->toArray();

        $this->hasMore = Message::where('grievance_id', $this->grievance->grievance_id)
            ->count() > count($this->messages);
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
    $sender = auth()->user();

    // Validate
    if (!$this->newMessage && empty($state['files'])) {
        $sender->notify(new GeneralNotification(
            'Nothing to Send',
            'You must type a message or attach a file before sending.',
            'warning',
            [],
            [],
            true
        ));
        return;
    }

    // Handle files
    $filePaths = [];
    $fileNames = [];
    if (!empty($state['files'])) {
        foreach ($state['files'] as $file) {
            $filePaths[] = $file;
            $fileNames[] = basename($file);
        }
    }

    // -----------------------------
    // Send message to HR Liaison / Receiver
    // -----------------------------
    if (!$sender->hasRole('admin')) {
        $receiverId = $this->getReceiverId();
        if ($receiverId) {
            $messageData = [
                'grievance_id' => $this->grievance->grievance_id,
                'sender_id'    => $sender->id,
                'receiver_id'  => $receiverId,
                'message'      => $this->newMessage,
                'file_path'    => $filePaths ? json_encode($filePaths) : null,
                'file_name'    => $fileNames ? json_encode($fileNames) : null,
            ];

            $message = Message::create($messageData)->load('sender');
            broadcast(new MessageSent($message));

            // Notify the receiver
            $receiver = \App\Models\User::find($receiverId);
            if ($receiver) {
                $viewRoute = $receiver->hasRole('citizen')
                    ? route('citizen.grievance.view', $this->grievance->grievance_ticket_id)
                    : route('hr-liaison.grievance.view', $this->grievance->grievance_ticket_id);

                $receiver->notify(new GeneralNotification(
                    'New Message Received',
                    "{$sender->name} sent you a message in Grievance #{$this->grievance->grievance_ticket_id}.",
                    'info',
                    ['grievance_id' => $this->grievance->grievance_id],
                    [],
                    true,
                    [['label' => 'Open Chat', 'url' => $viewRoute, 'open_new_tab' => true]]
                ));
            }
        }
    }

    // -----------------------------
    // Send message to Admins individually
    // -----------------------------
    $admins = \App\Models\User::role('admin')->get()->filter(fn($admin) => $admin->id !== $sender->id);

    foreach ($admins as $admin) {
        $adminMessageData = [
            'grievance_id' => $this->grievance->grievance_id,
            'sender_id'    => $sender->id,
            'receiver_id'  => $admin->id,
            'message'      => $this->newMessage,
            'file_path'    => $filePaths ? json_encode($filePaths) : null,
            'file_name'    => $fileNames ? json_encode($fileNames) : null,
        ];

        $adminMessage = Message::create($adminMessageData)->load('sender');
        broadcast(new MessageSent($adminMessage));

        $admin->notify(new GeneralNotification(
            'New Message in Grievance',
            "{$sender->name} sent a message in Grievance #{$this->grievance->grievance_ticket_id}.",
            'info',
            ['grievance_id' => $this->grievance->grievance_id],
            [],
            true,
            [['label' => 'Open Grievance', 'url' => route('admin.forms.grievances.view', $this->grievance->grievance_ticket_id), 'open_new_tab' => true]]
        ));
    }

    // -----------------------------
    // Notify sender
    // -----------------------------
    $sender->notify(new GeneralNotification(
        'Message Sent',
        'Your message has been successfully delivered.',
        'success',
        ['grievance_id' => $this->grievance->grievance_id],
        [],
        true
    ));

    // -----------------------------
    // Reset UI
    // -----------------------------
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
