<?php

namespace App\Livewire\User\Admin\Stakeholders\DepartmentsAndHrLiaisons;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Department;
use App\Models\User;
use Filament\Notifications\Notification;

#[Layout('layouts.app')]
#[Title('Departments & HR Liaisons')]
class Index extends Component
{
    use WithPagination;

    public $sortField = 'department_name';
    public $sortDirection = 'asc';
    public $selectedLiaisonsToAdd = [];
    public $selectedLiaisonsToRemove = [];

    protected $listeners = ['refresh' => '$refresh'];

    /** -------------------------------
     *  Sorting Logic
     *  ------------------------------- */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getDepartmentsProperty()
    {
        return Department::with('hrLiaisons')
            ->withCount('hrLiaisons')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function getAvailableLiaisons($departmentId)
    {
        $department = Department::find($departmentId);
        if (!$department) return [];

        // Get already assigned liaison IDs
        $assignedIds = $department->hrLiaisons->pluck('id')->toArray();

        // Get HR liaisons NOT assigned yet
        return User::role('hr_liaison')
            ->whereNotIn('id', $assignedIds)
            ->pluck('name', 'id')
            ->toArray();
    }


        public function getAssignedLiaisons($departmentId)
    {
        $department = Department::find($departmentId);
        if (!$department) return [];

        return $department->hrLiaisons->pluck('name', 'id')->toArray();
    }


    public function saveLiaison($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        if (empty($this->selectedLiaisonsToAdd)) {
            Notification::make()
                ->title('No HR Liaisons Selected')
                ->body('Please select at least one HR Liaison to add.')
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }

        $department->hrLiaisons()->attach($this->selectedLiaisonsToAdd);
        $this->reset('selectedLiaisonsToAdd');

        Notification::make()
            ->title('HR Liaisons Added Successfully')
            ->body("Selected HR Liaisons have been added to the {$department->department_name} department.")
            ->success()
            ->duration(4000)
            ->send();
    }

    public function removeLiaison($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        if (empty($this->selectedLiaisonsToRemove)) {
            Notification::make()
                ->title('No HR Liaisons Selected')
                ->body('Please select at least one HR Liaison to remove.')
                ->warning()
                ->duration(3000)
                ->send();
            return;
        }

        $department->hrLiaisons()->detach($this->selectedLiaisonsToRemove);
        $this->reset('selectedLiaisonsToRemove');

        Notification::make()
            ->title('HR Liaisons Removed Successfully')
            ->body("Selected HR Liaisons have been removed from the {$department->department_name} department.")
            ->success()
            ->duration(4000)
            ->send();
    }

    public function render()
    {
        $departments = $this->departments->map(function ($department) {
            $department->availableLiaisons = $this->getAvailableLiaisons($department->id);
            $department->assignedLiaisons = $this->getAssignedLiaisons($department->id);
            return $department;
        });

        return view('livewire.user.admin.stakeholders.departments-and-hr-liaisons.index', [
            'departments' => $departments,
        ]);
    }

}
