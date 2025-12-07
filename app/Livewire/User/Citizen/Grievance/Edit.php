<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use App\Models\HistoryLog;
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
            'grievance_title' => ['required', 'string', 'max:255'],
            'grievance_details' => ['required', 'string'],
            'attachments.*'  => ['nullable', 'file', 'max:51200'],
        ];
    }

    protected function messages(): array
    {
        return [
            'grievance_title.required' => 'Please provide a title of your report.',
            'grievance_title.max'      => 'The title cannot exceed 255 characters.',
            'grievance_details.required'    => 'Please provide detailed information about your report.',
            'attachments.*.file'       => 'Each attachment must be a valid file.',
            'attachments.*.max'        => 'Each attachment must not exceed 50MB.',
        ];
    }

    public function removeAttachment($attachmentId)
    {
        $attachment = GrievanceAttachment::find($attachmentId);

        if ($attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();

            $this->existing_attachments = $this->grievance->fresh()->attachments->toArray();

            Notification::make()
                ->title('Attachment Removed')
                ->body("The file **{$attachment->file_name}** was successfully removed.")
                ->success()
                ->send();

            $this->dispatch('close-all-modals');
        }
    }

    public function submit(): void
    {
        $this->showConfirmUpdateModal = false;

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

            Notification::make()
                ->title('Grievance Updated')
                ->body('Your grievance was successfully updated.')
                ->success()
                ->send();

            $this->reset([
                'attachments',
                'grievance_title',
                'grievance_details',
            ]);

            $this->dispatch('resetGrievanceDetails');

            $this->redirectRoute('citizen.grievance.index', navigate: true);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Update Failed')
                ->body('Something went wrong while updating your grievance. Please try again.')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.edit');
    }
}
