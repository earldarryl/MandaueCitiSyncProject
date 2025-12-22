<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\Grievance;
use App\Models\GrievanceReroute;
use App\Models\EditRequest;
use App\Models\HrLiaisonDepartment;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\URL;
#[Layout('layouts.app')]
#[Title('View Report')]
class View extends Component
{
    public Grievance $grievance;
    public $department;
    public $statusUpdate;
    public $priorityUpdate;
    public $grievance_type;
    public $grievance_category;
    public array $departmentOptions = [];
    public array $categoriesMap = [];
    public $message;
    public $limit = 10;
    public $totalRemarksCount;
    protected $listeners = [
        'loadMore' => 'loadMore',
    ];

    public function clearForm()
    {
        $this->reset([
            'department',
            'statusUpdate',
            'priorityUpdate',
            'grievance_category',
            'grievance_type',
        ]);

        $this->resetErrorBag();
        $this->dispatch('reset-reroute-form');
    }

    function displayRoleName(string $role): string
    {
        return match ($role) {
            'hr_liaison' => 'HR Liaison',
            'admin'      => 'Administrator',
            'citizen'    => 'Citizen',
            default      => ucwords(str_replace('_', ' ', $role)),
        };
    }

    private function formatStatus($value)
    {
        return strtolower(str_replace(' ', '_', trim($value)));
    }

    private function displayText($value)
    {
        return ucwords(str_replace('_', ' ', $value));
    }

    public function handleDelayedRedirect()
    {
        $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
    }

    public function handleDelayedRedirectReroute()
    {
        $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
    }

    public function mount(Grievance $grievance)
    {
        $user = auth()->user();

        $departments = $user->departments->pluck('department_name')->join(', ');

        $this->grievance = $grievance->load(['attachments', 'assignments', 'departments', 'user.userInfo']);

        $isAssigned = $this->grievance->assignments->contains('hr_liaison_id', $user->id);
        if (!$isAssigned) {
            abort(403, 'You are not authorized to view this report.');
        }

        $this->editRequests = EditRequest::where('grievance_id', $grievance->grievance_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        $excludedDepartmentIds = $user->departments->pluck('department_id');

        $departments = Department::
             whereNotIn('department_id', $excludedDepartmentIds)
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->get();

       $this->departmentOptions = $departments->pluck('department_name')->toArray();

        $this->categoriesMap = $departments
            ->mapWithKeys(fn ($dept) => [
                $dept->department_name => $dept->grievance_categories ?? [],
            ])
            ->toArray();

        $this->totalRemarksCount = count($this->grievance->grievance_remarks ?? []);

        if ($this->grievance->grievance_status === 'pending') {
            $oldStatus = $this->grievance->grievance_status;

            $this->grievance->forceFill([
                'grievance_status' => 'acknowledged',
            ])->save();

            $this->grievance->addRemark([
                'message'   => "Report acknowledged by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first())  .")",
                'user_id'   => $user->id,
                'user_name' => $user->name,
                'role'      => $this->displayRoleName($user->getRoleNames()->first()),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'status'    => 'acknowledged',
                'type'      => 'acknowledge',
            ]);

            ActivityLog::create([
                'user_id'      => $user->id,
                'role_id'      => $user->roles->first()?->id,
                'module'       => 'Report Management',
                'action'       => "Acknowledged report #{$this->grievance->grievance_ticket_id}",
                'action_type'  => 'acknowledge',
                'model_type'   => 'App\\Models\\Grievance',
                'model_id'     => $this->grievance->grievance_id,
                'description'  => "Administrator ({$user->name}) acknowledged report #{$this->grievance->grievance_ticket_id}.",
                'changes'      => [
                    'grievance_status' => [
                        'old' => ucfirst($oldStatus),
                        'new' => 'Acknowledged',
                    ],
                ],
                'status'       => 'success',
                'ip_address'   => request()->ip(),
                'device_info'  => request()->header('User-Agent'),
                'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                'platform'     => php_uname('s'),
                'location'     => geoip(request()->ip())?->city,
                'timestamp'    => now(),
            ]);

        }
    }

    public function refreshGrievance()
    {
        try {
            $this->grievance->refresh();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
            return;
        }

        if (method_exists($this->grievance, 'trashed') && $this->grievance->trashed()) {
            $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
            return;
        }

        $this->dispatch('$refresh');
        $this->dispatch('refreshChat', grievanceId: $this->grievance->grievance_id);
    }

