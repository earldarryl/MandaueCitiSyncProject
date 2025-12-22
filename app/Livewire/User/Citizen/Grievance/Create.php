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
#[Title('Report Form Submission')]
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
    public $departmentOptions = [];
    public $categoriesMap = [];
    protected $listeners = [
        'handleDelayedRedirect',
    ];

    public function handleDelayedRedirect()
    {
        $this->redirectRoute('citizen.grievance.index', navigate: true);
    }

    public function mount(): void
    {
        $departments = Department::whereHas('hrLiaisons')
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->where('requires_hr_liaison', 1)
            ->get(['department_name', 'grievance_categories']);

        $this->departmentOptions = $departments
            ->pluck('department_name', 'department_name')
            ->toArray();

        $this->categoriesMap = $departments
            ->mapWithKeys(fn ($dept) => [
                $dept->department_name => $dept->grievance_categories ?? [],
            ])
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'is_anonymous'        => ['required', 'boolean'],
            'grievance_type'      => ['required', 'string', 'max:255'],
            'grievance_category'  => ['required', 'string', 'max:255'],
            'priority_level'      => ['required', 'string', 'max:50'],
            'department'          => ['required', 'exists:departments,department_name'],
            'grievance_title'     => ['required', 'string', 'max:60'],
            'grievance_details'   => ['required', 'string'],
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
            'grievance_title.max'         => 'The title cannot exceed 60 characters.',
            'grievance_details.required'    => 'Please provide detailed information about your report.',
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
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'Invalid Department',
                'message' => 'The selected department does not exist.',
            ]);
            return;
        }

        if (! $department->is_active || !$department->is_available) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'Department Not Available',
                'message' => 'The selected department is either inactive or unavailable.',
            ]);
            return;
        }

        try {
            $processingDays = match ($this->priority_level) {
                'High'   => 20,
                'Normal' => 7,
                'Low'    => 3,
                default  => 7,
            };

            $grievance = Grievance::create([
                'user_id'          => auth()->id(),
                'department_id'    => $department->department_id,
                'grievance_type'   => $this->grievance_type,
                'grievance_category'=> $this->grievance_category,
                'priority_level'   => $this->priority_level,
                'grievance_title'  => $this->grievance_title,
                'grievance_details'=> $this->grievance_details,
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

            $this->reset([
                'is_anonymous',
                'grievance_type',
                'grievance_category',
                'priority_level',
                'department',
                'grievance_title',
                'grievance_details',
                'attachments',
            ]);

            auth()->user()->notify(new GeneralNotification(
                'Report Submitted',
                "Your report titled '{$grievance->grievance_title}' was submitted successfully.",
                'success',
                ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                ['type' => 'success'],
                true,
                [
                        [
                            'label' => 'View Report',
                            'url'   => route('citizen.grievance.index', $grievance->grievance_ticket_id),
                            'open_new_tab' => false,
                        ],
                ]
            ));

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
                    'New Report Assigned',
                    "A report titled '{$grievance->grievance_title}' has been assigned to you.",
                    'info',
                    ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                    ['type' => 'info'],
                    true,
                    [
                        [
                            'label'        => 'View Report',
                            'url'          => route('hr-liaison.grievance.view', $grievance->grievance_ticket_id),
                            'open_new_tab' => false,
                        ]
                    ]
                ));

            }

            $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();

            foreach ($admins as $admin) {
                $admin->notify(new GeneralNotification(
                    'New Report Submitted',
                    "A new report titled '{$grievance->grievance_title}' has been submitted.",
                    'warning',
                    ['grievance_ticket_id' => $grievance->grievance_ticket_id],
                    ['type' => 'info'],
                    true,
                    [
                        [
                            'label' => 'View Report',
                            'url'   => route('admin.forms.grievances.view', $grievance->grievance_ticket_id),
                            'open_new_tab' => false,
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

            $this->dispatch('resetGrievanceDetails');
            $this->dispatch('submit-finished');
            $this->dispatch('delayed-redirect');
            session()->put('grievance_submitted_once', true);

        } catch (\Exception $e) {

            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Submission Failed',
                'message' => 'Something went wrong while submitting your report.',
            ]);

            $this->showConfirmSubmitModal = false;
        }
    }

    public function render()
    {
        return view('livewire.user.citizen.grievance.create');
    }
}
