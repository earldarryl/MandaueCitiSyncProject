<?php

namespace App\Livewire\User\HrLiaison\Department;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Department;

#[Layout('layouts.app')]
#[Title('Department Info')]
class View extends Component
{
    public Department $department;

    protected $listeners = ['refreshHrLiaisons' => '$refresh'];

    public function mount(Department $department)
    {
        $this->department = $department->load('hrLiaisons');
    }

    public function render()
    {
        $hrLiaisons = $this->department->hrLiaisons()->get();

        return view('livewire.user.hr-liaison.department.view', [
            'hrLiaisons' => $hrLiaisons,
        ]);
    }
}