    public function getEditRequestsProperty()
    {
        return EditRequest::where('grievance_id', $this->grievance->grievance_id)
                        ->where('status', 'pending')
                        ->get();
    }

    public function reroute(): void
    {
        $this->validate([
            'department'         => 'required|exists:departments,department_name',
            'grievance_type'     => 'required|string',
            'grievance_category' => 'required|string',
        ], [
            'department.required'         => 'Department is required.',
            'department.exists'           => 'Selected department is invalid.',
            'grievance_type.required'     => 'Grievance type is required.',
            'grievance_category.required' => 'Category is required.',
        ]);

        $user = auth()->user();
        $department = Department::where('department_name', $this->department)->firstOrFail();

        $hrLiaisons = HrLiaisonDepartment::where('department_id', $department->department_id)
            ->pluck('hr_liaison_id')
            ->toArray();

        if ($department->requires_hr_liaison && empty($hrLiaisons)) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'title'   => 'No HR Liaisons Found',
                'message' => "Department {$department->department_name} requires at least one HR Liaison.",
            ]);
            return;
        }

        $grievance = $this->grievance;
        $ticketId = $grievance->grievance_ticket_id;

        $oldStatus       = $grievance->grievance_status;
        $oldType         = $grievance->grievance_type;
        $oldCategory     = $grievance->grievance_category;
        $oldDepartments  = $grievance->departments()->pluck('department_name')->toArray();
        $oldDepartmentId = $grievance->department_id;

        $grievance->update([
            'department_id'      => $department->department_id,
            'grievance_status'   => 'pending',
            'grievance_type'     => $this->grievance_type,
            'grievance_category' => $this->grievance_category,
            'updated_at'         => now(),
        ]);

        GrievanceReroute::create([
            'grievance_id'       => $grievance->grievance_id,
            'from_department_id' => $oldDepartmentId,
            'to_department_id'   => $department->department_id,
            'performed_by'       => $user->id,
            'from_type'          => $oldType,
            'to_type'            => $this->grievance_type,
            'from_category'      => $oldCategory,
            'to_category'        => $this->grievance_category,
        ]);

        $grievance->assignments()->delete();

        if ($department->requires_hr_liaison) {
            foreach ($hrLiaisons as $liaisonId) {
                Assignment::create([
                    'grievance_id'  => $grievance->grievance_id,
                    'department_id' => $department->department_id,
                    'hr_liaison_id' => $liaisonId,
                    'assigned_at'   => now(),
                ]);
            }
        } else {
            Assignment::create([
                'grievance_id'  => $grievance->grievance_id,
                'department_id' => $department->department_id,
                'hr_liaison_id' => null,
                'assigned_at'   => now(),
            ]);
        }

        $grievance->addRemark([
            'message'   => "Report rerouted to '{$department->department_name}', type '{$this->grievance_type}', category '{$this->grievance_category}' by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) . ").",
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $this->displayRoleName($user->getRoleNames()->first()),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'status'    => 'pending',
            'type'      => 'reroute',
        ]);

        if ($citizen = $grievance->user()->first()) {
            $citizen->notify(new GeneralNotification(
                'Your Report Was Rerouted',
                "Your report '{$grievance->grievance_title}' has been rerouted to {$department->department_name}.",
                'info',
                ['grievance_ticket_id' => $ticketId],
                ['type' => 'info'],
                true,
                [[
                    'label' => 'View Updated Report',
                    'url'   => route('citizen.grievance.view', $ticketId),
                    'open_new_tab' => true,
                ]]
            ));
        }

        if ($department->requires_hr_liaison) {
            $hrUsers = User::whereIn('id', $hrLiaisons)->get();
            foreach ($hrUsers as $hr) {
                $hr->notify(new GeneralNotification(
                    'New Rerouted Report Assigned',
                    "A report titled '{$grievance->grievance_title}' has been rerouted to your department.",
                    'info',
                    ['grievance_ticket_id' => $ticketId],
                    ['type' => 'info'],
                    true,
                    [[
                        'label' => 'View Report',
                        'url'   => route('hr-liaison.grievance.view', $ticketId),
                        'open_new_tab' => true,
                    ]]
                ));
            }
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Report Rerouted',
                "The report '{$grievance->grievance_title}' was rerouted to {$department->department_name}.",
                'warning',
                ['grievance_ticket_id' => $ticketId],
                ['type' => 'warning'],
                true,
                [[
                    'label' => 'View Report',
                    'url'   => route('admin.forms.grievances.view', $ticketId),
                    'open_new_tab' => true,
                ]]
            ));
        }

        $user->notify(new GeneralNotification(
            'Reroute Successful',
            "You have rerouted the report '{$grievance->grievance_title}' to {$department->department_name}.",
            'success',
            ['grievance_ticket_id' => $ticketId],
            ['type' => 'success'],
            true,
            []
        ));

        $changes = [
            'grievance_status'    => ['old' => ucfirst($oldStatus), 'new' => 'Pending'],
            'grievance_type'      => ['old' => $oldType, 'new' => $this->grievance_type],
            'grievance_category'  => ['old' => $oldCategory, 'new' => $this->grievance_category],
            'departments'         => ['old' => implode(', ', $oldDepartments), 'new' => $department->department_name],
            'assigned_hr_liaisons'=> [
                'new' => $department->requires_hr_liaison
                    ? implode(', ', User::whereIn('id', $hrLiaisons)->pluck('name')->toArray())
                    : 'Not required for this department'
            ],
        ];

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Management',
            'action'       => "Rerouted report #{$ticketId} to {$department->department_name}",
            'action_type'  => 'reroute',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $grievance->grievance_id,
            'description'  => $department->requires_hr_liaison
                ? "Assigned HR Liaisons: " . implode(', ', User::whereIn('id', $hrLiaisons)->pluck('name')->toArray())
                : "No HR Liaison required for this department",
            'changes'      => $changes,
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        $this->dispatch('close-all-modals');
        $this->dispatch('update-success-modal');
        $this->dispatch('delayed-redirect-reroute');
    }

    public function updateStatus()
    {
        $this->validate([
            'statusUpdate' => 'required|string',
        ], [
            'statusUpdate.required' => 'Status is required.',
            'statusUpdate.string'   => 'Status must be a valid text value.',
        ]);

        $formattedStatus = $this->formatStatus($this->statusUpdate);
        $user = auth()->user();
        $oldStatus = $this->grievance->grievance_status;

        $this->grievance->update([
            'grievance_status' => $formattedStatus,
        ]);

        $changes = [
            'grievance_status' => [
                'old' => ucfirst($oldStatus),
                'new' => ucfirst($formattedStatus),
            ],
        ];

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Management',
            'action'       => "Changed report #{$this->grievance->grievance_ticket_id} status from {$this->displayText($oldStatus)} to {$this->displayText($formattedStatus)}",
            'action_type'  => 'update_status',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "HR Liaison ({$user->email}) changed status of report #{$this->grievance->grievance_ticket_id} from {$this->displayText($oldStatus)} to {$this->displayText($formattedStatus)}.",
            'changes'      => $changes,
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        $this->grievance->addRemark([
            'message'   => "Status changed from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}' by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) .").",
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $this->displayRoleName($user->getRoleNames()->first()),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'status'    => $formattedStatus,
            'type'      => 'status_update',
        ]);

        $grievance = $this->grievance;
        $ticketId  = $grievance->grievance_ticket_id;

        $citizen = $grievance->user()->first();
        if ($citizen) {
            $citizen->notify(new GeneralNotification(
                'Report Status Updated',
                "The status of your report '{$grievance->grievance_title}' has changed from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}'.",
                'info',
                ['grievance_ticket_id' => $ticketId],
                ['type' => 'info'],
                true,
                [
                    [
                        'label'        => 'View Report',
                        'url'          => route('citizen.grievance.view', $ticketId),
                        'open_new_tab' => false,
                    ],
                ]
            ));


            $feedbackUrl = URL::temporarySignedRoute(
                'citizen.feedback-form',
                now()->addDays(7),
                ['ticket' => $ticketId]
            );


            if($formattedStatus === 'resolved'){
            $citizen->notify(new GeneralNotification(
                    'Your Report Has Been Resolved',
                    "Your report '{$grievance->grievance_title}' has been successfully resolved. Please take a moment to submit your feedback.",
                    'success',
                    ['grievance_ticket_id' => $ticketId],
                    ['type' => 'success'],
                    true,
                    [
                        [
                            'label'        => 'Submit Feedback',
                            'url'          => $feedbackUrl,
                            'open_new_tab' => false,
                        ],
                    ]
                ));
            }
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Report Status Updated',
                "The report '{$grievance->grievance_title}' status changed from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}'.",
                'warning',
                ['grievance_ticket_id' => $ticketId],
                ['type' => 'warning'],
                true,
                [
                    [
                        'label'        => 'View Report',
                        'url'          => route('admin.forms.grievances.view', $ticketId),
                        'open_new_tab' => false,
                    ],
                ]
            ));
        }

        $user->notify(new GeneralNotification(
            'Status Update Successful',
            "You changed the status of '{$grievance->grievance_title}' from '{$this->displayText($oldStatus)}' to '{$this->displayText($formattedStatus)}'.",
            'success',
            ['grievance_ticket_id' => $ticketId],
            ['type' => 'success'],
            true,
            [
                [
                    'label'        => 'View Report',
                    'url'          => route('hr-liaison.grievance.view', $ticketId),
                    'open_new_tab' => false,
                ],
            ]
        ));

        $this->dispatch('close-all-modals');
        $this->dispatch('update-success-modal');
        $this->grievance->refresh();
    }

    public function updatePriority()
    {
        $this->validate([
            'priorityUpdate' => 'required|string',
        ], [
            'priorityUpdate.required' => 'Priority is required.',
            'priorityUpdate.string'   => 'Priority must be a valid text value.',
        ]);

        $formattedPriority = $this->priorityUpdate;
        $user = auth()->user();
        $oldPriority = $this->grievance->priority_level;
        $oldProcessingDays = $this->grievance->processing_days;

        $priorityProcessingDays = match ($formattedPriority) {
            'Low'      => 3,
            'Normal'   => 7,
            'High'     => 20,
            'Critical' => 7,
            default    => 7,
        };

        $this->grievance->update([
            'priority_level'  => $formattedPriority,
            'processing_days' => $priorityProcessingDays,
        ]);

        $dueDate = $this->grievance->created_at->addDays($priorityProcessingDays);
        if (now()->greaterThan($dueDate)) {
            $this->grievance->update([
                'grievance_status' => 'overdue',
            ]);
        }

        $changes = [
            'priority_level' => [
                'old' => ucfirst($oldPriority),
                'new' => ucfirst($formattedPriority),
            ],
            'processing_days' => [
                'old' => $oldProcessingDays,
                'new' => $priorityProcessingDays,
            ],
            'grievance_status' => [
                'old' => $this->grievance->grievance_status,
                'new' => $this->grievance->grievance_status,
            ],
        ];

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Management',
            'action'       => "Changed report #{$this->grievance->grievance_ticket_id} priority from {$this->displayText($oldPriority)} to {$this->displayText($formattedPriority)} and processing days from {$oldProcessingDays} to {$priorityProcessingDays}",
            'action_type'  => 'update_priority',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "HR Liaison ({$user->email}) changed priority of report #{$this->grievance->grievance_ticket_id} from {$this->displayText($oldPriority)} to {$this->displayText($formattedPriority)}, updating processing days from {$oldProcessingDays} to {$priorityProcessingDays}.",
            'changes'      => $changes,
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        $this->grievance->addRemark([
            'message'   => "Priority changed from '{$oldPriority}' to '{$formattedPriority}' by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) ."). Processing days updated from {$oldProcessingDays} to {$priorityProcessingDays}.",
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $this->displayRoleName($user->getRoleNames()->first()),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'status'    => $this->grievance->grievance_status,
            'type'      => 'priority_update',
        ]);

        $grievance = $this->grievance;
        $ticketId  = $grievance->grievance_ticket_id;

        $citizen = $grievance->user()->first();
        if ($citizen) {
            $citizen->notify(new GeneralNotification(
                'Report Priority Updated',
                "The priority of your report '{$grievance->grievance_title}' has changed from '{$this->displayText($oldPriority)}' to '{$this->displayText($formattedPriority)}'. Processing days updated from {$oldProcessingDays} to {$priorityProcessingDays}.",
                'info',
                ['grievance_ticket_id' => $ticketId],
                ['type' => 'info'],
                true,
                [
                    [
                        'label'        => 'View Report',
                        'url'          => route('citizen.grievance.view', $ticketId),
                        'open_new_tab' => false,
                    ],
                ]
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Report Priority Updated',
                "The priority of report '{$grievance->grievance_title}' has changed from '{$this->displayText($oldPriority)}' to '{$this->displayText($formattedPriority)}'.",
                'warning',
                ['grievance_ticket_id' => $ticketId],
                ['type' => 'warning'],
                true,
                [
                    [
                        'label'        => 'View Report',
                        'url'          => route('admin.forms.grievances.view', $ticketId),
                        'open_new_tab' => false,
                    ],
                ]
            ));
        }

        $user->notify(new GeneralNotification(
            'Priority Update Successful',
            "You changed the priority of '{$grievance->grievance_title}' from '{$this->displayText($oldPriority)}' to '{$this->displayText($formattedPriority)}'.",
            'success',
            ['grievance_ticket_id' => $ticketId],
            ['type' => 'success'],
            true,
            [
                [
                    'label'        => 'View Report',
                    'url'          => route('hr-liaison.grievance.view', $ticketId),
                    'open_new_tab' => false,
                ],
            ]
        ));

        $this->dispatch('close-all-modals');
        $this->dispatch('update-success-modal');

        $this->grievance->refresh();
    }

    public function approveEditRequest($editRequestId)
    {
        $editRequest = EditRequest::findOrFail($editRequestId);
        $editRequest->update(['status' => 'approved']);

        $grievance = $editRequest->grievance;
        $ticketId = $grievance->grievance_ticket_id;
        $user = auth()->user();

        $grievance->addRemark([
            'message'   => "Edit request #{$editRequest->id} approved by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) .").",
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $this->displayRoleName($user->getRoleNames()->first()),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'status'    => $grievance->grievance_status,
            'type'      => 'edit_request',
        ]);

        $citizen = $editRequest->user()->first();
        if ($citizen) {
            $citizen->notify(new GeneralNotification(
                'Edit Request Approved',
                "Your request to edit report '{$grievance->grievance_title}' has been approved.",
                'success',
                [
                    'grievance_ticket_id' => $ticketId,
                    'edit_request_id'     => $editRequest->id
                ],
                [],
                true,
                [
                    [
                        'label' => 'View Report',
                        'url'   => route('citizen.grievance.view', $ticketId),
                        'open_new_tab' => false,
                    ]
                ]
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Edit Request Approved',
                "The edit request for report '{$grievance->grievance_title}' has been approved.",
                'info',
                [
                    'grievance_ticket_id' => $ticketId,
                    'edit_request_id'     => $editRequest->id
                ],
                [],
                true,
                [
                    [
                        'label' => 'View Report',
                        'url'   => route('admin.forms.grievances.view', $ticketId),
                        'open_new_tab' => false,
                    ]
                ]
            ));
        }

        $user->notify(new GeneralNotification(
            'Edit Request Approved',
            "You approved the edit request for '{$grievance->grievance_title}'.",
            'success',
            [
                'grievance_ticket_id' => $ticketId,
                'edit_request_id'     => $editRequest->id
            ],
            [],
            true,
            [
                [
                    'label' => 'View Report',
                    'url'   => route('hr-liaison.grievance.view', $ticketId),
                    'open_new_tab' => false,
                ]
            ]
        ));

        $this->editRequests = EditRequest::where('grievance_id', $grievance->grievance_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Edit Request Approved',
            'message' => "You approved the edit request for '{$grievance->grievance_title}'.",
        ]);
    }

    public function denyEditRequest($editRequestId)
    {
        $editRequest = EditRequest::findOrFail($editRequestId);
        $editRequest->update(['status' => 'denied']);

        $grievance = $editRequest->grievance;
        $ticketId = $grievance->grievance_ticket_id;
        $user = auth()->user();

        $grievance->addRemark([
            'message'   => "Edit request #{$editRequest->id} denied by {$user->name} (" . $this->displayRoleName($user->getRoleNames()->first()) .").",
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $this->displayRoleName($user->getRoleNames()->first()),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'status'    => $grievance->grievance_status,
            'type'      => 'edit_request',
        ]);

        $citizen = $editRequest->user()->first();
        if ($citizen) {
            $citizen->notify(new GeneralNotification(
                'Edit Request Denied',
                "Your request to edit report '{$grievance->grievance_title}' has been denied.",
                'danger',
                [
                    'grievance_ticket_id' => $ticketId,
                    'edit_request_id'     => $editRequest->id
                ],
                [],
                true,
                [
                    [
                        'label' => 'View Report',
                        'url'   => route('citizen.grievance.view', $ticketId),
                        'open_new_tab' => false,
                    ]
                ]
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Edit Request Denied',
                "The edit request for report '{$grievance->grievance_title}' has been denied.",
                'info',
                [
                    'grievance_ticket_id' => $ticketId,
                    'edit_request_id'     => $editRequest->id
                ],
                [],
                true,
                [
                    [
                        'label' => 'View Report',
                        'url'   => route('admin.forms.grievances.view', $ticketId),
                        'open_new_tab' => false,
                    ]
                ]
            ));
        }

        $user->notify(new GeneralNotification(
            'Edit Request Denied',
            "You denied the edit request for '{$grievance->grievance_title}'.",
            'danger',
            [
                'grievance_ticket_id' => $ticketId,
                'edit_request_id'     => $editRequest->id
            ],
            [],
            true,
            [
                [
                    'label' => 'View Report',
                    'url'   => route('hr-liaison.grievance.view', $ticketId),
                    'open_new_tab' => false,
                ]
            ]
        ));

        $this->editRequests = EditRequest::where('grievance_id', $grievance->grievance_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        $this->dispatch('notify', [
            'type' => 'info',
            'title' => 'Edit Request Denied',
            'message' => "You denied the edit request for '{$grievance->grievance_title}'.",
        ]);
    }

    public function addRemark()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        $grievance = $this->grievance;
        $ticketId = $grievance->grievance_ticket_id;
        $user = auth()->user();

        $this->grievance->addRemark([
            'message'   => $this->message,
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'role'      => $this->displayRoleName($user->getRoleNames()->first()),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'status'    => $grievance->grievance_status,
            'type'      => 'note',
        ]);

        $citizen = $grievance->user()->first();
        if ($citizen) {
            $citizen->notify(new GeneralNotification(
                'Progress Log Added',
                "A new note has been added to your report '{$grievance->grievance_title}'.",
                'info',
                ['grievance_ticket_id' => $ticketId],
                [],
                true,
                [
                    [
                        'label' => 'View Report',
                        'url'   => route('citizen.grievance.view', $ticketId),
                        'open_new_tab' => false,
                    ]
                ]
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Progress Log Added',
                "A new note has been added to report '{$grievance->grievance_title}'.",
                'info',
                ['grievance_ticket_id' => $ticketId],
                [],
                true,
                [
                    [
                        'label' => 'View Report',
                        'url'   => route('admin.forms.grievances.view', $ticketId),
                        'open_new_tab' => false,
                    ]
                ]
            ));
        }

        $user->notify(new GeneralNotification(
            'Progress Log Added',
            "You added a note to '{$grievance->grievance_title}'.",
            'success',
            ['grievance_ticket_id' => $ticketId],
            [],
            true,
            [
                [
                    'label' => 'View Report',
                    'url'   => route('hr-liaison.grievance.view', $ticketId),
                    'open_new_tab' => false,
                ]
            ]
        ));

        $this->message = '';
        $this->grievance->refresh();
        $this->dispatch('new-log');
    }


    public function loadMore()
    {
        if ($this->limit < $this->totalRemarksCount) {
            $this->limit += 10;
        }

        $this->dispatch('remarks-updated', canLoadMore: $this->canLoadMore);
    }

    public function getRemarksProperty()
    {
        $remarks = $this->grievance->grievance_remarks ?? [];

        return array_slice($remarks, -$this->limit);
    }

    public function getCanLoadMoreProperty()
    {
        return $this->limit < $this->totalRemarksCount;
    }

    public function readableSize($bytes)
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1024 * 1024 * 1024) return round($bytes / (1024 * 1024), 1) . ' MB';
        return round($bytes / (1024 * 1024 * 1024), 1) . ' GB';
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.view');
    }
}
