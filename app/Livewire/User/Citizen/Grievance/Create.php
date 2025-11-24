<?php

namespace App\Livewire\User\Citizen\Grievance;

use App\Models\ActivityLog;
use App\Models\HistoryLog;
use App\Notifications\GeneralNotification;
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
    public $grievance_category;
    public $priority_level;
    public $department;
    public $attachments = [];
    public $grievance_title;
    public $grievance_details;
    public $departmentOptions;
    public function mount(): void
    {
       $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->pluck('department_name', 'department_name')
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
                ->allowHtmlValidationMessages()
                ->placeholder('Edit report details...'),

        ];
    }

    protected function rules(): array
    {
        return [
            'is_anonymous'        => ['required', 'boolean'],
            'grievance_type'      => ['required', 'string', 'max:255'],
            'grievance_category'  => ['required', 'string', 'max:255'],
            'priority_level'      => ['required', 'string', 'max:50'],
            'department'          => ['required', 'exists:departments,department_name'],
            'grievance_title'     => ['required', 'string', 'max:255'],
            'attachments.*'       => ['nullable', 'file', 'max:51200'],
        ];
    }

    protected function messages(): array
    {
        return [
            'is_anonymous.required'       => 'Please specify whether the report is anonymous.',
            'grievance_type.required'     => 'Please select a type.',
            'grievance_category.required' => 'Please select a category.',
            'priority_level.required'     => 'Please choose a priority level.',
            'department.required'         => 'Please select a department.',
            'department.exists'           => 'The selected department does not exist.',
            'grievance_title.required'    => 'Please provide a title of your report.',
            'grievance_title.max'         => 'The title cannot exceed 255 characters.',
            'attachments.*.file'          => 'Each attachment must be a valid file.',
            'attachments.*.max'           => 'Each attachment must not exceed 50MB.',
        ];
    }

    public function getUploadingAttachmentsProperty()
    {
        return $this->attachments && collect($this->attachments)->some(fn($f) => $f->getError() === null && !$f->hashName());
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

        $department = Department::where('department_name', $this->department)->first();

        if (! $department) {
            Notification::make()
                ->title('Invalid Department')
                ->body('The selected department does not exist.')
                ->warning()
                ->send();
            return;
        }

        if (! $department->is_active || !$department->is_available) {
            Notification::make()
                ->title('Department Not Available')
                ->body('The selected department is either inactive or unavailable.')
                ->warning()
                ->send();
            return;
        }

        $data = $this->form->getState();

        $cleanDetails = trim(strip_tags($data['grievance_details'] ?? ''));

        if ($cleanDetails === '' || $cleanDetails === null) {
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
            $processingDays = match ($this->priority_level) {
                'High'   => 3,
                'Normal' => 7,
                'Low'    => 20,
                default  => 7,
            };

            $grievance = Grievance::create([
                'user_id'          => auth()->id(),
                'grievance_type'   => $this->grievance_type,
                'grievance_category'=> $this->grievance_category,
                'priority_level'   => $this->priority_level,
                'grievance_title'  => $this->grievance_title,
                'grievance_details'=> $data['grievance_details'],
                'is_anonymous'     => (int) $this->is_anonymous,
                'grievance_status' => 'pending',
                'processing_days'  => $processingDays,
            ]);

            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $storedPath = $file->store('grievance_files', 'public');

                    GrievanceAttachment::create([
                        'grievance_id' => $grievance->grievance_id,
                        'file_path'    => $storedPath,
                        'file_name'    => $file->getClientOriginalName(),
                    ]);
                }
            }

            ActivityLog::create([
                'user_id'     => auth()->id(),
                'role_id'     => auth()->user()->roles->first()?->id ?? null,
                'module'      => 'Grievance',
                'action'      => 'Submit',
                'action_type' => 'create',
                'model_type'  => Grievance::class,
                'model_id'    => $grievance->grievance_id,
                'description' => "User submitted a grievance titled '{$grievance->grievance_title}'",
                'changes'     => $grievance->toArray(),
                'status'      => 'success',
                'ip_address'  => request()->ip(),
                'device_info' => request()->header('device') ?? null,
                'user_agent'  => request()->userAgent(),
                'platform'    => php_uname('s'),
                'location'    => null,
                'timestamp'   => now(),
            ]);

            HistoryLog::create([
                'user_id'        => auth()->id(),
                'action_type'    => 'create',
                'description'    => "Submitted grievance titled '{$grievance->grievance_title}'",
                'reference_table'=> 'grievances',
                'reference_id'   => $grievance->grievance_id,
                'ip_address'     => request()->ip(),
            ]);

            $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
                ->whereHas('departments', fn($q) =>
                    $q->where('hr_liaison_departments.department_id', $department->department_id)
                )->get();

            foreach ($hrLiaisons as $hr) {
                Assignment::create([
                    'grievance_id'  => $grievance->grievance_id,
                    'department_id' => $department->department_id,
                    'assigned_at'   => now(),
                    'hr_liaison_id' => $hr->id,
                ]);

                $hr->notify(new GeneralNotification(
                    'New Grievance Assigned',
                    "A grievance titled '{$grievance->grievance_title}' has been assigned to you.",
                    'info',
                    ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                    [],
                    true,
                    [
                        [
                            'label'        => 'View Grievance',
                            'url'          => route('hr-liaison.grievance.view', $grievance->grievance_ticket_id),
                            'open_new_tab' => true,
                        ]
                    ]
                ));

            }

            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();

            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'New Grievance Submitted',
                    "A new grievance titled '{$grievance->grievance_title}' has been submitted.",
                    'warning',
                    ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                    [],
                    true,
                    [
                        [
                            'label' => 'Open in Admin Panel',
                            'url'   => route('admin.forms.grievances.view', $grievance->grievance_ticket_id),
                            'open_new_tab' => true,
                        ],
                        [
                            'label'   => 'Undo',
                            'color'   => 'gray',
                            'dispatch'=> 'undoLatestGrievance',
                            'close'   => true
                        ],
                    ]
                ));

            }

            Notification::make()
                ->title('Grievance Submitted')
                ->body('Your grievance was submitted and assigned to the relevant HR liaisons.')
                ->success()
                ->send();

            $this->redirectRoute('citizen.grievance.index', navigate: true);
            session()->put('grievance_submitted_once', true);


        } catch (\Exception $e) {
            Notification::make()
                ->title('Submission Failed')
                ->body('Something went wrong while submitting your grievance.')
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
