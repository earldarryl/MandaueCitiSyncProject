<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Grievance;
use App\Models\Assignment;
use App\Models\GrievanceAttachment;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Edit Assigned Grievance')]
class Edit extends Component implements Forms\Contracts\HasForms
{
    use WithFileUploads, InteractsWithForms;

    public Grievance $grievance;
    public $assignment;
    public $grievance_details;
    public $grievance_files = [];
    public $existing_attachments = [];
    public $showConfirmUpdateModal = false;
    public $is_anonymous;
    public $grievance_type;
    public $priority_level;
    public $grievance_title;
    public $department = [];
    public $departmentOptions = [];

    public function mount($id): void
    {
        $this->assignment = Assignment::with('grievance.attachments')
            ->where('hr_liaison_id', Auth::id())
            ->where('grievance_id', $id)
            ->firstOrFail();

        $this->grievance = $this->assignment->grievance;

        $this->is_anonymous     = (bool) $this->grievance->is_anonymous;
        $this->grievance_type   = $this->grievance->grievance_type;
        $this->priority_level   = $this->grievance->priority_level;
        $this->grievance_title  = $this->grievance->grievance_title;
        $this->grievance_details = $this->grievance->grievance_details;

        $this->existing_attachments = $this->grievance->attachments->toArray();
        $this->department = $this->grievance->assignments->pluck('department_id')->unique()->values()->toArray();

        $this->departmentOptions = Department::pluck('department_name', 'department_id')->toArray();

        $this->form->fill([
            'grievance_details' => $this->grievance_details,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('department')
                ->label('Department(s)')
                ->multiple()
                ->options($this->departmentOptions)
                ->searchable()
                ->required()
                ->helperText('Select the department(s) handling this grievance.'),

            RichEditor::make('grievance_details')
                ->hiddenLabel()
                ->required()
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'bulletList', 'orderedList', 'link',
                    'blockquote', 'codeBlock'
                ])
                ->placeholder('Edit grievance details or add liaison notes...'),

            FileUpload::make('grievance_files')
                ->hiddenLabel()
                ->multiple()
                ->preserveFilenames()
                ->downloadable()
                ->openable()
                ->previewable(true)
                ->reorderable()
                ->disk('public')
                ->directory('grievance_files')
                ->maxSize(51200)
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                ])
                ->helperText('Accepted files: PDF, Word, Excel, PowerPoint, JPG, PNG, GIF. Max size 50MB.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'department' => ['required', 'array', 'min:1'],
            'department.*' => ['exists:departments,department_id'],
            'grievance_details' => ['required', 'string'],
            'grievance_files.*' => ['nullable', 'file', 'max:51200'],
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
        }
    }

    public function submit(): void
    {
        $this->validate();

        $data = $this->form->getState();

        try {
            // Update grievance details
            $this->grievance->update([
                'grievance_details' => $data['grievance_details'],
            ]);

            // Upload new attachments if any
            if (!empty($this->grievance_files)) {
                foreach ($this->grievance_files as $file) {
                    $storedPath = is_string($file)
                        ? $file
                        : $file->store('grievance_files', 'public');

                    GrievanceAttachment::create([
                        'grievance_id' => $this->grievance->grievance_id,
                        'file_path' => $storedPath,
                        'file_name' => basename($storedPath),
                    ]);
                }
            }

            // Reassign departments (if changed)
            Assignment::where('grievance_id', $this->grievance->grievance_id)->delete();

            foreach ($this->department as $deptId) {
                $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                    ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $deptId))
                    ->get();

                foreach ($hrLiaisons as $hr) {
                    Assignment::create([
                        'grievance_id'  => $this->grievance->grievance_id,
                        'department_id' => $deptId,
                        'assigned_at'   => now(),
                        'hr_liaison_id' => $hr->id,
                    ]);
                }
            }

            Notification::make()
                ->title('Grievance Updated')
                ->body('The grievance was successfully updated and reassigned to the selected department(s).')
                ->success()
                ->send();

            $this->redirectRoute('hr-liaison.grievance.index', navigate: true);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Update Failed')
                ->body('Something went wrong: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.edit');
    }
}
