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

    protected function getFormSchema(): array
    {
        return [
            RichEditor::make('grievance_details')
                ->hiddenLabel(true)
                ->required()
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                    ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                    ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                    ['table', 'attachFiles'],
                    ['undo', 'redo'],
                ])
                ->allowHtmlValidationMessages()
                ->placeholder('Edit report details...'),
        ];
    }

    protected function rules(): array
    {
        return [
            'grievance_title' => ['required', 'string', 'max:255'],
            'attachments.*'  => ['nullable', 'file', 'max:51200'],
        ];
    }

    protected function messages(): array
    {
        return [
            'grievance_title.required' => 'Please provide a title of your report.',
            'grievance_title.max'      => 'The title cannot exceed 255 characters.',
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

        $data = $this->form->getState();

        $cleanDetails = trim(strip_tags($data['grievance_details'] ?? ''));

        if ($cleanDetails === '') {
            $this->addError('grievance_details', '
                <div class="flex items-center justify-start gap-2 mt-3 text-sm font-medium text-red-500 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        class="w-5 h-5 flex-shrink-0">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.596c.75 1.335-.213 3.05-1.742 3.05H3.48c-1.53 0-2.492-1.715-1.741-3.05L8.257 3.1zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-4a.75.75 0 00-.75.75v2.5c0 .414.336.75.75.75s.75-.336.75-.75v-2.5A.75.75 0 0010 9z"
                            clip-rule="evenodd" />
                    </svg>

                    <span>Please provide detailed information about your report.</span>
                </div>
            ');
            $this->showConfirmModal = true;
            return;
        }

        try {
            $this->grievance->update([
                'grievance_title'   => $this->grievance_title,
                'grievance_details' => $data['grievance_details'],
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
