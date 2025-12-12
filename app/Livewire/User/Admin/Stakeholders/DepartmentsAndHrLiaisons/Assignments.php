<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Grievance;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app')]
#[Title("HR Liaison's Assignment")]
class Assignments extends Component
{
    use WithPagination;

    public int $departmentId;
    public User $hrLiaison;
    public ?string $filterStatus = null;
    public ?string $filterDate = null;

    public function mount($department, User $hrLiaison)
    {
        $this->departmentId = $department;
        $this->hrLiaison = $hrLiaison;
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function assignAll()
    {
        $grievanceIds = Grievance::whereNull('deleted_at')->pluck('grievance_id');
        $count = 0;
        $creator = auth()->user();

        foreach ($grievanceIds as $gid) {
            $assignmentForHR = Assignment::where('grievance_id', $gid)
                ->where('department_id', $this->departmentId)
                ->where('hr_liaison_id', $this->hrLiaison->id)
                ->first();

            if ($assignmentForHR) continue;

            $existingAssignment = Assignment::where('grievance_id', $gid)
                ->where('department_id', $this->departmentId)
                ->whereNull('hr_liaison_id')
                ->first();

            if ($existingAssignment) {
                $existingAssignment->update([
                    'hr_liaison_id' => $this->hrLiaison->id,
                    'assigned_at' => now(),
                ]);
            } else {
                Assignment::create([
                    'grievance_id' => $gid,
                    'department_id' => $this->departmentId,
                    'hr_liaison_id' => $this->hrLiaison->id,
                    'assigned_at' => now(),
                ]);
            }

            $count++;

            ActivityLog::create([
                'user_id'     => $creator->id,
                'role_id'     => $creator->roles->first()?->id,
                'module'      => 'Grievance Assignment',
                'action'      => 'Assign',
                'action_type' => 'assign',
                'model_type'  => Assignment::class,
                'model_id'    => $gid,
                'description' => "Assigned grievance ID {$gid} to HR Liaison {$this->hrLiaison->name} in department ID {$this->departmentId}.",
                'changes'     => ['hr_liaison_id' => $this->hrLiaison->id, 'department_id' => $this->departmentId],
                'status'      => 'success',
                'ip_address'  => request()->ip(),
                'device_info' => request()->header('device') ?? null,
                'user_agent'  => request()->userAgent(),
                'platform'    => php_uname('s'),
                'location'    => null,
                'timestamp'   => now(),
            ]);
        }

        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $this->departmentId))
            ->get();

        foreach ($hrLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'Grievances Assigned',
                "{$creator->name} assigned $count grievance(s) to {$this->hrLiaison->name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Grievances Assigned',
                "{$creator->name} assigned $count grievance(s) to {$this->hrLiaison->name} in department {$this->department->department_name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'Grievances Assigned Successfully',
            "You successfully assigned $count grievance(s) to {$this->hrLiaison->name}.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $this->resetPage();
        $this->dispatch('close-all-modals');
    }

    public function assignSingle(int $grievanceId)
    {
        $grievance = Grievance::find($grievanceId);
        if (!$grievance) return;

        $creator = auth()->user();

        $assignment = Assignment::where('grievance_id', $grievance->grievance_id)
            ->where('department_id', $this->departmentId)
            ->whereNull('hr_liaison_id')
            ->first();

        if ($assignment) {
            if ($assignment->hr_liaison_id !== $this->hrLiaison->id) {
                $assignment->update([
                    'hr_liaison_id' => $this->hrLiaison->id,
                    'assigned_at' => now(),
                ]);
            }
        } else {
            Assignment::create([
                'grievance_id' => $grievance->grievance_id,
                'department_id' => $this->departmentId,
                'hr_liaison_id' => $this->hrLiaison->id,
                'assigned_at' => now(),
            ]);
        }

        ActivityLog::create([
            'user_id'     => $creator->id,
            'role_id'     => $creator->roles->first()?->id,
            'module'      => 'Grievance Assignment',
            'action'      => 'Assign',
            'action_type' => 'assign',
            'model_type'  => Assignment::class,
            'model_id'    => $grievance->grievance_id,
            'description' => "Assigned grievance ID {$grievance->grievance_id} to HR Liaison {$this->hrLiaison->name} in department ID {$this->departmentId}.",
            'changes'     => ['hr_liaison_id' => $this->hrLiaison->id, 'department_id' => $this->departmentId],
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);

        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $this->departmentId))
            ->get();

