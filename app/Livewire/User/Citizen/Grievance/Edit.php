<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Grievance;
use App\Models\GrievanceAttachment;
use App\Models\Department;
use App\Models\Assignment;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Edit Grievance')]
class Edit extends Component implements Forms\Contracts\HasForms
{
    use WithFileUploads, InteractsWithForms;

    public Grievance $grievance;

    public $showConfirmUpdateModal = false;

    public $is_anonymous;
    public $grievance_type;
    public $grievance_category;
    public $priority_level;
    public $department;
    public $grievance_title;
    public $grievance_details;
    public $grievance_files = [];
    public $departmentOptions = [];
    public $existing_attachments = [];

    public function mount(Grievance $grievance): void
    {
        $this->grievance = $grievance->load('attachments', 'assignments');

        if ($this->grievance->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to edit this grievance.');
        }

        $this->is_anonymous = (bool) $this->grievance->is_anonymous;
        $this->grievance_type = $this->grievance->grievance_type;
        $this->grievance_category = $this->grievance->grievance_category;
        $this->priority_level = $this->grievance->priority_level;
        $this->grievance_title = $this->grievance->grievance_title;
        $this->grievance_details = $this->grievance->grievance_details;
        $this->department = Department::whereIn(
            'department_id',
            $this->grievance->assignments->pluck('department_id')->unique()
        )->pluck('department_name')->first();

        $this->existing_attachments = $this->grievance->attachments->toArray();

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->pluck('department_name', 'department_name')
            ->toArray();


        $this->form->fill([
            'grievance_details' => $this->grievance->grievance_details,
        ]);
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
                ->placeholder('Edit grievance details...'),

            FileUpload::make('grievance_files')
                ->hiddenLabel(true)
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
            'is_anonymous'      => ['required', 'boolean'],
            'grievance_type'    => ['required', 'string', 'max:255'],
            'grievance_category'=> ['nullable', 'string', 'max:255'],
            'priority_level'    => ['required', 'string', 'max:50'],
            'department' => ['required', 'exists:departments,department_name'],
            'grievance_title'   => ['required', 'string', 'max:255'],
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
        $this->showConfirmUpdateModal = false;

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->showConfirmUpdateModal = true;
            $this->setErrorBag($e->validator->getMessageBag());
            return;
        }

        $department = Department::where('department_name', $this->department)->first();

        if (!$department) {
            Notification::make()
                ->title('Invalid Department')
                ->body('The selected department does not exist.')
                ->warning()
                ->send();
            return;
        }

        if (!$department->is_active || !$department->is_available) {
            Notification::make()
                ->title('Department Not Available')
                ->body('The selected department is either inactive or unavailable. Please select another department.')
                ->warning()
                ->send();
            return;
        }

        $categoriesMap = [
            'Business Permit and Licensing Office' => [
                'Complaint' => [
                    'Delayed Business Permit Processing',
                    'Unclear Requirements or Procedures',
                    'Unfair Treatment by Personnel'
                ],
                'Inquiry' => [
                    'Business Permit Requirements Inquiry',
                    'Renewal Process Clarification',
                    'Schedule or Fee Inquiry'
                ],
                'Request' => [
                    'Document Correction or Update Request',
                    'Business Record Verification Request',
                    'Appointment or Processing Schedule Request'
                ],
            ],
            'Traffic Enforcement Agency of Mandaue' => [
                'Complaint' => [
                    'Traffic Enforcer Misconduct',
                    'Unjust Ticketing or Penalty',
                    'Inefficient Traffic Management'
                ],
                'Inquiry' => [
                    'Traffic Rules Clarification',
                    'Citation or Violation Inquiry',
                    'Inquiry About Traffic Assistance'
                ],
                'Request' => [
                    'Request for Traffic Assistance',
                    'Request for Event Traffic Coordination',
                    'Request for Violation Review'
                ],
            ],
            'City Social Welfare Services' => [
                'Complaint' => [
                    'Discrimination or Neglect in Assistance',
                    'Delayed Social Service Response',
                    'Unprofessional Staff Behavior'
                ],
                'Inquiry' => [
                    'Assistance Program Inquiry',
                    'Eligibility or Requirements Clarification',
                    'Social Service Schedule Inquiry'
                ],
                'Request' => [
                    'Request for Social Assistance',
                    'Financial Aid or Program Enrollment Request',
                    'Home Visit or Consultation Request'
                ],
            ],
        ];

        if (!isset($categoriesMap[$this->department][$this->grievance_type]) ||
            !in_array($this->grievance_category, $categoriesMap[$this->department][$this->grievance_type])
        ) {
            Notification::make()
                ->title('Invalid Category')
                ->body('The selected grievance category does not match the department and grievance type.')
                ->warning()
                ->send();
            return;
        }

        $data = $this->form->getState();

        try {
            $processingDays = match ($this->priority_level) {
                'High'   => 3,
                'Normal' => 7,
                'Low'    => 15,
                default  => 7,
            };

            $this->grievance->update([
                'user_id'           => auth()->id(),
                'grievance_type'    => $this->grievance_type,
                'grievance_category'=> $this->grievance_category,
                'priority_level'    => $this->priority_level,
                'grievance_title'   => $this->grievance_title,
                'grievance_details' => $data['grievance_details'],
                'is_anonymous'      => (int) $this->is_anonymous,
                'processing_days'   => $processingDays,
            ]);

            if (!empty($this->grievance_files)) {
                foreach ($this->grievance_files as $file) {
                    $storedPath = is_string($file) ? $file : $file->store('grievance_files', 'public');
                    GrievanceAttachment::create([
                        'grievance_id' => $this->grievance->grievance_id,
                        'file_path'    => $storedPath,
                        'file_name'    => basename($storedPath),
                    ]);
                }
            }

            Assignment::where('grievance_id', $this->grievance->grievance_id)->delete();

            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $department->department_id))
                ->get();

            foreach ($hrLiaisons as $hr) {
                Assignment::create([
                    'grievance_id'  => $this->grievance->grievance_id,
                    'department_id' => $department->department_id,
                    'assigned_at'   => now(),
                    'hr_liaison_id' => $hr->id,
                ]);
            }

            ActivityLog::create([
                'user_id'      => auth()->id(),
                'role_id'      => auth()->user()->roles->first()?->id,
                'module'       => 'Grievance Management',
                'action'       => "Updated grievance #{$this->grievance->grievance_ticket_id} ({$this->grievance_title})",
                'action_type'  => 'update',
                'model_type'   => Grievance::class,
                'model_id'     => $this->grievance->grievance_id,
                'description'  => auth()->user()->name . " ({auth()->user()->email}) updated grievance #{$this->grievance->grievance_ticket_id} ({$this->grievance_title})",
                'changes'      => $this->grievance->getChanges(),
                'status'       => 'success',
                'ip_address'   => request()->ip(),
                'device_info'  => request()->header('User-Agent'),
                'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                'platform'     => php_uname('s'),
                'location'     => geoip(request()->ip())?->city,
                'timestamp'    => now(),
            ]);

            Notification::make()
                ->title('Grievance Updated')
                ->body('Your grievance was successfully updated and reassigned to the correct HR liaisons.')
                ->success()
                ->send();

            $this->redirectRoute('citizen.grievance.index', navigate: true);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Update Failed')
                ->body('Something went wrong while updating your grievance. Please try again.')
                ->danger()
                ->send();

            $this->showConfirmUpdateModal = false;
        }
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.edit');
    }
}
