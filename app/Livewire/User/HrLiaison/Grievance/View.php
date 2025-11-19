<?php

namespace App\Livewire\User\HrLiaison\Grievance;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Grievance;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
#[Title('View Grievance')]
class View extends Component
{
    public Grievance $grievance;
    public $department;
    public $statusUpdate;
    public $priorityUpdate;
    public $category;
    public $departmentOptions;

    public function mount(Grievance $grievance)
    {
        $user = auth()->user();
        $roleName = ucfirst($user->roles->first()?->name ?? 'User');

        $this->grievance = $grievance->load(['attachments', 'assignments', 'departments', 'user.userInfo']);

        $isAssigned = $this->grievance->assignments->contains('hr_liaison_id', $user->id);
        if (!$isAssigned) {
            abort(403, 'You are not authorized to view this grievance.');
        }

        $excludedDepartmentIds = $user->departments->pluck('department_id');

        $this->departmentOptions = Department::whereHas('hrLiaisons')
            ->whereNotIn('department_id', $excludedDepartmentIds)
            ->pluck('department_name', 'department_name')
            ->toArray();

        if ($this->grievance->grievance_status === 'pending') {
            $this->grievance->forceFill([
                'grievance_status' => 'acknowledged',
            ])->save();

            ActivityLog::create([
                'user_id'      => $user->id,
                'role_id'      => $user->roles->first()?->id,
                'module'       => 'Grievance Management',
                'action'       => "Acknowledged grievance #{$this->grievance->grievance_id}",
                'action_type'  => 'acknowledge',
                'model_type'   => 'App\\Models\\Grievance',
                'model_id'     => $this->grievance->grievance_id,
                'description'  => "{$roleName} ({$user->email}) acknowledged grievance #{$this->grievance->grievance_id}.",
                'changes'      => [],
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

    public function reroute()
    {
        $this->validate([
            'department' => 'required|exists:departments,department_name',
            'category'           => 'required|string',
        ]);

        $user = auth()->user();

        $department = Department::where('department_name', $this->department)->firstOrFail();

        $this->grievance->departments()->sync([$department->department_id]);

        $this->grievance->update([
            'grievance_status'   => 'pending',
            'grievance_category' => $this->category,
            'updated_at'         => now(),
        ]);

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Grievance Management',
            'action'       => "Rerouted grievance #{$this->grievance->grievance_id} to {$department->department_name} with category '{$this->category}'",
            'action_type'  => 'reroute',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "HR Liaison ({$user->email}) rerouted grievance #{$this->grievance->grievance_id} to {$department->department_name} and updated category to '{$this->category}'.",
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        Notification::make()
            ->title('Grievance Rerouted')
            ->body("Grievance successfully rerouted to {$department->department_name} with category '{$this->category}'. Status set to pending.")
            ->success()
            ->send();

        $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
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

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Grievance Management',
            'action'       => "Changed grievance #{$this->grievance->grievance_id} status from {$oldStatus} to {$formattedStatus}",
            'action_type'  => 'update_status',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "HR Liaison ({$user->email}) changed status of grievance #{$this->grievance->grievance_id} from {$oldStatus} to {$formattedStatus}.",
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        if ($oldStatus !== $formattedStatus) {
            Notification::make()
                ->title('Grievance Updated')
                ->body("Grievance status successfully changed from {$oldStatus} to {$formattedStatus}.")
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

        $priorityProcessingDays = match (strtolower($formattedPriority)) {
            'low'      => 7,
            'normal'   => 5,
            'high'     => 3,
            'critical' => 1,
            default    => 7,
        };

        $this->grievance->update([
            'priority_level' => $formattedPriority,
            'processing_days' => $priorityProcessingDays,
        ]);

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Grievance Management',
            'action'       => "Changed grievance #{$this->grievance->grievance_id} priority from {$oldPriority} to {$formattedPriority} and processing days from {$oldProcessingDays} to {$priorityProcessingDays}",
            'action_type'  => 'update_priority',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "HR Liaison ({$user->email}) changed priority of grievance #{$this->grievance->grievance_id} from {$oldPriority} to {$formattedPriority}, updating processing days from {$oldProcessingDays} to {$priorityProcessingDays}.",
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        Notification::make()
            ->title('Priority Updated')
            ->body("Grievance priority successfully changed from {$oldPriority} to {$formattedPriority}. Processing days updated from {$oldProcessingDays} to {$priorityProcessingDays}.")
            ->success()
            ->send();

        $this->dispatch('close-priority-modal');
        $this->dispatch('update-success-modal');

        $this->grievance->refresh();
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.view');
    }
}
