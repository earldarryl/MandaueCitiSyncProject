<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use App\Models\HistoryLog;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Edit Report')]
class Edit extends Component implements Forms\Contracts\HasForms
{
    use WithFileUploads, InteractsWithForms;

    public Grievance $grievance;

    public $grievance_title;
    public $grievance_details;
    public $attachments = [];
    public $existing_attachments = [];

    public $showConfirmModal = false;
    public $showConfirmUpdateModal = false;

    public function mount(Grievance $grievance): void
    {
        $this->grievance = $grievance->load('attachments');

        if ($this->grievance->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to edit this grievance.');
        }

        $this->grievance_title = $this->grievance->grievance_title;
        $this->grievance_details = $this->grievance->grievance_details;
        $this->existing_attachments = $this->grievance->attachments->toArray();

        $this->form->fill([
            'grievance_details' => $this->grievance_details,
        ]);
    }

    public function readableSize($bytes)
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1024 * 1024 * 1024) return round($bytes / (1024 * 1024), 1) . ' MB';
        return round($bytes / (1024 * 1024 * 1024), 1) . ' GB';
    }

    protected function rules(): array
    {
        return [
            'grievance_title' => ['required', 'string', 'max:60'],
            'grievance_details' => ['required', 'string'],
            'attachments.*'  => ['nullable', 'file', 'max:51200'],
        ];
    }

    protected function messages(): array
    {
        return [
            'grievance_title.required' => 'Please provide a title of your report.',
            'grievance_title.max'      => 'The title cannot exceed 60 characters.',
            'grievance_details.required'    => 'Please provide detailed information about your report.',
            'attachments.*.file'       => 'Each attachment must be a valid file.',
            'attachments.*.max'        => 'Each attachment must not exceed 50MB.',
        ];
    }

    public function getUploadingAttachmentsProperty()
    {
        return $this->attachments && collect($this->attachments)->some(fn($f) => $f->getError() === null && !$f->hashName());
    }

    public function removeAttachment($attachmentId)
    {
        $attachment = GrievanceAttachment::find($attachmentId);

        if ($attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();

            $this->existing_attachments = $this->grievance->fresh()->attachments->toArray();

            $message = "Attachment '{$attachment->file_name}' has been removed from grievance #{$this->grievance->grievance_ticket_id}.";

            $this->grievance->addRemark([
                'message'   => $message,
                'user_id'   => auth()->id(),
                'user_name' => $this->grievance->is_anonymous ? 'Anonymous' : auth()->user()->name,
                'role'      => auth()->user()->getRoleNames()->first(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'status'    => $this->grievance->grievance_status,
                'type'      => 'note',
            ]);

            auth()->user()->notify(new GeneralNotification(
                'Attachment Removed',
                "Attachment '{$attachment->file_name}' has been removed.",
                'success',
                ['grievance_ticket_id' => $this->grievance->grievance_ticket_id],
                ['type' => 'success'],
                true
            ));

            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                ->whereHas('departments', fn($q) =>
                    $q->where('hr_liaison_departments.department_id', $this->grievance->department_id)
                )->get();

            foreach ($hrLiaisons as $hr) {
                $hr->notify(new GeneralNotification(
                    'Attachment Removed',
                    $message,
                    'info',
                    ['grievance_ticket_id' => $this->grievance->grievance_ticket_id],
                    ['type' => 'info'],
                    true
                ));
            }

            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'Attachment Removed',
                    $message,
                    'warning',
                    ['grievance_ticket_id' => $this->grievance->grievance_ticket_id],
                    ['type' => 'info'],
                    true
                ));
            }

            $this->dispatch('close-all-modals');
        }
    }


    public function submit(): void
    {
        $this->showConfirmUpdateModal = false;

        $hasApproved = $this->grievance->editRequests()
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->exists();

        $pending = $this->grievance->editRequests()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        $denied = $this->grievance->editRequests()
            ->where('user_id', auth()->id())
            ->where('status', 'denied')
            ->exists();

        if ($pending) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Edit Request Denied',
                'message' => 'Your edit request is still awaiting approval.',
            ]);
            return;
        }

        if ($denied) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Edit Request Denied',
                'message' => 'Your request to edit this report was denied.',
            ]);
            return;
        }

        if (!$hasApproved) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Update Not Allowed',
                'message' => 'You are not authorized to update this report.',
            ]);
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->showConfirmModal = true;
            $this->setErrorBag($e->validator->getMessageBag());
            return;
        }

        try {
            $this->grievance->update([
                'grievance_title'   => $this->grievance_title,
                'grievance_details' => $this->grievance_details,
            ]);

            $changes = $this->grievance->getChanges();
            $originals = $this->grievance->getOriginal();
            $formattedMessage = '';

            foreach ($changes as $field => $newValue) {
                $oldValue = $originals[$field] ?? '';

                switch ($field) {
                    case 'grievance_title':
                        $label = 'Title';
                        break;
                    case 'grievance_details':
                        $label = 'Details';
                        $oldValue = strip_tags($oldValue);
                        $newValue = strip_tags($newValue);
                        break;
                    case 'updated_at':
                        $label = 'Last Updated';
                        $oldValue = \Carbon\Carbon::parse($oldValue)
                                    ->timezone(config('app.timezone'))
                                    ->format('Y-m-d h:i:s A');
                        $newValue = \Carbon\Carbon::parse($newValue)
                                    ->timezone(config('app.timezone'))
                                    ->format('Y-m-d h:i:s A');
                        break;
                    default:
                        $label = ucwords(str_replace('_', ' ', $field));
                }

                $formattedMessage .= "$label: changed from \"$oldValue\" to \"$newValue\"" . PHP_EOL;
            }

            $attachmentCount = !empty($this->attachments) ? count($this->attachments) : 0;
            if ($attachmentCount > 0) {
                $formattedMessage .= "Attachments Added: $attachmentCount file(s)" . PHP_EOL;
            }

            $this->grievance->addRemark([
                'message'   => trim($formattedMessage),
                'user_id'   => auth()->id(),
                'user_name' => $this->grievance->is_anonymous ? 'Anonymous' : auth()->user()->name,
                'role'      => auth()->user()->getRoleNames()->first(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'status'    => $this->grievance->grievance_status,
                'type'      => 'note',
            ]);

            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $storedPath = $file->store('grievance_files', 'public');

                    GrievanceAttachment::create([
                        'grievance_id' => $this->grievance->grievance_id,
                        'file_path'    => $storedPath,
                        'file_name'    => $file->getClientOriginalName(),
                    ]);
                }
            }

            $this->reset(['attachments']);
            $this->grievance->refresh();
            $this->grievance->load('attachments');
            $this->existing_attachments = $this->grievance->attachments->toArray();

            auth()->user()->notify(new GeneralNotification(
                'Report Updated',
                "Your report titled '{$this->grievance->grievance_title}' was updated.",
                'success',
                ['grievance_ticket_id' => $this->grievance->grievance_ticket_id],
                ['type' => 'success'],
                true,
                [
                    [
                        'label' => 'View Report',
                        'url'   => route('citizen.grievance.index', $this->grievance->grievance_ticket_id),
                        'open_new_tab' => false,
                    ],
                ]
            ));

            ActivityLog::create([
                'user_id'     => auth()->id(),
                'role_id'     => auth()->user()->roles->first()?->id,
                'module'      => 'Report Management',
                'action'      => "Updated report #{$this->grievance->grievance_ticket_id}",
                'action_type' => 'update',
                'model_type'  => Grievance::class,
                'model_id'    => $this->grievance->grievance_id,
                'description' => auth()->user()->name . " updated a report.",
                'changes'     => $this->grievance->getChanges(),
                'status'      => 'success',
                'ip_address'  => request()->ip(),
                'device_info' => request()->header('User-Agent'),
                'timestamp'   => now(),
            ]);

            HistoryLog::create([
                'user_id'        => auth()->id(),
                'action_type'    => 'update',
                'description'    => "Updated grievance titled '{$this->grievance->grievance_title}'",
                'reference_table'=> 'grievances',
                'reference_id'   => $this->grievance->grievance_id,
                'ip_address'     => request()->ip(),
            ]);

            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                ->whereHas('departments', fn($q) =>
                    $q->where('hr_liaison_departments.department_id', $this->grievance->department_id)
                )->get();

            foreach ($hrLiaisons as $hr) {
                $hr->notify(new GeneralNotification(
                    'Report Updated',
                    "A report titled '{$this->grievance->grievance_title}' has been updated.",
                    'info',
                    ['grievance_ticket_id' => $this->grievance->grievance_ticket_id],
                    ['type' => 'info'],
                    true,
                    [
                        [
                            'label'        => 'View Updated Report',
                            'url'          => route('hr-liaison.grievance.view', $this->grievance->grievance_ticket_id),
                            'open_new_tab' => false,
                        ],
                    ]
                ));
            }

            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'Report Updated',
                    "A report titled '{$this->grievance->grievance_title}' has been updated.",
                    'warning',
                    ['grievance_ticket_id' => $this->grievance->grievance_ticket_id],
                    ['type' => 'info'],
                    true,
                    [
                        [
                            'label'        => 'Open in Admin Panel',
                            'url'          => route('admin.forms.grievances.view', $this->grievance->grievance_ticket_id),
                            'open_new_tab' => false,
                        ],
                    ]
                ));
            }

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Update Failed',
                'message' => 'Something went wrong while updating your report. Please try again.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.edit');
    }
}
