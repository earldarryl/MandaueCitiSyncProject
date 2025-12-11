<?php

namespace App\Livewire\Partials;

use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Grievance;
use App\Notifications\GeneralNotification;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
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
    public int $limit = 10;
    public int $totalMessagesCount = 0;
    public int $loadedMessageCount = 10;
    public const MESSAGE_BATCH_SIZE = 10;
    public $hasMore = true;
    public $canLoadMore = false;
    public array $files = [];
    protected $listeners = ['loadMore' => 'loadOlderMessages'];

    public function mount(Grievance $grievance)
    {
        $this->currentUserId = Auth::id();
        $this->userRole = Auth::user()->roles->first()?->name ?? 'guest';
        $this->grievance = $grievance->load('assignments');
        $this->isAuthorized = $this->checkAuthorization();

        if ($this->isAuthorized) {
            $this->loadMessages();
            $this->totalMessagesCount = Message::where('grievance_id', $this->grievance->grievance_id)->count();
        }

        $this->form->fill();
    }

    #[On('refreshChat')]
    public function refreshChat($grievanceId)
    {
        if ($grievanceId == $this->grievance->grievance_id) {
            $this->loadMessages();
            $this->dispatch('messagesLoaded', canLoadMore: $this->canLoadMore);
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

    public function receiveMessage(array $message)
    {
        $this->loadMessages();
        $this->dispatch('messagesLoaded', canLoadMore: $this->canLoadMore);
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
                ->alignCenter()
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
                ->helperText('Uploading... Please wait until upload is complete before sending.')
        ];
    }

    public function loadMore()
    {
        if ($this->canLoadMore) {
            $this->loadedMessageCount += self::MESSAGE_BATCH_SIZE;
            $this->loadMessages();
        }

        $this->dispatch('messagesLoaded', canLoadMore: $this->canLoadMore);
    }

    public function loadOlderMessages()
    {
        $this->loadMore();
    }

    public function loadMessages()
    {
        $this->totalMessagesCount = Message::where('grievance_id', $this->grievance->grievance_id)->count();

        $loadSize = min($this->loadedMessageCount, $this->totalMessagesCount);

        $skip = max(0, $this->totalMessagesCount - $loadSize);

        $this->canLoadMore = $this->loadedMessageCount < $this->totalMessagesCount;

        $this->messages = $this->grievance->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->skip($skip)
            ->take($loadSize)
            ->get()
            ->toArray();
    }

    public function getMessagesProperty()
    {
        return $this->messages;
    }

    public function getCanLoadMoreProperty()
    {
        return $this->limit < $this->totalMessagesCount;
    }


    public function sendMessage()
    {
        $state = $this->form->getState();
        $sender = auth()->user();

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

        $filePaths = [];
        $fileNames = [];
        if (!empty($state['files'])) {
            foreach ($state['files'] as $file) {
                $filePaths[] = $file;
                $fileNames[] = basename($file);
            }
        }

        $recipientIds = $this->getReceiverIds();
        $recipients = \App\Models\User::whereIn('id', $recipientIds)
            ->where('id', '!=', $sender->id)
            ->get();

        $admins = \App\Models\User::role('admin')
            ->where('id', '!=', $sender->id)
            ->get();

        $recipients = $recipients->merge($admins);

        $firstRecipientId = $recipients->first()?->id ?? null;

        $messageData = [
            'grievance_id' => $this->grievance->grievance_id,
            'sender_id'    => $sender->id,
            'recipient_id' => $firstRecipientId,
            'message'      => $this->newMessage,
            'file_path'    => $filePaths ? json_encode($filePaths) : null,
            'file_name'    => $fileNames ? json_encode($fileNames) : null,
        ];

        $message = Message::create($messageData)->load('sender');

        foreach ($recipients as $recipient) {
            broadcast(new MessageSent($message));

            $viewRoute = match (true) {
                $recipient->hasRole('citizen') => route('citizen.grievance.view', $this->grievance->grievance_ticket_id),
                $recipient->hasRole('hr_liaison') => route('hr-liaison.grievance.view', $this->grievance->grievance_ticket_id),
                $recipient->hasRole('admin') => route('admin.forms.grievances.view', $this->grievance->grievance_ticket_id),
                default => null,
            };

            $recipient->notify(new GeneralNotification(
                'New Message Received',
                "{$sender->name} sent you a message in Grievance #{$this->grievance->grievance_ticket_id}.",
                'info',
                [
                    'grievance_id' => $this->grievance->grievance_id,
                    'message' => $this->newMessage,
                    'files' => $filePaths
                ],
                [],
                true,
                $viewRoute ? [['label' => 'Open Chat', 'url' => $viewRoute, 'open_new_tab' => true]] : []
            ));
        }

        $sender->notify(new GeneralNotification(
            'Message Sent',
            'Your message has been successfully delivered.',
            'success',
            [
                'grievance_id' => $this->grievance->grievance_id,
                'message' => $this->newMessage,
                'files' => $filePaths
            ],
            [],
            true
        ));

        $this->reset(['newMessage']);
        $this->form->fill();
        $this->loadMessages();

        $this->js('$nextTick(() => {
            const chatBox = document.getElementById("chat-box");
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        })');
    }

    private function getReceiverIds(): array
    {
        if ($this->currentUserId === $this->grievance->user_id) {
            return $this->grievance->assignments
                ->pluck('hr_liaison_id')
                ->filter(fn($id) => $id !== null)
                ->toArray();
        }

        return [$this->grievance->user_id];
    }

    public function readableSize($bytes)
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1024 * 1024 * 1024) return round($bytes / (1024 * 1024), 1) . ' MB';
        return round($bytes / (1024 * 1024 * 1024), 1) . ' GB';
    }

    public function render()
    {
        return view('livewire.partials.chat');
    }
}
