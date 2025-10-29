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
    public $selectedDepartment;
    public $statusUpdate;
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
                'status'       => 'success',
                'ip_address'   => request()->ip(),
                'device_info'  => request()->header('User-Agent'),
                'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
                'platform'     => php_uname('s'),
                'timestamp'    => now(),
            ]);
        }
    }

    public function reroute()
    {
        $this->validate([
            'selectedDepartment' => 'required|exists:departments,department_name',
        ]);

        $user = auth()->user();
        $department = Department::find($this->selectedDepartment);

        $this->grievance->departments()->sync([$department->id]);

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Grievance Management',
            'action'       => "Rerouted grievance #{$this->grievance->grievance_id} to {$department->name}",
            'action_type'  => 'reroute',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "HR Liaison ({$user->email}) rerouted grievance #{$this->grievance->grievance_id} to {$department->name}.",
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        Notification::make()
            ->title('Grievance Rerouted')
            ->body("Grievance successfully rerouted to {$department->name}.")
            ->success()
            ->send();

        $this->redirectRoute('hr-liaison.grievance.index', navigate: true);
    }

    public function updateStatus()
    {
        $this->validate([
            'statusUpdate' => 'required|string|in:pending,acknowledged,in_progress,escalated,resolved,rejected,closed',
        ]);

        $user = auth()->user();
        $oldStatus = $this->grievance->grievance_status;

        $this->grievance->update([
            'grievance_status' => $this->statusUpdate,
        ]);

        ActivityLog::create([
            'user_id'      => $user->id,
            'role_id'      => $user->roles->first()?->id,
            'module'       => 'Grievance Management',
            'action'       => "Changed grievance #{$this->grievance->grievance_id} status from {$oldStatus} to {$this->statusUpdate}",
            'action_type'  => 'update_status',
            'model_type'   => 'App\\Models\\Grievance',
            'model_id'     => $this->grievance->grievance_id,
            'description'  => "HR Liaison ({$user->email}) changed status of grievance #{$this->grievance->grievance_id} from {$oldStatus} to {$this->statusUpdate}.",
            'status'       => 'success',
            'ip_address'   => request()->ip(),
            'device_info'  => request()->header('User-Agent'),
            'user_agent'   => substr(request()->header('User-Agent'), 0, 255),
            'platform'     => php_uname('s'),
            'timestamp'    => now(),
        ]);

        if ($oldStatus !== $this->statusUpdate) {
            Notification::make()
                ->title('Grievance Updated')
                ->body("Grievance status successfully changed from {$oldStatus} to {$this->statusUpdate}.")
                ->success()
                ->send();
        }

        $this->dispatch('close-status-modal');
        $this->dispatch('update-success-modal');

        $this->grievance->refresh();

    }

    public function render()
    {
        return view('livewire.user.hr-liaison.grievance.view');
    }
}
