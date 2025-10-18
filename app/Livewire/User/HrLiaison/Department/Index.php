<?php

namespace App\Livewire\User\HrLiaison\Department;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Department;

#[Layout('layouts.app')]
#[Title('Department Info')]
class Index extends Component
{
    use WithFileUploads;

    public $departments = [];
    public $bgImage = [];
    public $profileImage = [];

    // Stats
    public $accountCreated;
    public $totalDepartments = 0;
    public $recentDepartment = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->departments = $user->departments()->get();

        // Stats
        $this->accountCreated = $user->created_at->format('M d, Y');
        $this->totalDepartments = $this->departments->count();
        $this->recentDepartment = $this->departments->sortByDesc('pivot_created_at')->first();
        // assumes your pivot table has timestamps. If not, you can sort by department_id
    }

    public function updatePhoto($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        if (isset($this->bgImage[$departmentId])) {
            $path = $this->bgImage[$departmentId]->store('departments/bg', 'public');
            $department->department_bg = $path;
        }

        if (isset($this->profileImage[$departmentId])) {
            $path = $this->profileImage[$departmentId]->store('departments/profile', 'public');
            $department->department_profile = $path;
        }

        $department->save();

        session()->flash('success', 'Department images updated successfully!');
        $this->departments = auth()->user()->departments()->get();

        // update stats again
        $this->totalDepartments = $this->departments->count();
        $this->recentDepartment = $this->departments->sortByDesc('pivot_created_at')->first();
    }

    public function render()
    {
        return view('livewire.user.hr-liaison.department.index');
    }
}


