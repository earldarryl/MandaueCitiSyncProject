<?php

namespace App\Livewire\User\Citizen\Grievance;

use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
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
    public $priority_level;
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
                ->prefixIcon("heroicon-o-document")
                ->label('Grievance Type')
                ->native(false)
                ->options([
                    'Complaints' => 'Complaints',
                    'Inquiry' => 'Inquiry',
                    'Request' => 'Request',
                    'Suggestion/Feedback' => 'Suggestion/Feedback',
                ])
                ->placeholder(false)
                ->extraAttributes(['class' => 'cursor-pointer'])
                ->required(),

            Select::make('department')
                ->label('Department')
                ->prefixIcon('heroicon-o-building-office')
                ->multiple()
                ->searchable()
                ->options(
                    Department::whereHas('hrLiaisons')
                        ->pluck('department_name', 'department_id')
                        ->toArray()
                )
                ->placeholder(false)
                ->extraAttributes(['class' => 'cursor-pointer'])
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
                ->placeholder(false)
                ->extraAttributes(['class' => 'cursor-pointer'])
                ->required(),

            TextInput::make('grievance_title')
                ->prefixIcon("heroicon-o-tag")
                ->label('Grievance Title')
                ->maxLength(255)
                ->placeholder('Enter the title of your grievance here...'),

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
                ->label('Upload Attachments')
                ->disk('public')
                ->directory('grievance_files')
                ->preserveFilenames()
                ->multiple()
                ->image()
                ->imageEditor()
                ->panelLayout('grid')
                ->maxFiles(5)
                ->maxSize(5120)
                ->storeFiles()
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->getUploadedFileNameForStorageUsing(fn (TemporaryUploadedFile $file): string => $file->getClientOriginalName()),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $grievance = Grievance::create([
            'user_id'          => auth()->id(),
            'grievance_type'   => $data['grievance_type'],
            'priority_level'   => $data['priority_level'],
            'grievance_title'  => $data['grievance_title'],
            'grievance_details'=> $data['grievance_details'],
            'category'         => 'General',
            'grievance_status' => 'pending',
            'processing_days'  => 0,
        ]);

        // Save attachments
        if (!empty($data['grievance_files'])) {
            foreach ($data['grievance_files'] as $filePath) {
                // Extract the filename from the stored path
                $originalName = basename($filePath);

                GrievanceAttachment::create([
                    'grievance_id' => $grievance->grievance_id,
                    'file_path'    => $filePath,
                    'file_name'    => $originalName,
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
                    'grievance_id'  => $grievance->grievance_id,
                    'department_id' => $deptId,
                    'assigned_at'   => now(),
                    'hr_liaison_id' => $hr->id,
                ]);
            }
        }

        $this->form->fill([]);
        $this->reset(['grievance_type', 'grievance_title', 'grievance_details', 'department', 'grievance_files', 'priority_level']);

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