        foreach ($hrLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'Grievance Assigned',
                "{$creator->name} assigned grievance {$grievance->grievance_ticket_id} to {$this->hrLiaison->name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Grievance Assigned',
                "{$creator->name} assigned grievance {$grievance->grievance_ticket_id} to {$this->hrLiaison->name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'Grievance Assigned Successfully',
            "You successfully assigned grievance {$grievance->grievance_ticket_id} to {$this->hrLiaison->name}.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $this->resetPage();
    }

    public function unassignSingle(int $grievanceId)
    {
        $assignment = Assignment::where('grievance_id', $grievanceId)
            ->where('department_id', $this->departmentId)
            ->where('hr_liaison_id', $this->hrLiaison->id)
            ->first();

        if (!$assignment) return;

        $assignment->update([
            'hr_liaison_id' => null,
            'assigned_at' => null,
        ]);

        $grievance = Grievance::withTrashed()->find($assignment->grievance_id);
        $creator = auth()->user();

        if ($grievance && $grievance->trashed()) {
            $grievance->forceDelete();
        }

        ActivityLog::create([
            'user_id'     => $creator->id,
            'role_id'     => $creator->roles->first()?->id,
            'module'      => 'Grievance Assignment',
            'action'      => 'Unassign',
            'action_type' => 'unassign',
            'model_type'  => Assignment::class,
            'model_id'    => $grievance?->grievance_id,
            'description' => "Unassigned grievance ID {$grievance?->grievance_id} from HR Liaison {$this->hrLiaison->name} in department ID {$this->departmentId}.",
            'changes'     => ['hr_liaison_id' => null, 'department_id' => $this->departmentId],
            'status'      => 'success',
            'ip_address'  => request()->ip(),
            'device_info' => request()->header('device') ?? null,
            'user_agent'  => request()->userAgent(),
            'platform'    => php_uname('s'),
            'location'    => null,
            'timestamp'   => now(),
        ]);

        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $this->departmentId))
            ->get();

        foreach ($hrLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'Grievance Unassigned',
                "{$creator->name} unassigned grievance {$grievance?->grievance_ticket_id} from {$this->hrLiaison->name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Grievance Unassigned',
                "{$creator->name} unassigned grievance {$grievance?->grievance_ticket_id} from {$this->hrLiaison->name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'Grievance Unassigned Successfully',
            "You successfully unassigned grievance {$grievance?->grievance_ticket_id} from {$this->hrLiaison->name}.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $this->resetPage();
    }

    public function unassignAll()
    {
        $grievanceIds = Assignment::where('department_id', $this->departmentId)
            ->where('hr_liaison_id', $this->hrLiaison->id)
            ->pluck('grievance_id')
            ->unique();

        $count = 0;
        $creator = auth()->user();

        foreach ($grievanceIds as $gid) {
            $updated = Assignment::where('grievance_id', $gid)
                ->where('department_id', $this->departmentId)
                ->where('hr_liaison_id', $this->hrLiaison->id)
                ->update([
                    'hr_liaison_id' => null,
                    'assigned_at' => null,
                ]);

            if ($updated) {
                $count++;

                $grievance = Grievance::withTrashed()->find($gid);
                if ($grievance && $grievance->trashed()) {
                    $grievance->forceDelete();
                }

                ActivityLog::create([
                    'user_id'     => $creator->id,
                    'role_id'     => $creator->roles->first()?->id,
                    'module'      => 'Grievance Assignment',
                    'action'      => 'Unassign',
                    'action_type' => 'unassign',
                    'model_type'  => Assignment::class,
                    'model_id'    => $gid,
                    'description' => "Unassigned grievance ID {$gid} from HR Liaison {$this->hrLiaison->name} in department ID {$this->departmentId}.",
                    'changes'     => ['hr_liaison_id' => null, 'department_id' => $this->departmentId],
                    'status'      => 'success',
                    'ip_address'  => request()->ip(),
                    'device_info' => request()->header('device') ?? null,
                    'user_agent'  => request()->userAgent(),
                    'platform'    => php_uname('s'),
                    'location'    => null,
                    'timestamp'   => now(),
                ]);
            }
        }

        $hrLiaisons = User::whereHas('roles', fn($q) => $q->where('name', 'hr_liaison'))
            ->whereHas('departments', fn($q) => $q->where('hr_liaison_departments.department_id', $this->departmentId))
            ->get();

        foreach ($hrLiaisons as $liaison) {
            $liaison->notify(new GeneralNotification(
                'Grievances Unassigned',
                "{$creator->name} unassigned $count grievance(s) from {$this->hrLiaison->name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $admins = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->where('id', '!=', $creator->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new GeneralNotification(
                'Grievances Unassigned',
                "{$creator->name} unassigned $count grievance(s) from {$this->hrLiaison->name}.",
                'info',
                [],
                ['type' => 'info'],
                true
            ));
        }

        $creator->notify(new GeneralNotification(
            'Grievances Unassigned Successfully',
            "You successfully unassigned $count grievance(s) from {$this->hrLiaison->name}.",
            'success',
            [],
            ['type' => 'success'],
            true
        ));

        $this->resetPage();
        $this->dispatch('close-all-modals');
    }

    public function render()
    {
        $query = Assignment::with(['grievance' => function ($q) {
                $q->whereNull('deleted_at');
                }, 'department'])
                ->where('department_id', $this->departmentId)
                ->where(function ($q) {
                    $q->where('hr_liaison_id', $this->hrLiaison->id)
                    ->orWhereNull('hr_liaison_id');
                })
                ->select('grievance_id', 'department_id', 'hr_liaison_id', 'assigned_at')
                ->distinct('grievance_id')
                ->whereHas('grievance', function ($q) {
                    $q->whereNull('deleted_at');
                });

        if ($this->filterStatus === 'Assigned') {
            $query->whereNotNull('hr_liaison_id');
        } elseif ($this->filterStatus === 'Unassigned') {
            $query->whereNull('hr_liaison_id');
        }

        if ($this->filterDate) {
            $query->whereDate('assigned_at', $this->filterDate);
        }

        $assignmentsPaginated = $query->paginate(10);

       $baseQuery = Assignment::where('department_id', $this->departmentId)
            ->whereHas('grievance', fn($q) => $q->whereNull('deleted_at'))
            ->distinct('grievance_id');

        $assignedCount = (clone $baseQuery)
            ->where('hr_liaison_id', $this->hrLiaison->id)
            ->distinct('grievance_id')
            ->count('grievance_id');

        $unassignedCount = (clone $baseQuery)
            ->whereNull('hr_liaison_id')
            ->distinct('grievance_id')
            ->count('grievance_id');

        $totalAssignments = $assignedCount + $unassignedCount;

        return view('livewire.user.admin.stakeholders.departments-and-hr-liaisons.assignments', [
            'assignments' => $assignmentsPaginated,
            'totalAssignments' => $totalAssignments,
            'assignedCount' => $assignedCount,
            'unassignedCount' => $unassignedCount,
            'hrLiaisonProfile' => [
                'name' => $this->hrLiaison->name,
                'email' => $this->hrLiaison->email,
                'is_online' => $this->hrLiaison->isOnline(),
            ],
        ]);
    }


}
