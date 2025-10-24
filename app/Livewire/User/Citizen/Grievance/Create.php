<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\HistoryLog;
use Filament\Actions\Concerns\InteractsWithActions;
use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use App\Models\Department;
use App\Models\Assignment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Grievance Form Submission')]
class Create extends Component implements Forms\Contracts\HasForms
{
    use WithFileUploads, InteractsWithForms, InteractsWithActions;

    public $showConfirmSubmitModal = false;
    public $showConfirmModal;
    public $is_anonymous;
    public $grievance_type;
    public $priority_level;
    public $department = [];
    public $grievance_files = [];
    public $grievance_title;
    public $grievance_details;
    public $departmentOptions;
    public function mount(): void
    {
        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->pluck('department_name', 'department_id')
            ->toArray();
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            RichEditor::make('grievance_details')
                ->hiddenLabel(true)
                ->required()
                ->toolbarButtons([
                    'bold','italic','underline','strike',
                    'bulletList','orderedList','link',
                    'blockquote','codeBlock'
                ])
                ->placeholder('Enter the details of your grievance here...'),

            FileUpload::make('grievance_files')
                ->hiddenLabel(true)
                ->multiple()
                ->preserveFilenames()
                ->downloadable()
                ->openable()
                ->previewable(true)
                ->reorderable()
                ->disk('public')
                ->panelLayout('grid')
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
            'is_anonymous'      => ['required', 'boolean'],
            'grievance_type'    => ['required', 'string', 'max:255'],
            'priority_level'    => ['required', 'string', 'max:50'],
            'department'        => ['required', 'array', 'min:1'],
            'department.*'      => ['exists:departments,department_id'],
            'grievance_title'   => ['required', 'string', 'max:255'],
            'grievance_details' => ['required', 'string'],
            'grievance_files.*' => ['nullable', 'file', 'max:51200'],
        ];
    }


    public function submit(): void
    {
        $this->showConfirmSubmitModal = false;

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->showConfirmModal = true;

            $this->setErrorBag($e->validator->getMessageBag());
            return;
        }

        $data = $this->form->getState();

        try {
            $grievance = Grievance::create([
                'user_id'          => auth()->id(),
                'grievance_type'   => $this->grievance_type,
                'priority_level'   => $this->priority_level,
                'grievance_title'  => $this->grievance_title,
                'grievance_details'=> $data['grievance_details'],
                'is_anonymous'     => (int) $this->is_anonymous,
                'grievance_status' => 'pending',
                'processing_days'  => 0,
            ]);

            if (!empty($this->grievance_files)) {
                foreach ($this->grievance_files as $file) {
                    $storedPath = is_string($file)
                        ? $file
                        : $file->store('grievance_files', 'public');

                    GrievanceAttachment::create([
                        'grievance_id' => $grievance->grievance_id,
                        'file_path'    => $storedPath,
                        'file_name'    => basename($storedPath),
                    ]);
                }
            }

            foreach ($this->department as $deptId) {
                $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                    ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $deptId))
                    ->get();

                foreach ($hrLiaisons as $hr) {
                    Assignment::create([
                        'grievance_id'  => $grievance->grievance_id,
                        'department_id' => $deptId,
                        'assigned_at'   => now(),
                        'hr_liaison_id' => $hr->id,
                    ]);
                }
            }

            HistoryLog::create([
                'user_id'         => auth()->id(),
                'action_type'     => 'grievance_submission',
                'description'     => "Submitted a new grievance titled '{$this->grievance_title}'.",
                'reference_id'    => $grievance->grievance_id,
                'reference_table' => 'grievances',
                'ip_address' => request()->ip(),
            ]);

            Notification::make()
                ->title('Grievance Submitted')
                ->body('Your grievance was submitted successfully and assigned to the relevant HR liaisons.')
                ->success()
                ->send();


            $this->showConfirmSubmitModal = false;

            $this->redirectRoute('citizen.grievance.index', navigate: true);

        } catch (\Exception $e) {
            if (!empty($this->grievance_files)) {
                foreach ($this->grievance_files as $file) {
                    if (is_string($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }

            Notification::make()
                ->title('Submission Failed')
                ->body('Something went wrong while submitting your grievance. Please try again.')
                ->danger()
                ->send();

            $this->showConfirmSubmitModal = false;
        }

    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.create');
    }
}
