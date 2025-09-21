<?php

namespace App\Livewire\User\Citizen\Grievance;

use Filament\Schemas\Components\Grid;
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
#[Title('Edit Grievance')]
class Edit extends Component implements Forms\Contracts\HasForms
{
    use WithFileUploads, Forms\Concerns\InteractsWithForms;

    public Grievance $grievance;

    public $grievance_type;
    public $grievance_title;
    public $grievance_details;
    public $priority_level;
    public $grievance_files = [];
    public $existing_attachments = [];
    public $department = [];

  public function mount($id): void
    {
        $this->grievance = Grievance::with('attachments', 'assignments')->findOrFail($id);
        $this->existing_attachments = $this->grievance->attachments->toArray() ?? [];

        $this->form->fill([
            'grievance_type'    => $this->grievance->grievance_type,
            'grievance_title'   => $this->grievance->grievance_title,
            'grievance_details' => $this->grievance->grievance_details,
            'priority_level'    => $this->grievance->priority_level,
            // 'department' => $this->department,  <-- remove pre-fill
        ]);
    }
    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Select::make('grievance_type')
                        ->prefixIcon("heroicon-o-document")
                        ->label('Grievance Type')
                        ->native(false)
                        ->options([
                            'Complaints' => 'Complaints',
                            'Inquiry' => 'Inquiry',
                            'Request' => 'Request',
                            'Suggestion/Feedback' => 'Suggestion/Feedback',
                        ])
                        ->required(),

                    Select::make('priority_level')
                        ->prefixIcon("heroicon-o-document-chart-bar")
                        ->label('Priority Level')
                        ->native(false)
                        ->options([
                            'Low' => 'Low',
                            'Normal' => 'Normal',
                            'High' => 'High',
                        ])
                        ->required(),
                ]),

            Select::make('department')
                ->label('Department')
                ->prefixIcon("heroicon-o-building-office")
                ->multiple()
                ->searchable()
                ->options(
                    Department::whereHas('hrLiaisons', function ($query) {
                        $query->whereHas('roles', function ($q) {
                            $q->where('name', 'hr_liaison');
                        });
                    })
                    ->get()
                    ->unique('department_id')
                    ->pluck('department_name', 'department_id')
                    ->toArray()
                )
                ->required(),

            TextInput::make('grievance_title')
                ->prefixIcon("heroicon-o-tag")
                ->label('Grievance Title')
                ->required()
                ->maxLength(255),

            RichEditor::make('grievance_details')
                ->label('Grievance Details')
                ->required()
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'bulletList', 'orderedList', 'link',
                    'blockquote', 'codeBlock',
                ])
                ->placeholder('Enter the details of your grievance here...'),

            FileUpload::make('grievance_files')
                ->label('Upload Attachments')
                ->disk('public')
                ->multiple()
                ->enableReordering()
                ->directory('grievance_files')
                ->maxFiles(5)
                ->maxSize(5120)
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ]),
        ];
    }

    public function removeAttachment($attachmentId)
    {
        $attachment = GrievanceAttachment::find($attachmentId);
        if ($attachment) {
            \Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();

            // Fix: Ensure it's always an array after removing
            $this->existing_attachments = $this->grievance->attachments->toArray() ?? [];
        }
    }

    public function submit()
    {
        $data = $this->form->getState();

        // 1️⃣ Update grievance
        $this->grievance->update([
            'grievance_type'   => $data['grievance_type'],
            'priority_level'   => $data['priority_level'],
            'grievance_title'  => $data['grievance_title'],
            'grievance_details'=> $data['grievance_details'],
        ]);

        // 2️⃣ Handle new attachments
        if (!empty($data['grievance_files']) && is_array($data['grievance_files'])) {
            foreach ($data['grievance_files'] as $filePath) {
                $cleanPath = str_starts_with($filePath, 'grievance_files/')
                    ? $filePath
                    : 'grievance_files/' . $filePath;

                GrievanceAttachment::create([
                    'grievance_id' => $this->grievance->grievance_id,
                    'file_path'    => $cleanPath,
                    'file_name'    => basename($cleanPath),
                ]);
            }
        }

        // 3️⃣ Handle department assignments
        $departments = $data['department'] ?? [];
        Assignment::where('grievance_id', $this->grievance->grievance_id)->delete();

        foreach ($departments as $deptId) {
            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                // Fix: Specify the full table name to resolve the ambiguity
                ->whereHas('departments', fn($q) => $q->where('departments.department_id', $deptId))
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

        // 4️⃣ Notification
        Notification::make()
            ->title('Grievance Updated')
            ->body('Your grievance was successfully updated.')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.edit');
    }
}
