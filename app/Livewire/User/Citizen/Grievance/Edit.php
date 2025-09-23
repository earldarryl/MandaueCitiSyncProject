<?php

namespace App\Livewire\User\Citizen\Grievance;

use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use App\Models\Department;
use App\Models\Assignment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Edit Grievance')]
class Edit extends Component implements HasForms
{
    use WithFileUploads, InteractsWithForms;

    public Grievance $grievance;

    // Properties bound to Blade fields
    public $is_anonymous = false;
    public $grievance_type;
    public $priority_level;
    public $department = [];
    public $grievance_title;
    public $grievance_details;
    public $grievance_files = [];
    public $existing_attachments = [];

   public function mount($id): void
    {
        $this->grievance = Grievance::with('attachments', 'assignments', 'departments')->findOrFail($id);

        $this->existing_attachments = $this->grievance->attachments->toArray() ?? [];

        $this->grievance_title   = $this->grievance->grievance_title;
        $this->grievance_type    = $this->grievance->grievance_type;
        $this->priority_level    = $this->grievance->priority_level;
        $this->is_anonymous      = $this->grievance->is_anonymous;

        $this->form->fill([
            'is_anonymous'      => (int) $this->grievance->is_anonymous,
            'grievance_details' => $this->grievance->grievance_details,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            ToggleButtons::make('is_anonymous')
                ->hiddenLabel(true)
                ->options([
                    1 => 'Yes',
                    0 => 'No',
                ])
                ->icons([
                    1 => Heroicon::Eye,
                    0 => Heroicon::EyeSlash,
                ])
                ->default(0)
                ->required(),

            Select::make('department')
                ->hiddenLabel(true)
                ->multiple()
                ->searchable()
                ->options(
                    Department::whereHas('hrLiaisons')->pluck('department_name', 'department_id')->toArray()
                )
                ->required(),

            RichEditor::make('grievance_details')
                ->hiddenLabel(true)
                ->required()
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'bulletList', 'orderedList', 'link',
                    'blockquote', 'codeBlock',
                ]),

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


    protected function rules(): array
    {
        return [
            'grievance_type'    => ['required', 'string', 'max:255'],
            'priority_level'    => ['required', 'string'],
            'grievance_title'   => ['required', 'string', 'max:255'],
        ];
    }

    public function removeAttachment($attachmentId)
    {
        $attachment = GrievanceAttachment::find($attachmentId);

        if ($attachment) {
            \Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();

            $this->existing_attachments = $this->grievance->attachments->toArray() ?? [];

            $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                Notification::make()
                    ->title('Image Removed')
                    ->body("The image file **{$attachment->file_name}** was successfully removed.")
                    ->success()
                    ->send();
            } elseif ($extension === 'pdf') {
                Notification::make()
                    ->title('PDF Removed')
                    ->body("The PDF file **{$attachment->file_name}** was successfully removed.")
                    ->success()
                    ->send();
            } elseif (in_array($extension, ['doc', 'docx'])) {
                Notification::make()
                    ->title('Document Removed')
                    ->body("The Word document **{$attachment->file_name}** was successfully removed.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('File Removed')
                    ->body("The file **{$attachment->file_name}** was successfully removed.")
                    ->success()
                    ->send();
            }
        }
    }

   public function submit()
    {
        $data = $this->form->getState();

        try {
            $this->grievance->update([
                'is_anonymous'      => (int) $data['is_anonymous'],
                'grievance_type'    => $this->grievance_type,
                'priority_level'    => $this->priority_level,
                'grievance_title'   => $this->grievance_title,
                'grievance_details' => $data['grievance_details'],
            ]);

            if (!empty($data['grievance_files'])) {
                foreach ($data['grievance_files'] as $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $storedPath = $file->store('grievance_files', 'public');
                        $fileName = $file->getClientOriginalName();
                    } elseif (is_string($file)) {
                        $storedPath = $file;
                        $fileName = basename($file);
                    } else {
                        continue;
                    }

                    GrievanceAttachment::create([
                        'grievance_id' => $this->grievance->grievance_id,
                        'file_path'    => $storedPath,
                        'file_name'    => $fileName,
                    ]);
                }
            }

            Assignment::where('grievance_id', $this->grievance->grievance_id)->delete();

            foreach ($data['department'] as $deptId) {
                $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                    ->whereHas('departments', fn($q) => $q->where('departments.department_id', $deptId))
                    ->pluck('id');

                foreach ($hrLiaisons as $hrId) {
                    Assignment::firstOrCreate(
                        [
                            'grievance_id'  => $this->grievance->grievance_id,
                            'department_id' => $deptId,
                            'hr_liaison_id' => $hrId,
                        ],
                        [
                            'assigned_at' => now(),
                        ]
                    );
                }
            }

            session()->flash('notification', [
                'type' => 'success',
                'title' => 'Grievance Updated',
                'body'  => 'Your grievance was successfully updated.',
            ]);

            return $this->redirect(route('grievance.index', absolute: false), navigate: true);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Update Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.edit');
    }
}
