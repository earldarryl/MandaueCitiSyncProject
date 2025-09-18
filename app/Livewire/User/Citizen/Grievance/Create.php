<?php

namespace App\Livewire\User\Citizen\Grievance;

use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use App\Models\Department;
use App\Models\Assignment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Grievance Form Submission')]
class Create extends Component implements Forms\Contracts\HasForms
{
    use WithFileUploads, Forms\Concerns\InteractsWithForms;

    public $grievance_type;
    public $grievance_title;
    public $grievance_details;
    public $department = [];
    public $grievance_files = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('grievance_type')
                ->label('Grievance Type')
                ->native(false)
                ->options([
                    'Complaints' => 'Complaints',
                    'Inquiry' => 'Inquiry',
                    'Request' => 'Request',
                    'Suggestion/Feedback' => 'Suggestion/Feedback',
                ])
                ->required(),

            Select::make('department')
                ->label('Department')
                ->multiple()
                ->options(Department::pluck('department_name', 'department_id')->toArray())
                ->required(),

            TextInput::make('grievance_title')
                ->label('Grievance Title')
                ->required()
                ->maxLength(255),

            RichEditor::make('grievance_details')
                ->label('Grievance Details')
                ->required()
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'underline',
                    'strike',
                    'bulletList',
                    'orderedList',
                    'link',
                    'blockquote',
                    'codeBlock',
                ])
                ->placeholder('Enter the details of your grievance here...'),

            FileUpload::make('grievance_files')
                ->label('Upload Attachment')
                ->multiple()
                ->maxFiles(5)
                ->maxSize(5120) // 5MB
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ]),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Save grievance
        $grievance = Grievance::create([
            'user_id' => auth()->id(),
            'grievance_type' => $data['grievance_type'],
            'grievance_title' => $data['grievance_title'],
            'grievance_details' => $data['grievance_details'],
            'category' => 'General',
            'grievance_status' => 'pending',
            'processing_days' => 0,
        ]);

        // Save attachments
        if (!empty($data['grievance_files'])) {
            foreach ($data['grievance_files'] as $filePath) {
                GrievanceAttachment::create([
                    'grievance_id' => $grievance->grievance_id,
                    'file_path'    => $filePath,
                    'file_name'    => basename($filePath),
                ]);
            }
        }

        // Assign HR liaisons
        foreach ($data['department'] as $deptId) {
            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                ->whereHas('departments', fn($q) => $q->where('hr_liaison_department.department_id', $deptId))
                ->get();

            foreach ($hrLiaisons as $hr) {
                Assignment::create([
                    'grievance_id' => $grievance->grievance_id,
                    'department_id' => $deptId,
                    'assigned_at' => now(),
                    'hr_liaison_id' => $hr->id,
                ]);
            }
        }

        // Reset form state AND public properties
        $this->form->fill([]);
        $this->grievance_type = null;
        $this->grievance_title = null;
        $this->grievance_details = null;
        $this->department = [];
        $this->grievance_files = [];

        // Filament notification
        Notification::make()
            ->title('Grievance Submitted')
            ->body('Your grievance was submitted successfully and assigned to the relevant HR liaisons.')
            ->success()
            ->send();
    }


    public function render()
    {
        return view('livewire.user.citizen.grievance.create');
    }
}
