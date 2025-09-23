<?php

namespace App\Livewire\User\Citizen\Grievance;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
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

    public $is_anonymous;
    public $grievance_type;
    public $grievance_title;
    public $grievance_details;
    public $priority_level;
    public $department = [];
    public $grievance_files = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            ToggleButtons::make('is_anonymous')
                ->label('Submit Anonymously?')
                ->options([true => 'Yes', false => 'No'])
                ->icons([true => Heroicon::Eye, false => Heroicon::EyeSlash])
                ->default(false)
                ->helperText('If checked, your identity will not be revealed to the assigned HR liaisons.')
                ->required(),

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

            Select::make('department')
                ->label('Department')
                ->prefixIcon('heroicon-o-building-office')
                ->multiple()
                ->searchable()
                ->options(
                    Department::whereHas('hrLiaisons')->pluck('department_name', 'department_id')->toArray()
                )
                ->required(),

            Select::make('priority_level')
                ->prefixIcon("heroicon-o-document-chart-bar")
                ->label('Priority Level')
                ->native(false)
                ->options(['Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High'])
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
                    'bold','italic','underline','strike',
                    'bulletList','orderedList','link',
                    'blockquote','codeBlock'
                ])
                ->placeholder('Enter the details of your grievance here...'),

            FileUpload::make('grievance_files')
                ->label('Upload Attachments')
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

    public function submit(): void
    {
        $data = $this->form->getState();

        try {
            $grievance = Grievance::create([
                'user_id'          => auth()->id(),
                'grievance_type'   => $data['grievance_type'],
                'priority_level'   => $data['priority_level'],
                'grievance_title'  => $data['grievance_title'],
                'grievance_details'=> $data['grievance_details'],
                'is_anonymous'     => $data['is_anonymous'],
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

            Notification::make()
                ->title('Grievance Submitted')
                ->body('Your grievance was submitted successfully and assigned to the relevant HR liaisons.')
                ->success()
                ->send();

            $this->redirectRoute('grievance.index', navigate: true);

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
        }
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.create');
    }
}
