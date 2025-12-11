<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use App\Models\Assignment;
use App\Models\Grievance;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
#[Title('HR Liaisons List View')]
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

        foreach ($grievanceIds as $gid) {
            $assignmentForHR = Assignment::where('grievance_id', $gid)
                ->where('department_id', $this->departmentId)
                ->where('hr_liaison_id', $this->hrLiaison->id)
                ->first();

            if ($assignmentForHR) {
                continue;
            }

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
        }

        Notification::make()
            ->title('Assignments Updated')
            ->body("$count grievance(s) assigned to {$this->hrLiaison->name}.")
            ->success()
            ->send();

        $this->resetPage();
    }


    public function assignSingle(int $grievanceId)
    {
        $grievance = Grievance::find($grievanceId);

        if (!$grievance) {
            return;
        }

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

        Notification::make()
            ->title('Assignment Updated')
            ->body("Grievance {$grievance->grievance_ticket_id} assigned to {$this->hrLiaison->name}.")
            ->success()
            ->send();

        $this->resetPage();
    }

    public function unassignSingle(int $grievanceId)
    {
        $assignment = Assignment::where('grievance_id', $grievanceId)
            ->where('department_id', $this->departmentId)
            ->where('hr_liaison_id', $this->hrLiaison->id)
            ->first();

        if ($assignment) {
            $assignment->update([
                'hr_liaison_id' => null,
                'assigned_at' => null,
            ]);

            // Load the grievance including soft-deleted
            $grievance = Grievance::withTrashed()->find($assignment->grievance_id);

            if ($grievance && $grievance->trashed()) {
                $grievance->forceDelete();
            }

            Notification::make()
                ->title('Assignment Updated')
                ->body("Grievance {$grievance?->grievance_ticket_id} unassigned from {$this->hrLiaison->name}.")
                ->success()
                ->send();

            $this->resetPage();
        }
    }


    public function unassignAll()
    {
        $grievanceIds = Assignment::where('department_id', $this->departmentId)
            ->where('hr_liaison_id', $this->hrLiaison->id)
            ->pluck('grievance_id')
            ->unique();

        $count = 0;

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

                // Load grievance including soft-deleted
                $grievance = Grievance::withTrashed()->find($gid);

                if ($grievance && $grievance->trashed()) {
                    $grievance->forceDelete();
                }
            }
        }

        Notification::make()
            ->title('Assignments Updated')
            ->body("$count grievance(s) unassigned from {$this->hrLiaison->name}.")
            ->success()
            ->send();

        $this->resetPage();
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
            ->whereHas('grievance', fn($q) => $q->whereNull('deleted_at'));

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
