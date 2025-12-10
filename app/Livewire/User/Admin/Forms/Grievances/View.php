<?php

namespace App\Livewire\User\Admin\Forms\Grievances;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Department;
use App\Models\EditRequest;
use App\Models\Grievance;
use App\Models\HrLiaisonDepartment;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.app')]
#[Title('View Report')]
class View extends Component
{
    public Grievance $grievance;
    public $department;
    public $statusUpdate;
    public $priorityUpdate;
    public $category;
    public $departmentOptions;
    public $message;
    public $limit = 10;
    public $totalRemarksCount;
    protected $listeners = [
        'loadMore' => 'loadMore',
    ];
    public function mount(Grievance $grievance)
    {
        $user = auth()->user();
        $roleName = ucfirst($user->roles->first()?->name ?? 'Admin');

        $this->grievance = $grievance->load([
            'attachments',
            'assignments',
            'departments',
            'user.userInfo'
        ]);

        $userDepartmentIds = $user->departments->pluck('department_id');
        $grievanceDepartmentIds = $this->grievance->departments->pluck('department_id');
        $excludedDepartmentIds = $userDepartmentIds->merge($grievanceDepartmentIds)->unique();

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->whereNotIn('department_id', $excludedDepartmentIds)
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->pluck('department_name', 'department_name')
            ->toArray();

        $this->editRequests = EditRequest::where('grievance_id', $grievance->grievance_id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $this->totalRemarksCount = count($this->grievance->grievance_remarks ?? []);

        if ($this->grievance->grievance_status === 'pending') {
            $oldStatus = $this->grievance->grievance_status;

            $this->grievance->forceFill([
                'grievance_status' => 'acknowledged',
            ])->save();

            ActivityLog::create([
                'user_id'      => $user->id,
                'role_id'      => $user->roles->first()?->id,
                'module'       => 'Report Management',
                'action'       => "Acknowledged report #{$this->grievance->grievance_ticket_id}",
                'action_type'  => 'acknowledge',
                'model_type'   => 'App\\Models\\Grievance',
                'model_id'     => $this->grievance->grievance_id,
                'description'  => "{$roleName} ({$user->name}) acknowledged report #{$this->grievance->grievance_ticket_id}.",
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

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Management',
            'action'       => "Viewed grievance #{$this->grievance->grievance_id}",
            'action_type'  => 'view',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "{$roleName} viewed this report.",
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'location'     => geoip(request()->ip())?->city,
            'timestamp'    => now(),
        ]);
    }

    public function refreshGrievance()
    {
        try {
            $this->grievance->refresh();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->redirectRoute('admin.forms.grievances.index', navigate: true);
            return;
        }

        if (method_exists($this->grievance, 'trashed') && $this->grievance->trashed()) {
            $this->redirectRoute('admin.forms.grievances.index', navigate: true);
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

    public function reroute()
    {
        $this->validate([
            'department' => 'required|exists:departments,department_name',
            'category'   => 'required|string',
        ]);

        $user = auth()->user();

        $department = Department::where('department_name', $this->department)->firstOrFail();

        $hrLiaisons = HrLiaisonDepartment::where('department_id', $department->department_id)
                        ->pluck('hr_liaison_id')
                        ->toArray();

        if (empty($hrLiaisons)) {
            Notification::make()
                ->title('No HR Liaisons Found')
                ->body("This department has no HR Liaisons assigned.")
                ->danger()
                ->send();

            return;
        }

        $oldStatus      = $this->grievance->grievance_status;
        $oldCategory    = $this->grievance->grievance_category;
        $oldDepartments = $this->grievance->departments()->pluck('department_name')->toArray();

        $this->grievance->update([
            'department_id'      => $department->department_id,
            'grievance_status'   => 'pending',
            'grievance_category' => $this->category,
            'updated_at'         => now(),
        ]);

        $this->grievance->assignments()->delete();

        foreach ($hrLiaisons as $liaisonId) {
            Assignment::create([
                'grievance_id'  => $this->grievance->grievance_id,
                'department_id' => $department->department_id,
                'hr_liaison_id' => $liaisonId,
                'assigned_at'   => now(),
            ]);
        }

        $changes = [
            'grievance_status' => [
                'old' => ucfirst($oldStatus),
                'new' => 'Pending',
            ],
            'grievance_category' => [
                'old' => $oldCategory,
                'new' => $this->category,
            ],
            'departments' => [
                'old' => implode(', ', $oldDepartments),
                'new' => $department->department_name,
            ],
            'assigned_hr_liaisons' => [
                'new' => implode(', ', User::whereIn('id', $hrLiaisons)->pluck('name')->toArray()),
            ],
        ];

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Management',
            'action'       => "Rerouted report #{$this->grievance->grievance_ticket_id} to {$department->department_name}",
            'action_type'  => 'reroute',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "Assigned HR Liaisons: " . implode(', ', User::whereIn('id', $hrLiaisons)->pluck('name')->toArray()),
            'changes'      => $changes,
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);


        Notification::make()
            ->title('Report Rerouted')
            ->body("Report rerouted to {$department->department_name}. HR Liaisons assigned.")
            ->success()
            ->send();

        return $this->redirectRoute('admin.forms.grievances.index', navigate: true);
    }

    private function formatStatus($value)
    {
        return strtolower(str_replace(' ', '_', trim($value)));
    }

    public function updateStatus()
    {

        $this->validate([
            'statusUpdate' => 'required|string',
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
            'action'       => "Changed report #{$this->grievance->grievance_ticket_id} status from {$oldStatus} to {$formattedStatus}",
            'action_type'  => 'update_status',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "Administrator ({$user->email}) changed status of report #{$this->grievance->grievance_ticket_id} from {$oldStatus} to {$formattedStatus}.",
            'changes'      => $changes,
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        if ($oldStatus !== $formattedStatus) {
            Notification::make()
                ->title('Report Updated')
                ->body("Report status successfully changed from {$oldStatus} to {$formattedStatus}.")
                ->success()
                ->send();
        }

        $this->dispatch('close-status-modal');
        $this->dispatch('update-success-modal');

        $this->grievance->refresh();
    }

    public function updatePriority()
    {
        $this->validate([
            'priorityUpdate' => 'required|string',
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
            'priority_level' => $formattedPriority,
            'processing_days' => $priorityProcessingDays,
        ]);

        $changes = [
            'priority_level' => [
                'old' => ucfirst($oldPriority),
                'new' => ucfirst($formattedPriority),
            ],
            'processing_days' => [
                'old' => $oldProcessingDays,
                'new' => $priorityProcessingDays,
            ],
        ];

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Report Management',
            'action'       => "Changed report #{$this->grievance->grievance_id} priority from {$oldPriority} to {$formattedPriority} and processing days from {$oldProcessingDays} to {$priorityProcessingDays}",
            'action_type'  => 'update_priority',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "Administrator ({$user->email}) changed priority of report #{$this->grievance->grievance_id} from {$oldPriority} to {$formattedPriority}, updating processing days from {$oldProcessingDays} to {$priorityProcessingDays}.",
            'changes'      => $changes,
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        Notification::make()
            ->title('Priority Updated')
            ->body("Report priority successfully changed from {$oldPriority} to {$formattedPriority}. Processing days updated from {$oldProcessingDays} to {$priorityProcessingDays}.")
            ->success()
            ->send();

        $this->dispatch('close-priority-modal');
        $this->dispatch('update-success-modal');

        $this->grievance->refresh();
    }

    public function approveEditRequest($editRequestId)
    {
        $editRequest = EditRequest::findOrFail($editRequestId);
        $editRequest->update(['status' => 'approved']);

        $user = $editRequest->user;
        $grievance = $editRequest->grievance;

        $user->notify(new GeneralNotification(
            'Edit Request Approved',
            "Your request to edit report '{$grievance->grievance_title}' has been approved.",
            'success',
            [
                'grievance_ticket_id' => $grievance->grievance_ticket_id,
                'edit_request_id'     => $editRequest->id
            ],
            [],
            true,
            [
                [
                    'label' => 'View Report',
                    'url'   => route('citizen.grievance.view', $grievance->grievance_ticket_id),
                    'open_new_tab' => true,
                ]
            ]
        ));

        $this->editRequests = EditRequest::where('grievance_id', $grievance->grievance_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        Notification::make()
            ->title('Edit Request Approved')
            ->body("You approved the edit request for '{$grievance->grievance_title}'.")
            ->success()
            ->send();
    }

    public function denyEditRequest($editRequestId)
    {
        $editRequest = EditRequest::findOrFail($editRequestId);
        $editRequest->update(['status' => 'denied']);

        $user = $editRequest->user;
        $grievance = $editRequest->grievance;

        $user->notify(new GeneralNotification(
            'Edit Request Denied',
            "Your request to edit report '{$grievance->grievance_title}' has been denied.",
            'danger',
            [
                'grievance_ticket_id' => $grievance->grievance_ticket_id,
                'edit_request_id'     => $editRequest->id
            ],
            [],
            true,
            [
                [
                    'label' => 'View Report',
                    'url'   => route('citizen.grievance.view', $grievance->grievance_ticket_id),
                    'open_new_tab' => true,
                ]
            ]
        ));

        $this->editRequests = EditRequest::where('grievance_id', $grievance->grievance_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        Notification::make()
            ->title('Edit Request Denied')
            ->body("You denied the edit request for '{$grievance->grievance_title}'.")
            ->warning()
            ->send();
    }

    public function addRemark()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        $this->grievance->addRemark([
            'message' => $this->message,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'role' => auth()->user()->getRoleNames()->first(),
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'status' => $this->grievance->grievance_status,
            'type' => 'note',
        ]);

        $this->message = '';

        $this->grievance->refresh();

        $this->dispatch('new-log');

        Notification::make()
            ->title('Progress Log Added')
            ->body('Your note has been recorded.')
            ->success()
            ->send();
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
        return view('livewire.user.admin.forms.grievances.view');
    }
}
